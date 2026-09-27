<?php

namespace App\Http\Controllers\Cvdv;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhanCongController extends Controller
{
    /**
     * Danh sách phiếu đang chờ phân công (Đã tiếp nhận, chưa có KTV)
     */
    public function danhSach(Request $request)
    {
        $tuKhoa = $request->query('q', '');

        $query = DB::table('PhieuSuaChua as p')
            ->join('Xe as x', 'p.xe_id', '=', 'x.id')
            ->join('KhachHang as kh', 'x.khachhang_id', '=', 'kh.id')
            ->whereNull('p.ktv_id')
            ->where('p.trang_thai', 'Da tiep nhan')
            ->select('p.id', 'p.mo_ta_su_co', 'p.nhom_su_co_ai', 'p.ngay_tiep_nhan', 'x.bien_so', 'x.hang_xe', 'x.dong_xe', 'kh.ho_ten');

        if ($tuKhoa) {
            $query->where(function ($q) use ($tuKhoa) {
                $q->where('kh.ho_ten', 'like', "%$tuKhoa%")
                  ->orWhere('x.bien_so', 'like', "%$tuKhoa%")
                  ->orWhere('p.id', $tuKhoa);
            });
        }

        $dsPhieuChoPhanCong = $query->orderBy('p.ngay_tiep_nhan', 'asc')->get();

        $dsKTV = DB::table('TaiKhoan')->where('vai_tro', 'Ky thuat vien')->where('trang_thai', 'Hoat dong')->get();

        return view('cvdv.phan-cong', compact('dsPhieuChoPhanCong', 'dsKTV', 'tuKhoa'));
    }

    /**
     * Gọi AI gợi ý ưu tiên + phân công (AJAX) — chỉ chạy khi ≥2 phiếu chờ (business rule đã chốt)
     */
    public function goiYAI(GeminiService $gemini)
    {
        $dsPhieuChoPhanCong = DB::table('PhieuSuaChua as p')
            ->whereNull('p.ktv_id')
            ->where('p.trang_thai', 'Da tiep nhan')
            ->select('p.id', 'p.mo_ta_su_co', 'p.nhom_su_co_ai')
            ->get()
            ->toArray();

        if (count($dsPhieuChoPhanCong) < 2) {
            return response()->json([
                'aiApDung' => false,
                'lyDo' => 'Chỉ có ' . count($dsPhieuChoPhanCong) . ' phiếu chờ, không cần AI gợi ý — tự phân công trực tiếp.',
            ]);
        }

        $dsKTV = DB::table('TaiKhoan')
            ->where('vai_tro', 'Ky thuat vien')
            ->where('trang_thai', 'Hoat dong')
            ->select('id', 'email_sdt')
            ->get()
            ->toArray();

        $taiCongViec = DB::table('PhieuSuaChua')
            ->select('ktv_id', DB::raw('count(*) as tong'))
            ->whereNotNull('ktv_id')
            ->whereIn('trang_thai', ['Da tiep nhan', 'Cho bao gia'])
            ->groupBy('ktv_id')
            ->get()
            ->toArray();

        $ketQua = $gemini->goiYUuTien($dsPhieuChoPhanCong, $dsKTV, $taiCongViec);

        return response()->json($ketQua);
    }

    /**
     * CVDV xác nhận phân công 1 phiếu cho 1 KTV
     */
    public function phanCong(Request $request, int $phieuId)
    {
        $request->validate(['ktv_id' => 'required|integer|exists:TaiKhoan,id']);

        DB::table('PhieuSuaChua')->where('id', $phieuId)->update(['ktv_id' => $request->ktv_id]);

        return redirect()->route('cvdv.phan-cong')->with('thanhcong', 'Đã phân công kỹ thuật viên!');
    }
}