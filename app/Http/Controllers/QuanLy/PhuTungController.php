<?php

namespace App\Http\Controllers\QuanLy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhuTungController extends Controller
{
    public function danhSach(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $locTonKho = $request->query('loc_ton_kho', 'tat_ca');
        $locTrangThai = $request->query('loc_trang_thai', 'hoat_dong'); // tat_ca | sap_het

        $query = DB::table('PhuTung');

        if ($locTonKho === 'sap_het') {
            $query->where('ton_kho', '<=', 5);
        }
        if ($locTrangThai === 'hoat_dong') {
    $query->where('trang_thai', 'Hoat dong');
} elseif ($locTrangThai === 'da_an') {
    $query->where('trang_thai', '!=', 'Hoat dong');
}

        if ($tuKhoa) {
            $query->where('ten', 'like', "%$tuKhoa%");
        }

        $dsPhuTung = $query->orderBy('ten')->get();

        return view('quan-ly.phu-tung.danh-sach', compact('dsPhuTung', 'tuKhoa', 'locTonKho', 'locTrangThai'));
    }

    public function showTaoMoi()
    {
        return view('quan-ly.phu-tung.tao-moi');
    }

    public function taoMoi(Request $request)
    {
        $request->validate([
            'ten'      => 'required|string|max:150',
            'gia_ban'  => 'required|numeric|min:0',
            'ton_kho'  => 'required|integer|min:0',
        ], [
            'ten.required'     => 'Vui lòng nhập tên phụ tùng',
            'gia_ban.required' => 'Vui lòng nhập giá bán',
            'ton_kho.required' => 'Vui lòng nhập số lượng tồn kho ban đầu',
        ]);

        $phuTungId = DB::table('PhuTung')->insertGetId([
            'ten'        => $request->ten,
            'gia_ban'    => $request->gia_ban,
            'ton_kho'    => $request->ton_kho,
            'trang_thai' => 'Hoat dong',
        ]);

        // Ghi nhận giao dịch nhập kho ban đầu
        if ($request->ton_kho > 0) {
            DB::table('GiaoDichKho')->insert([
                'phutung_id'      => $phuTungId,
                'phieu_id'        => null,
                'loai'            => 'Nhap',
                'so_luong'        => $request->ton_kho,
                'nguoi_thuc_hien' => auth()->id(),
                'thoi_diem'       => now(),
            ]);
        }

        return redirect()->route('quan-ly.phu-tung')->with('thanhcong', 'Đã thêm phụ tùng mới!');
    }

    /**
     * Nhập thêm kho (form nhỏ ngay trên danh sách)
     */
    public function nhapKho(Request $request, int $id)
    {
        $request->validate([
            'so_luong_nhap' => 'required|integer|min:1',
            'nha_cung_cap'  => 'nullable|string|max:150',
        ]);

        $phuTung = DB::table('PhuTung')->where('id', $id)->first();
        if (!$phuTung) {
            abort(404);
        }

        DB::table('PhuTung')->where('id', $id)->increment('ton_kho', $request->so_luong_nhap);

        DB::table('GiaoDichKho')->insert([
            'phutung_id'      => $id,
            'phieu_id'        => null,
            'loai'            => 'Nhap',
            'so_luong'        => $request->so_luong_nhap,
            'nha_cung_cap'    => $request->nha_cung_cap,
            'nguoi_thuc_hien' => auth()->id(),
            'thoi_diem'       => now(),
        ]);

        return back()->with('thanhcong', "Đã nhập thêm {$request->so_luong_nhap} {$phuTung->ten} vào kho.");
    }
   public function lichSu(int $id)
{
    $phuTung = DB::table('PhuTung')->where('id', $id)->first();
    if (!$phuTung) {
        abort(404);
    }
 
    $dsGiaoDich = DB::table('GiaoDichKho as gd')
        ->leftJoin('TaiKhoan as nv', 'gd.nguoi_thuc_hien', '=', 'nv.id')
        ->where('gd.phutung_id', $id)
        ->select('gd.*', 'nv.email_sdt as nguoi_thuc_hien_email')
        ->orderBy('gd.thoi_diem', 'desc')
        ->get();
 
    return view('quan-ly.phu-tung.lich-su', compact('phuTung', 'dsGiaoDich'));
}

public function doiTrangThai(int $id)
{
    $phuTung = DB::table('PhuTung')->where('id', $id)->first();
    if (!$phuTung) {
        abort(404);
    }
 
    DB::table('PhuTung')->where('id', $id)->update([
        'trang_thai' => $phuTung->trang_thai === 'Hoat dong' ? 'Ngung' : 'Hoat dong',
    ]);
 
    return back()->with('thanhcong', $phuTung->trang_thai === 'Hoat dong' ? 'Đã ẩn phụ tùng.' : 'Đã hiện lại phụ tùng.');
}
 
}