@extends('layouts.admin')

@section('title', 'Dashboard - Quản lý')

@section('sidebar-menu')
    <a href="{{ route('quan-ly.dashboard') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Báo cáo</a>
    <a href="{{ route('quan-ly.dich-vu') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Danh mục dịch vụ</a>
    <a href="{{ route('quan-ly.phu-tung') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Tồn kho phụ tùng</a>
    <a href="{{ route('quan-ly.tai-khoan') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản trị tài khoản</a>
@endsection

@section('content')
    <h1 class="text-xl font-semibold text-navy mb-1">Dashboard báo cáo</h1>

    <form method="GET" class="flex items-end gap-2 mb-6">
        <div>
            <label class="text-xs text-slate-400 block mb-1">Từ ngày</label>
            <input type="date" name="tu_ngay" value="{{ $tuNgay }}"
                   class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs text-slate-400 block mb-1">Đến ngày</label>
            <input type="date" name="den_ngay" value="{{ $denNgay }}"
                   class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-aiblue">
        </div>
        <button type="submit" class="text-sm bg-navy text-white rounded-lg px-4 py-2 hover:bg-navylight">Xem</button>

        <div class="flex gap-1 ml-2">
            <a href="{{ route('quan-ly.dashboard', ['tu_ngay' => date('Y-m-d'), 'den_ngay' => date('Y-m-d')]) }}"
               class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 hover:bg-slate-50">Hôm nay</a>
            <a href="{{ route('quan-ly.dashboard', ['tu_ngay' => now()->startOfWeek()->format('Y-m-d'), 'den_ngay' => date('Y-m-d')]) }}"
               class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 hover:bg-slate-50">Tuần này</a>
            <a href="{{ route('quan-ly.dashboard') }}"
               class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 hover:bg-slate-50">Tháng này</a>
            <a href="{{ route('quan-ly.dashboard', ['tu_ngay' => now()->startOfQuarter()->format('Y-m-d'), 'den_ngay' => date('Y-m-d')]) }}"
               class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 hover:bg-slate-50">Quý này</a>
        </div>

        <div class="ml-auto flex gap-2">
            <a href="{{ route('quan-ly.dashboard.xuat-csv', ['tu_ngay' => $tuNgay, 'den_ngay' => $denNgay]) }}"
               class="text-xs border border-slate-200 rounded-lg px-3 py-2 hover:bg-slate-50">
                📊 Xuất Excel
            </a>
        </div>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-xs text-slate-400">Doanh thu tháng</p>
            </div>
            <p class="text-xl font-semibold text-navy">{{ number_format($doanhThuThang) }}đ</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" class="w-3.5 h-3.5"><path d="M9 12l2 2 4-4M7.8 4.2A5 5 0 0116.2 4.2a5 5 0 013.5 6.4 5 5 0 010 6.9 5 5 0 01-6.4 3.5 5 5 0 01-6.9 0 5 5 0 01-3.5-6.4 5 5 0 010-6.9 5 5 0 013.5-3.5z" stroke="#3B82F6" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <p class="text-xs text-slate-400">Phiếu hoàn thành</p>
            </div>
            <p class="text-xl font-semibold text-navy">{{ $soPhieuHoanThanh }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-full bg-aipurplebg flex items-center justify-center flex-shrink-0">
                    <span class="text-aipurple text-xs leading-none">✦</span>
                </div>
                <p class="text-xs text-slate-400">Đề xuất AI chấp nhận</p>
            </div>
            <p class="text-xl font-semibold text-aipurple">{{ $tyLeAiChapNhan }}%</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg viewBox="0 0 24 24" fill="#F59E0B" class="w-3.5 h-3.5"><path d="M12 2l2.9 6.6 7.1.7-5.4 4.7 1.6 7-6.2-3.7-6.2 3.7 1.6-7-5.4-4.7 7.1-.7z"/></svg>
                </div>
                <p class="text-xs text-slate-400">Đánh giá trung bình</p>
            </div>
            <p class="text-xl font-semibold text-navy">
                {{ $diemDanhGiaTrungBinh ? number_format($diemDanhGiaTrungBinh, 1) : '—' }} ★
                <span class="text-xs text-slate-400 font-normal">({{ $soLuongDanhGia }})</span>
            </p>
        </div>
    </div>

    <p class="font-semibold text-navy text-base mb-3">Hiệu suất kỹ thuật viên</p>
    <div class="bg-white border border-slate-200 rounded-xl p-5">
        @forelse ($hieuSuatKTV as $ktv)
            <div class="mb-4 last:mb-0">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-navy">{{ $ktv->email_sdt }}</span>
                    <span class="text-slate-400">{{ $ktv->so_phieu }} phiếu</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-aiblue rounded-full" style="width: {{ ($ktv->so_phieu / $soPhieuMax) * 100 }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">Chưa có dữ liệu.</p>
        @endforelse
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="font-semibold text-navy text-base mb-3">Doanh thu theo ngày</p>
            @if ($doanhThuTheoNgay->isNotEmpty())
                <canvas id="bieuDoDoanhThu" height="180"></canvas>
            @else
                <p class="text-sm text-slate-400 text-center py-10">Chưa có dữ liệu trong kỳ này.</p>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="font-semibold text-navy text-base mb-3">Phân bổ trạng thái phiếu</p>
            @if ($phanBoTrangThaiPhieu->isNotEmpty())
                <canvas id="bieuDoTrangThai" height="180"></canvas>
            @else
                <p class="text-sm text-slate-400 text-center py-10">Chưa có dữ liệu trong kỳ này.</p>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="font-semibold text-navy text-base mb-3">Nhóm lỗi thường gặp nhất</p>
            @if ($thongKeNhomLoi->isNotEmpty())
                <canvas id="bieuDoNhomLoi" height="180"></canvas>
            @else
                <p class="text-sm text-slate-400 text-center py-10">Chưa có dữ liệu phân loại AI trong kỳ này.</p>
            @endif
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <script>
        @if ($doanhThuTheoNgay->isNotEmpty())
        new Chart(document.getElementById('bieuDoDoanhThu'), {
            type: 'line',
            data: {
                labels: {!! json_encode($doanhThuTheoNgay->map(fn($d) => \Carbon\Carbon::parse($d->ngay)->format('d/m'))) !!},
                datasets: [{
                    label: 'Doanh thu',
                    data: {!! json_encode($doanhThuTheoNgay->pluck('tong')) !!},
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { callback: (v) => (v / 1000) + 'k' } }
                }
            }
        });
        @endif

        @if ($phanBoTrangThaiPhieu->isNotEmpty())
        @php
            $tenTrangThai = ['Da tiep nhan' => 'Đã tiếp nhận', 'Cho bao gia' => 'Chờ báo giá', 'Hoan thanh' => 'Hoàn thành', 'Da huy' => 'Đã hủy'];
            $mauTrangThai = ['Da tiep nhan' => '#3B82F6', 'Cho bao gia' => '#F59E0B', 'Hoan thanh' => '#10B981', 'Da huy' => '#94A3B8'];
        @endphp
        new Chart(document.getElementById('bieuDoTrangThai'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($phanBoTrangThaiPhieu->map(fn($p) => $tenTrangThai[$p->trang_thai] ?? $p->trang_thai)) !!},
                datasets: [{
                    data: {!! json_encode($phanBoTrangThaiPhieu->pluck('so_luong')) !!},
                    backgroundColor: {!! json_encode($phanBoTrangThaiPhieu->map(fn($p) => $mauTrangThai[$p->trang_thai] ?? '#CBD5E1')) !!},
                    borderWidth: 0,
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
            }
        });
        @endif

        @if ($thongKeNhomLoi->isNotEmpty())
        new Chart(document.getElementById('bieuDoNhomLoi'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($thongKeNhomLoi->pluck('nhom_su_co_ai')) !!},
                datasets: [{
                    label: 'Số phiếu',
                    data: {!! json_encode($thongKeNhomLoi->pluck('so_luong')) !!},
                    backgroundColor: '#4438C9',
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { ticks: { stepSize: 1 } } }
            }
        });
        @endif
    </script>
@endsection