@extends('layouts.admin')

@section('title', 'Tạo tài khoản nhân viên - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('quan-ly.tai-khoan') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Quản trị tài khoản</a>
@endsection

@section('content')
<div class="max-w-md">
    <h1 class="text-xl font-semibold text-navy mb-6">Tạo tài khoản nhân viên</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('quan-ly.tai-khoan.tao-moi') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Họ tên</label>
            <input type="text" name="ho_ten" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Email hoặc số điện thoại</label>
            <input type="text" name="email_sdt" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Vai trò</label>
            <select name="vai_tro" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                <option value="Co van dich vu">Cố vấn dịch vụ</option>
                <option value="Ky thuat vien">Kỹ thuật viên</option>
                <option value="Quan ly">Quản lý</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Tạo tài khoản
        </button>
    </form>
</div>
@endsection