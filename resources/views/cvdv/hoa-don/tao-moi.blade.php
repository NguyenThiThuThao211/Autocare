@extends('layouts.admin')

@section('title', 'Lập hóa đơn - CVDV')

@section('sidebar-menu')
    <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Quản lý phiếu sửa chữa</a>
@endsection

@section('content')
<div class="max-w-lg">
    <a href="{{ route('cvdv.phieu.chi-tiet', $phieu->id) }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại phiếu</a>

    <h1 class="text-xl font-semibold text-navy mt-2 mb-1">Lập hóa đơn</h1>
    <p class="text-sm text-slate-500 mb-5">{{ $phieu->hang_xe }} {{ $phieu->dong_xe }} ({{ $phieu->bien_so }}) — {{ $phieu->ho_ten }}</p>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-5">
        <p class="text-xs font-semibold text-slate-400 mb-3 uppercase">Chi tiết hạng mục (từ báo giá đã duyệt)</p>
        @foreach ($chiTiet as $ct)
            <div class="flex items-center justify-between py-1.5">
                <span class="text-sm text-navy">{{ $ct->ten_dich_vu ?? $ct->ten_phu_tung }} (x{{ $ct->so_luong }})</span>
                <span class="text-sm text-slate-500">{{ number_format($ct->don_gia * $ct->so_luong) }}đ</span>
            </div>
        @endforeach
        <div class="flex items-center justify-between pt-3 mt-2 border-t border-slate-200">
            <p class="font-semibold text-navy">Tổng cộng</p>
            <p class="font-bold text-navy text-lg">{{ number_format($baoGiaDaDuyet->tong_tien) }}đ</p>
        </div>
    </div>

    <form method="POST" action="{{ route('cvdv.hoa-don.luu', $phieu->id) }}"
          onsubmit="return confirm('Xác nhận lập hóa đơn {{ number_format($baoGiaDaDuyet->tong_tien) }}đ và gửi cho khách hàng?');">
        @csrf
        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Lập hóa đơn &amp; gửi cho khách
        </button>
    </form>
</div>
@endsection