<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Composer cho header/footer khách hàng (đã có từ Giai đoạn 1)
        view()->composer(['partials.header', 'partials.footer'], function ($view) {
            $nhomDichVu = \Illuminate\Support\Facades\DB::table('DichVu')
                ->where('trang_thai', 'Hoat dong')
                ->distinct()
                ->pluck('nhom_dich_vu');

            $soThongBaoChuaXem = 0;
            $dsThongBaoGanDay = collect();

            if (auth()->check() && auth()->user()->khachHang) {
                $khachHangId = auth()->user()->khachHang->id;

                $soThongBaoChuaXem = \Illuminate\Support\Facades\DB::table('ThongBao')
                    ->where('khachhang_id', $khachHangId)->where('da_xem', false)->count();

                $dsThongBaoGanDay = \Illuminate\Support\Facades\DB::table('ThongBao')
                    ->where('khachhang_id', $khachHangId)
                    ->orderBy('ngay_gui', 'desc')->limit(10)->get();
            }

            $view->with([
                'nhomDichVu' => $nhomDichVu,
                'soThongBaoChuaXem' => $soThongBaoChuaXem,
                'dsThongBaoGanDay' => $dsThongBaoGanDay,
            ]);
        });

        // Giai đoạn 16 — Composer MỚI cho layout admin (CVDV/KTV/Quản lý) — chuông thông báo nhân viên
        view()->composer(['layouts.app', 'layouts.admin'], function ($view) {
            $soThongBaoChuaXemNV = 0;
            $dsThongBaoGanDayNV = collect();

            if (auth()->check() && auth()->user()->vai_tro !== 'Khach hang') {
                $soThongBaoChuaXemNV = \Illuminate\Support\Facades\DB::table('ThongBao')
                    ->where('taikhoan_id', auth()->id())->where('da_xem', false)->count();

                $dsThongBaoGanDayNV = \Illuminate\Support\Facades\DB::table('ThongBao')
                    ->where('taikhoan_id', auth()->id())
                    ->orderBy('ngay_gui', 'desc')->limit(10)->get();
            }

            $view->with([
                'soThongBaoChuaXemNV' => $soThongBaoChuaXemNV,
                'dsThongBaoGanDayNV' => $dsThongBaoGanDayNV,
            ]);
        });
    }
}