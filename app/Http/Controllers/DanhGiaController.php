<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DanhGiaController extends Controller
{
    /**
     * Form đánh giá — chỉ khi phiếu đã Hoàn thành và CHƯA từng đánh giá
     */
    public function showTaoMoi(int $phieuId)
    {
        $khachHang = Auth::user()->khachHang;

        $phieu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('p.id', $phieuId)
            ->where('x.khachhang_id', $khachHang->id ?? 0)
            ->where('p.trang_thai', 'Hoan thanh')
            ->select('p.id', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
            ->first();

        if (!$phieu) {
            abort(404);
        }

        $daDanhGia = DB::table('DanhGia')->where('phieu_id', $phieuId)->exists();
        if ($daDanhGia) {
            return redirect()->route('trang-chu')->with('thanhcong', 'Bạn đã đánh giá phiếu này rồi.');
        }

        return view('danh-gia.tao-moi', compact('phieu'));
    }

    public function luu(Request $request, int $phieuId)
    {
        $khachHang = Auth::user()->khachHang;

        $phieu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('p.id', $phieuId)
            ->where('x.khachhang_id', $khachHang->id ?? 0)
            ->first();

        if (!$phieu) {
            abort(404);
        }

        if (DB::table('DanhGia')->where('phieu_id', $phieuId)->exists()) {
            return redirect()->route('trang-chu')->with('thanhcong', 'Bạn đã đánh giá phiếu này rồi.');
        }

        $request->validate([
            'so_sao'   => 'required|integer|min:1|max:5',
            'nhan_xet' => 'nullable|string|max:500',
        ], [
            'so_sao.required' => 'Vui lòng chọn số sao đánh giá',
        ]);

        DB::table('DanhGia')->insert([
            'phieu_id'      => $phieuId,
            'so_sao'        => $request->so_sao,
            'nhan_xet'      => $request->nhan_xet,
            'ngay_danh_gia' => now(),
        ]);

        return redirect()->route('trang-chu')->with('thanhcong', 'Cảm ơn bạn đã đánh giá!');
    }
}