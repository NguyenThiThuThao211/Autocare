<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class XeController extends Controller
{
    public function danhSach()
    {
        $khachHang = Auth::user()->khachHang;
        $dsXe = DB::table('Xe')
            ->where('khachhang_id', $khachHang->id ?? 0)
            ->where('trang_thai', 'Hoat dong')
            ->get();

        return view('xe.danh-sach', compact('dsXe'));
    }

    public function showThemMoi()
    {
        return view('xe.them-moi');
    }

    public function themMoi(Request $request)
    {
        $request->validate([
            'bien_so' => 'required|string|max:20|unique:Xe,bien_so',
            'hang_xe' => 'required|string|max:50',
            'dong_xe' => 'nullable|string|max:50',
            'doi_xe'  => 'nullable|string|max:10',
        ], [
            'bien_so.required' => 'Vui lòng nhập biển số',
            'bien_so.unique'   => 'Biển số này đã tồn tại trong hệ thống',
            'hang_xe.required' => 'Vui lòng nhập hãng xe',
        ]);

        $khachHang = Auth::user()->khachHang;

        DB::table('Xe')->insert([
            'khachhang_id' => $khachHang->id,
            'bien_so'      => $request->bien_so,
            'hang_xe'      => $request->hang_xe,
            'dong_xe'      => $request->dong_xe,
            'doi_xe'       => $request->doi_xe,
            'trang_thai'   => 'Hoat dong',
        ]);

        return redirect()->route('xe.danh-sach')->with('thanhcong', 'Đã thêm xe mới!');
    }
}