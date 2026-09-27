@extends('layouts.app')

@section('title', 'AutoCare')

@section('content')
<div class="max-w-sm mx-auto px-5 py-16 text-center">
    <p class="text-3xl mb-3">✓</p>
    <p class="text-sm text-slate-600 mb-6">{{ $thongDiep }}</p>
    <a href="{{ route('trang-chu-cong-khai') }}" class="text-sm text-aiblue hover:underline">Về trang chủ</a>
</div>
@endsection