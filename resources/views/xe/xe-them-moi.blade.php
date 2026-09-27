@extends('layouts.app')

@section('title', 'Thêm xe mới - AutoCare')

@section('content')
<div class="max-w-xl mx-auto px-5 py-8 md:py-12">

    <h1 class="text-lg md:text-2xl font-semibold text-navy mb-6">Thêm xe mới</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('xe.them-moi.xu-ly') }}" class="bg-white border border-slate-200 rounded-xl p-5 md:p-6 space-y-4">
        @csrf

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Biển số <span class="text-red-500">*</span></label>
            <input type="text" name="bien_so" value="{{ old('bien_so') }}" placeholder="51A-123.45"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Hãng xe <span class="text-red-500">*</span></label>
            <input type="text" name="hang_xe" value="{{ old('hang_xe') }}" placeholder="Honda"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Dòng xe <span class="text-slate-400">(tùy chọn)</span></label>
            <input type="text" name="dong_xe" value="{{ old('dong_xe') }}" placeholder="Civic"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Đời xe <span class="text-slate-400">(tùy chọn)</span></label>
            <input type="text" name="doi_xe" value="{{ old('doi_xe') }}" placeholder="2020"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <button type="submit"
                class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Thêm xe
        </button>
    </form>

</div>
@endsection