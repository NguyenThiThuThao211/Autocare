@extends('layouts.admin')

@section('title', 'Danh sách khách hàng - CVDV')

@section('sidebar-menu')
    <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý phiếu sửa chữa</a>
    <a href="{{ route('cvdv.khach-hang') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Danh sách khách hàng</a>
@endsection

@section('content')
    <h1 class="text-xl font-semibold text-navy mb-4">Danh sách khách hàng</h1>

    <form method="GET" class="flex flex-wrap gap-2 mb-5">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo tên hoặc số điện thoại..."
               class="flex-1 min-w-[200px] text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        <select name="so_lan_sua" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            <option value="">Số lần sửa chữa</option>
            <option value="0" {{ $locSoLanSua === '0' ? 'selected' : '' }}>Chưa từng sửa</option>
            <option value="1-3" {{ $locSoLanSua === '1-3' ? 'selected' : '' }}>1 – 3 lần</option>
            <option value="4+" {{ $locSoLanSua === '4+' ? 'selected' : '' }}>Từ 4 lần trở lên</option>
        </select>
        <select name="co_xe" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            <option value="">Tất cả (xe)</option>
            <option value="co" {{ $locXe === 'co' ? 'selected' : '' }}>Đã có xe</option>
            <option value="chua_co" {{ $locXe === 'chua_co' ? 'selected' : '' }}>Chưa có xe</option>
        </select>
        @if ($tuKhoa || $locSoLanSua || $locXe)
            <a href="{{ route('cvdv.khach-hang') }}" class="text-sm text-slate-400 self-center hover:text-slate-600">Xóa lọc</a>
        @endif
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($dsKhachHang as $kh)
            <a href="{{ route('cvdv.khach-hang.chi-tiet', $kh->id) }}"
               class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
                <div>
                    <p class="text-sm font-medium text-navy">{{ $kh->ho_ten }}</p>
                    <p class="text-xs text-slate-400">{{ $kh->sdt }}</p>
                    @if ($kh->ten_xe_dau_tien)
                        <p class="text-xs text-slate-400 mt-0.5">
                            🚗 {{ $kh->ten_xe_dau_tien }}
                            @if ($kh->so_xe > 1)
                                <span class="text-slate-300">+{{ $kh->so_xe - 1 }} xe khác</span>
                            @endif
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">{{ $kh->so_xe }} xe</p>
                    <p class="text-xs text-slate-400">{{ $kh->so_phieu }} lần sửa chữa</p>
                </div>
            </a>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Không tìm thấy khách hàng nào.</p>
        @endforelse
    </div>
@endsection