@extends('layouts.admin')

@section('title', 'Quản lý phiếu sửa chữa - CVDV')

@section('sidebar-menu')
    <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Quản lý phiếu sửa chữa</a>
    <a href="#" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Lập hóa đơn</a>
@endsection

@section('content')
    <h1 class="text-xl font-semibold text-navy mb-4">Quản lý phiếu sửa chữa</h1>

    <form method="GET" class="flex gap-2 mb-5">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo biển số, SĐT, tên khách, hoặc mã phiếu..."
               class="flex-1 max-w-md text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        <select name="trang_thai_loc" onchange="this.form.submit()"
                class="text-sm border border-slate-200 rounded-lg px-2 py-2.5 focus:outline-none focus:border-aiblue">
            <option value="tat_ca" {{ $locTrangThai === 'tat_ca' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="da_tiep_nhan" {{ $locTrangThai === 'da_tiep_nhan' ? 'selected' : '' }}>Đã tiếp nhận</option>
            <option value="cho_bao_gia" {{ $locTrangThai === 'cho_bao_gia' ? 'selected' : '' }}>Chờ báo giá</option>
            <option value="hoan_thanh" {{ $locTrangThai === 'hoan_thanh' ? 'selected' : '' }}>Hoàn thành</option>
            <option value="da_huy" {{ $locTrangThai === 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
        </select>
    </form>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        @forelse ($dsPhieu as $p)
            <a href="{{ route('cvdv.phieu.chi-tiet', $p->id) }}"
               class="flex items-center justify-between px-4 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50">
                <div>
                    <p class="text-sm font-medium text-navy">
                        #PSC-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }} — {{ $p->hang_xe }} {{ $p->dong_xe }} ({{ $p->bien_so }})
                    </p>
                    <p class="text-xs text-slate-400">{{ $p->ho_ten }} — {{ $p->sdt }}</p>
                    <p class="text-xs text-slate-400">{{ $p->mo_ta_su_co }}</p>
                    @if ($p->ktv_email)
                        <p class="text-xs text-slate-400">KTV: {{ $p->ktv_email }}</p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    @php
                        $mau = match($p->trang_thai) {
                            'Da tiep nhan' => 'bg-blue-50 text-blue-700',
                            'Cho bao gia' => 'bg-amber-50 text-amber-700',
                            'Hoan thanh' => 'bg-green-50 text-green-700',
                            'Da huy' => 'bg-slate-100 text-slate-500',
                            default => 'bg-slate-100 text-slate-500',
                        };
                        $ten = match($p->trang_thai) {
                            'Da tiep nhan' => 'Đã tiếp nhận',
                            'Cho bao gia' => 'Chờ báo giá',
                            'Hoan thanh' => 'Hoàn thành',
                            'Da huy' => 'Đã hủy',
                            default => $p->trang_thai,
                        };
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $mau }}">{{ $ten }}</span>
                    @if ($p->bao_gia_trang_thai)
                        <p class="text-[11px] text-slate-400 mt-1">
                            Báo giá: {{ $p->bao_gia_trang_thai === 'Da duyet' ? 'Đã duyệt' : 'Chờ duyệt' }}
                        </p>
                    @endif
                </div>
            </a>
        @empty
            <p class="text-sm text-slate-400 text-center py-8">Không tìm thấy phiếu nào.</p>
        @endforelse
    </div>
@endsection