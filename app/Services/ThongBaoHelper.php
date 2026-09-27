<?php

namespace App\Services;

use App\Mail\ThongBaoMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Bản hoàn chỉnh Giai đoạn 17 — gửi cả thông báo trong app LẪN email thật
 */
class ThongBaoHelper
{
    public static function guiChoKhachHang(int $khachHangId, string $noiDung, string $loai = 'Tien do', ?string $duongDan = null)
    {
        DB::table('ThongBao')->insert([
            'khachhang_id' => $khachHangId,
            'noi_dung'     => $noiDung,
            'loai'         => $loai,
            'da_xem'       => false,
            'ngay_gui'     => now(),
        ]);

        $khachHang = DB::table('KhachHang')->where('id', $khachHangId)->first();
        if ($khachHang && $khachHang->email) {
            self::guiEmail($khachHang->email, $noiDung, $duongDan);
        }
    }

    public static function guiChoNhanVien(int $taiKhoanId, string $noiDung, string $loai = 'Tien do')
    {
        DB::table('ThongBao')->insert([
            'taikhoan_id' => $taiKhoanId,
            'noi_dung'    => $noiDung,
            'loai'        => $loai,
            'da_xem'      => false,
            'ngay_gui'    => now(),
        ]);

        // Nhân viên chỉ nhận thông báo trong app, không gửi email (tránh spam hộp thư công việc)
    }

    private static function guiEmail(string $email, string $noiDung, ?string $duongDan = null)
    {
        try {
            Mail::to($email)->send(new ThongBaoMail($noiDung, 'Thông báo từ AutoCare', $duongDan));
        } catch (\Exception $e) {
            Log::warning('Gui email that bai: ' . $e->getMessage());
            // Không throw lỗi ra ngoài — gửi app luôn thành công dù email lỗi, đảm bảo luồng nghiệp vụ chính không bị gián đoạn
        }
    }
}