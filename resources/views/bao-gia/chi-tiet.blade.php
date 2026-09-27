@extends('layouts.app')

@section('title', 'Duyệt báo giá - AutoCare')

@section('content')
<div class="max-w-xl mx-auto px-5 py-8 md:py-12">

    <p class="text-xs text-slate-400 mb-1">Phiếu #PSC-{{ str_pad($baoGia->phieu_id, 4, '0', STR_PAD_LEFT) }}</p>
    <h1 class="text-lg md:text-xl font-semibold text-navy mb-1">Duyệt báo giá</h1>
    <p class="text-sm text-slate-500 mb-2">{{ $baoGia->hang_xe }} {{ $baoGia->dong_xe }} — {{ $baoGia->bien_so }}</p>

    @if ($baoGia->ket_qua_chan_doan)
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-4">
            <p class="text-xs font-medium text-slate-500 mb-1">Kết quả chẩn đoán</p>
            <p class="text-sm text-slate-700">{{ $baoGia->ket_qua_chan_doan }}</p>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-4">
        @foreach ($chiTiet as $ct)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm text-navy">{{ $ct->ten_dich_vu ?? $ct->ten_phu_tung ?? $ct->mo_ta_khac }}</p>
                    <p class="text-xs text-slate-400">Số lượng: {{ $ct->so_luong }}</p>
                </div>
                <p class="text-sm font-medium text-navy">{{ number_format($ct->don_gia * $ct->so_luong) }}đ</p>
            </div>
        @endforeach

        <div class="flex items-center justify-between pt-3 mt-2 border-t border-slate-200">
            <p class="font-semibold text-navy">Tổng cộng</p>
            <p class="font-bold text-navy text-lg">{{ number_format($baoGia->tong_tien) }}đ</p>
        </div>
    </div>

    @if ($baoGia->nguon_goi_y === 'AI')
        <div class="bg-aipurplebg text-aipurple text-xs rounded-lg px-3 py-2 mb-4 flex items-center gap-2">
            <span>✦</span> Báo giá có sự hỗ trợ gợi ý từ AI, đã được Cố vấn dịch vụ xác nhận
        </div>
    @endif

    @if ($baoGia->trang_thai === 'Cho khach duyet')
        <div class="flex flex-col sm:flex-row gap-3">
            <form method="POST" action="{{ route('bao-gia.duyet', $baoGia->id) }}" class="flex-1"
                  onsubmit="return confirm('Xác nhận đồng ý báo giá {{ number_format($baoGia->tong_tien) }}đ? Garage sẽ tiến hành sửa chữa theo đúng hạng mục này.');">
                @csrf
                <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
                    Đồng ý báo giá
                </button>
            </form>
            <form method="POST" action="{{ route('bao-gia.tu-choi', $baoGia->id) }}" class="flex-1"
                  onsubmit="return confirm('Từ chối báo giá này? Garage sẽ liên hệ điều chỉnh lại.');">
                @csrf
                <button type="submit" class="w-full border border-red-200 text-red-500 font-medium text-sm py-2.5 rounded-lg hover:bg-red-50 transition">
                    Từ chối báo giá
                </button>
            </form>
        </div>
        <p class="text-xs text-slate-400 text-center mt-3">
            Chưa chắc chắn? Bấm icon 📞 góc phải màn hình để liên hệ trực tiếp garage.
        </p>
    @else
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 text-center">
            ✓ Báo giá đã được duyệt
        </div>
    @endif

</div>
@endsection