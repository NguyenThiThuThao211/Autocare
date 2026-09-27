@extends('layouts.admin')

@section('title', 'Danh mục dịch vụ - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Báo cáo</a>
    <a href="{{ route('quan-ly.dich-vu') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Danh mục dịch vụ</a>
    <a href="{{ route('quan-ly.phu-tung') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Tồn kho phụ tùng</a>
    <a href="{{ route('quan-ly.tai-khoan') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản trị tài khoản</a>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-navy">Danh mục dịch vụ</h1>
        <a href="{{ route('quan-ly.dich-vu.tao-moi') }}" class="text-xs bg-navy text-white rounded-lg px-3 py-2 hover:bg-navylight">
            + Thêm dịch vụ
        </a>
    </div>

    <form method="GET" class="flex gap-2 mb-5">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo tên dịch vụ..."
               class="flex-1 max-w-md text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        <select name="trang_thai_loc" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-lg px-2 py-2.5 focus:outline-none focus:border-aiblue">
            <option value="tat_ca" {{ $locTrangThai === 'tat_ca' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="hoat_dong" {{ $locTrangThai === 'hoat_dong' ? 'selected' : '' }}>Đang hoạt động</option>
            <option value="da_an" {{ $locTrangThai === 'da_an' ? 'selected' : '' }}>Đã ẩn</option>
        </select>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($dsDichVu as $dv)
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-navy">{{ $dv->ten_dich_vu }}</p>
                    <p class="text-xs text-slate-400">{{ $dv->nhom_dich_vu }} — {{ number_format($dv->don_gia) }}đ
                        @if ($dv->chu_ky_khuyen_nghi_thang) · Định kỳ {{ $dv->chu_ky_khuyen_nghi_thang }} tháng @endif
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full {{ $dv->trang_thai === 'Hoat dong' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dv->trang_thai === 'Hoat dong' ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                        {{ $dv->trang_thai === 'Hoat dong' ? 'Hoạt động' : 'Đã ẩn' }}
                    </span>
                    <a href="{{ route('quan-ly.dich-vu.sua', $dv->id) }}" class="text-xs text-aiblue hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('quan-ly.dich-vu.doi-trang-thai', $dv->id) }}"
                          onsubmit="return confirm('{{ $dv->trang_thai === 'Hoat dong' ? 'Ẩn' : 'Hiện lại' }} dịch vụ này?');">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-navy underline">
                            {{ $dv->trang_thai === 'Hoat dong' ? 'Ẩn' : 'Hiện' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Không tìm thấy dịch vụ nào.</p>
        @endforelse
    </div>
@endsection