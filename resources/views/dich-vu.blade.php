@extends('layouts.app')

@section('title', ($nhom ?? 'Dịch vụ') . ' - AutoCare')

@section('content')
<div class="max-w-6xl mx-auto px-5 md:px-10 py-8 md:py-12">

    <h1 class="text-xl md:text-3xl font-semibold text-navy mb-6">
        {{ $nhom ?? 'Tất cả dịch vụ' }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($dichVuList as $dv)
            <div class="border border-slate-200 rounded-xl p-4 hover:shadow-md transition">
                <p class="font-semibold text-navy text-sm md:text-base mb-1">{{ $dv->ten_dich_vu }}</p>
                <p class="text-aiblue font-semibold text-sm">{{ number_format($dv->don_gia) }}đ</p>
                @if ($dv->chu_ky_khuyen_nghi_thang)
                    <p class="text-xs text-slate-400 mt-1">Khuyến nghị: {{ $dv->chu_ky_khuyen_nghi_thang }} tháng/lần</p>
                @endif
            </div>
        @endforeach
    </div>

</div>
@endsection