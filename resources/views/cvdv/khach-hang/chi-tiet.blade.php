@extends('layouts.admin')

@section('title', 'Chi tiết khách hàng - CVDV')

@section('sidebar-menu')
    <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý phiếu sửa chữa</a>
    <a href="{{ route('cvdv.khach-hang') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Danh sách khách hàng</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('cvdv.khach-hang') }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại danh sách</a>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-navy">{{ $khachHang->ho_ten }}</h1>
            <p class="text-sm text-slate-500">{{ $khachHang->sdt }} @if($khachHang->email) — {{ $khachHang->email }} @endif</p>
        </div>
        <a href="{{ route('cvdv.khach-hang.sua', $khachHang->id) }}" class="text-xs text-aiblue border border-blue-200 rounded-lg px-3 py-2 hover:bg-blue-50">
            Sửa thông tin
        </a>
    </div>

    <p class="font-semibold text-navy text-sm mb-3">Xe của khách hàng</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
        @forelse ($dsXe as $xe)
            <div class="bg-white border border-slate-200 rounded-xl p-4">
                <p class="text-sm font-medium text-navy">{{ $xe->hang_xe }} {{ $xe->dong_xe }}</p>
                <p class="text-xs text-slate-400">Biển số: {{ $xe->bien_so }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-400">Chưa có xe nào trong hồ sơ.</p>
        @endforelse
    </div>

    <p class="font-semibold text-navy text-sm mb-3">Lịch sử sửa chữa</p>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($lichSuPhieu as $p)
            <a href="{{ route('cvdv.phieu.chi-tiet', $p->id) }}"
               class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
                <div>
                    <p class="text-sm text-navy">#PSC-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }} — {{ $p->hang_xe }} {{ $p->dong_xe }} ({{ $p->bien_so }})</p>
                    <p class="text-xs text-slate-400">{{ $p->mo_ta_su_co }}</p>
                </div>
                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($p->ngay_tiep_nhan)->format('d/m/Y') }}</p>
            </a>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Chưa có lịch sử sửa chữa nào.</p>
        @endforelse
    </div>
</div>
@endsection