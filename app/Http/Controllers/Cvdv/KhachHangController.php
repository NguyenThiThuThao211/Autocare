<?php

namespace App\Http\Controllers\Cvdv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhachHangController extends Controller
{
    public function danhSach(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $locSoLanSua = $request->query('so_lan_sua', ''); // '' | '0' | '1-3' | '4+'
        $locXe = $request->query('co_xe', ''); // '' | 'co' | 'chua_co'

        $query = DB::table('KhachHang')->where('trang_thai', 'Hoat dong');

        if ($tuKhoa) {
            $query->where(function ($q) use ($tuKhoa) {
                $q->where('ho_ten', 'like', "%$tuKhoa%")
                  ->orWhere('sdt', 'like', "%$tuKhoa%");
            });
        }

        $dsKhachHang = $query->orderBy('ho_ten')->get();

        // Đếm nhanh số xe + số phiếu + lấy tên xe từng khách, tránh N+1 query nặng
        foreach ($dsKhachHang as $kh) {
            $dsXeCuaKhach = DB::table('Xe')->where('khachhang_id', $kh->id)->get(['hang_xe', 'dong_xe', 'bien_so']);
            $kh->so_xe = $dsXeCuaKhach->count();
            $kh->ten_xe_dau_tien = $dsXeCuaKhach->isNotEmpty()
                ? $dsXeCuaKhach->first()->hang_xe . ' ' . $dsXeCuaKhach->first()->dong_xe . ' (' . $dsXeCuaKhach->first()->bien_so . ')'
                : null;
            $kh->so_phieu = DB::table('PhieuSuaChua as p')
                ->join('Xe as x', 'p.xe_id', '=', 'x.id')
                ->where('x.khachhang_id', $kh->id)
                ->count();
        }

        // Lọc theo số lần sửa chữa — thực hiện sau khi đã đếm, vì không lọc được trực tiếp bằng SQL ở trên
        if ($locSoLanSua === '0') {
            $dsKhachHang = $dsKhachHang->where('so_phieu', 0);
        } elseif ($locSoLanSua === '1-3') {
            $dsKhachHang = $dsKhachHang->whereBetween('so_phieu', [1, 3]);
        } elseif ($locSoLanSua === '4+') {
            $dsKhachHang = $dsKhachHang->where('so_phieu', '>=', 4);
        }

        // Lọc theo có/chưa có xe trong hồ sơ
        if ($locXe === 'co') {
            $dsKhachHang = $dsKhachHang->where('so_xe', '>', 0);
        } elseif ($locXe === 'chua_co') {
            $dsKhachHang = $dsKhachHang->where('so_xe', 0);
        }

        $dsKhachHang = $dsKhachHang->values();

        return view('cvdv.khach-hang.danh-sach', compact('dsKhachHang', 'tuKhoa', 'locSoLanSua', 'locXe'));
    }

    public function chiTiet(int $id)
    {
        $khachHang = DB::table('KhachHang')->where('id', $id)->first();
        if (!$khachHang) {
            abort(404);
        }

        $dsXe = DB::table('Xe')->where('khachhang_id', $id)->get();

        $lichSuPhieu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('x.khachhang_id', $id)
            ->select('p.id', 'p.mo_ta_su_co', 'p.trang_thai', 'p.ngay_tiep_nhan', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
            ->orderBy('p.ngay_tiep_nhan', 'desc')
            ->get();

        return view('cvdv.khach-hang.chi-tiet', compact('khachHang', 'dsXe', 'lichSuPhieu'));
    }

    public function showSua(int $id)
    {
        $khachHang = DB::table('KhachHang')->where('id', $id)->first();
        if (!$khachHang) {
            abort(404);
        }
        return view('cvdv.khach-hang.sua', compact('khachHang'));
    }

    /**
     * Sửa thông tin khách — hữu ích nhất cho khách vãng lai KHÔNG có tài khoản
     * đăng nhập (không tự sửa được qua "Cập nhật hồ sơ" như khách có tài khoản)
     */
    public function sua(Request $request, int $id)
    {
        $khachHang = DB::table('KhachHang')->where('id', $id)->first();
        if (!$khachHang) {
            abort(404);
        }

        $request->validate([
            'ho_ten'    => 'required|string|max:150',
            'sdt'       => 'required|string|max:20',
            'email'     => 'nullable|email|max:100',
            'dia_chi'   => 'nullable|string|max:255',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên',
            'sdt.required'    => 'Vui lòng nhập số điện thoại',
        ]);

        DB::table('KhachHang')->where('id', $id)->update([
            'ho_ten'  => $request->ho_ten,
            'sdt'     => $request->sdt,
            'email'   => $request->email,
            'dia_chi' => $request->dia_chi,
        ]);

        return redirect()->route('cvdv.khach-hang.chi-tiet', $id)->with('thanhcong', 'Đã cập nhật thông tin khách hàng.');
    }
}