@extends('layouts.app')

@section('title', 'Quên mật khẩu - AutoCare')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-5 py-10">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">

        <h1 class="text-lg md:text-xl font-semibold text-navy mb-1">Quên mật khẩu</h1>
        <p class="text-xs text-slate-400 mb-6">Nhập số điện thoại hoặc email đã đăng ký để nhận mã xác thực</p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('quen-mat-khau.gui-otp') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Số điện thoại / Email</label>
                <input type="text" name="email_sdt" value="{{ old('email_sdt') }}" placeholder="0901234567"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>

            <button type="submit"
                    class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
                Gửi mã xác thực
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-5">
            <a href="{{ route('dang-nhap') }}" class="text-aiblue hover:underline">← Quay lại đăng nhập</a>
        </p>
    </div>
</div>
@endsection