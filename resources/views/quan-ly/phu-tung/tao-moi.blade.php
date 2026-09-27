@extends('layouts.admin')

@section('title', 'Thêm phụ tùng mới - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('quan-ly.phu-tung') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Tồn kho phụ tùng</a>
@endsection

@section('content')
<div class="max-w-md">
    <h1 class="text-xl font-semibold text-navy mb-6">Thêm phụ tùng mới</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('quan-ly.phu-tung.tao-moi') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Tên phụ tùng</label>
            <input type="text" name="ten" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Giá bán (đ)</label>
            <input type="number" name="gia_ban" min="1" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <p class="text-[11px] text-slate-400">Tồn kho khởi tạo = 0, dùng chức năng "Nhập kho" sau khi tạo để thêm số lượng.</p>
        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Thêm phụ tùng
        </button>
    </form>
</div>
@endsection