<?php

namespace App\Console\Commands;

use App\Services\GeminiService;
use App\Services\ThongBaoHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NhacBaoDuong extends Command
{
    protected $signature = 'nhac-bao-duong:chay';
    protected $description = 'Quét TOÀN BỘ xe trong hệ thống, gửi thông báo (app + email) chủ động cho xe sắp đến hạn bảo dưỡng';

    public function handle(GeminiService $gemini)
    {
        $this->info('Bắt đầu quét toàn bộ xe...');

        $dsXe = DB::table('Xe')->where('trang_thai', 'Hoat dong')->get();
        $soDaGui = 0;

        foreach ($dsXe as $xe) {
            // Đã gửi trong 24h qua rồi thì bỏ qua, tránh gửi trùng
            if (cache()->has('da_gui_nhac_bao_duong_' . $xe->id)) {
                continue;
            }

            // Khách đã XỬ LÝ đề xuất này rồi (dù Chấp nhận hay Từ chối) → KHÔNG gửi lại,
            // đúng theo đặc tả: "Từ chối → hệ thống ghi nhận, kết thúc" (không hỏi lại)
            if (cache()->has('de_xuat_da_xu_ly_' . $xe->id)) {
                continue;
            }

            $lichSu = DB::table('ChiTietBaoGia as ctbg')
                ->join('BaoGia as bg', 'ctbg.baogia_id', '=', 'bg.id')
                ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
                ->join('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
                ->where('p.xe_id', $xe->id)
                ->where('bg.trang_thai', 'Da duyet')
                ->select('dv.ten_dich_vu', 'dv.chu_ky_khuyen_nghi_thang', 'bg.ngay_lap')
                ->orderBy('bg.ngay_lap', 'desc')
                ->get();

            if ($lichSu->isEmpty()) {
                continue; // Xe chưa có lịch sử dịch vụ nào, không có gì để tính
            }

            $ketQua = $gemini->nhacBaoDuong($lichSu->toArray());

            // Hiện rõ lỗi nếu API thất bại — tránh nhầm lẫn với "AI nói không cần nhắc"
            if (isset($ketQua['loi'])) {
                $this->error("  ✗ Lỗi khi gọi AI cho xe #{$xe->id}: " . $ketQua['loi']);
                continue;
            }

            if ($ketQua['coDeXuat'] ?? false) {
                ThongBaoHelper::guiChoKhachHang(
                    $xe->khachhang_id,
                    'Sắp đến hạn: ' . ($ketQua['dichVuSapDenHan'] ?? '') . '. ' . ($ketQua['giaiThich'] ?? ''),
                    'Nhac bao duong',
                    \Illuminate\Support\Facades\URL::signedRoute('de-xuat-bao-duong.xem', ['xeId' => $xe->id])
                );

                cache()->put('da_gui_nhac_bao_duong_' . $xe->id, true, now()->addDay());
                $soDaGui++;

                $this->line("  ✓ Đã gửi cho xe #{$xe->id} ({$xe->bien_so})");
            }
        }

        $this->info("Hoàn tất. Đã gửi {$soDaGui}/{$dsXe->count()} xe.");
        return Command::SUCCESS;
    }
}