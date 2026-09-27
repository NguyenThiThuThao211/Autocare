@extends('layouts.admin')

@section('title', 'Phiếu được phân công - KTV')

@section('sidebar-menu')
    <a href="{{ route('ktv.phieu') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Phiếu được phân công</a>
@endsection

@section('content')
    <h1 class="text-xl font-semibold text-navy mb-4">Phiếu được phân công</h1>

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-3 py-2 mb-4">{{ session('thanhcong') }}</div>
    @endif

    <form method="GET" class="flex flex-col gap-2 mb-4">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo tên khách, biển số, mã phiếu..."
               class="w-full max-w-md text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        <div class="flex gap-2">
            <select name="trang_thai_loc" onchange="this.form.submit()"
                    class="text-sm border border-slate-200 rounded-lg px-2 py-2 focus:outline-none focus:border-aiblue">
                <option value="tat_ca" {{ $locTrangThai === 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
                <option value="chua_kiem_tra" {{ $locTrangThai === 'chua_kiem_tra' ? 'selected' : '' }}>Chưa kiểm tra</option>
                <option value="cho_duyet" {{ $locTrangThai === 'cho_duyet' ? 'selected' : '' }}>Chờ khách duyệt</option>
                <option value="san_sang_sua" {{ $locTrangThai === 'san_sang_sua' ? 'selected' : '' }}>Sẵn sàng sửa</option>
                <option value="hoan_thanh" {{ $locTrangThai === 'hoan_thanh' ? 'selected' : '' }}>Đã hoàn thành</option>
            </select>
            <select name="sap_xep" onchange="this.form.submit()"
                    class="text-sm border border-slate-200 rounded-lg px-2 py-2 focus:outline-none focus:border-aiblue">
                <option value="ngay_tiep_nhan" {{ $sapXep === 'ngay_tiep_nhan' ? 'selected' : '' }}>Theo ngày tiếp nhận</option>
                <option value="ngay_duyet" {{ $sapXep === 'ngay_duyet' ? 'selected' : '' }}>Theo ngày duyệt (mới nhất)</option>
            </select>
        </div>
    </form>

    <div class="space-y-3">
        @forelse ($dsPhieu as $p)
            <a href="{{ route('ktv.phieu.chi-tiet', $p->id) }}"
               class="block bg-white border border-slate-200 rounded-xl p-4 hover:shadow-sm transition">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm font-medium text-navy">#PSC-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }} — {{ $p->hang_xe }} {{ $p->dong_xe }} ({{ $p->bien_so }})</p>

                    @php
                        if ($p->trang_thai === 'Hoan thanh') {
                            $mau = 'bg-slate-100 text-slate-500';
                            $ten = 'Đã hoàn thành';
                        } elseif ($p->trang_thai === 'Da tiep nhan') {
                            $mau = 'bg-blue-50 text-blue-700';
                            $ten = 'Chưa kiểm tra';
                        } elseif ($p->bao_gia_trang_thai === 'Da duyet') {
                            $mau = 'bg-green-50 text-green-700';
                            $ten = '✓ Sẵn sàng sửa';
                        } else {
                            $mau = 'bg-amber-50 text-amber-700';
                            $ten = 'Chờ khách duyệt';
                        }
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $mau }} flex-shrink-0">{{ $ten }}</span>
                </div>
                <p class="text-xs text-slate-400">{{ $p->ho_ten }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $p->mo_ta_su_co }}</p>
            </a>
        @empty
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
                <p class="text-sm text-slate-400">Không tìm thấy phiếu nào.</p>
            </div>
        @endforelse
    </div>
@endsection