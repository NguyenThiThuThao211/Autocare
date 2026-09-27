@extends('layouts.app')

@section('title', 'AutoCare - Bảo dưỡng ô tô thông minh')

@section('content')

{{-- ============ HERO ============ --}}
<div class="bg-gradient-to-br from-navy to-navylight relative overflow-hidden">
    {{-- Hoạ tiết trang trí --}}
    <svg class="absolute -right-10 -top-10 w-72 h-72 opacity-[0.06]" viewBox="0 0 24 24" fill="white">
        <path d="M12 2a1 1 0 011 1v1.06a7.96 7.96 0 012.6 1.08l.75-.75a1 1 0 011.42 1.42l-.75.75A7.96 7.96 0 0118.94 9H20a1 1 0 010 2h-1.06a7.96 7.96 0 01-1.08 2.6l.75.75a1 1 0 01-1.42 1.42l-.75-.75A7.96 7.96 0 0114 15.94V17a1 1 0 01-2 0v-1.06a7.96 7.96 0 01-2.6-1.08l-.75.75a1 1 0 01-1.42-1.42l.75-.75A7.96 7.96 0 015.06 11H4a1 1 0 010-2h1.06a7.96 7.96 0 011.08-2.6l-.75-.75a1 1 0 011.42-1.42l.75.75A7.96 7.96 0 0111 4.06V3a1 1 0 011-1zm0 6a4 4 0 100 8 4 4 0 000-8z"/>
    </svg>

    <div class="max-w-6xl mx-auto px-5 md:px-10 py-10 md:py-20 relative">
        <div class="md:flex md:items-center md:gap-12">
            <div class="md:flex-1">
                <span class="inline-flex items-center gap-1 bg-white/10 text-blue-200 text-xs md:text-sm px-3 py-1.5 rounded-full mb-4">
                    ✦ Tích hợp trí tuệ nhân tạo
                </span>

                <h1 class="text-white text-2xl md:text-5xl font-bold leading-tight mb-3 md:mb-5">
                    Bảo dưỡng ô tô<br><span class="text-blue-300">thông minh, nhanh chóng</span>
                </h1>
                <p class="text-slate-300 text-sm md:text-lg mb-6 md:mb-8 max-w-md">
                    AI ước lượng giá ngay khi bạn mô tả sự cố — không cần chờ đến garage mới biết chi phí.
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('dat-lich') }}"
                       class="inline-block text-center bg-aiblue text-white font-semibold text-sm md:text-base px-6 py-3 md:px-8 md:py-4 rounded-lg hover:bg-blue-600 transition">
                        Đặt lịch ngay
                    </a>
                    <a href="#" onclick="document.getElementById('btn-mo-chatbot').click(); return false;"
                       class="inline-flex items-center justify-center gap-2 text-white font-semibold text-sm md:text-base px-6 py-3 md:px-8 md:py-4 rounded-lg border border-white/30 hover:bg-white/10 transition">
                        ✦ Hỏi AI ngay
                    </a>
                </div>
            </div>

            {{-- Minh hoạ bên phải (desktop) --}}
            <div class="hidden md:flex md:flex-1 justify-center">
                <div class="w-72 h-72 rounded-full bg-white/5 flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" class="w-40 h-40">
                        <path d="M5 17h14M5 17a2 2 0 100-4 2 2 0 000 4zm14 0a2 2 0 100-4 2 2 0 000 4zM5 13l1.5-5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.37L19 13M5 13h14"
                              stroke="#93C5FD" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

@auth
    <div class="max-w-6xl mx-auto px-5 md:px-10 pt-6">
    @if ($baoGiaChoDuyet->isNotEmpty())
        <div class="mb-6">
            @foreach ($baoGiaChoDuyet as $bg)
                <a href="{{ route('bao-gia.xem', $bg->id) }}"
                   class="block bg-amber-50 border border-amber-200 rounded-xl p-4 mb-2 hover:bg-amber-100 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-700 font-semibold text-sm">⚠ Có báo giá mới cần duyệt</p>
                            <p class="text-xs text-slate-600 mt-0.5">
                                {{ $bg->hang_xe }} {{ $bg->dong_xe }} — {{ $bg->bien_so }} — {{ number_format($bg->tong_tien) }}đ
                            </p>
                        </div>
                        <span class="text-amber-700 text-sm">Xem →</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
    </div>

    @if ($lichHenSapToi->isNotEmpty())
        <div class="max-w-6xl mx-auto px-5 md:px-10 py-6 md:py-8">
            <h2 class="font-semibold text-navy text-base md:text-lg mb-3">Lịch hẹn sắp tới của bạn</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($lichHenSapToi as $lh)
                    <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-medium text-navy">
                                    {{ \Carbon\Carbon::parse($lh->ngay_gio)->format('d/m/Y - H:i') }}
                                </p>
                                <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">Đã đặt</span>
                            </div>
                            <p class="text-xs text-slate-500">
                                {{ $lh->ten_dich_vu ?: 'Chưa chọn dịch vụ' }}
                                — {{ $lh->bien_so ?? (($lh->hang_xe_du_kien ?? '') . ' ' . ($lh->dong_xe_du_kien ?? '')) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('lich-hen.sua', $lh->id) }}"
                               class="text-xs text-aiblue border border-blue-200 rounded-lg px-3 py-1.5 hover:bg-blue-50">
                                Sửa lịch
                            </a>
                            <form method="POST" action="{{ route('lich-hen.huy', $lh->id) }}" onsubmit="return confirm('Hủy lịch hẹn này?');">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50">
                                    Hủy lịch
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endauth

{{-- ============ THANH TIN CẬY ============ --}}
<div class="border-b border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto flex justify-around md:justify-center md:gap-24 py-5 md:py-8">
        <a href="{{ route('danh-gia') }}" class="text-center hover:opacity-70">
            <p class="font-bold text-navy text-lg md:text-2xl">★ 4.8</p>
            <p class="text-xs md:text-sm text-slate-400">Đánh giá</p>
        </a>
        <div class="text-center border-x md:border-x-0 border-slate-200 px-8 md:px-0">
            <p class="font-bold text-navy text-lg md:text-2xl">5.000+</p>
            <p class="text-xs md:text-sm text-slate-400">Khách hàng</p>
        </div>
        <div class="text-center">
            <p class="font-bold text-navy text-lg md:text-2xl">15 phút</p>
            <p class="text-xs md:text-sm text-slate-400">Phản hồi TB</p>
        </div>
    </div>
</div>

{{-- ============ DỊCH VỤ NỔI BẬT ============ --}}
<div class="max-w-6xl mx-auto px-5 md:px-10 py-10 md:py-16">
    <h2 class="font-bold text-navy text-lg md:text-3xl mb-1 md:mb-2 text-center">Dịch vụ nổi bật</h2>
    <p class="text-slate-400 text-sm text-center mb-6 md:mb-10">Đầy đủ hạng mục, giá rõ ràng ngay từ đầu</p>

    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 md:gap-6">
        @foreach ($dichVuNoiBat as $dv)
            <div class="text-center group cursor-pointer">
                <div class="w-14 h-14 md:w-20 md:h-20 mx-auto mb-2 md:mb-3 rounded-2xl bg-blue-50 flex items-center justify-center group-hover:bg-aiblue transition">
                    <svg viewBox="0 0 24 24" fill="none" class="w-6 h-6 md:w-8 md:h-8">
                        <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"
                              stroke="#3B82F6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:stroke-white"/>
                    </svg>
                </div>
                <p class="text-xs md:text-sm text-slate-600 font-medium">{{ $dv->ten_dich_vu }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- ============ QUY TRÌNH HOẠT ĐỘNG ============ --}}
<div class="bg-slate-50 py-10 md:py-16">
    <div class="max-w-6xl mx-auto px-5 md:px-10">
        <h2 class="font-bold text-navy text-lg md:text-3xl mb-1 md:mb-2 text-center">Chỉ 3 bước đơn giản</h2>
        <p class="text-slate-400 text-sm text-center mb-8 md:mb-12">Từ đặt lịch đến nhận xe, mọi thứ đều minh bạch</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-10">
            <div class="text-center">
                <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full bg-navy text-white flex items-center justify-center font-bold text-lg md:text-xl">1</div>
                <p class="font-semibold text-navy text-sm md:text-lg mb-1">Đặt lịch trực tuyến</p>
                <p class="text-slate-500 text-xs md:text-sm">Chọn dịch vụ, mô tả sơ bộ vấn đề — AI chuẩn bị trước cho garage</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full bg-navy text-white flex items-center justify-center font-bold text-lg md:text-xl">2</div>
                <p class="font-semibold text-navy text-sm md:text-lg mb-1">Nhận báo giá minh bạch</p>
                <p class="text-slate-500 text-xs md:text-sm">AI gợi ý hạng mục & chi phí, bạn duyệt trước khi sửa</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full bg-navy text-white flex items-center justify-center font-bold text-lg md:text-xl">3</div>
                <p class="font-semibold text-navy text-sm md:text-lg mb-1">Nhận xe & đánh giá</p>
                <p class="text-slate-500 text-xs md:text-sm">Thanh toán tại quầy, quét QR — hóa đơn lưu sẵn trong tài khoản</p>
            </div>
        </div>
    </div>
</div>

{{-- ============ MINH BẠCH GIÁ + LIÊN HỆ NHANH ============ --}}
<div class="max-w-6xl mx-auto px-5 md:px-10 py-10 md:py-16">
    <div class="md:flex md:gap-10">
        <div class="bg-aipurplebg rounded-2xl p-5 md:p-8 flex gap-3 md:gap-5 mb-6 md:mb-0 md:flex-1">
            <span class="text-aipurple text-xl md:text-3xl">🛡️</span>
            <div>
                <p class="text-aipurple font-bold text-base md:text-xl mb-1">Minh bạch giá ngay từ đầu</p>
                <p class="text-slate-600 text-xs md:text-base">AI ước lượng chi phí trước khi bạn đến garage, không phát sinh bất ngờ.</p>
            </div>
        </div>

        <div class="md:flex-1">
            <h2 class="font-semibold text-navy text-base md:text-lg mb-3">Cần hỗ trợ ngay?</h2>
            <div class="flex flex-col md:flex-row gap-3">
                <a href="tel:1900xxxx"
                   class="flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-4 md:flex-1 hover:bg-slate-50 hover:shadow-sm transition">
                    <span class="text-navy text-lg md:text-xl">📞</span>
                    <div>
                        <p class="font-semibold text-sm md:text-base text-navy">Gọi hotline</p>
                        <p class="text-xs md:text-sm text-slate-400">1900 xxxx — 8:00-20:00</p>
                    </div>
                </a>
                <a href="#" onclick="document.getElementById('btn-mo-chatbot').click(); return false;"
                   class="flex items-center gap-3 border border-indigo-200 bg-aipurplebg rounded-xl px-4 py-4 md:flex-1 hover:bg-indigo-100 transition">
                    <span class="text-aipurple text-lg md:text-xl">✦</span>
                    <div>
                        <p class="font-semibold text-sm md:text-base text-aipurple">Hỏi trợ lý AI</p>
                        <p class="text-xs md:text-sm text-slate-500">Ước lượng giá tức thì</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ============ ĐÁNH GIÁ THẬT (nếu có dữ liệu) ============ --}}
@if (isset($danhGiaGanNhat) && $danhGiaGanNhat->count() > 0)
<div class="bg-navy py-10 md:py-16">
    <div class="max-w-6xl mx-auto px-5 md:px-10">
        <h2 class="font-bold text-white text-lg md:text-3xl mb-1 md:mb-2 text-center">Khách hàng nói gì?</h2>
        <p class="text-slate-400 text-sm text-center mb-8 md:mb-12">
            <a href="{{ route('danh-gia') }}" class="hover:text-white underline">Xem tất cả đánh giá →</a>
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
            @foreach ($danhGiaGanNhat as $dg)
                <div class="bg-white/5 rounded-xl p-5">
                    <p class="text-amber-400 mb-2">{{ str_repeat('★', $dg->so_sao) }}{{ str_repeat('☆', 5 - $dg->so_sao) }}</p>
                    <p class="text-slate-200 text-sm">{{ $dg->nhan_xet ?? 'Khách hàng hài lòng với dịch vụ' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection