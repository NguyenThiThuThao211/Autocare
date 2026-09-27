<?php

namespace App\Http\Controllers\Cvdv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    public function showTaoMoi(int $phieuId)
    {
        $phieu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->where('p.id', $phieuId)
            ->select('p.*', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten', 'kh.sdt')
            ->first();

        if (!$phieu || $phieu->trang_thai !== 'Hoan thanh') {
            abort(404);
        }

        if (DB::table('HoaDon')->where('phieu_id', $phieuId)->exists()) {
            return redirect()->route('cvdv.phieu.chi-tiet', $phieuId)->with('thanhcong', 'Phiếu này đã có hóa đơn rồi.');
        }

        $baoGiaDaDuyet = DB::table('BaoGia')->where('phieu_id', $phieuId)->where('trang_thai', 'Da duyet')->first();

        $chiTiet = DB::table('ChiTietBaoGia as ctbg')
            ->leftJoin('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
            ->leftJoin('PhuTung as pt', 'ctbg.phutung_id', '=', 'pt.id')
            ->where('ctbg.baogia_id', $baoGiaDaDuyet->id ?? 0)
            ->select('ctbg.so_luong', 'ctbg.don_gia', 'dv.ten_dich_vu', 'pt.ten as ten_phu_tung')
            ->get();

        return view('cvdv.hoa-don.tao-moi', compact('phieu', 'baoGiaDaDuyet', 'chiTiet'));
    }

    public function luu(int $phieuId)
    {
        $phieu = DB::table('PhieuSuaChua')->where('id', $phieuId)->first();
        if (!$phieu || $phieu->trang_thai !== 'Hoan thanh') {
            abort(404);
        }

        $baoGiaDaDuyet = DB::table('BaoGia')->where('phieu_id', $phieuId)->where('trang_thai', 'Da duyet')->first();
        if (!$baoGiaDaDuyet) {
            return back()->withErrors(['loi' => 'Phiếu chưa có báo giá đã được duyệt.']);
        }

        if (DB::table('HoaDon')->where('phieu_id', $phieuId)->exists()) {
            return redirect()->route('cvdv.phieu.chi-tiet', $phieuId)->with('thanhcong', 'Phiếu này đã có hóa đơn rồi.');
        }

        DB::table('HoaDon')->insert([
            'phieu_id'              => $phieuId,
            'tong_tien'             => $baoGiaDaDuyet->tong_tien,
            'nguoi_lap'              => Auth::id(),
            'trang_thai_thanh_toan' => 'Chua thanh toan',
            'ngay_lap'              => now(),
        ]);

        // Thông báo cho khách hàng — hóa đơn đã sẵn sàng
        $khachHangId = DB::table('Xe')->where('id', $phieu->xe_id)->value('khachhang_id');
        DB::table('ThongBao')->insert([
            'khachhang_id' => $khachHangId,
            'noi_dung'     => 'Hóa đơn cho phiếu #PSC-' . str_pad($phieuId, 4, '0', STR_PAD_LEFT) . ' đã sẵn sàng. Vui lòng thanh toán tại quầy.',
            'loai'         => 'Hoa don', 'da_xem' => false, 'ngay_gui' => now(),
        ]);

        return redirect()->route('cvdv.phieu.chi-tiet', $phieuId)->with('thanhcong', 'Đã lập hóa đơn thành công!');
    }

    /**
     * CVDV đánh dấu đã thanh toán — hành vi THỦ CÔNG hoàn toàn (đã chốt từ đầu:
     * không xử lý giao dịch thật, chỉ đánh dấu cờ trạng thái sau khi tự kiểm tra
     * tiền đã vào tài khoản qua app ngân hàng riêng, ngoài phạm vi hệ thống)
     */
    public function danhDauDaThanhToan(int $hoaDonId)
    {
        $hoaDon = DB::table('HoaDon')->where('id', $hoaDonId)->first();
        if (!$hoaDon) {
            abort(404);
        }

        DB::table('HoaDon')->where('id', $hoaDonId)->update(['trang_thai_thanh_toan' => 'Da thanh toan']);

        return redirect()->route('cvdv.phieu.chi-tiet', $hoaDon->phieu_id)->with('thanhcong', 'Đã đánh dấu hóa đơn là Đã thanh toán.');
    }
}