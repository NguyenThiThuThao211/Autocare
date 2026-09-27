<?php

namespace App\Http\Controllers\Cvdv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuanLyPhieuController extends Controller
{
    public function danhSach(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $locTrangThai = $request->query('trang_thai_loc', 'tat_ca');

        $query = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->leftJoin('TaiKhoan as ktv', 'p.ktv_id', '=', 'ktv.id')
            ->leftJoin('BaoGia as bg', 'bg.phieu_id', '=', 'p.id')
            ->select(
                'p.id', 'p.mo_ta_su_co', 'p.ket_qua_chan_doan', 'p.trang_thai', 'p.ngay_tiep_nhan',
                'x.bien_so', 'x.hang_xe', 'x.dong_xe',
                'kh.ho_ten', 'kh.sdt',
                'ktv.email_sdt as ktv_email',
                DB::raw('MAX(bg.id) as bao_gia_id'), DB::raw('MAX(bg.trang_thai) as bao_gia_trang_thai')
            )
            ->groupBy('p.id', 'p.mo_ta_su_co', 'p.ket_qua_chan_doan', 'p.trang_thai', 'p.ngay_tiep_nhan',
                       'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten', 'kh.sdt', 'ktv.email_sdt');

        if ($locTrangThai !== 'tat_ca') {
            $query->where('p.trang_thai', match ($locTrangThai) {
                'da_tiep_nhan' => 'Da tiep nhan',
                'cho_bao_gia'  => 'Cho bao gia',
                'hoan_thanh'   => 'Hoan thanh',
                'da_huy'       => 'Da huy',
                default        => 'Da tiep nhan',
            });
        }

        if ($tuKhoa) {
            $query->where(function ($q) use ($tuKhoa) {
                $q->where('x.bien_so', 'like', "%$tuKhoa%")
                  ->orWhere('kh.sdt', 'like', "%$tuKhoa%")
                  ->orWhere('kh.ho_ten', 'like', "%$tuKhoa%")
                  ->orWhere('p.id', $tuKhoa);
            });
        }

        $dsPhieu = $query->orderBy('p.ngay_tiep_nhan', 'desc')->get();

        return view('cvdv.phieu.danh-sach', compact('dsPhieu', 'tuKhoa', 'locTrangThai'));
    }

    public function chiTiet(int $id)
    {
        $phieu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->leftJoin('TaiKhoan as ktv', 'p.ktv_id', '=', 'ktv.id')
            ->where('p.id', $id)
            ->select('p.*', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten', 'kh.sdt', 'ktv.email_sdt as ktv_email')
            ->first();

        if (!$phieu) {
            abort(404);
        }

        $dsBaoGia = DB::table('BaoGia')->where('phieu_id', $id)->orderBy('id', 'desc')->get();

        foreach ($dsBaoGia as $bg) {
            $bg->chi_tiet = DB::table('ChiTietBaoGia as ctbg')
                ->leftJoin('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
                ->leftJoin('PhuTung as pt', 'ctbg.phutung_id', '=', 'pt.id')
                ->where('ctbg.baogia_id', $bg->id)
                ->select('ctbg.so_luong', 'ctbg.don_gia', 'dv.ten_dich_vu', 'pt.ten as ten_phu_tung', 'ctbg.mo_ta_khac')
                ->get();
        }

        $coBaoGiaDaDuyet = $dsBaoGia->contains('trang_thai', 'Da duyet');
        
$hoaDon = DB::table('HoaDon as hd')
    ->leftJoin('TaiKhoan as nl', 'hd.nguoi_lap', '=', 'nl.id')
    ->where('hd.phieu_id', $id)
    ->select('hd.*', 'nl.email_sdt as nguoi_lap_email')
    ->first();

        return view('cvdv.phieu.chi-tiet', compact('phieu', 'dsBaoGia', 'hoaDon', 'coBaoGiaDaDuyet'));
    }

    /**
     * CVDV đánh dấu phiếu Hoàn thành — sau khi đã nghiệm thu, xác nhận KTV sửa xong
     * đúng theo báo giá đã được khách duyệt (business rule đã chốt ở UC-08/UC-09)
     */
    /**
     * CVDV duyệt báo giá THAY khách hàng — dùng khi khách đồng ý qua điện thoại
     * hoặc trực tiếp tại quầy, không qua luồng online. Chạy đúng logic y hệt
     * lúc khách tự duyệt (chuyển trạng thái + thông báo đầy đủ cho KTV).
     */
    public function duyetThayKhach(int $baoGiaId)
    {
        $baoGia = DB::table('BaoGia as bg')
            ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
            ->where('bg.id', $baoGiaId)
            ->select('bg.*', 'p.id as phieu_id', 'p.ktv_id')
            ->first();

        if (!$baoGia || $baoGia->trang_thai !== 'Cho khach duyet') {
            return back()->withErrors(['loi' => 'Báo giá này không ở trạng thái chờ duyệt.']);
        }

        DB::table('BaoGia')->where('id', $baoGiaId)->update(['trang_thai' => 'Da duyet', 'ngay_duyet' => now()]);

        if ($baoGia->ktv_id) {
            $dsHangMuc = DB::table('ChiTietBaoGia as ctbg')
                ->leftJoin('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
                ->leftJoin('PhuTung as pt', 'ctbg.phutung_id', '=', 'pt.id')
                ->where('ctbg.baogia_id', $baoGiaId)
                ->get()
                ->map(fn($ct) => $ct->ten_dich_vu ?? $ct->ten_phu_tung ?? $ct->mo_ta_khac)
                ->implode(', ');

            DB::table('ThongBao')->insert([
                'taikhoan_id' => $baoGia->ktv_id,
                'noi_dung'    => 'CVDV đã duyệt thay khách báo giá phiếu #PSC-' . str_pad($baoGia->phieu_id, 4, '0', STR_PAD_LEFT)
                                . '. Tiến hành sửa chữa: ' . $dsHangMuc,
                'loai'        => 'Tien do', 'da_xem' => false, 'ngay_gui' => now(),
            ]);
        }

        return redirect()->route('cvdv.phieu.chi-tiet', $baoGia->phieu_id)->with('thanhcong', 'Đã duyệt báo giá thay khách hàng.');
    }

    /**
     * CVDV hủy phiếu giữa chừng — chỉ khi CHƯA "Hoàn thành" (khách đổi ý không sửa nữa)
     */
    public function huyPhieu(Request $request, int $id)
    {
        $phieu = DB::table('PhieuSuaChua')->where('id', $id)->first();
        if (!$phieu) {
            abort(404);
        }

        if ($phieu->trang_thai === 'Hoan thanh' || $phieu->trang_thai === 'Da huy') {
            return back()->withErrors(['loi' => 'Không thể hủy phiếu đã hoàn thành hoặc đã hủy trước đó.']);
        }

        $request->validate(['ly_do_huy_phieu' => 'required|string|max:255'], [
            'ly_do_huy_phieu.required' => 'Vui lòng nhập lý do hủy phiếu',
        ]);

        DB::table('PhieuSuaChua')->where('id', $id)->update(['trang_thai' => 'Da huy']);

        $khachHangId = DB::table('Xe')->where('id', $phieu->xe_id)->value('khachhang_id');
        \App\Services\ThongBaoHelper::guiChoKhachHang(
            $khachHangId,
            'Phiếu #PSC-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' đã bị hủy. Lý do: ' . $request->ly_do_huy_phieu,
            'Tien do'
        );

        return redirect()->route('cvdv.phieu.danh-sach')->with('thanhcong', 'Đã hủy phiếu sửa chữa.');
    }
}