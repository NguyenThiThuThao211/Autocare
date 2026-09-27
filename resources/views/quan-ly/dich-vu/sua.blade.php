@extends('layouts.admin')

@section('title', 'Sửa dịch vụ - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Báo cáo</a>
    <a href="{{ route('quan-ly.dich-vu') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Danh mục dịch vụ</a>
    <a href="{{ route('quan-ly.phu-tung') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Tồn kho phụ tùng</a>
    <a href="{{ route('quan-ly.tai-khoan') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản trị tài khoản</a>
@endsection

@section('content')
<div class="max-w-md">
    <a href="{{ route('quan-ly.dich-vu') }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại danh sách</a>
    <h1 class="text-xl font-semibold text-navy mt-2 mb-6">Sửa dịch vụ</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('quan-ly.dich-vu.sua.xu-ly', $dichVu->id) }}" class="bg-white border border-slate-200 rounded-xl p-5 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Tên dịch vụ <span class="text-red-500">*</span></label>
            <input type="text" name="ten_dich_vu" value="{{ old('ten_dich_vu', $dichVu->ten_dich_vu) }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Nhóm dịch vụ <span class="text-red-500">*</span></label>
            <input type="text" name="nhom_dich_vu" value="{{ old('nhom_dich_vu', $dichVu->nhom_dich_vu) }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Đơn giá (đ) <span class="text-red-500">*</span></label>
            <input type="number" name="don_gia" value="{{ old('don_gia', $dichVu->don_gia) }}" min="0"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Chu kỳ khuyến nghị (tháng)</label>
            <input type="number" name="chu_ky_khuyen_nghi_thang" value="{{ old('chu_ky_khuyen_nghi_thang', $dichVu->chu_ky_khuyen_nghi_thang) }}" min="1"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Mô tả</label>
            <textarea name="mo_ta" rows="2" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">{{ old('mo_ta', $dichVu->mo_ta) }}</textarea>
        </div>
        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Lưu thay đổi
        </button>
    </form>
</div>
@endsection