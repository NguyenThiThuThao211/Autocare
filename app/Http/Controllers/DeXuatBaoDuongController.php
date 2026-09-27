<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeXuatBaoDuongController extends Controller
{
    /**
     * Trang chi tiết đề xuất — KHÔNG cần đăng nhập, xác thực qua chữ ký link
     * (route được bảo vệ bằng middleware 'signed' — chỉ link do hệ thống tự tạo mới hợp lệ,
     * ai tự đoán số xe_id và gõ URL tay sẽ bị từ chối vì thiếu/sai chữ ký)
     */
    public function show(int $xeId, GeminiService $gemini)
    {
        $xe = DB::table('Xe')->where('id', $xeId)->first();
        if (!$xe) {
            abort(404);
        }

        $deXuat = cache()->remember(
            'goi_y_bao_duong_xe_' . $xeId,
            now()->addDay(),
            fn() => $gemini->nhacBaoDuong(
                DB::table('ChiTietBaoGia as ctbg')
                    ->join('BaoGia as bg', 'ctbg.baogia_id', '=', 'bg.id')
                    ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
                    ->join('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
                    ->where('p.xe_id', $xeId)
                    ->where('bg.trang_thai', 'Da duyet')
                    ->select('dv.ten_dich_vu', 'dv.chu_ky_khuyen_nghi_thang', 'bg.ngay_lap')
                    ->orderBy('bg.ngay_lap', 'desc')
                    ->get()
                    ->toArray()
            )
        );

        $daXuLy = cache()->has('de_xuat_da_xu_ly_' . $xeId);

        return view('de-xuat-bao-duong.chi-tiet', compact('xe', 'deXuat', 'daXuLy'));
    }

    /**
     * Chấp nhận — không cần đăng nhập, dùng thẳng khachhang_id gắn với xe (đã xác định từ link có chữ ký)
     */
    public function chapNhan(Request $request, int $xeId)
    {
        $xe = DB::table('Xe')->where('id', $xeId)->first();
        if (!$xe) {
            abort(404);
        }

        $request->validate([
            'ngay' => 'required|date',
            'gio'  => 'required|string',
        ], [
            'ngay.required' => 'Vui lòng chọn ngày',
            'gio.required'  => 'Vui lòng chọn khung giờ',
        ]);

        $deXuat = cache()->get('goi_y_bao_duong_xe_' . $xeId);
        $tenDichVu = $deXuat['dichVuSapDenHan'] ?? null;

        $dichVu = $tenDichVu ? DB::table('DichVu')->where('ten_dich_vu', 'like', "%$tenDichVu%")->first() : null;

        $lichHenId = DB::table('LichHen')->insertGetId([
            'khachhang_id' => $xe->khachhang_id,
            'xe_id'        => $xeId,
            'ngay_gio'     => $request->ngay . ' ' . $request->gio . ':00',
            'trang_thai'   => 'Da dat',
            'nguon'        => 'AI de xuat',
            'mo_ta_so_bo'  => 'Đặt lịch từ đề xuất bảo dưỡng: ' . ($tenDichVu ?? ''),
        ]);

        if ($dichVu) {
            DB::table('ChiTietLichHen')->insert(['lichhen_id' => $lichHenId, 'dichvu_id' => $dichVu->id]);
        }

        cache()->put('de_xuat_da_xu_ly_' . $xeId, true, now()->addDays(30));

        return back()->with('thanhcong', 'Đã đặt lịch hẹn theo đề xuất! Hẹn gặp bạn tại garage.');
    }

    public function tuChoi(int $xeId)
    {
        $xe = DB::table('Xe')->where('id', $xeId)->first();
        if (!$xe) {
            abort(404);
        }

        cache()->put('de_xuat_da_xu_ly_' . $xeId, true, now()->addDays(30));

        return back()->with('thanhcong', 'Đã ghi nhận. Bạn có thể đặt lịch lại bất cứ lúc nào.');
    }
}