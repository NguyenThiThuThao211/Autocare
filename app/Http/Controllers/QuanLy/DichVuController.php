<?php

namespace App\Http\Controllers\QuanLy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DichVuController extends Controller
{
    public function danhSach(Request $request)
{
    $tuKhoa = $request->query('q', '');
    $locTrangThai = $request->query('trang_thai_loc', 'tat_ca'); // MỚI — hoat_dong | da_an | tat_ca
 
    $query = DB::table('DichVu');
 
    if ($tuKhoa) {
        $query->where('ten_dich_vu', 'like', "%$tuKhoa%");
    }
 
    // MỚI — lọc theo trạng thái hoạt động/ẩn
    if ($locTrangThai === 'hoat_dong') {
        $query->where('trang_thai', 'Hoat dong');
    } elseif ($locTrangThai === 'da_an') {
        $query->where('trang_thai', 'Ngung');
    }
 
    $dsDichVu = $query->orderBy('nhom_dich_vu')->orderBy('ten_dich_vu')->get();
 
    return view('quan-ly.dich-vu.danh-sach', compact('dsDichVu', 'tuKhoa', 'locTrangThai'));
}
    public function showTaoMoi()
    {
        return view('quan-ly.dich-vu.tao-moi');
    }

    public function taoMoi(Request $request)
    {
        $request->validate([
            'ten_dich_vu'                => 'required|string|max:150',
            'nhom_dich_vu'                => 'required|string|max:50',
            'don_gia'                     => 'required|numeric|min:1',
            'chu_ky_khuyen_nghi_thang'    => 'nullable|integer|min:1',
            'mo_ta'                       => 'nullable|string|max:500',
        ], [
            'ten_dich_vu.required' => 'Vui lòng nhập tên dịch vụ',
            'nhom_dich_vu.required' => 'Vui lòng nhập nhóm dịch vụ',
            'don_gia.required'      => 'Vui lòng nhập đơn giá',
        ]);

        DB::table('DichVu')->insert([
            'ten_dich_vu'             => $request->ten_dich_vu,
            'nhom_dich_vu'             => $request->nhom_dich_vu,
            'don_gia'                  => $request->don_gia,
            'chu_ky_khuyen_nghi_thang' => $request->chu_ky_khuyen_nghi_thang,
            'mo_ta'                    => $request->mo_ta,
            'trang_thai'               => 'Hoat dong',
        ]);

        return redirect()->route('quan-ly.dich-vu')->with('thanhcong', 'Đã thêm dịch vụ mới!');
    }

    public function showSua(int $id)
    {
        $dichVu = DB::table('DichVu')->where('id', $id)->first();
        if (!$dichVu) {
            abort(404);
        }
        return view('quan-ly.dich-vu.sua', compact('dichVu'));
    }

    public function sua(Request $request, int $id)
    {
        $request->validate([
            'ten_dich_vu'                => 'required|string|max:150',
            'nhom_dich_vu'                => 'required|string|max:50',
            'don_gia'                     => 'required|numeric|min:1',
            'chu_ky_khuyen_nghi_thang'    => 'nullable|integer|min:1',
            'mo_ta'                       => 'nullable|string|max:500',
        ]);

        DB::table('DichVu')->where('id', $id)->update([
            'ten_dich_vu'             => $request->ten_dich_vu,
            'nhom_dich_vu'             => $request->nhom_dich_vu,
            'don_gia'                  => $request->don_gia,
            'chu_ky_khuyen_nghi_thang' => $request->chu_ky_khuyen_nghi_thang,
            'mo_ta'                    => $request->mo_ta,
        ]);

        return redirect()->route('quan-ly.dich-vu')->with('thanhcong', 'Đã cập nhật dịch vụ!');
    }

    /**
     * Ẩn/Hiện dịch vụ — không xóa hẳn (giữ lịch sử báo giá/phiếu cũ đã tham chiếu tới)
     */
    public function doiTrangThai(int $id)
    {
        $dichVu = DB::table('DichVu')->where('id', $id)->first();
        if (!$dichVu) {
            abort(404);
        }

        $trangThaiMoi = $dichVu->trang_thai === 'Hoat dong' ? 'Ngung' : 'Hoat dong';
        DB::table('DichVu')->where('id', $id)->update(['trang_thai' => $trangThaiMoi]);

        return back()->with('thanhcong', $trangThaiMoi === 'Ngung' ? 'Đã ẩn dịch vụ.' : 'Đã hiện lại dịch vụ.');
    }
}