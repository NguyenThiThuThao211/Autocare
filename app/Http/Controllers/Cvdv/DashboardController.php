<?php

namespace App\Http\Controllers\Cvdv;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $phieuCho = DB::table('PhieuSuaChua')->where('trang_thai', 'Da tiep nhan')->count();
        $choBaoGia = DB::table('PhieuSuaChua')->where('trang_thai', 'Cho bao gia')->count();
        $lichHenHomNay = DB::table('LichHen')
            ->whereDate('ngay_gio', today())
            ->where('trang_thai', 'Da dat')
            ->count();

        $dsPhieuCanXuLy = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->whereIn('p.trang_thai', ['Da tiep nhan', 'Cho bao gia'])
            ->select('p.id', 'p.trang_thai', 'p.mo_ta_su_co', 'p.ngay_tiep_nhan', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
            ->orderBy('p.ngay_tiep_nhan', 'asc')
            ->limit(8)
            ->get();

        // Phiếu đã hoàn thành nhưng khách chưa đến thanh toán — cần chủ động nhắc
        $dsPhieuChoThanhToan = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->join('HoaDon as hd', 'p.id', '=', 'hd.phieu_id')
            ->where('p.trang_thai', 'Hoan thanh')
            ->where('hd.trang_thai_thanh_toan', 'Chua thanh toan')
            ->select('p.id', 'x.bien_so', 'kh.ho_ten', 'kh.sdt', 'hd.tong_tien', 'hd.ngay_lap')
            ->orderBy('hd.ngay_lap')
            ->get();

        // Báo giá đã lập, đang chờ KHÁCH duyệt (khác với "Chờ báo giá" — đây là CVDV đã lập rồi)
        $dsBaoGiaChoDuyet = DB::table('BaoGia as bg')
            ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->where('bg.trang_thai', 'Cho khach duyet')
            ->select('bg.id', 'p.id as phieu_id', 'x.bien_so', 'kh.ho_ten', 'kh.sdt', 'bg.tong_tien', 'bg.ngay_lap')
            ->orderBy('bg.ngay_lap')
            ->get();

        return view('cvdv.dashboard', compact(
            'phieuCho', 'choBaoGia', 'lichHenHomNay', 'dsPhieuCanXuLy',
            'dsPhieuChoThanhToan', 'dsBaoGiaChoDuyet'
        ));
    }
}