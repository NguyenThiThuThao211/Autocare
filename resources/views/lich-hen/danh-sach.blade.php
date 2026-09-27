@extends('layouts.app')

@section('title', 'Lịch hẹn của tôi - AutoCare')

@section('content')
<div class="max-w-3xl mx-auto px-5 py-8 md:py-12">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg md:text-2xl font-semibold text-navy">Lịch hẹn của tôi</h1>
        <a href="{{ route('dat-lich') }}" class="text-aiblue text-sm hover:underline">+ Đặt lịch mới</a>
    </div>

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-3 py-2 mb-4">{{ session('thanhcong') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="space-y-3">
        @forelse ($dsLichHen as $lh)
            <div class="bg-white border border-slate-200 rounded-xl p-4 md:p-5">
                <div class="flex items-center justify-between mb-2">
                    <p class="font-medium text-navy text-sm md:text-base">
                        {{ \Carbon\Carbon::parse($lh->ngay_gio)->format('d/m/Y - H:i') }}
                    </p>
                    @php
                        $mau = match($lh->trang_thai) {
                            'Da dat' => 'bg-blue-50 text-blue-700',
                            'Da hoan tat' => 'bg-slate-100 text-slate-500',
                            'Da huy' => 'bg-red-50 text-red-600',
                            default => 'bg-slate-100 text-slate-500',
                        };
                        $ten = match($lh->trang_thai) {
                            'Da dat' => 'Đã đặt',
                            'Da hoan tat' => 'Đã hoàn tất',
                            'Da huy' => 'Đã hủy',
                            default => $lh->trang_thai,
                        };
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $mau }}">{{ $ten }}</span>
                </div>

                <p class="text-sm text-slate-600 mb-1">{{ $lh->ten_dich_vu ?: 'Chưa chọn dịch vụ' }}</p>
                <p class="text-xs text-slate-400 mb-3">
                    {{ $lh->bien_so ?? (($lh->hang_xe_du_kien ?? '') . ' ' . ($lh->dong_xe_du_kien ?? '')) }}
                </p>

                @if ($lh->trang_thai === 'Da dat')
                    <div class="flex items-center gap-2">
                        <a href="{{ route('lich-hen.sua', $lh->id) }}"
                           class="text-xs text-aiblue border border-blue-200 rounded-lg px-3 py-1.5 hover:bg-blue-50">
                            Sửa lịch
                        </a>
                        <form method="POST" action="{{ route('lich-hen.huy', $lh->id) }}"
                              onsubmit="return confirm('Hủy lịch hẹn này?');">
                            @csrf
                            <button type="submit" class="text-xs text-red-500 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50">
                                Hủy lịch
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
                <p class="text-sm text-slate-400 mb-3">Bạn chưa có lịch hẹn nào.</p>
                <a href="{{ route('dat-lich') }}" class="text-aiblue text-sm hover:underline">Đặt lịch ngay →</a>
            </div>
        @endforelse
    </div>

</div>
@endsection