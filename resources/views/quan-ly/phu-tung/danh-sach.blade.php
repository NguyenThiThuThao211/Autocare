@extends('layouts.admin')

@section('title', 'Tồn kho phụ tùng - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Báo cáo</a>
    <a href="{{ route('quan-ly.dich-vu') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Danh mục dịch vụ</a>
    <a href="{{ route('quan-ly.phu-tung') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Tồn kho phụ tùng</a>
    <a href="{{ route('quan-ly.tai-khoan') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản trị tài khoản</a>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-navy">Tồn kho phụ tùng</h1>
        <a href="{{ route('quan-ly.phu-tung.tao-moi') }}" class="text-xs bg-navy text-white rounded-lg px-3 py-2 hover:bg-navylight">
            + Thêm phụ tùng
        </a>
    </div>

    <form method="GET" class="flex gap-2 mb-5">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo tên phụ tùng..."
               class="flex-1 max-w-md text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        <select name="loc_ton_kho" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-lg px-2 py-2.5 focus:outline-none focus:border-aiblue">
            <option value="tat_ca" {{ $locTonKho === 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
            <option value="sap_het" {{ $locTonKho === 'sap_het' ? 'selected' : '' }}>Sắp hết (≤5)</option>
        </select>
        <select name="loc_trang_thai" onchange="this.form.submit()"
        class="text-sm border border-slate-200 rounded-lg px-2 py-2.5 focus:outline-none focus:border-aiblue">
    <option value="hoat_dong" {{ $locTrangThai === 'hoat_dong' ? 'selected' : '' }}>Đang hoạt động</option>
    <option value="da_an" {{ $locTrangThai === 'da_an' ? 'selected' : '' }}>Đã ẩn</option>
    <option value="tat_ca" {{ $locTrangThai === 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
</select>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($dsPhuTung as $pt)
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0 {{ $pt->trang_thai !== 'Hoat dong' ? 'opacity-50' : '' }}">
                <div>
                    <p class="text-sm font-medium text-navy">{{ $pt->ten }}</p>
                    <p class="text-xs text-slate-400">{{ number_format($pt->gia_ban) }}đ/cái</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $pt->ton_kho <= 5 ? 'bg-red-50 text-red-600' : 'bg-slate-100 text-slate-600' }}">
                        Tồn: {{ $pt->ton_kho }}
                    </span>
                    <a href="{{ route('quan-ly.phu-tung.lich-su', $pt->id) }}" class="text-xs text-aiblue hover:underline">Lịch sử</a>
                    <form method="POST" action="{{ route('quan-ly.phu-tung.nhap-kho', $pt->id) }}" class="flex items-center gap-1">
                        @csrf
                        <input type="number" name="so_luong_nhap" min="1" placeholder="SL" required
                               class="w-14 text-xs border border-slate-200 rounded px-2 py-1">
                        <input type="text" name="nha_cung_cap" placeholder="Nhà CC (tùy chọn)"
                               class="w-28 text-xs border border-slate-200 rounded px-2 py-1">
                        <button type="submit" class="text-xs bg-slate-100 text-slate-700 rounded px-2 py-1 hover:bg-slate-200">Nhập</button>
                    </form>
                    <form method="POST" action="{{ route('quan-ly.phu-tung.doi-trang-thai', $pt->id) }}">
                        @csrf
                        <button type="submit" class="text-xs {{ $pt->trang_thai === 'Hoat dong' ? 'text-red-500' : 'text-green-600' }} hover:underline">
                            {{ $pt->trang_thai === 'Hoat dong' ? 'Ẩn' : 'Hiện lại' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Không tìm thấy phụ tùng nào.</p>
        @endforelse
    </div>
@endsection