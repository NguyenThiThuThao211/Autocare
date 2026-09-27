@extends('layouts.admin')

@section('title', 'Lập báo giá - CVDV')

@section('sidebar-menu')
    <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Quản lý phiếu sửa chữa</a>
    <a href="#" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Lập hóa đơn</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('cvdv.phieu.chi-tiet', $phieu->id) }}" class="text-xs text-slate-400 hover:text-slate-600">← Quay lại phiếu</a>

    <h1 class="text-xl font-semibold text-navy mt-2 mb-1">Lập báo giá</h1>
    <p class="text-sm text-slate-500 mb-2">{{ $phieu->hang_xe }} {{ $phieu->dong_xe }} ({{ $phieu->bien_so }})</p>
    <p class="text-xs text-slate-400 bg-slate-50 rounded-lg px-3 py-2 mb-3">Chẩn đoán: {{ $phieu->ket_qua_chan_doan }}</p>

    @if (count($dsIdDeXuatKTV) > 0)
        <p class="text-xs text-blue-700 bg-blue-50 rounded-lg px-3 py-2 mb-5">
            ℹ️ Đã tự động tích sẵn {{ count($dsIdDeXuatKTV) }} hạng mục theo đề xuất của kỹ thuật viên — bạn có thể điều chỉnh lại trước khi gửi khách.
        </p>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <button type="button" id="btn-goi-y-ai" class="text-xs text-aipurple border border-indigo-200 rounded-lg px-3 py-2 hover:bg-aipurplebg flex items-center gap-1.5 mb-4">
        <span>✦</span> Gợi ý hạng mục & chi phí (AI)
    </button>

    <div id="ket-qua-ai" class="hidden bg-aipurplebg border border-indigo-200 rounded-xl p-4 mb-5">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-aipurple text-sm">✦</span>
            <span class="text-xs font-semibold text-aipurple">Gợi ý từ AI</span>
            <span id="ai-do-tin-cay" class="text-[10px] bg-white px-2 py-0.5 rounded-full ml-auto"></span>
        </div>
        <div id="ai-danh-sach-hang-muc" class="space-y-1"></div>
    </div>

    <form id="form-bao-gia" method="POST" action="{{ route('cvdv.bao-gia.luu', $phieu->id) }}" class="space-y-5"
          onsubmit="return confirm('Xác nhận gửi báo giá này cho khách hàng?');">
        @csrf
        <input type="hidden" name="co_dung_ai" id="co-dung-ai-hidden" value="0">

        {{-- Chọn dịch vụ --}}
        <div>
            <p class="text-sm font-semibold text-navy mb-2">Hạng mục dịch vụ</p>
            <div class="border border-slate-200 rounded-lg p-3 max-h-56 overflow-y-auto space-y-3">
                @foreach ($danhMucDichVu->groupBy('nhom_dich_vu') as $nhom => $dsDichVu)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 mb-1">{{ $nhom }}</p>
                        @foreach ($dsDichVu as $dv)
                            <label class="dich-vu-checkbox flex items-center justify-between text-sm py-1 cursor-pointer" data-ten="{{ $dv->ten_dich_vu }}">
                                <span class="flex items-center gap-2">
                                    <input type="checkbox" name="dich_vu_ids[]" value="{{ $dv->id }}" class="rounded"
                                           {{ in_array($dv->id, $dsIdDeXuatKTV) ? 'checked' : '' }}>
                                    {{ $dv->ten_dich_vu }}
                                    @if (in_array($dv->id, $dsIdDeXuatKTV))
                                        <span class="text-[10px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded-full">KTV đề xuất</span>
                                    @endif
                                </span>
                                <span class="text-slate-400 text-xs">{{ number_format($dv->don_gia) }}đ</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Chọn phụ tùng, kiểm tra tồn kho realtime --}}
        <div>
            <p class="text-sm font-semibold text-navy mb-2">Phụ tùng (nếu cần)</p>
            <div class="border border-slate-200 rounded-lg p-3 space-y-2">
                @foreach ($danhMucPhuTung as $pt)
                    <div class="flex items-center justify-between text-sm">
                        <span>{{ $pt->ten }} <span class="text-slate-400 text-xs">(còn {{ $pt->ton_kho }})</span></span>
                        <input type="number" name="phu_tung[{{ $pt->id }}]" min="0" max="{{ $pt->ton_kho }}" value="0"
                               class="phu-tung-input w-16 text-sm border border-slate-200 rounded px-2 py-1 text-center"
                               data-ton-kho="{{ $pt->ton_kho }}" data-ten="{{ $pt->ten }}">
                    </div>
                @endforeach
            </div>
            <p id="canh-bao-ton-kho" class="hidden text-xs text-red-500 mt-2"></p>
        </div>

        {{-- Chi phí khác — không nằm trong danh mục dịch vụ/phụ tùng có sẵn --}}
        <div>
            <p class="text-sm font-semibold text-navy mb-2">Chi phí khác <span class="text-slate-400 font-normal">(tùy chọn)</span></p>
            <div class="border border-slate-200 rounded-lg p-3 grid grid-cols-3 gap-2">
                <input type="text" name="chi_phi_khac_ghi_chu" id="o-chi-phi-khac-ghi-chu" placeholder="Ghi chú (VD: phí vệ sinh xe)"
                       class="col-span-2 text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-aiblue">
                <input type="number" name="chi_phi_khac_so_tien" id="o-chi-phi-khac-so-tien" min="0" placeholder="Số tiền"
                       class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-aiblue">
            </div>
        </div>

        <div class="bg-slate-50 rounded-lg p-3 flex items-center justify-between">
            <p class="text-sm font-semibold text-navy">Tổng cộng (tạm tính)</p>
            <p id="tong-tien-hien-thi" class="text-lg font-bold text-navy">0đ</p>
        </div>

        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Gửi báo giá cho khách hàng
        </button>
    </form>
</div>

<script>
    const donGiaDichVu = @json($danhMucDichVu->pluck('don_gia', 'id'));
    const donGiaPhuTung = @json($danhMucPhuTung->pluck('gia_ban', 'id'));

    function tinhTongTien() {
        let tong = 0;
        document.querySelectorAll('input[name="dich_vu_ids[]"]:checked').forEach(cb => tong += parseFloat(donGiaDichVu[cb.value] || 0));
        document.querySelectorAll('.phu-tung-input').forEach(input => {
            const id = input.name.match(/\[(\d+)\]/)[1];
            const sl = parseInt(input.value) || 0;
            tong += sl * parseFloat(donGiaPhuTung[id] || 0);
        });
        tong += parseFloat(document.getElementById('o-chi-phi-khac-so-tien').value) || 0;
        document.getElementById('tong-tien-hien-thi').innerText = tong.toLocaleString('vi-VN') + 'đ';
    }
    document.querySelectorAll('input[name="dich_vu_ids[]"], .phu-tung-input, #o-chi-phi-khac-so-tien').forEach(el => el.addEventListener('input', tinhTongTien));
    tinhTongTien(); // Tính ngay khi tải trang, vì có thể đã có checkbox được tích sẵn từ đề xuất KTV

    // Kiểm tra tồn kho realtime khi CVDV nhập số lượng
    document.querySelectorAll('.phu-tung-input').forEach(function (input) {
        input.addEventListener('input', function () {
            const tonKho = parseInt(input.dataset.tonKho);
            const soLuong = parseInt(input.value) || 0;
            const canhBao = document.getElementById('canh-bao-ton-kho');
            if (soLuong > tonKho) {
                canhBao.innerText = `⚠ "${input.dataset.ten}" không đủ tồn kho (còn ${tonKho}, đang chọn ${soLuong})`;
                canhBao.classList.remove('hidden');
                input.value = tonKho;
            } else {
                canhBao.classList.add('hidden');
            }
        });
    });

    // --- Gọi AI gợi ý ---
    document.getElementById('btn-goi-y-ai').addEventListener('click', async function () {
        this.innerText = 'Đang phân tích...';
        try {
            const res = await fetch('{{ route("cvdv.bao-gia.goi-y-ai", $phieu->id) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            const data = await res.json();
            const khung = document.getElementById('ket-qua-ai');

            if (data.loi) {
                khung.innerHTML = `<p class="text-sm text-red-500">${data.loi}</p>`;
                khung.classList.remove('hidden');
                this.innerHTML = '<span>✦</span> Gợi ý hạng mục & chi phí (AI)';
                return;
            }

            document.getElementById('ai-do-tin-cay').innerText = (data.doTinCay || 0) + '%';
            document.getElementById('ai-danh-sach-hang-muc').innerHTML = (data.hangMuc || []).map(function (hm) {
                return `<p class="text-sm text-navy">• ${hm.tenDichVu} <span class="text-xs text-slate-500">— ${hm.lyDo}</span></p>`;
            }).join('');
            khung.classList.remove('hidden');
            document.getElementById('co-dung-ai-hidden').value = '1';

            // Tự động tích chọn đúng dịch vụ AI gợi ý (khớp theo tên)
            (data.hangMuc || []).forEach(function (hm) {
                document.querySelectorAll('.dich-vu-checkbox').forEach(function (label) {
                    if (label.dataset.ten === hm.tenDichVu) {
                        label.querySelector('input[type=checkbox]').checked = true;
                    }
                });
            });
            tinhTongTien();
        } catch (err) {
            alert('Không thể lấy gợi ý AI lúc này.');
        }
        this.innerHTML = '<span>✦</span> Gợi ý hạng mục & chi phí (AI)';
    });
</script>
@endsection