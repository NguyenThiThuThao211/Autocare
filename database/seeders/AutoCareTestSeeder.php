<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AutoCareTestSeeder extends Seeder
{
    /**
     * Test fixture IDs are in the 9000 range so they are easy to identify
     * and avoid the IDs in the current autocare-updated.sql dump.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('Test@12345');

        DB::table('taikhoan')->upsert([
            ['id'=>9001,'email_sdt'=>'test_customer@autocare.local','mat_khau_hash'=>$password,'vai_tro'=>'Khach hang','trang_thai'=>'Hoat dong','ngay_tao'=>$now],
            ['id'=>9002,'email_sdt'=>'test_cvdv@autocare.local','mat_khau_hash'=>$password,'vai_tro'=>'Co van dich vu','trang_thai'=>'Hoat dong','ngay_tao'=>$now],
            ['id'=>9003,'email_sdt'=>'test_ktv@autocare.local','mat_khau_hash'=>$password,'vai_tro'=>'Ky thuat vien','trang_thai'=>'Hoat dong','ngay_tao'=>$now],
            ['id'=>9004,'email_sdt'=>'test_manager@autocare.local','mat_khau_hash'=>$password,'vai_tro'=>'Quan ly','trang_thai'=>'Hoat dong','ngay_tao'=>$now],
        ], ['id'], ['email_sdt','mat_khau_hash','vai_tro','trang_thai']);

        DB::table('khachhang')->updateOrInsert(['id'=>9001], [
            'taikhoan_id'=>9001,'ho_ten'=>'KH Test AutoCare','sdt'=>'0909000001',
            'email'=>'test_customer@autocare.local','dia_chi'=>'HCMC','ngay_sinh'=>null,
            'trang_thai'=>'Hoat dong','ngay_tao'=>$now,
        ]);

        DB::table('xe')->updateOrInsert(['id'=>9001], [
            'khachhang_id'=>9001,'bien_so'=>'51T-TEST01','hang_xe'=>'Honda',
            'dong_xe'=>'Civic','doi_xe'=>'2020','trang_thai'=>'Hoat dong',
        ]);

        DB::table('dichvu')->updateOrInsert(['id'=>9001], [
            'ten_dich_vu'=>'Dịch vụ test thay dầu','nhom_dich_vu'=>'Dầu & lọc',
            'mo_ta'=>'Dữ liệu phục vụ kiểm thử','don_gia'=>350000,
            'chu_ky_khuyen_nghi_thang'=>6,'trang_thai'=>'Hoat dong',
        ]);
        DB::table('dichvu')->updateOrInsert(['id'=>9002], [
            'ten_dich_vu'=>'Kiểm tra ắc quy - TEST','nhom_dich_vu'=>'Điện & ắc quy',
            'mo_ta'=>'Dữ liệu phục vụ kiểm thử AI','don_gia'=>80000,
            'chu_ky_khuyen_nghi_thang'=>6,'trang_thai'=>'Hoat dong',
        ]);

        DB::table('phutung')->updateOrInsert(['id'=>9001], [
            'ten'=>'Ắc quy TEST','don_vi_tinh'=>'Bình','gia_nhap'=>1100000,
            'gia_ban'=>1500000,'ton_kho'=>10,'nguong_toi_thieu'=>3,'trang_thai'=>'Hoat dong',
        ]);

        DB::table('lichhen')->updateOrInsert(['id'=>9001], [
            'khachhang_id'=>9001,'xe_id'=>9001,'hang_xe_du_kien'=>null,'dong_xe_du_kien'=>null,
            'ngay_gio'=>'2026-10-05 09:00:00','trang_thai'=>'Da dat','ly_do_huy'=>null,
            'nguon'=>'Tu dat','mo_ta_so_bo'=>'Test đặt lịch',
        ]);
        DB::table('chitietlichhen')->updateOrInsert(['id'=>9001], [
            'lichhen_id'=>9001,'dichvu_id'=>9001,
        ]);

        DB::table('phieusuachua')->updateOrInsert(['id'=>9001], [
            'xe_id'=>9001,'ktv_id'=>9003,
            'mo_ta_su_co'=>'Xe khó nổ máy vào buổi sáng, khi đề máy nghe tiếng tạch tạch và đèn taplo sáng yếu',
            'nhom_su_co_ai'=>null,'ket_qua_chan_doan'=>null,'de_xuat_hang_muc_ktv'=>null,
            'thoi_gian_du_kien_hoan_thanh'=>'2026-10-05 15:00:00','trang_thai'=>'Da tiep nhan',
            'ly_do_huy'=>null,'ngay_tiep_nhan'=>'2026-10-05 09:15:00',
        ]);

        DB::table('phieusuachua')->updateOrInsert(['id'=>9002], [
            'xe_id'=>9001,'ktv_id'=>9003,
            'mo_ta_su_co'=>'Xe kêu lạ khi phanh, rung tay lái',
            'nhom_su_co_ai'=>'Hệ thống phanh','ket_qua_chan_doan'=>'Đĩa phanh mòn không đều',
            'de_xuat_hang_muc_ktv'=>'["9001"]','thoi_gian_du_kien_hoan_thanh'=>'2026-10-05 16:00:00',
            'trang_thai'=>'Cho bao gia','ly_do_huy'=>null,'ngay_tiep_nhan'=>'2026-10-05 09:30:00',
        ]);

        DB::table('baogia')->updateOrInsert(['id'=>9001], [
            'phieu_id'=>9002,'tong_tien'=>430000,'trang_thai'=>'Cho khach duyet',
            'ngay_duyet'=>null,'nguon_goi_y'=>'Thu cong','ngay_lap'=>'2026-10-05 10:00:00',
        ]);
        DB::table('baogia')->updateOrInsert(['id'=>9002], [
            'phieu_id'=>9002,'tong_tien'=>1580000,'trang_thai'=>'Da duyet',
            'ngay_duyet'=>'2026-10-05 11:00:00','nguon_goi_y'=>'AI','ngay_lap'=>'2026-10-05 10:30:00',
        ]);

        DB::table('chitietbaogia')->updateOrInsert(['id'=>9001], [
            'baogia_id'=>9001,'dichvu_id'=>9001,'phutung_id'=>null,'mo_ta_khac'=>null,'so_luong'=>1,'don_gia'=>350000,
        ]);
        DB::table('chitietbaogia')->updateOrInsert(['id'=>9002], [
            'baogia_id'=>9002,'dichvu_id'=>null,'phutung_id'=>9001,'mo_ta_khac'=>null,'so_luong'=>1,'don_gia'=>1500000,
        ]);
        DB::table('chitietbaogia')->updateOrInsert(['id'=>9003], [
            'baogia_id'=>9002,'dichvu_id'=>9002,'phutung_id'=>null,'mo_ta_khac'=>null,'so_luong'=>1,'don_gia'=>80000,
        ]);

        DB::table('hoadon')->updateOrInsert(['id'=>9001], [
            'phieu_id'=>9002,'tong_tien'=>1580000,'ngay_lap'=>'2026-10-05 16:30:00',
            'nguoi_lap'=>9002,'trang_thai_thanh_toan'=>'Chua thanh toan','ngay_thanh_toan'=>null,
        ]);

        DB::table('thongbao')->updateOrInsert(['id'=>9001], [
            'khachhang_id'=>9001,'taikhoan_id'=>null,'noi_dung'=>'Test thông báo tiến độ',
            'loai'=>'Tien do','link_dinh_kem'=>null,'da_xem'=>0,'ngay_gui'=>$now,
        ]);
        DB::table('thongbao')->updateOrInsert(['id'=>9002], [
            'khachhang_id'=>9001,'taikhoan_id'=>null,'noi_dung'=>'Test nhắc bảo dưỡng',
            'loai'=>'Nhac bao duong','link_dinh_kem'=>null,'da_xem'=>0,'ngay_gui'=>$now,
        ]);
    }
}
