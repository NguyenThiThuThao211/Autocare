<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $table = 'KhachHang';
    public $timestamps = false;

    protected $fillable = [
        'taikhoan_id', 'ho_ten', 'sdt', 'email', 'dia_chi', 'ngay_sinh', 'trang_thai',
    ];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'taikhoan_id');
    }
}