<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThongBaoNhanVienController extends Controller
{
    /**
     * Dùng chung cho CVDV/KTV/Quản lý — đánh dấu tất cả thông báo của tài khoản đang đăng nhập là đã đọc
     */
    public function danhDauDaXem()
    {
        DB::table('ThongBao')->where('taikhoan_id', Auth::id())->update(['da_xem' => true]);
        return response()->json(['ok' => true]);
    }
}