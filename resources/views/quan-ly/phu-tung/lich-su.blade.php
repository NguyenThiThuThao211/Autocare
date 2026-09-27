@extends('layouts.admin')

@section('title', 'Lịch sử giao dịch - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Báo cáo</a>
    <a href="{{ route('quan-ly.dich-vu') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Danh mục dịch vụ</a>
    <a href="{{ route('quan-ly.phu-tung') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Tồn kho phụ tùng</a>
    <a href="{{ route('quan-ly.tai-khoan') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản trị tài khoản</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('quan-ly.phu-tung') }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại tồn kho</a>
    <h1 class="text-xl font-semibold text-navy mt-2 mb-1">Lịch sử giao dịch — {{ $phuTung->ten }}</h1>
    <p class="text-sm text-slate-400 mb-6">Tồn kho hiện tại: {{ $phuTung->ton_kho }}</p>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($dsGiaoDich as $gd)
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium {{ $gd->loai === 'Nhap' ? 'text-green-700' : 'text-red-600' }}">
                        {{ $gd->loai === 'Nhap' ? '+ Nhập' : '- Xuất' }} {{ $gd->so_luong }}
                        @if ($gd->loai === 'Nhap' && $gd->nha_cung_cap)
                            <span class="text-slate-400 font-normal">— NCC: {{ $gd->nha_cung_cap }}</span>
                        @endif
                        @if ($gd->loai === 'Xuat' && $gd->phieu_id)
                            <span class="text-slate-400 font-normal">— Phiếu #PSC-{{ str_pad($gd->phieu_id, 4, '0', STR_PAD_LEFT) }}</span>
                        @endif
                    </p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Nhân viên thực hiện: {{ $gd->nguoi_thuc_hien_email ?? '(không rõ)' }}
                    </p>
                </div>
                <p class="text-xs text-slate-400 flex-shrink-0">{{ \Carbon\Carbon::parse($gd->thoi_diem)->format('H:i - d/m/Y') }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Chưa có giao dịch nào.</p>
        @endforelse
    </div>
</div>
@endsection