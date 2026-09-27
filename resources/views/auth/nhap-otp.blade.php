@extends('layouts.app')

@section('title', 'Nhập mã OTP - AutoCare')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-5 py-10">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">

        <h1 class="text-lg md:text-xl font-semibold text-navy mb-1">Nhập mã xác thực</h1>
        <p class="text-xs text-slate-400 mb-6">Mã gồm 6 chữ số đã gửi đến {{ session('reset_email_sdt') }}</p>

        @if (session('otp_demo'))
            <div class="bg-aipurplebg text-aipurple text-sm rounded-lg px-3 py-2 mb-4">
                <span class="font-semibold">Demo:</span> Mã OTP của bạn là
                <span class="font-bold text-base">{{ session('otp_demo') }}</span>
                <p class="text-xs mt-1 text-slate-500">(Ở bản triển khai thật, mã này sẽ được gửi qua SMS/email)</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('quen-mat-khau.xac-thuc-otp') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Mã OTP</label>
                <input type="text" name="otp" maxlength="6" placeholder="------"
                       class="w-full text-center tracking-[0.5em] text-lg font-semibold border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>

            <button type="submit"
                    class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
                Xác nhận
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-5">
            <a href="{{ route('quen-mat-khau') }}" class="text-aiblue hover:underline">← Gửi lại mã</a>
        </p>
    </div>
</div>
@endsection