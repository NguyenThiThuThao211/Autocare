@extends('layouts.app')

@section('title', 'Trang chủ - AutoCare')

@section('content')
<div class="max-w-6xl mx-auto px-5 md:px-10 py-6 md:py-10">

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('thanhcong') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-slate-400 text-sm">Xin chào,</p>
            <h1 class="text-xl md:text-2xl font-semibold text-navy">{{ $khachHang->ho_ten ?? 'Khách hàng' }}</h1>
        </div>
        <a href="{{ route('ho-so') }}" class="text-aiblue text-sm hover:underline">Cập nhật hồ sơ →</a>
    </div>

    @if ($khachHang && $khachHang->sdt === 'Chua cap nhat')
        <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-lg px-4 py-3 mb-6">
            ⚠️ Vui lòng <a href="{{ route('ho-so') }}" class="underline font-medium">cập nhật số điện thoại</a> thật để tiện liên hệ khi đặt lịch.
        </div>
    @endif

    {{-- ============ GỢI Ý BẢO DƯỠNG (AI) ============ --}}
    @if ($goiYBaoDuong && ($goiYBaoDuong['coDeXuat'] ?? false))
        <div class="bg-aipurplebg border border-indigo-200 rounded-xl p-4 md:p-5 mb-6 flex gap-3">
            <span class="text-aipurple text-xl">✦</span>
            <div class="flex-1">
                <p class="font-semibold text-aipurple text-sm md:text-base mb-1">
                    Sắp đến hạn: {{ $goiYBaoDuong['dichVuSapDenHan'] ?? '' }}
                </p>
                <p class="text-slate-600 text-xs md:text-sm mb-3">{{ $goiYBaoDuong['giaiThich'] ?? '' }}</p>
                <a href="{{ route('de-xuat-bao-duong.xem', $dsXe->first()->id) }}"
                   class="inline-block bg-aipurple text-white text-xs md:text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Xem chi tiết & đặt lịch
                </a>
            </div>
        </div>
    @endif

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

    {{-- ============ LỊCH HẸN SẮP TỚI (đã đặt, chưa đến garage) ============ --}}
    @if ($lichHenSapToi->isNotEmpty())
        <div class="mb-6 md:mb-10">
            <h2 class="font-semibold text-navy text-base md:text-lg mb-3">Lịch hẹn sắp tới</h2>
            <div class="space-y-3">
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

    {{-- 2 nút nhanh --}}
    <div class="grid grid-cols-2 gap-3 md:gap-4 mb-6 md:mb-10">
        <a href="{{ route('dat-lich') }}" class="bg-white border border-slate-200 rounded-xl p-5 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5"><path d="M8 2v3M16 2v3M3.5 9h17M5 4h14a2 2 0 012 2v13a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" stroke="#3B82F6" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <p class="text-sm font-medium text-navy">Đặt lịch hẹn</p>
        </a>
        <a href="#hoa-don" class="bg-white border border-slate-200 rounded-xl p-5 text-center hover:shadow-md transition">
            <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5"><path d="M9 12h6m-6 4h6M9 8h6M5 4h14a1 1 0 011 1v15l-4-2-3 2-3-2-4 2V5a1 1 0 011-1z" stroke="#3B82F6" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <p class="text-sm font-medium text-navy">Hóa đơn</p>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">

        {{-- ============ LỊCH SỬ LỊCH HẸN (Đã hoàn tất / Đã hủy) ============ --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-navy text-base md:text-lg">Lịch sử lịch hẹn</h2>
                <a href="{{ route('lich-hen') }}" class="text-xs text-aiblue hover:underline">Xem tất cả →</a>
            </div>
            <div class="space-y-3 mb-6">
                @forelse ($lichSuLichHen as $lh)
                    <div class="bg-white border border-slate-200 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-navy">
                                {{ \Carbon\Carbon::parse($lh->ngay_gio)->format('d/m/Y - H:i') }}
                            </p>
                            @if ($lh->trang_thai === 'Da hoan tat')
                                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">Đã hoàn tất</span>
                            @else
                                <span class="text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full">Đã hủy</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500">
                            {{ $lh->ten_dich_vu ?: 'Chưa chọn dịch vụ' }}
                            — {{ $lh->bien_so ?? (($lh->hang_xe_du_kien ?? '') . ' ' . ($lh->dong_xe_du_kien ?? '')) }}
                        </p>
                    </div>
                @empty
                    <div class="bg-white border border-slate-200 rounded-xl p-5 text-center">
                        <p class="text-sm text-slate-400">Chưa có lịch sử lịch hẹn nào.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============ LỊCH SỬ DỊCH VỤ ============ --}}
        <div>
            <h2 class="font-semibold text-navy text-base md:text-lg mb-3">Lịch sử dịch vụ</h2>
            <div class="space-y-3">
                @forelse ($lichSuDichVu as $ls)
                    <div class="bg-white border border-slate-200 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-navy">{{ $ls->hang_xe }} {{ $ls->dong_xe }} — {{ $ls->bien_so }}</p>
                            @php
                                $mauTrangThai = match($ls->trang_thai) {
                                    'Hoan thanh' => 'bg-green-50 text-green-700',
                                    'Cho bao gia' => 'bg-amber-50 text-amber-700',
                                    'Da huy' => 'bg-slate-100 text-slate-500',
                                    default => 'bg-blue-50 text-blue-700',
                                };
                                $tenTrangThai = match($ls->trang_thai) {
                                    'Hoan thanh' => 'Hoàn thành',
                                    'Cho bao gia' => 'Chờ báo giá',
                                    'Da huy' => 'Đã hủy',
                                    default => 'Đã tiếp nhận',
                                };
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $mauTrangThai }}">{{ $tenTrangThai }}</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $ls->mo_ta_su_co }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($ls->ngay_tiep_nhan)->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <div class="bg-white border border-slate-200 rounded-xl p-5 text-center">
                        <p class="text-sm text-slate-400">Chưa có lịch sử dịch vụ nào.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============ HÓA ĐƠN ĐÃ THANH TOÁN ============ --}}
        <div id="hoa-don">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-navy text-base md:text-lg">Hóa đơn đã thanh toán</h2>
                <a href="{{ route('hoa-don') }}" class="text-xs text-aiblue hover:underline">Xem tất cả →</a>
            </div>
            <div class="space-y-3">
                @forelse ($hoaDonDaThanhToan as $hd)
                    <a href="{{ route('hoa-don.xem', $hd->id) }}"
                       class="block bg-white border border-slate-200 rounded-xl p-4 flex items-center justify-between hover:shadow-sm transition">
                        <div>
                            <p class="text-sm font-medium text-navy">Hóa đơn #{{ $hd->id }} — {{ $hd->bien_so }}</p>
                            <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($hd->ngay_lap)->format('d/m/Y') }}</p>
                        </div>
                        <p class="text-sm font-semibold text-navy">{{ number_format($hd->tong_tien) }}đ</p>
                    </a>
                @empty
                    <div class="bg-white border border-slate-200 rounded-xl p-5 text-center">
                        <p class="text-sm text-slate-400">Chưa có hóa đơn nào đã thanh toán.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection