@extends('layouts.admin')

@section('title', 'Chi tiết phiếu - CVDV')

@section('sidebar-menu')
    <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Quản lý phiếu sửa chữa</a>
    <a href="#" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Lập hóa đơn</a>
@endsection

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại danh sách</a>

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-3 py-2 mt-3 mb-2">{{ session('thanhcong') }}</div>
    @endif

    {{-- ===== HEADER: mã phiếu + trạng thái ===== --}}
    <div class="flex items-center justify-between mt-2 mb-1">
        <h1 class="text-xl font-semibold text-navy">Phiếu #PSC-{{ str_pad($phieu->id, 4, '0', STR_PAD_LEFT) }}</h1>
        @php
            $mauTrangThai = match($phieu->trang_thai) {
                'Da tiep nhan' => 'bg-blue-50 text-blue-700',
                'Cho bao gia' => 'bg-amber-50 text-amber-700',
                'Hoan thanh' => 'bg-green-50 text-green-700',
                'Da huy' => 'bg-slate-100 text-slate-500',
                default => 'bg-slate-100 text-slate-500',
            };
            $tenTrangThai = match($phieu->trang_thai) {
                'Da tiep nhan' => 'Đã tiếp nhận',
                'Cho bao gia' => 'Chờ báo giá',
                'Hoan thanh' => 'Hoàn thành',
                'Da huy' => 'Đã hủy',
                default => $phieu->trang_thai,
            };
        @endphp
        <span class="text-sm px-3 py-1 rounded-full {{ $mauTrangThai }} font-medium">{{ $tenTrangThai }}</span>
    </div>
    <p class="text-xs text-slate-400 mb-4">Tiếp nhận lúc {{ \Carbon\Carbon::parse($phieu->ngay_tiep_nhan)->format('H:i - d/m/Y') }}</p>

    @if (!in_array($phieu->trang_thai, ['Hoan thanh', 'Da huy']))
        <details class="mb-6">
            <summary class="text-xs text-red-500 cursor-pointer hover:underline">Hủy phiếu này (khách đổi ý không sửa nữa) →</summary>
            <form method="POST" action="{{ route('cvdv.phieu.huy', $phieu->id) }}" class="mt-2 flex gap-2"
                  onsubmit="return confirm('Xác nhận hủy phiếu này?');">
                @csrf
                <input type="text" name="ly_do_huy_phieu" placeholder="Bắt buộc: lý do hủy" required
                       class="flex-1 text-xs border border-slate-200 rounded-lg px-3 py-2">
                <button type="submit" class="text-xs bg-red-500 text-white rounded-lg px-4 py-2 hover:bg-red-600">Hủy phiếu</button>
            </form>
        </details>
    @endif

    {{-- ===== THÔNG TIN KHÁCH HÀNG + XE ===== --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-4">
        <p class="text-xs font-semibold text-slate-400 mb-3 uppercase tracking-wide">Thông tin khách hàng & xe</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-400">Khách hàng</p>
                <p class="text-sm font-medium text-navy">{{ $phieu->ho_ten }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Số điện thoại</p>
                <p class="text-sm font-medium text-navy">{{ $phieu->sdt }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Biển số xe</p>
                <p class="text-sm font-medium text-navy">{{ $phieu->bien_so }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Hãng / dòng xe</p>
                <p class="text-sm font-medium text-navy">{{ $phieu->hang_xe }} {{ $phieu->dong_xe }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Kỹ thuật viên phụ trách</p>
                <p class="text-sm font-medium text-navy">{{ $phieu->ktv_email ?? '(chưa phân công)' }}</p>
            </div>
            @if ($phieu->thoi_gian_du_kien_hoan_thanh)
                <div>
                    <p class="text-xs text-slate-400">Dự kiến hoàn thành</p>
                    <p class="text-sm font-medium text-navy">{{ \Carbon\Carbon::parse($phieu->thoi_gian_du_kien_hoan_thanh)->format('H:i - d/m/Y') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ===== MÔ TẢ SỰ CỐ + PHÂN LOẠI AI ===== --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-4">
        <p class="text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wide">Mô tả sự cố</p>
        <p class="text-sm text-navy mb-3">{{ $phieu->mo_ta_su_co }}</p>

        @if ($phieu->nhom_su_co_ai)
            <div class="inline-flex items-center gap-1.5 bg-aipurplebg text-aipurple text-xs px-3 py-1.5 rounded-full">
                <span>✦</span> Phân loại AI: {{ $phieu->nhom_su_co_ai }}
            </div>
        @endif
    </div>

    {{-- ===== KẾT QUẢ KIỂM TRA / CHẨN ĐOÁN CỦA KTV ===== --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-4">
        <p class="text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wide">Kết quả kiểm tra (kỹ thuật viên)</p>

        @if ($phieu->ket_qua_chan_doan)
            <p class="text-sm text-navy">{{ $phieu->ket_qua_chan_doan }}</p>
        @else
            <div class="bg-amber-50 text-amber-700 text-xs rounded-lg px-3 py-2.5 flex items-center gap-2">
                <span>⏳</span>
                Đang chờ {{ $phieu->ktv_email ?? 'kỹ thuật viên' }} kiểm tra và ghi nhận kết quả.
            </div>
        @endif
    </div>

    {{-- ===== BÁO GIÁ — hạng mục cụ thể (thay má phanh, thay bánh...) ===== --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5 mb-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Hạng mục sửa chữa / Báo giá</p>
            @if ($phieu->ket_qua_chan_doan && $dsBaoGia->isEmpty())
                <a href="{{ route('cvdv.bao-gia.tao-moi', $phieu->id) }}"
                   class="text-xs bg-navy text-white rounded-lg px-3 py-1.5 hover:bg-navylight">
                    + Lập báo giá
                </a>
            @endif
        </div>

        @forelse ($dsBaoGia as $bg)
            <div class="border border-slate-100 rounded-lg p-3 mb-2 last:mb-0">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-navy">Báo giá #{{ $bg->id }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $bg->trang_thai === 'Da duyet' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $bg->trang_thai === 'Da duyet' ? '✓ Đã duyệt' : 'Chờ khách duyệt' }}
                    </span>
                </div>

                @if ($bg->trang_thai === 'Cho khach duyet')
                    <form method="POST" action="{{ route('cvdv.bao-gia.duyet-thay-khach', $bg->id) }}" class="mb-2"
                          onsubmit="return confirm('Xác nhận khách đã đồng ý (qua điện thoại/trực tiếp) và duyệt thay báo giá này?');">
                        @csrf
                        <button type="submit" class="text-xs text-navy border border-slate-300 rounded-lg px-3 py-1.5 hover:bg-slate-50">
                            Duyệt thay khách hàng
                        </button>
                    </form>
                @endif

                @foreach ($bg->chi_tiet as $ct)
                    <p class="text-xs text-slate-500 pl-2">• {{ $ct->ten_dich_vu ?? $ct->ten_phu_tung ?? $ct->mo_ta_khac }} (x{{ $ct->so_luong }}) — {{ number_format($ct->don_gia * $ct->so_luong) }}đ</p>
                @endforeach

                <p class="text-sm font-semibold text-navy mt-2 pt-2 border-t border-slate-100">Tổng: {{ number_format($bg->tong_tien) }}đ</p>
            </div>
        @empty
            <p class="text-sm text-slate-400">
                @if (!$phieu->ket_qua_chan_doan)
                    Chưa thể lập báo giá — đang chờ kỹ thuật viên chẩn đoán.
                @else
                    Chưa có báo giá nào.
                @endif
            </p>
        @endforelse

        {{-- Đánh dấu Hoàn thành giờ do CHÍNH KTV thực hiện (sau khi sửa xong thực tế) — CVDV chỉ theo dõi --}}
        @if ($coBaoGiaDaDuyet && $phieu->trang_thai !== 'Hoan thanh')
            <div class="bg-blue-50 text-blue-700 text-xs rounded-lg px-3 py-2.5 mt-4">
                ⏳ Đã duyệt — đang chờ {{ $phieu->ktv_email ?? 'kỹ thuật viên' }} sửa xong và xác nhận hoàn thành.
            </div>
        @endif
    </div>

    {{-- ===== HÓA ĐƠN ===== --}}
    <div class="bg-white border border-slate-200 rounded-xl p-5">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Hóa đơn</p>
            @if ($phieu->trang_thai === 'Hoan thanh' && !$hoaDon)
                <a href="{{ route('cvdv.hoa-don.tao-moi', $phieu->id) }}"
                   class="text-xs bg-navy text-white rounded-lg px-3 py-1.5 hover:bg-navylight">
                    + Lập hóa đơn
                </a>
            @endif
        </div>

        @if ($hoaDon)
            <p class="text-sm text-navy">Hóa đơn #{{ $hoaDon->id }} — {{ number_format($hoaDon->tong_tien) }}đ</p>
            <p class="text-xs text-slate-400 mt-0.5">Lập bởi: {{ $hoaDon->nguoi_lap_email ?? '(không rõ)' }} — {{ \Carbon\Carbon::parse($hoaDon->ngay_lap)->format('H:i - d/m/Y') }}</p>

            @if ($hoaDon->trang_thai_thanh_toan === 'Da thanh toan')
                <span class="inline-block text-xs bg-green-50 text-green-700 px-2.5 py-1 rounded-full mt-1">✓ Đã thanh toán</span>
            @else
                <span class="inline-block text-xs bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full mt-1 mb-3">Chưa thanh toán</span>

                <div class="bg-slate-50 rounded-lg p-4 text-center my-3">
                    <div class="w-28 h-28 mx-auto mb-2" style="background-image: repeating-conic-gradient(#0B2545 0% 25%, #fff 0% 50%); background-size: 7px 7px; border-radius: 6px;"></div>
                    <p class="text-xs text-slate-500">Mã QR chuyển khoản — {{ number_format($hoaDon->tong_tien) }}đ</p>
                </div>

                <form method="POST" action="{{ route('cvdv.hoa-don.danh-dau-thanh-toan', $hoaDon->id) }}"
                      onsubmit="return confirm('Xác nhận đã nhận đủ tiền (tiền mặt hoặc đã kiểm tra chuyển khoản)?');">
                    @csrf
                    <button type="submit" class="w-full bg-navy text-white text-sm font-medium py-2.5 rounded-lg hover:bg-navylight transition">
                        Đánh dấu đã thanh toán
                    </button>
                </form>
            @endif
        @elseif ($phieu->trang_thai !== 'Hoan thanh')
            <p class="text-sm text-slate-400">Chỉ lập hóa đơn được sau khi phiếu "Hoàn thành".</p>
        @else
            <p class="text-sm text-slate-400">Chưa có hóa đơn.</p>
        @endif
    </div>
</div>
@endsection