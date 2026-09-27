@extends('layouts.app')

@section('title', 'Hồ sơ của tôi - AutoCare')

@section('content')
<div class="max-w-xl mx-auto px-5 py-8 md:py-12">

    <h1 class="text-lg md:text-2xl font-semibold text-navy mb-6">Thông tin cá nhân</h1>

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-3 py-2 mb-4">{{ session('thanhcong') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('ho-so.cap-nhat') }}" class="bg-white border border-slate-200 rounded-xl p-5 md:p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Họ tên</label>
            <input type="text" name="ho_ten" value="{{ old('ho_ten', $khachHang->ho_ten ?? '') }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Số điện thoại</label>
            <input type="text" name="sdt"
                   value="{{ old('sdt', ($khachHang->sdt ?? '') === 'Chua cap nhat' ? '' : ($khachHang->sdt ?? '')) }}"
                   placeholder="0901234567"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Email</label>
            <input type="email" name="email" value="{{ old('email', $khachHang->email ?? '') }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Địa chỉ</label>
            <input type="text" name="dia_chi" value="{{ old('dia_chi', $khachHang->dia_chi ?? '') }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Ngày sinh</label>
            <input type="date" name="ngay_sinh" value="{{ old('ngay_sinh', $khachHang->ngay_sinh ?? '') }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <button type="submit"
                class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Lưu thay đổi
        </button>
    </form>

</div>
@endsection