<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $dichVuNoiBat = DB::table('DichVu')
            ->where('trang_thai', 'Hoat dong')
            ->limit(6)
            ->get();

        $danhGiaGanNhat = DB::table('DanhGia')
            ->whereNotNull('nhan_xet')
            ->orderBy('ngay_danh_gia', 'desc')
            ->limit(3)
            ->get();

        // Nếu đã đăng nhập, lấy thêm lịch hẹn sắp tới (đã đặt, chưa đến garage)
        // để hiện chung trên cả trang chủ công khai này
        $baoGiaChoDuyet = collect();
        $lichHenSapToi = collect();
        if (Auth::check() && Auth::user()->khachHang) {
            $baoGiaChoDuyet = DB::table('BaoGia as bg')
                ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
                ->join('Xe as x', 'p.xe_id', '=', 'x.id')
                ->where('x.khachhang_id', Auth::user()->khachHang->id)
                ->where('bg.trang_thai', 'Cho khach duyet')
                ->select('bg.id', 'bg.tong_tien', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
                ->get();

            $lichHenSapToi = DB::table('LichHen as lh')
                ->leftJoin('Xe as x', 'lh.xe_id', '=', 'x.id')
                ->where('lh.khachhang_id', Auth::user()->khachHang->id)
                ->where('lh.trang_thai', 'Da dat')
                ->select('lh.id', 'lh.ngay_gio', 'lh.hang_xe_du_kien', 'lh.dong_xe_du_kien', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
                ->orderBy('lh.ngay_gio', 'asc')
                ->get();

            foreach ($lichHenSapToi as $lh) {
                $lh->ten_dich_vu = DB::table('ChiTietLichHen as ctlh')
                    ->join('DichVu as dv', 'ctlh.dichvu_id', '=', 'dv.id')
                    ->where('ctlh.lichhen_id', $lh->id)
                    ->pluck('dv.ten_dich_vu')
                    ->implode(', ');
            }
        }

        return view('trang-chu', compact('dichVuNoiBat', 'danhGiaGanNhat', 'baoGiaChoDuyet', 'lichHenSapToi'));
    }

    public function dichVu(string $nhom = null)
    {
        $query = DB::table('DichVu')->where('trang_thai', 'Hoat dong');
        if ($nhom) {
            $query->where('nhom_dich_vu', $nhom);
        }
        $dichVuList = $query->orderBy('nhom_dich_vu')->get();

        return view('dich-vu', compact('dichVuList', 'nhom'));
    }

    public function danhGia()
    {
        $danhGiaList = DB::table('DanhGia')->orderBy('ngay_danh_gia', 'desc')->get();
        $tongSoDanhGia = $danhGiaList->count();
        $diemTrungBinh = $tongSoDanhGia > 0 ? round($danhGiaList->avg('so_sao'), 1) : 0;

        return view('danh-gia', compact('danhGiaList', 'tongSoDanhGia', 'diemTrungBinh'));
    }

    public function veGarage()
    {
        return view('ve-garage');
    }
}