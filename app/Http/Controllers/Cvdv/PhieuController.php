<?php

namespace App\Http\Controllers\Cvdv;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhieuController extends Controller
{
    public function showTaoMoi(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $sapXep = $request->query('sap_xep', 'moi_nhat');
        $locTrangThai = $request->query('trang_thai', 'chua_tiep_nhan'); // chua_tiep_nhan | da_tiep_nhan | tat_ca

        $query = DB::table('LichHen as lh')
            ->join('KhachHang as kh', 'lh.khachhang_id', '=', 'kh.id')
            ->leftJoin('Xe as x', 'lh.xe_id', '=', 'x.id')
            ->select(
                'lh.id as lich_hen_id', 'lh.ngay_gio', 'lh.mo_ta_so_bo', 'lh.trang_thai',
                'lh.hang_xe_du_kien', 'lh.dong_xe_du_kien', 'lh.khachhang_id',
                'x.id as xe_id', 'x.bien_so', 'x.hang_xe', 'x.dong_xe',
                'kh.ho_ten', 'kh.sdt'
            );

        match ($locTrangThai) {
            'da_tiep_nhan' => $query->where('lh.trang_thai', 'Da hoan tat'),
            'da_huy'       => $query->where('lh.trang_thai', 'Da huy'),
            'tat_ca'       => $query->whereIn('lh.trang_thai', ['Da dat', 'Da hoan tat', 'Da huy']),
            default        => $query->where('lh.trang_thai', 'Da dat'), // chua_tiep_nhan
        };

        if ($tuKhoa) {
            $query->where(function ($q) use ($tuKhoa) {
                $q->where('kh.ho_ten', 'like', "%$tuKhoa%")
                  ->orWhere('kh.sdt', 'like', "%$tuKhoa%")
                  ->orWhere('x.bien_so', 'like', "%$tuKhoa%");
            });
        }

        $query->orderBy('lh.ngay_gio', $sapXep === 'cu_nhat' ? 'asc' : 'desc');
        $dsLichHenChoXuLy = $query->get();

        foreach ($dsLichHenChoXuLy as $lh) {
            $dsDichVuDaChon = DB::table('ChiTietLichHen as ctlh')
                ->join('DichVu as dv', 'ctlh.dichvu_id', '=', 'dv.id')
                ->where('ctlh.lichhen_id', $lh->lich_hen_id)
                ->select('dv.id', 'dv.ten_dich_vu')->get();
            $lh->ten_dich_vu = $dsDichVuDaChon->pluck('ten_dich_vu')->implode(', ');
            $lh->dich_vu_ids_da_chon = $dsDichVuDaChon->pluck('id')->toArray();
        }

        $dsKTV = DB::table('TaiKhoan')->where('vai_tro', 'Ky thuat vien')->where('trang_thai', 'Hoat dong')->get();
        $danhMucDichVu = DB::table('DichVu')->where('trang_thai', 'Hoat dong')->orderBy('nhom_dich_vu')->get()->groupBy('nhom_dich_vu');

        return view('cvdv.phieu.tao-moi', compact('dsLichHenChoXuLy', 'dsKTV', 'tuKhoa', 'sapXep', 'locTrangThai', 'danhMucDichVu'));
    }

    public function timKiem(Request $request)
    {
        $tuKhoa = $request->query('q', '');
        $ketQua = DB::table('Xe as x')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->where('x.bien_so', 'like', "%$tuKhoa%")
            ->orWhere('kh.sdt', 'like', "%$tuKhoa%")
            ->select('x.id as xe_id', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten', 'kh.sdt', 'kh.id as khachhang_id')
            ->limit(8)->get();
        return response()->json($ketQua);
    }

    public function phanLoaiAI(Request $request, GeminiService $gemini)
    {
        $request->validate(['mo_ta_su_co' => 'required|string']);
        return response()->json($gemini->phanLoaiSuCo($request->mo_ta_su_co));
    }

    public function goiYHangMucSom(Request $request, GeminiService $gemini)
    {
        $request->validate(['mo_ta_su_co' => 'required|string']);
        $danhMucDichVu = DB::table('DichVu')->where('trang_thai', 'Hoat dong')
            ->select('id', 'ten_dich_vu', 'don_gia', 'nhom_dich_vu')->get()->toArray();
        return response()->json($gemini->goiYBaoGia($request->mo_ta_su_co, $request->nhom_su_co_ai, $danhMucDichVu));
    }

    public function goiYKTV(Request $request, GeminiService $gemini)
    {
        $dsKTV = DB::table('TaiKhoan')->where('vai_tro', 'Ky thuat vien')->where('trang_thai', 'Hoat dong')
            ->select('id', 'email_sdt')->get();

        $taiCongViec = DB::table('PhieuSuaChua')
            ->select('ktv_id', DB::raw('count(*) as tong'))
            ->whereNotNull('ktv_id')->whereIn('trang_thai', ['Da tiep nhan', 'Cho bao gia'])
            ->groupBy('ktv_id')->get();

        $tongPhieuChoXuLy = DB::table('PhieuSuaChua')->whereNull('ktv_id')->where('trang_thai', 'Da tiep nhan')->count();

        if ($tongPhieuChoXuLy < 1) {
            $ktvRanhNhat = $dsKTV->sortBy(fn($ktv) => $taiCongViec->firstWhere('ktv_id', $ktv->id)->tong ?? 0)->first();
            return response()->json([
                'aiApDung' => false,
                'ktvDeXuat' => $ktvRanhNhat ? ['ktvId' => $ktvRanhNhat->id, 'lyDo' => 'KTV đang ít việc nhất hiện tại'] : null,
            ]);
        }

        $ketQua = $gemini->goiYUuTien(
            [['phieuId' => 0, 'moTaSuCo' => $request->mo_ta_su_co, 'nhomSuCo' => $request->nhom_su_co_ai]],
            $dsKTV->toArray(), $taiCongViec->toArray()
        );
        $deXuat = collect($ketQua['ktvDeXuat'] ?? [])->first();

        return response()->json([
            'aiApDung' => true,
            'ktvDeXuat' => $deXuat ? ['ktvId' => $deXuat['ktvId'], 'lyDo' => $deXuat['lyDo']] : null,
        ]);
    }

    /**
     * Lưu phiếu — logic ĐƠN GIẢN HÓA: chỉ 1 ô "Biển số xe" duy nhất, luôn sửa được.
     *
     * Cách xác định xe:
     * - Nếu có "xe_id" (đã chọn xe/khách có sẵn) VÀ biển số nhập KHÔNG đổi → dùng thẳng xe đó
     * - Mọi trường hợp còn lại (biển số trống/khác, hoặc khách hoàn toàn mới)
     *   → cần đủ: biển số + hãng xe + (khách hàng: có sẵn qua khachhang_id, hoặc nhập mới tên+sđt)
     */
    public function taoMoi(Request $request)
    {
        $request->validate([
            'mo_ta_su_co'   => 'required|string|max:1000',
            'nhom_su_co_ai' => 'nullable|string|max:100',
            'ktv_id'        => 'required|integer|exists:TaiKhoan,id',
            'bien_so'       => 'required|string|max:20',
        ], [
            'mo_ta_su_co.required' => 'Vui lòng nhập mô tả sự cố',
            'ktv_id.required'      => 'Vui lòng chọn kỹ thuật viên',
            'bien_so.required'     => 'Vui lòng nhập biển số xe',
        ]);

        $bienSoMoi = trim($request->bien_so);
        $lichHenId = $request->filled('lich_hen_id') ? (int) $request->lich_hen_id : null;
        $xeIdCoSan = $request->filled('xe_id') ? (int) $request->xe_id : null;
        $khachHangIdCoSan = $request->filled('khachhang_id') ? (int) $request->khachhang_id : null;

        $xeCoSan = $xeIdCoSan ? DB::table('Xe')->where('id', $xeIdCoSan)->first() : null;

        if ($xeCoSan && $xeCoSan->bien_so === $bienSoMoi) {
            // Không đổi gì — dùng thẳng xe đã chọn
            $xeId = $xeCoSan->id;
        } else {
            // Biển số mới/khác — kiểm tra trùng trong hệ thống trước
            $daTrung = DB::table('Xe')->where('bien_so', $bienSoMoi)->first();
            if ($daTrung && (!$xeCoSan || $daTrung->id !== $xeCoSan->id)) {
                return back()->withErrors(['bien_so' => "Biển số \"$bienSoMoi\" đã tồn tại (xe #{$daTrung->id})"])->withInput();
            }

            // Xác định khách hàng để gắn xe mới vào
            if ($khachHangIdCoSan) {
                $khachHangId = $khachHangIdCoSan;
            } elseif ($lichHenId) {
                $khachHangId = DB::table('LichHen')->where('id', $lichHenId)->value('khachhang_id');
            } else {
                // Khách hoàn toàn mới — cần tên + SĐT
                $request->validate([
                    'khach_moi_ten' => 'required|string|max:150',
                    'khach_moi_sdt' => 'required|string|max:20',
                ], [
                    'khach_moi_ten.required' => 'Vui lòng nhập tên khách hàng',
                    'khach_moi_sdt.required' => 'Vui lòng nhập số điện thoại khách hàng',
                ]);

                $khachHangDaCo = DB::table('KhachHang')->where('sdt', $request->khach_moi_sdt)->first();
                $khachHangId = $khachHangDaCo ? $khachHangDaCo->id : DB::table('KhachHang')->insertGetId([
                    'ho_ten' => $request->khach_moi_ten, 'sdt' => $request->khach_moi_sdt, 'trang_thai' => 'Hoat dong',
                ]);
            }

            if ($xeCoSan) {
                // Cập nhật lại biển số xe đã chọn (CVDV sửa biển số cho đúng thực tế)
                DB::table('Xe')->where('id', $xeCoSan->id)->update(['bien_so' => $bienSoMoi]);
                $xeId = $xeCoSan->id;
            } else {
                // Tạo xe mới hoàn toàn
                $request->validate(['hang_xe' => 'required|string|max:50'], ['hang_xe.required' => 'Vui lòng nhập hãng xe']);
                $xeId = DB::table('Xe')->insertGetId([
                    'khachhang_id' => $khachHangId,
                    'bien_so'      => $bienSoMoi,
                    'hang_xe'      => $request->hang_xe,
                    'dong_xe'      => $request->dong_xe,
                    'trang_thai'   => 'Hoat dong',
                ]);
            }
        }

        // Đóng lịch hẹn nếu có
        if ($lichHenId) {
            DB::table('LichHen')->where('id', $lichHenId)->update(['trang_thai' => 'Da hoan tat']);
        } else {
            DB::table('LichHen')
                ->where('xe_id', $xeId)->where('trang_thai', 'Da dat')
                ->orderBy('ngay_gio', 'desc')->limit(1)
                ->update(['trang_thai' => 'Da hoan tat']);
        }

        // Đồng bộ lại dịch vụ đã chọn (CVDV có thể đã chỉnh sửa so với lúc khách đặt lịch)
        if ($lichHenId && $request->has('dich_vu_ids')) {
            DB::table('ChiTietLichHen')->where('lichhen_id', $lichHenId)->delete();
            foreach ($request->dich_vu_ids ?? [] as $dichVuId) {
                DB::table('ChiTietLichHen')->insert(['lichhen_id' => $lichHenId, 'dichvu_id' => $dichVuId]);
            }
        }

        $phieuId = DB::table('PhieuSuaChua')->insertGetId([
            'xe_id'          => $xeId,
            'ktv_id'         => $request->ktv_id,
            'mo_ta_su_co'    => $request->mo_ta_su_co,
            'nhom_su_co_ai'  => $request->nhom_su_co_ai,
            'trang_thai'     => 'Da tiep nhan',
            'ngay_tiep_nhan' => now(),
        ]);

        DB::table('ThongBao')->insert([
            'taikhoan_id' => $request->ktv_id,
            'noi_dung'    => 'Bạn được phân công phiếu #PSC-' . str_pad($phieuId, 4, '0', STR_PAD_LEFT),
            'loai'        => 'Tien do', 'da_xem' => false, 'ngay_gui' => now(),
        ]);

        return redirect()->route('cvdv.dashboard')->with('thanhcong', 'Đã tạo phiếu #PSC-' . str_pad($phieuId, 4, '0', STR_PAD_LEFT) . ' và phân công KTV thành công!');
    }

    /**
     * CVDV đề xuất đổi giờ mới cho lịch hẹn (do phía garage — thiếu nhân lực, sự cố...)
     */
    public function doiGio(Request $request, int $lichHenId)
    {
        $lichHen = DB::table('LichHen')->where('id', $lichHenId)->first();

        if (!$lichHen || $lichHen->trang_thai !== 'Da dat') {
            return back()->withErrors(['loi' => 'Lịch hẹn này không ở trạng thái có thể xử lý.']);
        }

        $request->validate([
            'ngay_moi' => 'required|date',
            'gio_moi'  => 'required|string',
        ], [
            'ngay_moi.required' => 'Vui lòng chọn ngày mới',
            'gio_moi.required'  => 'Vui lòng chọn khung giờ mới',
        ]);

        DB::table('LichHen')->where('id', $lichHenId)->update([
            'ngay_gio' => $request->ngay_moi . ' ' . $request->gio_moi . ':00',
        ]);

        \App\Services\ThongBaoHelper::guiChoKhachHang(
            $lichHen->khachhang_id,
            'Lịch hẹn của bạn đã được đổi sang ' . \Carbon\Carbon::parse($request->ngay_moi . ' ' . $request->gio_moi)->format('H:i - d/m/Y')
                . ' do garage cần điều chỉnh. Bạn có thể tự đổi lại hoặc hủy trong mục "Lịch hẹn của tôi" nếu không phù hợp.',
            'Tien do'
        );

        return redirect()->route('cvdv.lich-hen')->with('thanhcong', 'Đã đổi giờ và thông báo cho khách.');
    }

    /**
     * CVDV hủy lịch hẹn (do phía garage) — BẮT BUỘC nhập lý do, đúng UC-04
     */
    public function huyCoLyDo(Request $request, int $lichHenId)
    {
        $lichHen = DB::table('LichHen')->where('id', $lichHenId)->first();

        if (!$lichHen || $lichHen->trang_thai !== 'Da dat') {
            return back()->withErrors(['loi' => 'Lịch hẹn này không ở trạng thái có thể xử lý.']);
        }

        $request->validate(['ly_do_huy' => 'required|string|max:255'], [
            'ly_do_huy.required' => 'Vui lòng nhập lý do hủy',
        ]);

        DB::table('LichHen')->where('id', $lichHenId)->update([
            'trang_thai' => 'Da huy',
            'ly_do_huy'  => $request->ly_do_huy,
        ]);

        \App\Services\ThongBaoHelper::guiChoKhachHang(
            $lichHen->khachhang_id,
            'Lịch hẹn ngày ' . \Carbon\Carbon::parse($lichHen->ngay_gio)->format('d/m/Y - H:i') . ' đã bị hủy. Lý do: ' . $request->ly_do_huy . '. Vui lòng đặt lịch lại nếu vẫn có nhu cầu.',
            'Tien do'
        );

        return redirect()->route('cvdv.lich-hen')->with('thanhcong', 'Đã hủy lịch hẹn và thông báo lý do cho khách.');
    }
}