@extends('layouts.admin')

@section('title', 'Kiểm tra phiếu - KTV')

@section('sidebar-menu')
    <a href="{{ route('ktv.phieu') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Phiếu được phân công</a>
@endsection

@section('content')
<div class="max-w-xl">
    <a href="{{ route('ktv.phieu') }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại danh sách</a>

    <h1 class="text-xl font-semibold text-navy mt-2 mb-1">Phiếu #PSC-{{ str_pad($phieu->id, 4, '0', STR_PAD_LEFT) }}</h1>
    <p class="text-sm text-slate-500 mb-1">{{ $phieu->hang_xe }} {{ $phieu->dong_xe }} ({{ $phieu->bien_so }}) — {{ $phieu->ho_ten }}</p>
    <p class="text-xs text-slate-400 bg-slate-50 rounded-lg px-3 py-2 mb-5">Mô tả sự cố: {{ $phieu->mo_ta_su_co }}</p>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-3 py-2 mb-4">{{ session('thanhcong') }}</div>
    @endif

    @php
        $baoGiaDaDuyet = \DB::table('BaoGia')->where('phieu_id', $phieu->id)->where('trang_thai', 'Da duyet')->exists();
    @endphp

    {{-- Khi báo giá đã được khách duyệt — hiện nút Hoàn thành, ẩn form chẩn đoán (đã xong việc) --}}
    @if ($baoGiaDaDuyet && $phieu->trang_thai !== 'Hoan thanh')
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
            <p class="text-sm text-green-700 font-medium mb-1">✓ Khách đã duyệt báo giá</p>
            <p class="text-xs text-slate-500 mb-3">Sau khi sửa chữa xong theo đúng hạng mục đã duyệt, bấm nút bên dưới.</p>
            <form method="POST" action="{{ route('ktv.phieu.hoan-thanh', $phieu->id) }}"
                  onsubmit="return confirm('Xác nhận đã sửa xong xe theo đúng báo giá đã duyệt?');">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-green-700 transition">
                    ✓ Đánh dấu đã sửa xong
                </button>
            </form>
        </div>
    @elseif ($phieu->trang_thai === 'Hoan thanh')
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-4 text-center">
            <p class="text-sm text-slate-500">✓ Phiếu đã hoàn thành</p>
        </div>
    @endif

    <form method="POST" action="{{ route('ktv.phieu.cap-nhat', $phieu->id) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Kết quả kiểm tra</label>
            <textarea name="ket_qua_chan_doan" id="o-chan-doan" rows="3" placeholder="VD: đĩa phanh mòn không đều, má phanh gần hết"
                      class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">{{ old('ket_qua_chan_doan', $phieu->ket_qua_chan_doan) }}</textarea>
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Thời gian dự kiến hoàn thành <span class="text-slate-400">(tùy chọn)</span></label>
            <input type="datetime-local" name="thoi_gian_du_kien"
                   value="{{ $phieu->thoi_gian_du_kien_hoan_thanh ? \Carbon\Carbon::parse($phieu->thoi_gian_du_kien_hoan_thanh)->format('Y-m-d\TH:i') : '' }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <button type="button" id="btn-goi-y-ai" class="text-xs text-aipurple border border-indigo-200 rounded-lg px-3 py-2 hover:bg-aipurplebg flex items-center gap-1.5">
            <span>✦</span> Gợi ý hạng mục cần thay/sửa (AI)
        </button>

        <div id="ket-qua-ai" class="hidden bg-aipurplebg border border-indigo-200 rounded-lg p-3">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-aipurple text-sm">✦</span>
                <span class="text-xs font-semibold text-aipurple">Gợi ý từ AI</span>
                <span id="ai-do-tin-cay" class="text-[10px] bg-white px-2 py-0.5 rounded-full ml-auto"></span>
            </div>
            <p class="text-xs text-slate-500">Đã tự động tích chọn bên dưới — bạn có thể điều chỉnh lại.</p>
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Hạng mục đề xuất thay/sửa</label>
            <div class="border border-slate-200 rounded-lg p-3 max-h-56 overflow-y-auto space-y-3">
                @foreach ($danhMucDichVu as $nhom => $dsDichVu)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 mb-1">{{ $nhom }}</p>
                        @foreach ($dsDichVu as $dv)
                            <label class="flex items-center justify-between text-sm py-1 cursor-pointer o-dich-vu-label" data-ten="{{ $dv->ten_dich_vu }}">
                                <span class="flex items-center gap-2">
                                    <input type="checkbox" name="dich_vu_ids[]" value="{{ $dv->id }}" class="rounded o-dich-vu-checkbox"
                                           {{ in_array($dv->id, $dsIdDaChon) ? 'checked' : '' }}>
                                    {{ $dv->ten_dich_vu }}
                                </span>
                                <span class="text-slate-400 text-xs">{{ number_format($dv->don_gia) }}đ</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <p class="text-[11px] text-slate-400 mt-1">* CVDV sẽ xem lại và có thể điều chỉnh khi lập báo giá chính thức.</p>
        </div>

        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Lưu kết quả kiểm tra
        </button>
    </form>
</div>

<script>
    document.getElementById('btn-goi-y-ai').addEventListener('click', async function () {
        const chanDoan = document.getElementById('o-chan-doan').value.trim();
        if (!chanDoan) { alert('Vui lòng nhập kết quả kiểm tra trước'); return; }

        this.innerText = 'Đang phân tích...';
        try {
            const res = await fetch('{{ route("ktv.phieu.goi-y-ai") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ket_qua_chan_doan: chanDoan }),
            });
            const data = await res.json();
            if (data.loi) { alert(data.loi); this.innerHTML = '<span>✦</span> Gợi ý hạng mục cần thay/sửa (AI)'; return; }

            document.getElementById('ai-do-tin-cay').innerText = (data.doTinCay || 0) + '%';
            document.getElementById('ket-qua-ai').classList.remove('hidden');

            document.querySelectorAll('.o-dich-vu-checkbox').forEach(cb => cb.checked = false);
            (data.hangMuc || []).forEach(function (hm) {
                document.querySelectorAll('.o-dich-vu-label').forEach(function (label) {
                    if (label.dataset.ten === hm.tenDichVu) {
                        label.querySelector('.o-dich-vu-checkbox').checked = true;
                    }
                });
            });
        } catch (err) { alert('Không thể lấy gợi ý lúc này.'); }
        this.innerHTML = '<span>✦</span> Gợi ý hạng mục cần thay/sửa (AI)';
    });
</script>
@endsection