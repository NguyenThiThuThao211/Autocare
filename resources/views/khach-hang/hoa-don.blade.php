@extends('layouts.app')

@section('title', 'Hóa đơn của tôi - AutoCare')

@section('content')
<div class="max-w-3xl mx-auto px-5 py-8 md:py-12">

    <h1 class="text-lg md:text-2xl font-semibold text-navy mb-6">Hóa đơn của tôi</h1>

    <div class="space-y-3">
        @forelse ($dsHoaDon as $hd)
            <a href="{{ route('hoa-don.xem', $hd->id) }}"
               class="block bg-white border border-slate-200 rounded-xl p-4 md:p-5 hover:shadow-sm transition">
                <div class="flex items-center justify-between mb-2">
                    <p class="font-medium text-navy text-sm md:text-base">Hóa đơn #{{ $hd->id }} — {{ $hd->bien_so }}</p>
                    @if ($hd->trang_thai_thanh_toan === 'Da thanh toan')
                        <span class="text-xs bg-green-50 text-green-700 px-2.5 py-1 rounded-full">Đã thanh toán</span>
                    @else
                        <span class="text-xs bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full">Chưa thanh toán</span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mb-2">{{ \Carbon\Carbon::parse($hd->ngay_lap)->format('d/m/Y') }}</p>
                <p class="text-base font-semibold text-navy">{{ number_format($hd->tong_tien) }}đ</p>
            </a>
        @empty
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
                <p class="text-sm text-slate-400">Bạn chưa có hóa đơn nào.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection