@extends('layouts.app')

@section('title', 'Thông báo - AutoCare')

@section('content')
<div class="max-w-2xl mx-auto px-5 py-8 md:py-12">

    <h1 class="text-lg md:text-2xl font-semibold text-navy mb-6">Thông báo</h1>

    <div class="space-y-3">
        @forelse ($dsThongBao as $tb)
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex gap-3">
                @php
                    $icon = match($tb->loai) {
                        'Nhac bao duong' => '✦',
                        'Hoa don' => '📄',
                        default => '🔔',
                    };
                @endphp
                <span class="text-lg">{{ $icon }}</span>
                <div class="flex-1">
                    <p class="text-sm text-slate-700">{{ $tb->noi_dung }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($tb->ngay_gui)->diffForHumans() }}</p>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
                <p class="text-sm text-slate-400">Bạn chưa có thông báo nào.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection