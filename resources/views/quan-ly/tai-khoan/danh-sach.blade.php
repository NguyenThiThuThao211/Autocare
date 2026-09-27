@extends('layouts.admin')

@section('title', 'Quản trị tài khoản - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Báo cáo</a>
    <a href="{{ route('quan-ly.dich-vu') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Danh mục dịch vụ</a>
    <a href="{{ route('quan-ly.phu-tung') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Tồn kho phụ tùng</a>
    <a href="{{ route('quan-ly.tai-khoan') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Quản trị tài khoản</a>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-navy">Quản trị tài khoản</h1>
        <a href="{{ route('quan-ly.tai-khoan.tao-moi') }}" class="text-xs bg-navy text-white rounded-lg px-3 py-2 hover:bg-navylight">
            + Tạo tài khoản nhân viên
        </a>
    </div>

    <form method="GET" class="flex gap-2 mb-5">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo email/SĐT..."
               class="flex-1 max-w-md text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        <select name="vai_tro_loc" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-lg px-2 py-2.5 focus:outline-none focus:border-aiblue">
            <option value="nhan_vien" {{ $locVaiTro === 'nhan_vien' ? 'selected' : '' }}>Nhân viên</option>
            <option value="khach_hang" {{ $locVaiTro === 'khach_hang' ? 'selected' : '' }}>Khách hàng</option>
            <option value="tat_ca" {{ $locVaiTro === 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
        </select>
        <select name="trang_thai_loc" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-lg px-2 py-2.5 focus:outline-none focus:border-aiblue">
            <option value="tat_ca" {{ $locTrangThai === 'tat_ca' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="hoat_dong" {{ $locTrangThai === 'hoat_dong' ? 'selected' : '' }}>Đang hoạt động</option>
            <option value="khoa" {{ $locTrangThai === 'khoa' ? 'selected' : '' }}>Đã khóa</option>
        </select>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($dsTaiKhoan as $tk)
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-navy">{{ $tk->email_sdt }}</p>
                    @php
                        $tenVaiTro = match($tk->vai_tro) {
                            'Co van dich vu' => 'Cố vấn dịch vụ',
                            'Ky thuat vien' => 'Kỹ thuật viên',
                            'Quan ly' => 'Quản lý',
                            'Khach hang' => 'Khách hàng',
                            default => $tk->vai_tro,
                        };
                    @endphp
                    <p class="text-xs text-slate-400">{{ $tenVaiTro }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full {{ $tk->trang_thai === 'Hoat dong' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $tk->trang_thai === 'Hoat dong' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $tk->trang_thai === 'Hoat dong' ? 'Hoạt động' : 'Đã khóa' }}
                    </span>
                    <form method="POST" action="{{ route('quan-ly.tai-khoan.doi-trang-thai', $tk->id) }}"
                          onsubmit="return confirm('{{ $tk->trang_thai === 'Hoat dong' ? 'Khóa' : 'Mở khóa' }} tài khoản này?');">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-navy underline">
                            {{ $tk->trang_thai === 'Hoat dong' ? 'Khóa' : 'Mở khóa' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Không tìm thấy tài khoản nào.</p>
        @endforelse
    </div>
@endsection