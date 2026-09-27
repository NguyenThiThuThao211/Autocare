@extends('layouts.app')

@section('title', 'Đánh giá khách hàng - AutoCare')

@section('content')
<div class="max-w-4xl mx-auto px-5 md:px-10 py-8 md:py-12">

    <h1 class="text-xl md:text-3xl font-semibold text-navy mb-2">Đánh giá từ khách hàng</h1>

    <div class="flex items-center gap-3 mb-8">
        <span class="text-3xl font-semibold text-navy">{{ $diemTrungBinh }}</span>
        <div>
            <p class="text-amber-500 text-lg">{{ str_repeat('★', round($diemTrungBinh)) }}{{ str_repeat('☆', 5 - round($diemTrungBinh)) }}</p>
            <p class="text-slate-400 text-xs md:text-sm">{{ $tongSoDanhGia }} lượt đánh giá</p>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($danhGiaList as $dg)
            <div class="border border-slate-200 rounded-xl p-4 md:p-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-amber-500">{{ str_repeat('★', $dg->so_sao) }}{{ str_repeat('☆', 5 - $dg->so_sao) }}</p>
                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($dg->ngay_danh_gia)->format('d/m/Y') }}</p>
                </div>
                @if ($dg->nhan_xet)
                    <p class="text-sm text-slate-700">{{ $dg->nhan_xet }}</p>
                @else
                    <p class="text-sm text-slate-400 italic">Khách hàng không để lại nhận xét</p>
                @endif
            </div>
        @empty
            <p class="text-slate-400 text-sm">Chưa có đánh giá nào.</p>
        @endforelse
    </div>

</div>
@endsection