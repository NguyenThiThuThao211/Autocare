<?php

namespace App\Http\Controllers\Ktv;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhieuController extends Controller
{
    /**
     * Danh sách phiếu được phân công cho đúng KTV đang đăng nhập
     */
    public function danhSach(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $locTrangThai = $request->query('trang_thai_loc', 'tat_ca');
        $sapXep = $request->query('sap_xep', 'ngay_tiep_nhan');

        $query = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->leftJoin('BaoGia as bg', 'bg.phieu_id', '=', 'p.id')
            ->where('p.ktv_id', Auth::id())
            ->select(
                'p.id', 'p.mo_ta_su_co', 'p.trang_thai', 'p.ngay_tiep_nhan',
                'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten',
                DB::raw('MAX(bg.trang_thai) as bao_gia_trang_thai'),
                DB::raw('MAX(bg.ngay_duyet) as ngay_duyet')
            )
            ->groupBy('p.id', 'p.mo_ta_su_co', 'p.trang_thai', 'p.ngay_tiep_nhan', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten');

        if ($tuKhoa) {
            $query->where(function ($q) use ($tuKhoa) {
                $q->where('kh.ho_ten', 'like', "%$tuKhoa%")
                  ->orWhere('x.bien_so', 'like', "%$tuKhoa%")
                  ->orWhere('p.id', $tuKhoa);
            });
        }

        // Lọc theo 4 trạng thái rõ ràng — thực hiện SAU khi group by, dùng having qua collection
        $dsPhieu = $query->orderBy($sapXep === 'ngay_duyet' ? DB::raw('ngay_duyet') : 'p.ngay_tiep_nhan',
                                     $sapXep === 'ngay_duyet' ? 'desc' : 'asc')
                          ->get();

        $dsPhieu = $dsPhieu->filter(function ($p) use ($locTrangThai) {
            return match ($locTrangThai) {
                'chua_kiem_tra' => $p->trang_thai === 'Da tiep nhan',
                'cho_duyet'     => $p->trang_thai === 'Cho bao gia' && $p->bao_gia_trang_thai !== 'Da duyet',
                'san_sang_sua'  => $p->trang_thai === 'Cho bao gia' && $p->bao_gia_trang_thai === 'Da duyet',
                'hoan_thanh'    => $p->trang_thai === 'Hoan thanh',
                default         => $p->trang_thai !== 'Da huy', // tat_ca — trừ đã hủy
            };
        })->values();

        return view('ktv.danh-sach', compact('dsPhieu', 'tuKhoa', 'locTrangThai', 'sapXep'));
    }

    /**
     * Chi tiết phiếu — nơi KTV ghi chẩn đoán + chọn hạng mục đề xuất
     */
    public function chiTiet(int $id)
    {
        $phieu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->where('p.id', $id)
            ->where('p.ktv_id', Auth::id())
            ->select('p.*', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten', 'kh.sdt')
            ->first();

        if (!$phieu) {
            abort(404);
        }

        $danhMucDichVu = DB::table('DichVu')->where('trang_thai', 'Hoat dong')->orderBy('nhom_dich_vu')->get()->groupBy('nhom_dich_vu');
        $dsIdDaChon = $phieu->de_xuat_hang_muc_ktv ? json_decode($phieu->de_xuat_hang_muc_ktv, true) : [];

        return view('ktv.chi-tiet', compact('phieu', 'danhMucDichVu', 'dsIdDaChon'));
    }

    /**
     * AI gợi ý hạng mục dựa trên chẩn đoán KTV vừa nhập (dùng chung logic UC-05)
     */
    public function goiYAI(Request $request, GeminiService $gemini)
    {
        $request->validate(['ket_qua_chan_doan' => 'required|string']);

        $danhMucDichVu = DB::table('DichVu')
            ->where('trang_thai', 'Hoat dong')
            ->select('id', 'ten_dich_vu', 'don_gia', 'nhom_dich_vu')
            ->get()->toArray();

        $ketQua = $gemini->goiYBaoGia($request->ket_qua_chan_doan, null, $danhMucDichVu);

        return response()->json($ketQua);
    }

    /**
     * Lưu chẩn đoán + hạng mục đề xuất, chuyển phiếu sang "Chờ báo giá"
     */
    public function capNhat(Request $request, int $id)
    {
        $phieu = DB::table('PhieuSuaChua')->where('id', $id)->where('ktv_id', Auth::id())->first();
        if (!$phieu) {
            abort(404);
        }

        // Chuyển chuỗi rỗng "" thành null trước khi validate —
        // tránh HTML form gửi "" cho ô để trống làm MySQL từ chối kiểu DATETIME
        $request->merge([
            'thoi_gian_du_kien' => $request->thoi_gian_du_kien ?: null,
        ]);

        $request->validate([
            'ket_qua_chan_doan' => 'required|string|max:2000',
            'dich_vu_ids'       => 'nullable|array',
            'thoi_gian_du_kien' => 'nullable|date',
        ], [
            'ket_qua_chan_doan.required' => 'Vui lòng nhập kết quả kiểm tra',
        ]);

        DB::table('PhieuSuaChua')->where('id', $id)->update([
            'ket_qua_chan_doan'            => $request->ket_qua_chan_doan,
            'de_xuat_hang_muc_ktv'          => json_encode($request->dich_vu_ids ?? []),
            'thoi_gian_du_kien_hoan_thanh'  => $request->thoi_gian_du_kien,
            'trang_thai'                    => 'Cho bao gia',
        ]);

        // Thông báo cho CVDV biết phiếu đã sẵn sàng lập báo giá (gửi hết cho mọi CVDV đang hoạt động)
        $dsCVDV = DB::table('TaiKhoan')->where('vai_tro', 'Co van dich vu')->where('trang_thai', 'Hoat dong')->pluck('id');
        foreach ($dsCVDV as $cvdvId) {
            DB::table('ThongBao')->insert([
                'taikhoan_id' => $cvdvId,
                'noi_dung'    => 'Phiếu #PSC-' . str_pad($id, 4, '0', STR_PAD_LEFT) . ' đã có kết quả kiểm tra, sẵn sàng lập báo giá.',
                'loai'        => 'Tien do', 'da_xem' => false, 'ngay_gui' => now(),
            ]);
        }

        return redirect()->route('ktv.phieu')->with('thanhcong', 'Đã cập nhật kết quả kiểm tra!');
    }
    /**
     * KTV đánh dấu đã sửa xong — chỉ khi báo giá đã được khách duyệt.
     * Gửi thông báo cho khách hàng biết xe đã sửa xong.
     */
    public function danhDauHoanThanh(int $id)
    {
        $phieu = DB::table('PhieuSuaChua')->where('id', $id)->where('ktv_id', Auth::id())->first();
        if (!$phieu) {
            abort(404);
        }

        $coBaoGiaDaDuyet = DB::table('BaoGia')->where('phieu_id', $id)->where('trang_thai', 'Da duyet')->exists();
        if (!$coBaoGiaDaDuyet) {
            return back()->withErrors(['loi' => 'Phiếu cần có báo giá đã được khách duyệt trước khi đánh dấu Hoàn thành.']);
        }

        DB::table('PhieuSuaChua')->where('id', $id)->update(['trang_thai' => 'Hoan thanh']);

        // Thông báo cho khách hàng — xe đã sửa xong
        $khachHangId = DB::table('Xe')->where('id', $phieu->xe_id)->value('khachhang_id');
        DB::table('ThongBao')->insert([
            'khachhang_id' => $khachHangId,
            'noi_dung'     => 'Xe của bạn đã sửa xong (phiếu #PSC-' . str_pad($id, 4, '0', STR_PAD_LEFT) . '). Vui lòng đến nhận xe và thanh toán.',
            'loai'         => 'Tien do', 'da_xem' => false, 'ngay_gui' => now(),
        ]);

        return redirect()->route('ktv.phieu')->with('thanhcong', 'Đã đánh dấu Hoàn thành! CVDV có thể lập hóa đơn ngay bây giờ.');
    }
}