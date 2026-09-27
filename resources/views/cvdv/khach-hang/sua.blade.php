@extends('layouts.admin')

@section('title', 'Sửa khách hàng - CVDV')

@section('content')
<div class="max-w-md">
    <a href="{{ route('cvdv.khach-hang.chi-tiet', $khachHang->id) }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại</a>
    <h1 class="text-xl font-semibold text-navy mt-2 mb-6">Sửa thông tin khách hàng</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('cvdv.khach-hang.sua.xu-ly', $khachHang->id) }}" class="bg-white border border-slate-200 rounded-xl p-5 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Họ tên <span class="text-red-500">*</span></label>
            <input type="text" name="ho_ten" value="{{ old('ho_ten', $khachHang->ho_ten) }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Số điện thoại <span class="text-red-500">*</span></label>
            <input type="text" name="sdt" value="{{ old('sdt', $khachHang->sdt) }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Email</label>
            <input type="email" name="email" value="{{ old('email', $khachHang->email) }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Địa chỉ</label>
            <input type="text" name="dia_chi" value="{{ old('dia_chi', $khachHang->dia_chi) }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Lưu thay đổi
        </button>
    </form>
</div>
@endsection