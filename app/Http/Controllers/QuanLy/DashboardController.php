<?php

namespace App\Http\Controllers\QuanLy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Cho phép chọn kỳ báo cáo — mặc định tháng hiện tại nếu không truyền vào
        $tuNgay = $request->query('tu_ngay') ?: now()->startOfMonth()->format('Y-m-d');
        $denNgay = $request->query('den_ngay') ?: now()->format('Y-m-d');

        $doanhThuThang = DB::table('HoaDon')
            ->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->sum('tong_tien');

        $soPhieuHoanThanh = DB::table('PhieuSuaChua')
            ->where('trang_thai', 'Hoan thanh')
            ->whereBetween('ngay_tiep_nhan', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->count();

        $tongBaoGia = DB::table('BaoGia')
            ->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->count();
        $baoGiaTuAI = DB::table('BaoGia')
            ->where('nguon_goi_y', 'AI')
            ->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->count();
        $tyLeAiChapNhan = $tongBaoGia > 0 ? round(($baoGiaTuAI / $tongBaoGia) * 100) : 0;

        $diemDanhGiaTrungBinh = DB::table('DanhGia')
            ->whereBetween('ngay_danh_gia', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->avg('so_sao');
        $soLuongDanhGia = DB::table('DanhGia')
            ->whereBetween('ngay_danh_gia', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->count();

        $hieuSuatKTV = DB::table('PhieuSuaChua as p')
            ->join('TaiKhoan as tk', 'p.ktv_id', '=', 'tk.id')
            ->where('p.trang_thai', 'Hoan thanh')
            ->whereBetween('p.ngay_tiep_nhan', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->select('tk.email_sdt', DB::raw('count(*) as so_phieu'))
            ->groupBy('tk.id', 'tk.email_sdt')
            ->orderByDesc('so_phieu')
            ->get();

        $soPhieuMax = $hieuSuatKTV->max('so_phieu') ?: 1;

        // Tiền chưa thu — hóa đơn đã lập nhưng khách chưa thanh toán trong kỳ
        $tongTienChuaThu = DB::table('HoaDon')
            ->where('trang_thai_thanh_toan', 'Chua thanh toan')
            ->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->sum('tong_tien');

        // Nhóm lỗi thường gặp nhất — dựa vào kết quả phân loại AI
        $thongKeNhomLoi = DB::table('PhieuSuaChua')
            ->whereNotNull('nhom_su_co_ai')
            ->where('nhom_su_co_ai', '!=', '')
            ->whereBetween('ngay_tiep_nhan', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->selectRaw('nhom_su_co_ai, COUNT(*) as so_luong')
            ->groupBy('nhom_su_co_ai')
            ->orderByDesc('so_luong')
            ->limit(6)
            ->get();

        // Dữ liệu cho biểu đồ — doanh thu theo từng ngày trong kỳ đã chọn
        $doanhThuTheoNgay = DB::table('HoaDon')
            ->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->selectRaw('DATE(ngay_lap) as ngay, SUM(tong_tien) as tong')
            ->groupBy('ngay')
            ->orderBy('ngay')
            ->get();

        // Dữ liệu cho biểu đồ — phân bổ trạng thái phiếu trong kỳ
        $phanBoTrangThaiPhieu = DB::table('PhieuSuaChua')
            ->whereBetween('ngay_tiep_nhan', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->selectRaw('trang_thai, COUNT(*) as so_luong')
            ->groupBy('trang_thai')
            ->get();

        return view('quan-ly.dashboard', compact(
            'doanhThuThang', 'soPhieuHoanThanh', 'tyLeAiChapNhan',
            'diemDanhGiaTrungBinh', 'soLuongDanhGia', 'hieuSuatKTV', 'soPhieuMax',
            'doanhThuTheoNgay', 'phanBoTrangThaiPhieu', 'tongTienChuaThu', 'thongKeNhomLoi',
            'tuNgay', 'denNgay'
        ));
    }

    /**
     * Lấy sẵn dữ liệu báo cáo — dùng chung cho cả trang xem, xuất PDF, xuất CSV
     */
    private function layDuLieuBaoCao(string $tuNgay, string $denNgay): array
    {
        $doanhThuThang = DB::table('HoaDon')
            ->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->sum('tong_tien');

        $soPhieuHoanThanh = DB::table('PhieuSuaChua')
            ->where('trang_thai', 'Hoan thanh')
            ->whereBetween('ngay_tiep_nhan', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->count();

        $tongBaoGia = DB::table('BaoGia')->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])->count();
        $baoGiaTuAI = DB::table('BaoGia')->where('nguon_goi_y', 'AI')->whereBetween('ngay_lap', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])->count();
        $tyLeAiChapNhan = $tongBaoGia > 0 ? round(($baoGiaTuAI / $tongBaoGia) * 100) : 0;

        $diemDanhGiaTrungBinh = DB::table('DanhGia')->whereBetween('ngay_danh_gia', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])->avg('so_sao');
        $soLuongDanhGia = DB::table('DanhGia')->whereBetween('ngay_danh_gia', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])->count();

        $hieuSuatKTV = DB::table('PhieuSuaChua as p')
            ->join('TaiKhoan as tk', 'p.ktv_id', '=', 'tk.id')
            ->where('p.trang_thai', 'Hoan thanh')
            ->whereBetween('p.ngay_tiep_nhan', [$tuNgay . ' 00:00:00', $denNgay . ' 23:59:59'])
            ->select('tk.email_sdt', DB::raw('count(*) as so_phieu'))
            ->groupBy('tk.id', 'tk.email_sdt')
            ->orderByDesc('so_phieu')
            ->get();

        return compact('doanhThuThang', 'soPhieuHoanThanh', 'tyLeAiChapNhan', 'diemDanhGiaTrungBinh', 'soLuongDanhGia', 'hieuSuatKTV', 'tuNgay', 'denNgay');
    }

    public function xuatPdf(Request $request)
    {
        $tuNgay = $request->query('tu_ngay') ?: now()->startOfMonth()->format('Y-m-d');
        $denNgay = $request->query('den_ngay') ?: now()->format('Y-m-d');

        $duLieu = $this->layDuLieuBaoCao($tuNgay, $denNgay);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quan-ly.bao-cao-pdf', $duLieu);

        return $pdf->download('bao-cao-van-hanh-' . $tuNgay . '-den-' . $denNgay . '.pdf');
    }

    public function xuatCsv(Request $request)
    {
        $tuNgay = $request->query('tu_ngay') ?: now()->startOfMonth()->format('Y-m-d');
        $denNgay = $request->query('den_ngay') ?: now()->format('Y-m-d');

        $duLieu = $this->layDuLieuBaoCao($tuNgay, $denNgay);

        $tenFile = 'bao-cao-van-hanh-' . $tuNgay . '-den-' . $denNgay . '.csv';

        return response()->streamDownload(function () use ($duLieu) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM — để Excel hiển thị đúng tiếng Việt có dấu

            fputcsv($out, ['Báo cáo vận hành', $duLieu['tuNgay'] . ' đến ' . $duLieu['denNgay']]);
            fputcsv($out, []);
            fputcsv($out, ['Chỉ số', 'Giá trị']);
            fputcsv($out, ['Doanh thu', number_format($duLieu['doanhThuThang']) . 'đ']);
            fputcsv($out, ['Phiếu hoàn thành', $duLieu['soPhieuHoanThanh']]);
            fputcsv($out, ['Tỷ lệ AI được chấp nhận', $duLieu['tyLeAiChapNhan'] . '%']);
            fputcsv($out, ['Đánh giá trung bình', $duLieu['diemDanhGiaTrungBinh'] ? round($duLieu['diemDanhGiaTrungBinh'], 1) : '—']);
            fputcsv($out, ['Số lượt đánh giá', $duLieu['soLuongDanhGia']]);
            fputcsv($out, []);
            fputcsv($out, ['Hiệu suất kỹ thuật viên', '']);
            fputcsv($out, ['Kỹ thuật viên', 'Số phiếu hoàn thành']);
            foreach ($duLieu['hieuSuatKTV'] as $ktv) {
                fputcsv($out, [$ktv->email_sdt, $ktv->so_phieu]);
            }

            fclose($out);
        }, $tenFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}