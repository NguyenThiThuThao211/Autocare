<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BaoGiaController extends Controller
{
    public function show(int $id)
    {
        $khachHang = Auth::user()->khachHang;

        $baoGia = DB::table('BaoGia as bg')
            ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('bg.id', $id)
            ->where('x.khachhang_id', $khachHang->id ?? 0)
            ->select('bg.*', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'p.mo_ta_su_co', 'p.ket_qua_chan_doan')
            ->first();

        if (!$baoGia) {
            abort(404);
        }

        $chiTiet = DB::table('ChiTietBaoGia as ctbg')
            ->leftJoin('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
            ->leftJoin('PhuTung as pt', 'ctbg.phutung_id', '=', 'pt.id')
            ->where('ctbg.baogia_id', $id)
            ->select('ctbg.so_luong', 'ctbg.don_gia', 'dv.ten_dich_vu', 'pt.ten as ten_phu_tung', 'ctbg.mo_ta_khac')
            ->get();

        return view('bao-gia.chi-tiet', compact('baoGia', 'chiTiet'));
    }

    /**
     * Khách đồng ý báo giá — kèm gửi ĐẦY ĐỦ thông tin + hạng mục sửa chữa cho KTV phụ trách
     */
    public function duyet(int $id)
    {
        $khachHang = Auth::user()->khachHang;

        $baoGia = DB::table('BaoGia as bg')
            ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('bg.id', $id)
            ->where('x.khachhang_id', $khachHang->id ?? 0)
            ->select('bg.*', 'p.id as phieu_id', 'p.ktv_id')
            ->first();

        if (!$baoGia) {
            abort(404);
        }

        DB::table('BaoGia')->where('id', $id)->update(['trang_thai' => 'Da duyet', 'ngay_duyet' => now()]);

        // Thông báo đầy đủ cho KTV — kèm liệt kê hạng mục cụ thể để KTV biết chính xác cần làm gì
        if ($baoGia->ktv_id) {
            $dsHangMuc = DB::table('ChiTietBaoGia as ctbg')
                ->leftJoin('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
                ->leftJoin('PhuTung as pt', 'ctbg.phutung_id', '=', 'pt.id')
                ->where('ctbg.baogia_id', $id)
                ->get()
                ->map(fn($ct) => $ct->ten_dich_vu ?? $ct->ten_phu_tung ?? $ct->mo_ta_khac)
                ->implode(', ');

            DB::table('ThongBao')->insert([
                'taikhoan_id' => $baoGia->ktv_id,
                'noi_dung'    => 'Khách đã duyệt báo giá phiếu #PSC-' . str_pad($baoGia->phieu_id, 4, '0', STR_PAD_LEFT)
                                . '. Tiến hành sửa chữa: ' . $dsHangMuc,
                'loai'        => 'Tien do', 'da_xem' => false, 'ngay_gui' => now(),
            ]);
        }

        return redirect()->route('trang-chu')->with('thanhcong', 'Đã duyệt báo giá. Garage sẽ tiến hành sửa chữa.');
    }

    public function tuChoi(Request $request, int $id)
    {
        $khachHang = Auth::user()->khachHang;

        $baoGia = DB::table('BaoGia as bg')
            ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('bg.id', $id)
            ->where('x.khachhang_id', $khachHang->id ?? 0)
            ->select('bg.*', 'p.id as phieu_id')
            ->first();

        if (!$baoGia) {
            abort(404);
        }

        // Thông báo cho tất cả CVDV biết để điều chỉnh lại báo giá
        $dsCVDV = DB::table('TaiKhoan')->where('vai_tro', 'Co van dich vu')->where('trang_thai', 'Hoat dong')->pluck('id');
        foreach ($dsCVDV as $cvdvId) {
            DB::table('ThongBao')->insert([
                'taikhoan_id' => $cvdvId,
                'noi_dung'    => 'Khách đã từ chối báo giá #' . $id . ' (phiếu #PSC-' . str_pad($baoGia->phieu_id, 4, '0', STR_PAD_LEFT) . '). Vui lòng liên hệ điều chỉnh.',
                'loai'        => 'Tien do', 'da_xem' => false, 'ngay_gui' => now(),
            ]);
        }

        return redirect()->route('trang-chu')->with('thanhcong', 'Đã gửi từ chối. Garage sẽ liên hệ điều chỉnh báo giá.');
    }
}