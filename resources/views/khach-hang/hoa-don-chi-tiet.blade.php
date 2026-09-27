@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn - AutoCare')

@section('content')
<div class="max-w-xl mx-auto px-5 py-8 md:py-12">

    <p class="text-xs text-slate-400 mb-1">Phiếu #PSC-{{ str_pad($hoaDon->phieu_id, 4, '0', STR_PAD_LEFT) }}</p>
    <h1 class="text-lg md:text-2xl font-semibold text-navy mb-1">Hóa đơn #{{ $hoaDon->id }}</h1>
    <p class="text-xs text-slate-400 mb-6">{{ $hoaDon->hang_xe }} {{ $hoaDon->dong_xe }} — {{ $hoaDon->bien_so }}</p>

    @if ($hoaDon->mo_ta_su_co)
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-4">
            <p class="text-xs font-medium text-slate-500 mb-1">Nội dung sửa chữa</p>
            <p class="text-sm text-slate-700">{{ $hoaDon->mo_ta_su_co }}</p>
        </div>
    @endif

    {{-- Chi tiết hạng mục --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-4">
        @forelse ($chiTiet as $ct)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm text-navy">{{ $ct->ten_dich_vu ?? $ct->ten_phu_tung }}</p>
                    <p class="text-xs text-slate-400">Số lượng: {{ $ct->so_luong }}</p>
                </div>
                <p class="text-sm font-medium text-navy">{{ number_format($ct->don_gia * $ct->so_luong) }}đ</p>
            </div>
        @empty
            <p class="text-sm text-slate-400">Không có chi tiết hạng mục.</p>
        @endforelse

        <div class="flex items-center justify-between pt-3 mt-2 border-t border-slate-200">
            <p class="font-semibold text-navy">Tổng cộng</p>
            <p class="font-bold text-navy text-lg">{{ number_format($hoaDon->tong_tien) }}đ</p>
        </div>
    </div>

    {{-- Trạng thái + QR nếu chưa thanh toán --}}
    @if ($hoaDon->trang_thai_thanh_toan === 'Da thanh toan')
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 text-center font-medium mb-3">
            ✓ Đã thanh toán
        </div>
        @if (!$daDanhGia)
            <a href="{{ route('danh-gia.tao-moi', $hoaDon->phieu_id) }}"
               class="block w-full text-center bg-aipurplebg text-aipurple font-medium text-sm py-2.5 rounded-lg hover:bg-indigo-100 transition">
                ✦ Đánh giá dịch vụ này
            </a>
        @endif
    @else
        <div class="bg-white border border-slate-200 rounded-xl p-5 text-center">
            <div class="w-32 h-32 mx-auto mb-3" style="background-image: repeating-conic-gradient(#0B2545 0% 25%, #fff 0% 50%); background-size: 8px 8px; border-radius: 8px;"></div>
            <p class="text-xs text-slate-500 mb-2">Quét mã QR để chuyển khoản, hoặc thanh toán tiền mặt tại quầy</p>
            <span class="inline-block text-xs bg-amber-50 text-amber-700 px-3 py-1 rounded-full">Chưa thanh toán</span>
        </div>
    @endif

    <p class="text-xs text-slate-400 text-center mt-4">
        Lập ngày {{ \Carbon\Carbon::parse($hoaDon->ngay_lap)->format('d/m/Y') }}
    </p>

</div>
@endsection