@extends('layouts.admin')

@section('title', 'Dashboard - Cố vấn dịch vụ')

@section('sidebar-menu')
        <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg{{ request()->routeIs('cvdv.dashboard') ? ' bg-navylight text-white' : ' text-slate-300 hover:bg-navylight' }} text-sm">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg{{ request()->routeIs('cvdv.lich-hen') || request()->routeIs('cvdv.phieu.tao-moi') || request()->routeIs('cvdv.phan-cong') ? ' bg-navylight text-white' : ' text-slate-300 hover:bg-navylight' }} text-sm">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg{{ request()->routeIs('cvdv.phieu.*') && !request()->routeIs('cvdv.phieu.tao-moi') ? ' bg-navylight text-white' : ' text-slate-300 hover:bg-navylight' }} text-sm">Quản lý phiếu sửa chữa</a>
    <a href="{{ route('cvdv.khach-hang') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Danh sách khách hàng</a>
    <a href="#" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Lập hóa đơn</a>
@endsection

@section('content')
    <h1 class="text-xl font-semibold text-navy mb-1">Dashboard hôm nay</h1>
    <p class="text-sm text-slate-400 mb-6">{{ \Carbon\Carbon::now()->translatedFormat('l, d/m/Y') }}</p>

    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Phiếu chờ xử lý</p>
            <p class="text-3xl font-semibold text-navy">{{ $phieuCho }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Chờ báo giá</p>
            <p class="text-3xl font-semibold text-navy">{{ $choBaoGia }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-1">Lịch hẹn hôm nay</p>
            <p class="text-3xl font-semibold text-navy">{{ $lichHenHomNay }}</p>
        </div>
    </div>

    @if ($dsPhieuChoThanhToan->isNotEmpty())
        <div class="flex items-center gap-2 mb-3">
            <h2 class="font-semibold text-navy text-base">Chờ khách đến thanh toán</h2>
            <span class="text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full">{{ $dsPhieuChoThanhToan->count() }}</span>
        </div>
        <div class="bg-white border border-red-100 rounded-xl overflow-hidden mb-8">
            @foreach ($dsPhieuChoThanhToan as $p)
                <a href="{{ route('cvdv.phieu.chi-tiet', $p->id) }}" class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
                    <div>
                        <p class="text-sm text-navy">#PSC-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }} — {{ $p->bien_so }}</p>
                        <p class="text-xs text-slate-400">{{ $p->ho_ten }} — {{ $p->sdt }} — Xong từ {{ \Carbon\Carbon::parse($p->ngay_lap)->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-medium text-red-600 flex-shrink-0">{{ number_format($p->tong_tien) }}đ</span>
                </a>
            @endforeach
        </div>
    @endif

    @if ($dsBaoGiaChoDuyet->isNotEmpty())
        <div class="flex items-center gap-2 mb-3">
            <h2 class="font-semibold text-navy text-base">Chờ khách duyệt báo giá</h2>
            <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full">{{ $dsBaoGiaChoDuyet->count() }}</span>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-8">
            @foreach ($dsBaoGiaChoDuyet as $bg)
                <a href="{{ route('cvdv.phieu.chi-tiet', $bg->phieu_id) }}" class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
                    <div>
                        <p class="text-sm text-navy">#PSC-{{ str_pad($bg->phieu_id, 4, '0', STR_PAD_LEFT) }} — {{ $bg->bien_so }}</p>
                        <p class="text-xs text-slate-400">{{ $bg->ho_ten }} — {{ $bg->sdt }} — Gửi từ {{ \Carbon\Carbon::parse($bg->ngay_lap)->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-medium text-amber-700 flex-shrink-0">{{ number_format($bg->tong_tien) }}đ</span>
                </a>
            @endforeach
        </div>
    @endif

    <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-navy text-base">Phiếu cần xử lý</h2>
        <a href="{{ route('cvdv.phieu.tao-moi') }}" class="text-xs text-aiblue hover:underline">+ Tạo phiếu mới</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($dsPhieuCanXuLy as $p)
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
                <div>
                    <p class="text-sm font-medium text-navy">#PSC-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }} — {{ $p->hang_xe }} {{ $p->dong_xe }} ({{ $p->bien_so }})</p>
                    <p class="text-xs text-slate-400">{{ $p->mo_ta_su_co }}</p>
                </div>
                @php
                    $mau = $p->trang_thai === 'Cho bao gia' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700';
                    $ten = $p->trang_thai === 'Cho bao gia' ? 'Chờ báo giá' : 'Đã tiếp nhận';
                @endphp
                <span class="text-xs px-2.5 py-1 rounded-full {{ $mau }} flex-shrink-0">{{ $ten }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Không có phiếu nào cần xử lý.</p>
        @endforelse
    </div>
@endsection