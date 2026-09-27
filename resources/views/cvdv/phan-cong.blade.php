@extends('layouts.admin')

@section('title', 'Phân công kỹ thuật viên - CVDV')

@section('sidebar-menu')
        <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg{{ request()->routeIs('cvdv.dashboard') ? ' bg-navylight text-white' : ' text-slate-300 hover:bg-navylight' }} text-sm">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg{{ request()->routeIs('cvdv.lich-hen') || request()->routeIs('cvdv.phieu.tao-moi') || request()->routeIs('cvdv.phan-cong') ? ' bg-navylight text-white' : ' text-slate-300 hover:bg-navylight' }} text-sm">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg{{ request()->routeIs('cvdv.phieu.*') && !request()->routeIs('cvdv.phieu.tao-moi') ? ' bg-navylight text-white' : ' text-slate-300 hover:bg-navylight' }} text-sm">Quản lý phiếu sửa chữa</a>
    <a href="#" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Lập hóa đơn</a>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-navy">Phân công kỹ thuật viên</h1>
        <button id="btn-goi-y-ai" class="text-xs text-aipurple border border-indigo-200 rounded-lg px-3 py-2 hover:bg-aipurplebg flex items-center gap-1.5">
            <span>✦</span> Gợi ý AI
        </button>
    </div>

    <form method="GET" class="mb-5">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo tên khách, biển số, mã phiếu..."
               class="w-full max-w-md text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
    </form>

    {{-- Kết quả gợi ý AI --}}
    <div id="khung-goi-y-ai" class="hidden bg-aipurplebg border border-indigo-200 rounded-xl p-4 mb-6"></div>

    <div class="space-y-3" id="danh-sach-phieu">
        @forelse ($dsPhieuChoPhanCong as $p)
            <div class="bg-white border border-slate-200 rounded-xl p-4" data-phieu-id="{{ $p->id }}">
                <p class="text-sm font-medium text-navy">#PSC-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }} — {{ $p->hang_xe }} {{ $p->dong_xe }} ({{ $p->bien_so }})</p>
                <p class="text-xs text-slate-500 mb-3">{{ $p->mo_ta_su_co }}</p>

                <form method="POST" action="{{ route('cvdv.phan-cong.xu-ly', $p->id) }}" class="flex items-center gap-2">
                    @csrf
                    <select name="ktv_id" class="ktv-select flex-1 text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-aiblue">
                        <option value="">-- Chọn kỹ thuật viên --</option>
                        @foreach ($dsKTV as $ktv)
                            <option value="{{ $ktv->id }}">{{ $ktv->email_sdt }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="text-xs bg-navy text-white rounded-lg px-4 py-2 hover:bg-navylight">
                        Xác nhận
                    </button>
                </form>
            </div>
        @empty
            <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
                <p class="text-sm text-slate-400">Không có phiếu nào đang chờ phân công.</p>
            </div>
        @endforelse
    </div>
@endsection

<script>
    document.getElementById('btn-goi-y-ai').addEventListener('click', async function () {
        this.innerText = 'Đang phân tích...';
        try {
            const res = await fetch('{{ route("cvdv.phan-cong.goi-y-ai") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            const data = await res.json();
            const khung = document.getElementById('khung-goi-y-ai');

            if (!data.aiApDung) {
                khung.innerHTML = `<p class="text-sm text-slate-500">${data.lyDo}</p>`;
                khung.classList.remove('hidden');
                this.innerHTML = '<span>✦</span> Gợi ý AI';
                return;
            }

            let html = '<div class="flex items-center gap-2 mb-3"><span class="text-aipurple text-sm">✦</span><span class="text-xs font-semibold text-aipurple">Gợi ý phân công từ AI</span></div>';

            (data.ktvDeXuat || []).forEach(function (item) {
                html += `<div class="text-sm text-navy mb-2 pb-2 border-b border-indigo-100 last:border-0 last:mb-0 last:pb-0">
                            <p class="font-medium">Phiếu #PSC-${String(item.phieuId).padStart(4, '0')} → KTV ID ${item.ktvId}</p>
                            <p class="text-xs text-slate-500">${item.lyDo}</p>
                         </div>`;
            });

            khung.innerHTML = html;
            khung.classList.remove('hidden');

            // Tự động chọn sẵn KTV theo gợi ý AI vào đúng dropdown
            (data.ktvDeXuat || []).forEach(function (item) {
                const dong = document.querySelector(`[data-phieu-id="${item.phieuId}"]`);
                if (dong) {
                    const select = dong.querySelector('.ktv-select');
                    if (select) select.value = item.ktvId;
                }
            });
        } catch (err) {
            alert('Không thể lấy gợi ý AI lúc này.');
        }
        this.innerHTML = '<span>✦</span> Gợi ý AI';
    });
</script>