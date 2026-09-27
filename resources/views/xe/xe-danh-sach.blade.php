@extends('layouts.app')

@section('title', 'Xe của tôi - AutoCare')

@section('content')
<div class="max-w-3xl mx-auto px-5 py-8 md:py-12">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg md:text-2xl font-semibold text-navy">Xe của tôi</h1>
        <a href="{{ route('xe.them-moi') }}" class="text-aiblue text-sm hover:underline">+ Thêm xe mới</a>
    </div>

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-3 py-2 mb-4">{{ session('thanhcong') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse ($dsXe as $xe)
            <div class="bg-white border border-slate-200 rounded-xl p-4 md:p-5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5">
                        <path d="M5 17h14M5 17a2 2 0 100-4 2 2 0 000 4zm14 0a2 2 0 100-4 2 2 0 000 4zM5 13l1.5-5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.37L19 13M5 13h14" stroke="#3B82F6" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="font-medium text-navy text-sm md:text-base">{{ $xe->hang_xe }} {{ $xe->dong_xe }}</p>
                <p class="text-xs text-slate-400 mt-1">Biển số: {{ $xe->bien_so }}</p>
                @if ($xe->doi_xe)
                    <p class="text-xs text-slate-400">Đời xe: {{ $xe->doi_xe }}</p>
                @endif
            </div>
        @empty
            <div class="md:col-span-2 bg-white border border-slate-200 rounded-xl p-8 text-center">
                <p class="text-sm text-slate-400 mb-3">Bạn chưa có xe nào trong hồ sơ.</p>
                <a href="{{ route('xe.them-moi') }}" class="text-aiblue text-sm hover:underline">Thêm xe ngay →</a>
            </div>
        @endforelse
    </div>

</div>
@endsection