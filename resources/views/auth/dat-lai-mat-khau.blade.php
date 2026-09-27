@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - AutoCare')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-5 py-10">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">

        <h1 class="text-lg md:text-xl font-semibold text-navy mb-1">Đặt mật khẩu mới</h1>
        <p class="text-xs text-slate-400 mb-6">Nhập mật khẩu mới cho tài khoản của bạn</p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('quen-mat-khau.dat-lai.xu-ly') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Mật khẩu mới</label>
                <input type="password" name="password" placeholder="Tối thiểu 6 ký tự"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>

            <button type="submit"
                    class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
                Đặt lại mật khẩu
            </button>
        </form>
    </div>
</div>
@endsection