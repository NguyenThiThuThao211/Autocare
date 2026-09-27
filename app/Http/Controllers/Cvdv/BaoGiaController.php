<?php

namespace App\Http\Controllers\Cvdv;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaoGiaController extends Controller
{
    public function showTaoMoi(int $phieuId)
    {
        $phieu = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->where('p.id', $phieuId)
            ->select('p.*', 'x.bien_so', 'x.hang_xe', 'x.dong_xe')
            ->first();

        if (!$phieu || !$phieu->ket_qua_chan_doan) {
            abort(404);
        }

        $danhMucDichVu = DB::table('DichVu')->where('trang_thai', 'Hoat dong')->orderBy('nhom_dich_vu')->get();
        $danhMucPhuTung = DB::table('PhuTung')->where('trang_thai', 'Hoat dong')->get();

        // Đề xuất hạng mục mà KTV đã chọn sẵn khi kiểm tra — tự tích sẵn vào form,
        // CVDV vẫn có thể điều chỉnh lại trước khi gửi khách
        $dsIdDeXuatKTV = $phieu->de_xuat_hang_muc_ktv ? json_decode($phieu->de_xuat_hang_muc_ktv, true) : [];

        return view('cvdv.bao-gia.tao-moi', compact('phieu', 'danhMucDichVu', 'danhMucPhuTung', 'dsIdDeXuatKTV'));
    }

    public function goiYAI(int $phieuId, GeminiService $gemini)
    {
        $phieu = DB::table('PhieuSuaChua')->where('id', $phieuId)->first();

        if (!$phieu || !$phieu->ket_qua_chan_doan) {
            return response()->json(['loi' => 'Phiếu chưa có kết quả chẩn đoán']);
        }

        $danhMucDichVu = DB::table('DichVu')
            ->where('trang_thai', 'Hoat dong')
            ->select('id', 'ten_dich_vu', 'don_gia', 'nhom_dich_vu')
            ->get()
            ->toArray();

        $ketQua = $gemini->goiYBaoGia($phieu->ket_qua_chan_doan, $phieu->nhom_su_co_ai, $danhMucDichVu);

        return response()->json($ketQua);
    }

    public function kiemTraTonKho(Request $request)
    {
        $phuTungId = $request->query('phutung_id');
        $soLuongCan = (int) $request->query('so_luong', 1);

        $phuTung = DB::table('PhuTung')->where('id', $phuTungId)->first();

        return response()->json([
            'du' => $phuTung && $phuTung->ton_kho >= $soLuongCan,
            'ton_kho_hien_tai' => $phuTung->ton_kho ?? 0,
        ]);
    }

    public function luu(Request $request, int $phieuId)
    {
        $request->validate([
            'dich_vu_ids'          => 'nullable|array',
            'phu_tung'             => 'nullable|array',
            'chi_phi_khac_ghi_chu' => 'nullable|string|max:255',
            'chi_phi_khac_so_tien' => 'nullable|numeric|min:0',
        ]);

        $phieu = DB::table('PhieuSuaChua')->where('id', $phieuId)->first();
        if (!$phieu) {
            abort(404);
        }

        $tongTien = 0;
        $chiTiet = [];

        foreach ($request->dich_vu_ids ?? [] as $dichVuId) {
            $dv = DB::table('DichVu')->where('id', $dichVuId)->first();
            if ($dv) {
                $tongTien += $dv->don_gia;
                $chiTiet[] = ['dichvu_id' => $dv->id, 'phutung_id' => null, 'mo_ta_khac' => null, 'so_luong' => 1, 'don_gia' => $dv->don_gia];
            }
        }

        foreach ($request->phu_tung ?? [] as $phuTungId => $soLuong) {
            $soLuong = (int) $soLuong;
            if ($soLuong <= 0) continue;

            $pt = DB::table('PhuTung')->where('id', $phuTungId)->first();
            if (!$pt) continue;

            if ($pt->ton_kho < $soLuong) {
                return back()->withErrors(['loi' => "Phụ tùng \"{$pt->ten}\" không đủ tồn kho (còn {$pt->ton_kho}, cần {$soLuong})"])->withInput();
            }

            $tongTien += $pt->gia_ban * $soLuong;
            $chiTiet[] = ['dichvu_id' => null, 'phutung_id' => $pt->id, 'mo_ta_khac' => null, 'so_luong' => $soLuong, 'don_gia' => $pt->gia_ban];
        }

        // Chi phí khác — không gắn dịch vụ/phụ tùng có sẵn nào trong danh mục
        $soTienKhac = (float) ($request->chi_phi_khac_so_tien ?? 0);
        if ($soTienKhac > 0) {
            $ghiChuKhac = $request->chi_phi_khac_ghi_chu ?: 'Chi phí khác';
            $tongTien += $soTienKhac;
            $chiTiet[] = ['dichvu_id' => null, 'phutung_id' => null, 'mo_ta_khac' => $ghiChuKhac, 'so_luong' => 1, 'don_gia' => $soTienKhac];
        }

        if (empty($chiTiet)) {
            return back()->withErrors(['loi' => 'Vui lòng chọn ít nhất 1 hạng mục'])->withInput();
        }

        DB::transaction(function () use ($phieuId, $tongTien, $chiTiet, $request) {
            $baoGiaId = DB::table('BaoGia')->insertGetId([
                'phieu_id'    => $phieuId,
                'tong_tien'   => $tongTien,
                'trang_thai'  => 'Cho khach duyet',
                'nguon_goi_y' => $request->boolean('co_dung_ai') ? 'AI' : 'Thu cong',
                'ngay_lap'    => now(),
            ]);

            foreach ($chiTiet as $ct) {
                DB::table('ChiTietBaoGia')->insert(array_merge(['baogia_id' => $baoGiaId], $ct));
            }

            foreach ($chiTiet as $ct) {
                if ($ct['phutung_id']) {
                    DB::table('PhuTung')->where('id', $ct['phutung_id'])->decrement('ton_kho', $ct['so_luong']);
                    DB::table('GiaoDichKho')->insert([
                        'phutung_id'      => $ct['phutung_id'],
                        'phieu_id'        => $phieuId,
                        'loai'            => 'Xuat',
                        'so_luong'        => $ct['so_luong'],
                        'nguoi_thuc_hien' => auth()->id(),
                        'thoi_diem'       => now(),
                    ]);
                }
            }

            DB::table('PhieuSuaChua')->where('id', $phieuId)->update(['trang_thai' => 'Cho bao gia']);
        });

        // Thông báo cho khách hàng — có báo giá mới cần duyệt
        $khachHangId = DB::table('Xe as x')->join('PhieuSuaChua as p', 'x.id', '=', 'p.xe_id')
            ->where('p.id', $phieuId)->value('x.khachhang_id');
        DB::table('ThongBao')->insert([
            'khachhang_id' => $khachHangId,
            'noi_dung'     => 'Bạn có báo giá mới cho phiếu #PSC-' . str_pad($phieuId, 4, '0', STR_PAD_LEFT) . ', vui lòng xem và duyệt.',
            'loai'         => 'Tien do', 'da_xem' => false, 'ngay_gui' => now(),
        ]);

        return redirect()->route('cvdv.phieu.chi-tiet', $phieuId)->with('thanhcong', 'Đã lập báo giá, đang chờ khách duyệt.');
    }
}