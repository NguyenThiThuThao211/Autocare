<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TaiKhoan extends Authenticatable
{
    protected $table = 'TaiKhoan';

    public $timestamps = false; // Bảng chỉ có ngay_tao, không có created_at/updated_at

    protected $fillable = [
        'email_sdt', 'mat_khau_hash', 'vai_tro', 'trang_thai',
    ];

    protected $hidden = ['mat_khau_hash'];

    /**
     * Laravel mặc định tìm cột "password" — ta có cột tên khác (mat_khau_hash),
     * nên phải override để chỉ đúng cột thật.
     */
    public function getAuthPassword()
    {
        return $this->mat_khau_hash;
    }

    /**
     * Quan hệ tới hồ sơ khách hàng (nếu vai_tro = Khach hang)
     */
    public function khachHang()
    {
        return $this->hasOne(KhachHang::class, 'taikhoan_id');
    }
}