<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KhachHangController extends Controller
{
    /**
     * Lấy hồ sơ KhachHang của người đang đăng nhập — tự tạo nếu bị thiếu
     * (trường hợp tài khoản cũ bị lỗi giữa chừng khi tạo, không có hồ sơ liên kết)
     */
    private function layHoSoHienTai(): KhachHang
    {
        $taiKhoan = Auth::user();
        $khachHang = $taiKhoan->khachHang;

        if (!$khachHang) {
            $khachHang = KhachHang::create([
                'taikhoan_id' => $taiKhoan->id,
                'ho_ten'      => 'Khách hàng',
                'sdt'         => 'Chua cap nhat',
                'trang_thai'  => 'Hoat dong',
            ]);
        }

        return $khachHang;
    }

    public function trangChu(GeminiService $gemini)
    {
        $khachHang = $this->layHoSoHienTai();

        $dsXe = DB::table('Xe')->where('khachhang_id', $khachHang->id)->get();
        $dsXeId = $dsXe->pluck('id');

        // Báo giá đang chờ khách duyệt — ưu tiên hiển thị cao nhất (cần hành động ngay)
        $baoGiaChoDuyet = DB::table('BaoGia as bg')
            ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('x.khachhang_id', $khachHang->id)
            ->where('bg.trang_thai', 'Cho khach duyet')
            ->select('bg.id', 'bg.tong_tien', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
            ->get();

        // Lịch hẹn sắp tới — đã đặt nhưng CHƯA đến garage (chưa tạo phiếu sửa chữa)
        $lichHenSapToi = DB::table('LichHen as lh')
            ->leftJoin('Xe as x', 'lh.xe_id', '=', 'x.id')
            ->where('lh.khachhang_id', $khachHang->id)
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

        // Lịch sử lịch hẹn — Đã hoàn tất hoặc Đã hủy (khác với "sắp tới" chỉ gồm Đã đặt)
        $lichSuLichHen = DB::table('LichHen as lh')
            ->leftJoin('Xe as x', 'lh.xe_id', '=', 'x.id')
            ->where('lh.khachhang_id', $khachHang->id)
            ->whereIn('lh.trang_thai', ['Da hoan tat', 'Da huy'])
            ->select('lh.id', 'lh.ngay_gio', 'lh.trang_thai', 'lh.hang_xe_du_kien', 'lh.dong_xe_du_kien', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
            ->orderBy('lh.ngay_gio', 'desc')
            ->limit(3)
            ->get();

        foreach ($lichSuLichHen as $lh) {
            $lh->ten_dich_vu = DB::table('ChiTietLichHen as ctlh')
                ->join('DichVu as dv', 'ctlh.dichvu_id', '=', 'dv.id')
                ->where('ctlh.lichhen_id', $lh->id)
                ->pluck('dv.ten_dich_vu')
                ->implode(', ');
        }

        $lichSuDichVu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->whereIn('p.xe_id', $dsXeId)
            ->select('p.id', 'p.mo_ta_su_co', 'p.trang_thai', 'p.ngay_tiep_nhan', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
            ->orderBy('p.ngay_tiep_nhan', 'desc')
            ->limit(5)
            ->get();

        $hoaDonDaThanhToan = DB::table('HoaDon as h')
            ->join('PhieuSuaChua as p', 'h.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->whereIn('p.xe_id', $dsXeId)
            ->where('h.trang_thai_thanh_toan', 'Da thanh toan')
            ->select('h.id', 'h.tong_tien', 'h.ngay_lap', 'x.bien_so')
            ->orderBy('h.ngay_lap', 'desc')
            ->limit(5)
            ->get();

        $goiYBaoDuong = null;
        if ($dsXe->isNotEmpty()) {
            $xeChinh = $dsXe->first();
            $goiYBaoDuong = cache()->remember(
                'goi_y_bao_duong_xe_' . $xeChinh->id,
                now()->addDay(),
                fn() => $gemini->nhacBaoDuong(
                    DB::table('ChiTietBaoGia as ctbg')
                        ->join('BaoGia as bg', 'ctbg.baogia_id', '=', 'bg.id')
                        ->join('PhieuSuaChua as p', 'bg.phieu_id', '=', 'p.id')
                        ->join('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
                        ->where('p.xe_id', $xeChinh->id)
                        ->where('bg.trang_thai', 'Da duyet')
                        ->select('dv.ten_dich_vu', 'dv.chu_ky_khuyen_nghi_thang', 'bg.ngay_lap')
                        ->orderBy('bg.ngay_lap', 'desc')
                        ->get()
                        ->toArray()
                )
            );
            // Lưu ý: KHÔNG gửi thông báo/email ở đây nữa — việc gửi chủ động
            // đã chuyển hẳn sang batch job (php artisan nhac-bao-duong:chay, chạy 8h sáng mỗi ngày).
            // Ở đây CHỈ tính toán để HIỂN THỊ khối gợi ý trên trang chủ khi khách ghé thăm.
        }

        return view('khach-hang.trang-chu', compact('khachHang', 'baoGiaChoDuyet', 'lichHenSapToi', 'lichSuLichHen', 'lichSuDichVu', 'hoaDonDaThanhToan', 'goiYBaoDuong', 'dsXe'));
    }

    public function showHoSo()
    {
        $khachHang = $this->layHoSoHienTai();
        return view('khach-hang.ho-so', compact('khachHang'));
    }

    public function capNhatHoSo(Request $request)
    {
        // Chuyển chuỗi rỗng "" thành null trước khi validate —
        // tránh HTML form gửi "" cho ô để trống làm sai luật 'nullable|date'
        $request->merge([
            'ho_ten'    => $request->ho_ten ?: null,
            'dia_chi'   => $request->dia_chi ?: null,
            'ngay_sinh' => $request->ngay_sinh ?: null,
        ]);

        // Không trường nào bắt buộc — nhưng nếu có điền thì phải đúng định dạng
        $request->validate([
            'ho_ten'    => 'nullable|string|max:150',
            'sdt'       => 'nullable|string|max:20|regex:/^[0-9]{9,11}$/',
            'email'     => 'nullable|email|max:100',
            'dia_chi'   => 'nullable|string|max:255',
            'ngay_sinh' => 'nullable|date',
        ], [
            'sdt.regex'   => 'Số điện thoại không đúng định dạng (9-11 chữ số)',
            'email.email' => 'Email không đúng định dạng',
        ]);

        $khachHang = $this->layHoSoHienTai();

        $khachHang->update([
            'ho_ten'    => $request->ho_ten,
            'sdt'       => $request->sdt,
            'email'     => $request->email,
            'dia_chi'   => $request->dia_chi,
            'ngay_sinh' => $request->ngay_sinh,
        ]);

        return back()->with('thanhcong', 'Cập nhật thông tin thành công!');
    }
    /**
     * Danh sách ĐẦY ĐỦ hóa đơn của khách (cả Chưa thanh toán lẫn Đã thanh toán)
     */
    public function hoaDonCuaToi()
    {
        $khachHang = $this->layHoSoHienTai();
        $dsXeId = DB::table('Xe')->where('khachhang_id', $khachHang->id)->pluck('id');

        $dsHoaDon = DB::table('HoaDon as h')
            ->join('PhieuSuaChua as p', 'h.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->whereIn('p.xe_id', $dsXeId)
            ->select('h.id', 'h.tong_tien', 'h.ngay_lap', 'h.trang_thai_thanh_toan', 'x.bien_so')
            ->orderBy('h.ngay_lap', 'desc')
            ->get();

        return view('khach-hang.hoa-don', compact('dsHoaDon'));
    }
    /**
     * Chi tiết 1 hóa đơn cụ thể — kèm QR chuyển khoản nếu chưa thanh toán
     */
    public function hoaDonChiTiet(int $id)
    {
        $khachHang = $this->layHoSoHienTai();

        $hoaDon = DB::table('HoaDon as h')
            ->join('PhieuSuaChua as p', 'h.phieu_id', '=', 'p.id')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('h.id', $id)
            ->where('x.khachhang_id', $khachHang->id)
            ->select('h.*', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'p.mo_ta_su_co')
            ->first();

        if (!$hoaDon) {
            abort(404);
        }

        // Lấy chi tiết từ báo giá đã duyệt gắn với phiếu này
        // ($hoaDon->phieu_id đã có sẵn từ câu truy vấn "h.*" ở trên, không cần query lại)
        $chiTiet = DB::table('ChiTietBaoGia as ctbg')
            ->join('BaoGia as bg', 'ctbg.baogia_id', '=', 'bg.id')
            ->leftJoin('DichVu as dv', 'ctbg.dichvu_id', '=', 'dv.id')
            ->leftJoin('PhuTung as pt', 'ctbg.phutung_id', '=', 'pt.id')
            ->where('bg.phieu_id', $hoaDon->phieu_id)
            ->select('ctbg.so_luong', 'ctbg.don_gia', 'dv.ten_dich_vu', 'pt.ten as ten_phu_tung')
            ->get();

        $daDanhGia = DB::table('DanhGia')->where('phieu_id', $hoaDon->phieu_id)->exists();

        return view('khach-hang.hoa-don-chi-tiet', compact('hoaDon', 'chiTiet', 'daDanhGia'));
    }
}