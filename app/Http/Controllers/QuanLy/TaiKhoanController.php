<?php

namespace App\Http\Controllers\QuanLy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TaiKhoanController extends Controller
{
    public function danhSach(Request $request)
{
    $tuKhoa = $request->query('q', '');
    $locVaiTro = $request->query('vai_tro_loc', 'nhan_vien'); // nhan_vien | khach_hang | tat_ca
    $locTrangThai = $request->query('trang_thai_loc', 'tat_ca'); // MỚI — hoat_dong | khoa | tat_ca
 
    $query = DB::table('TaiKhoan');
 
    if ($locVaiTro === 'nhan_vien') {
        $query->whereIn('vai_tro', ['Co van dich vu', 'Ky thuat vien', 'Quan ly']);
    } elseif ($locVaiTro === 'khach_hang') {
        $query->where('vai_tro', 'Khach hang');
    }
 
    // MỚI — lọc theo trạng thái hoạt động/khóa
    if ($locTrangThai === 'hoat_dong') {
        $query->where('trang_thai', 'Hoat dong');
    } elseif ($locTrangThai === 'khoa') {
        $query->where('trang_thai', 'Khoa');
    }
 
    if ($tuKhoa) {
        $query->where('email_sdt', 'like', "%$tuKhoa%");
    }
 
    $dsTaiKhoan = $query->orderBy('id', 'desc')->get();
 
    return view('quan-ly.tai-khoan.danh-sach', compact('dsTaiKhoan', 'tuKhoa', 'locVaiTro', 'locTrangThai'));
}

    public function showTaoMoi()
    {
        return view('quan-ly.tai-khoan.tao-moi');
    }

    public function taoMoi(Request $request)
    {
        $request->validate([
            'email_sdt' => 'required|string|max:100|unique:TaiKhoan,email_sdt',
            'vai_tro'   => 'required|in:Co van dich vu,Ky thuat vien,Quan ly',
            'mat_khau'  => 'nullable|string|min:6',
        ], [
            'email_sdt.required' => 'Vui lòng nhập email/SĐT đăng nhập',
            'email_sdt.unique'   => 'Email/SĐT này đã được sử dụng',
            'vai_tro.required'   => 'Vui lòng chọn vai trò',
            'mat_khau.min'       => 'Mật khẩu tối thiểu 6 ký tự',
        ]);

        // Nếu không nhập mật khẩu, tự sinh ngẫu nhiên — hiện ra 1 lần để QL gửi cho nhân viên
        $matKhauGoc = $request->mat_khau ?: Str::random(8);

        DB::table('TaiKhoan')->insert([
            'email_sdt'     => $request->email_sdt,
            'mat_khau_hash' => Hash::make($matKhauGoc),
            'vai_tro'       => $request->vai_tro,
            'trang_thai'    => 'Hoat dong',
        ]);

        return redirect()->route('quan-ly.tai-khoan')
            ->with('thanhcong', "Đã tạo tài khoản {$request->email_sdt}. Mật khẩu ban đầu: {$matKhauGoc} (hãy gửi cho nhân viên và yêu cầu đổi lại).");
    }

    /**
     * Khóa / Mở khóa tài khoản — không xóa hẳn, chỉ đổi trạng thái (giữ lịch sử dữ liệu liên quan)
     */
    public function doiTrangThai(int $id)
    {
        $taiKhoan = DB::table('TaiKhoan')->where('id', $id)->first();
        if (!$taiKhoan) {
            abort(404);
        }

        $trangThaiMoi = $taiKhoan->trang_thai === 'Hoat dong' ? 'Khoa' : 'Hoat dong';
        DB::table('TaiKhoan')->where('id', $id)->update(['trang_thai' => $trangThaiMoi]);

        return back()->with('thanhcong', $trangThaiMoi === 'Khoa' ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.');
    }
}