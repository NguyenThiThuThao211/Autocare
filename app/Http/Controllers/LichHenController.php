<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LichHenController extends Controller
{
    /**
     * Form đặt lịch hẹn — dùng chung cho cả khách vãng lai lẫn đã đăng nhập
     */
    public function showDatLich()
    {
        $danhMucDichVu = DB::table('DichVu')
            ->where('trang_thai', 'Hoat dong')
            ->orderBy('nhom_dich_vu')
            ->get()
            ->groupBy('nhom_dich_vu');

        // Nếu đã đăng nhập, gợi ý sẵn xe có trong hồ sơ
        $dsXeCoSan = [];
        if (Auth::check() && Auth::user()->khachHang) {
            $dsXeCoSan = DB::table('Xe')
                ->where('khachhang_id', Auth::user()->khachHang->id)
                ->where('trang_thai', 'Hoat dong')
                ->get();
        }

        return view('lich-hen.dat-lich', compact('danhMucDichVu', 'dsXeCoSan'));
    }

    /**
     * Lấy danh sách khung giờ còn trống trong 1 ngày (gọi qua AJAX)
     */
    public function khungGioTrong(Request $request)
    {
        $ngay = $request->query('ngay');
        $khungGioCoDinh = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

        $daDat = DB::table('LichHen')
            ->whereDate('ngay_gio', $ngay)
            ->where('trang_thai', 'Da dat')
            ->pluck('ngay_gio')
            ->map(fn($dt) => \Carbon\Carbon::parse($dt)->format('H:i'))
            ->toArray();

        $conTrong = array_values(array_diff($khungGioCoDinh, $daDat));

        return response()->json(['khung_gio' => $conTrong]);
    }

    /**
     * Xử lý submit đặt lịch — 1 form dùng chung cho cả khách đã/chưa đăng nhập
     */
    public function datLich(Request $request)
    {
        $request->validate([
            'ho_ten'      => 'required|string|max:150',
            'sdt'         => 'required|string|max:20',
            'dich_vu_ids' => 'required|array|min:1',
            'ngay'        => 'required|date',
            'gio'         => 'required|string',
            'xe_id'       => 'nullable|integer',
            'hang_xe'     => 'nullable|string|max:50',
            'dong_xe'     => 'nullable|string|max:50',
            'mo_ta_so_bo' => 'nullable|string|max:500',
        ], [
            'ho_ten.required'      => 'Vui lòng nhập họ tên',
            'sdt.required'         => 'Vui lòng nhập số điện thoại',
            'dich_vu_ids.required' => 'Vui lòng chọn ít nhất 1 dịch vụ',
            'ngay.required'        => 'Vui lòng chọn ngày',
            'gio.required'         => 'Vui lòng chọn khung giờ',
        ]);

        // Nếu khách mới (chưa có xe cụ thể), chỉ bắt buộc hãng xe — đủ để garage chuẩn bị sơ bộ.
        // Dòng xe và biển số chính xác đều là tùy chọn (khách có thể chưa nhớ, xác nhận sau tại garage)
        if (!$request->xe_id && !$request->hang_xe) {
            return back()
                ->withErrors(['hang_xe' => 'Vui lòng chọn xe có sẵn, hoặc nhập hãng xe'])
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            // Nếu ĐÃ ĐĂNG NHẬP: dùng thẳng đúng hồ sơ khách hàng của tài khoản này,
            // không tìm/tạo mới theo SĐT (tránh tạo ra hồ sơ trùng/lạc, không liên kết đúng tài khoản)
            if (Auth::check() && Auth::user()->khachHang) {
                $khachHangId = Auth::user()->khachHang->id;

                // Nếu SĐT nhập trong form khác với hồ sơ hiện tại (VD: mới cập nhật SĐT thật
                // lần đầu sau khi đăng nhập Google), đồng bộ luôn cho khớp
                if (Auth::user()->khachHang->sdt !== $request->sdt) {
                    DB::table('KhachHang')->where('id', $khachHangId)->update(['sdt' => $request->sdt]);
                }
            } else {
                // Khách vãng lai (chưa đăng nhập): tìm hoặc tạo mới theo SĐT như cũ
                $khachHang = DB::table('KhachHang')->where('sdt', $request->sdt)->first();

                if (!$khachHang) {
                    $khachHangId = DB::table('KhachHang')->insertGetId([
                        'ho_ten'     => $request->ho_ten,
                        'sdt'        => $request->sdt,
                        'trang_thai' => 'Hoat dong',
                    ]);
                } else {
                    $khachHangId = $khachHang->id;
                }
            }

            $lichHenId = DB::table('LichHen')->insertGetId([
                'khachhang_id'     => $khachHangId,
                'xe_id'            => $request->xe_id,
                'hang_xe_du_kien'  => $request->xe_id ? null : $request->hang_xe,
                'dong_xe_du_kien'  => $request->xe_id ? null : $request->dong_xe,
                'ngay_gio'         => $request->ngay . ' ' . $request->gio . ':00',
                'trang_thai'       => 'Da dat',
                'nguon'            => 'Tu dat',
                'mo_ta_so_bo'      => $request->mo_ta_so_bo,
            ]);

            foreach ($request->dich_vu_ids as $dichVuId) {
                DB::table('ChiTietLichHen')->insert([
                    'lichhen_id' => $lichHenId,
                    'dichvu_id'  => $dichVuId,
                ]);
            }
        });

        return redirect()->route('dat-lich')->with('thanhcong', 'Đặt lịch hẹn thành công! Garage sẽ liên hệ xác nhận nếu cần.');
    }

    /**
     * Danh sách lịch hẹn của khách đã đăng nhập
     */
    public function lichHenCuaToi()
    {
        $khachHang = Auth::user()->khachHang;

        $dsLichHen = DB::table('LichHen as lh')
            ->leftJoin('Xe as x', 'lh.xe_id', '=', 'x.id')
            ->where('lh.khachhang_id', $khachHang->id ?? 0)
            ->select('lh.*', 'x.bien_so', 'x.hang_xe as xe_hang', 'x.dong_xe as xe_dong')
            ->orderBy('lh.ngay_gio', 'desc')
            ->get();

        // Lấy tên dịch vụ cho từng lịch hẹn
        foreach ($dsLichHen as $lh) {
            $lh->ten_dich_vu = DB::table('ChiTietLichHen as ctlh')
                ->join('DichVu as dv', 'ctlh.dichvu_id', '=', 'dv.id')
                ->where('ctlh.lichhen_id', $lh->id)
                ->pluck('dv.ten_dich_vu')
                ->implode(', ');
        }

        return view('lich-hen.danh-sach', compact('dsLichHen'));
    }

    /**
     * Form sửa lịch hẹn — chỉ khi CHƯA "Đã hoàn tất" (đúng business rule như hủy lịch)
     */
    public function showSuaLich(int $id)
    {
        $khachHang = Auth::user()->khachHang;
        $lichHen = DB::table('LichHen')->where('id', $id)->where('khachhang_id', $khachHang->id ?? 0)->first();

        if (!$lichHen || $lichHen->trang_thai === 'Da hoan tat') {
            abort(404);
        }

        $dichVuDaChon = DB::table('ChiTietLichHen')->where('lichhen_id', $id)->pluck('dichvu_id')->toArray();

        $danhMucDichVu = DB::table('DichVu')
            ->where('trang_thai', 'Hoat dong')
            ->orderBy('nhom_dich_vu')
            ->get()
            ->groupBy('nhom_dich_vu');

        return view('lich-hen.sua', compact('lichHen', 'dichVuDaChon', 'danhMucDichVu'));
    }

    /**
     * Xử lý cập nhật lịch hẹn
     */
    public function suaLich(Request $request, int $id)
    {
        $khachHang = Auth::user()->khachHang;
        $lichHen = DB::table('LichHen')->where('id', $id)->where('khachhang_id', $khachHang->id ?? 0)->first();

        if (!$lichHen || $lichHen->trang_thai === 'Da hoan tat') {
            abort(404);
        }

        $request->validate([
            'dich_vu_ids' => 'required|array|min:1',
            'ngay'        => 'required|date',
            'gio'         => 'required|string',
        ], [
            'dich_vu_ids.required' => 'Vui lòng chọn ít nhất 1 dịch vụ',
            'ngay.required'        => 'Vui lòng chọn ngày',
            'gio.required'         => 'Vui lòng chọn khung giờ',
        ]);

        DB::transaction(function () use ($request, $id) {
            DB::table('LichHen')->where('id', $id)->update([
                'ngay_gio' => $request->ngay . ' ' . $request->gio . ':00',
            ]);

            DB::table('ChiTietLichHen')->where('lichhen_id', $id)->delete();
            foreach ($request->dich_vu_ids as $dichVuId) {
                DB::table('ChiTietLichHen')->insert(['lichhen_id' => $id, 'dichvu_id' => $dichVuId]);
            }
        });

        return redirect()->route('lich-hen')->with('thanhcong', 'Đã cập nhật lịch hẹn!');
    }

    /**
     * Hủy lịch hẹn (chỉ khi chưa "Đã hoàn tất")
     */
    public function huyLich(int $id)
    {
        $khachHang = Auth::user()->khachHang;

        $lichHen = DB::table('LichHen')->where('id', $id)->where('khachhang_id', $khachHang->id ?? 0)->first();

        if (!$lichHen || $lichHen->trang_thai === 'Da hoan tat') {
            return back()->withErrors(['loi' => 'Không thể hủy lịch hẹn này']);
        }

        DB::table('LichHen')->where('id', $id)->update(['trang_thai' => 'Da huy']);

        return back()->with('thanhcong', 'Đã hủy lịch hẹn');
    }
}