<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThongBaoController extends Controller
{
    public function danhSach()
    {
        $khachHang = Auth::user()->khachHang;

        $dsThongBao = DB::table('ThongBao')
            ->where('khachhang_id', $khachHang->id ?? 0)
            ->orderBy('ngay_gui', 'desc')
            ->get();

        DB::table('ThongBao')->where('khachhang_id', $khachHang->id ?? 0)->update(['da_xem' => true]);

        return view('thong-bao.danh-sach', compact('dsThongBao'));
    }

    /**
     * Gọi qua AJAX khi khách mở dropdown thông báo ở header — đánh dấu đã đọc hết
     */
    public function danhDauDaXem()
    {
        $khachHang = Auth::user()->khachHang;
        DB::table('ThongBao')->where('khachhang_id', $khachHang->id ?? 0)->update(['da_xem' => true]);

        return response()->json(['ok' => true]);
    }
}