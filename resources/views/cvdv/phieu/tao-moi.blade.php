@extends('layouts.admin')

@section('title', 'Quản lý lịch hẹn - CVDV')

@section('sidebar-menu')
    <a href="{{ route('cvdv.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Dashboard</a>
    <a href="{{ route('cvdv.lich-hen') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Quản lý lịch hẹn</a>
    <a href="{{ route('cvdv.phieu.danh-sach') }}" class="block px-3 py-2 rounded-lg text-slate-300 text-sm hover:bg-navylight">Quản lý phiếu sửa chữa</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <h1 class="text-lg font-bold text-navy mb-1">Danh sách lịch hẹn</h1>
        <p class="text-xs text-slate-400 mb-4">Chọn đúng lịch hẹn khi khách đến garage</p>

        <form method="GET" class="flex flex-col gap-2 mb-4">
            <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm theo tên, SĐT, biển số..."
                   class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-aiblue/20 focus:border-aiblue">
            <div class="flex gap-2">
                <select name="trang_thai" onchange="this.form.submit()"
                        class="flex-1 text-sm border border-slate-200 rounded-xl px-2 py-2 focus:outline-none focus:border-aiblue">
                    <option value="chua_tiep_nhan" {{ $locTrangThai === 'chua_tiep_nhan' ? 'selected' : '' }}>Chưa tiếp nhận</option>
                    <option value="da_tiep_nhan" {{ $locTrangThai === 'da_tiep_nhan' ? 'selected' : '' }}>Đã tiếp nhận</option>
                    <option value="da_huy" {{ $locTrangThai === 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="tat_ca" {{ $locTrangThai === 'tat_ca' ? 'selected' : '' }}>Tất cả</option>
                </select>
                <select name="sap_xep" onchange="this.form.submit()"
                        class="flex-1 text-sm border border-slate-200 rounded-xl px-2 py-2 focus:outline-none focus:border-aiblue">
                    <option value="moi_nhat" {{ $sapXep === 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="cu_nhat" {{ $sapXep === 'cu_nhat' ? 'selected' : '' }}>Cũ nhất</option>
                </select>
            </div>
        </form>

        <div class="space-y-2 max-h-[480px] overflow-y-auto pr-1">
            @forelse ($dsLichHenChoXuLy as $lh)
                {{-- ĐÃ SỬA: đổi <button> thành <div> vì bên trong có chứa input/textarea/button khác
                     (HTML không cho phép lồng phần tử tương tác bên trong <button>) --}}
                <div class="nut-chon-lich w-full text-left bg-white border border-slate-200 rounded-xl p-3 transition cursor-pointer {{ in_array($lh->trang_thai, ['Da hoan tat', 'Da huy']) ? 'opacity-50 pointer-events-none' : 'hover:border-aiblue' }}"
                     data-json="{{ json_encode([
                         'lichHenId' => $lh->lich_hen_id,
                         'xeId' => $lh->xe_id,
                         'khachhangId' => $lh->khachhang_id,
                         'bienSo' => $lh->bien_so ?? '',
                         'hangXe' => $lh->hang_xe ?? $lh->hang_xe_du_kien ?? '',
                         'dongXe' => $lh->dong_xe ?? $lh->dong_xe_du_kien ?? '',
                         'tenKH' => "$lh->ho_ten — $lh->sdt",
                         'moTa' => $lh->mo_ta_so_bo ?? '',
                         'tenDichVu' => $lh->ten_dich_vu ?? '',
                         'dichVuIds' => $lh->dich_vu_ids_da_chon ?? [],
                     ]) }}">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-navy">{{ \Carbon\Carbon::parse($lh->ngay_gio)->format('H:i - d/m') }}</p>
                        <div class="flex items-center gap-1">
                            @if ($lh->trang_thai === 'Da hoan tat')
                                <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">Đã tiếp nhận</span>
                            @elseif ($lh->trang_thai === 'Da huy')
                                <span class="text-[10px] bg-red-50 text-red-600 px-2 py-0.5 rounded-full">Đã hủy</span>
                            @elseif (!$lh->xe_id)
                                <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full">Khách mới</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">{{ $lh->ho_ten }} — {{ $lh->sdt }}</p>
                    <p class="text-xs text-slate-400">
                        {{ $lh->xe_id ? "$lh->hang_xe $lh->dong_xe — $lh->bien_so" : "$lh->hang_xe_du_kien $lh->dong_xe_du_kien" }}
                    </p>
                    @if ($lh->ten_dich_vu)
                        <p class="text-xs text-slate-400 mt-1">Dịch vụ: {{ $lh->ten_dich_vu }}</p>
                    @endif

                    @if ($lh->trang_thai === 'Da dat')
                        <div class="mt-2 flex items-center gap-3">
                            <span onclick="event.stopPropagation(); moKhungXuLy('doi-gio-{{ $lh->lich_hen_id }}')"
                                  class="text-[11px] text-aiblue hover:underline cursor-pointer">Đổi giờ</span>
                            <span onclick="event.stopPropagation(); moKhungXuLy('huy-{{ $lh->lich_hen_id }}')"
                                  class="text-[11px] text-red-500 hover:underline cursor-pointer">Hủy lịch</span>
                        </div>

                        <div id="khung-doi-gio-{{ $lh->lich_hen_id }}" class="hidden mt-2 p-3 bg-slate-50 rounded-lg" onclick="event.stopPropagation()">
                            <form method="POST" action="{{ route('cvdv.lich-hen.doi-gio', $lh->lich_hen_id) }}" class="space-y-2 form-doi-gio">
                                @csrf
                                <input type="date" name="ngay_moi" min="{{ date('Y-m-d') }}" required
                                       class="o-ngay-moi w-full text-xs border border-slate-200 rounded-lg px-2 py-1.5">
                                <div class="ds-khung-gio-moi grid grid-cols-3 gap-1.5">
                                    <p class="col-span-3 text-[11px] text-slate-400">Chọn ngày để xem khung giờ trống</p>
                                </div>
                                <button type="submit" class="w-full text-xs bg-aiblue text-white rounded-lg py-1.5 hover:bg-blue-600 disabled:opacity-40" disabled>
                                    Xác nhận đổi giờ
                                </button>
                            </form>
                        </div>

                        <div id="khung-huy-{{ $lh->lich_hen_id }}" class="hidden mt-2 p-3 bg-slate-50 rounded-lg" onclick="event.stopPropagation()">
                            <form method="POST" action="{{ route('cvdv.lich-hen.huy-co-ly-do', $lh->lich_hen_id) }}" class="space-y-2"
                                  onsubmit="return confirm('Xác nhận hủy lịch hẹn này?');">
                                @csrf
                                <textarea name="ly_do_huy" rows="2" placeholder="Bắt buộc: nhập lý do hủy" required
                                          class="w-full text-xs border border-slate-200 rounded-lg px-2 py-1.5"></textarea>
                                <button type="submit" class="w-full text-xs bg-red-500 text-white rounded-lg py-1.5 hover:bg-red-600">
                                    Xác nhận hủy
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-xl p-6 text-center">
                    <p class="text-sm text-slate-400">Không tìm thấy lịch hẹn nào.</p>
                </div>
            @endforelse
        </div>

        <button type="button" id="btn-mo-khach-vang-lai"
                class="w-full mt-4 border-2 border-dashed border-slate-300 rounded-xl py-3 text-sm text-slate-500 hover:border-aiblue hover:text-aiblue transition flex items-center justify-center gap-2">
            <span class="text-lg leading-none">+</span> Khách vãng lai (không có lịch hẹn trước)
        </button>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <h2 class="font-bold text-navy text-base mb-4">Tạo phiếu sửa chữa</h2>

        <form id="form-tao-phieu" method="POST" action="{{ route('cvdv.phieu.luu') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="lich_hen_id" id="lich-hen-id-hidden">
            <input type="hidden" name="xe_id" id="xe-id-hidden">
            <input type="hidden" name="khachhang_id" id="khachhang-id-hidden">

            <p id="thong-tin-dich-vu-da-chon" class="hidden text-xs text-slate-500 bg-blue-50 border border-blue-200 rounded-lg px-3 py-2"></p>
            <div id="khu-vuc-tim-kiem" class="hidden relative">
                <label class="text-xs font-medium text-slate-600 mb-1 block">Tìm khách/xe đã có (tùy chọn)</label>
                <input type="text" id="o-tim-kiem" placeholder="SĐT hoặc biển số..."
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                <div id="ket-qua-tim-kiem" class="hidden absolute z-10 w-full bg-white border border-slate-200 rounded-lg mt-1 shadow-lg max-h-48 overflow-y-auto"></div>
            </div>

            <div id="khu-vuc-dich-vu" class="hidden">
                <label class="text-xs font-medium text-slate-600 mb-1 block">Dịch vụ (có thể chỉnh sửa lại)</label>
                <div class="border border-slate-200 rounded-lg p-3 max-h-40 overflow-y-auto space-y-2">
                    @foreach ($danhMucDichVu as $nhom => $dsDichVu)
                        <div>
                            <p class="text-xs font-semibold text-slate-400 mb-1">{{ $nhom }}</p>
                            @foreach ($dsDichVu as $dv)
                                <label class="flex items-center gap-2 text-sm py-0.5 cursor-pointer">
                                    <input type="checkbox" name="dich_vu_ids[]" value="{{ $dv->id }}" class="rounded o-dich-vu-checkbox">
                                    {{ $dv->ten_dich_vu }}
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div id="khu-vuc-khach-moi" class="hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-600 mb-1 block">Tên khách <span class="text-red-500">*</span></label>
                    <input type="text" name="khach_moi_ten" id="o-khach-ten" placeholder="Nguyễn Văn A"
                           class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600 mb-1 block">SĐT <span class="text-red-500">*</span></label>
                    <input type="text" name="khach_moi_sdt" id="o-khach-sdt" placeholder="0901234567"
                           class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                </div>
            </div>

            
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-1">
                    <label class="text-xs font-medium text-slate-600 mb-1 block">Biển số xe <span class="text-red-500">*</span></label>
                    <input type="text" name="bien_so" id="o-bien-so" placeholder="51A-123.45"
                           class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600 mb-1 block">Hãng xe <span class="text-red-500">*</span></label>
                    <input type="text" name="hang_xe" id="o-hang-xe" placeholder="Honda"
                           class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600 mb-1 block">Dòng xe</label>
                    <input type="text" name="dong_xe" id="o-dong-xe" placeholder="Civic"
                           class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                </div>
            </div>
            <p class="text-[11px] text-slate-400">* Có thể chỉnh sửa lại biển số/hãng/dòng xe nếu cần, kể cả khi đã chọn từ lịch hẹn.</p>

            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Mô tả sự cố</label>
                <textarea name="mo_ta_su_co" id="o-mo-ta" rows="3"
                          class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue"></textarea>
            </div>

            <div class="flex gap-2">
                <button type="button" id="btn-phan-loai" class="flex-1 text-xs text-aipurple border border-indigo-200 rounded-lg px-3 py-2 hover:bg-aipurplebg flex items-center justify-center gap-1.5">
                    <span>✦</span> Phân loại sự cố
                </button>
                <button type="button" id="btn-goi-y-hang-muc" class="flex-1 text-xs text-aipurple border border-indigo-200 rounded-lg px-3 py-2 hover:bg-aipurplebg flex items-center justify-center gap-1.5">
                    <span>✦</span> Gợi ý hạng mục
                </button>
            </div>

            <div id="ket-qua-ai" class="hidden bg-aipurplebg border border-indigo-200 rounded-lg p-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-aipurple text-sm">✦</span>
                    <span class="text-xs font-semibold text-aipurple">Phân loại sự cố</span>
                    <span id="ai-do-tin-cay" class="text-[10px] bg-white px-2 py-0.5 rounded-full ml-auto"></span>
                </div>
                <p class="text-sm text-navy" id="ai-nhom-su-co"></p>
            </div>
            <input type="hidden" name="nhom_su_co_ai" id="nhom-su-co-hidden">

            <div id="ket-qua-hang-muc" class="hidden bg-aipurplebg border border-indigo-200 rounded-lg p-3">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-aipurple text-sm">✦</span>
                    <span class="text-xs font-semibold text-aipurple">Hạng mục có thể liên quan (tham khảo sớm)</span>
                </div>
                <div id="ai-danh-sach-hang-muc" class="space-y-1"></div>
            </div>

            <div class="border-t border-slate-200 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-medium text-slate-600">Phân công kỹ thuật viên <span class="text-red-500">*</span></label>
                    <button type="button" id="btn-goi-y-ktv" class="text-xs text-aipurple hover:underline flex items-center gap-1">
                        <span>✦</span> Gợi ý AI
                    </button>
                </div>
                <div id="goi-y-ktv-ket-qua" class="hidden bg-aipurplebg border border-indigo-200 rounded-lg p-2.5 mb-2 text-xs text-navy"></div>
                <select name="ktv_id" id="chon-ktv" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                    <option value="">-- Chọn kỹ thuật viên --</option>
                    @foreach ($dsKTV as $ktv)
                        <option value="{{ $ktv->id }}">{{ $ktv->email_sdt }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
                Lưu phiếu &amp; phân công
            </button>
        </form>
    </div>
</div>

<script>
    function moKhungXuLy(id) {
        const khung = document.getElementById('khung-' + id);
        const dangAn = khung.classList.contains('hidden');
        document.querySelectorAll('[id^="khung-doi-gio-"], [id^="khung-huy-"]').forEach(k => k.classList.add('hidden'));
        khung.classList.toggle('hidden', !dangAn);
    }

    // Chọn khung giờ động cho từng form "Đổi giờ"
    document.querySelectorAll('.form-doi-gio').forEach(function (form) {
        const oNgay = form.querySelector('.o-ngay-moi');
        const dsKhungGio = form.querySelector('.ds-khung-gio-moi');
        const btnSubmit = form.querySelector('button[type="submit"]');

        oNgay.addEventListener('change', async function () {
            dsKhungGio.innerHTML = '<p class="col-span-3 text-[11px] text-slate-400">Đang tải...</p>';
            btnSubmit.disabled = true;

            const res = await fetch(`{{ route('dat-lich.khung-gio') }}?ngay=${oNgay.value}`);
            const data = await res.json();

            if (data.khung_gio.length === 0) {
                dsKhungGio.innerHTML = '<p class="col-span-3 text-[11px] text-red-500">Ngày này đã kín lịch</p>';
                return;
            }

            dsKhungGio.innerHTML = '';
            data.khung_gio.forEach(function (gio) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerText = gio;
                btn.className = 'text-[11px] border border-slate-200 rounded-lg py-1.5 hover:border-aiblue transition';
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    dsKhungGio.querySelectorAll('button').forEach(b => b.classList.remove('bg-aiblue', 'text-white', 'border-aiblue'));
                    btn.classList.add('bg-aiblue', 'text-white', 'border-aiblue');

                    form.querySelectorAll('input[name="gio_moi"]').forEach(el => el.remove());
                    const oGioHidden = document.createElement('input');
                    oGioHidden.type = 'hidden'; oGioHidden.name = 'gio_moi'; oGioHidden.value = gio;
                    form.appendChild(oGioHidden);

                    btnSubmit.disabled = false;
                });
                dsKhungGio.appendChild(btn);
            });
        });
    });

    function datGiaTri(id, giaTri) { document.getElementById(id).value = giaTri || ''; }

    function chonTuLichHen(d) {
        datGiaTri('lich-hen-id-hidden', d.lichHenId);
        datGiaTri('xe-id-hidden', d.xeId);
        datGiaTri('khachhang-id-hidden', d.khachhangId);
        datGiaTri('o-bien-so', d.bienSo);
        datGiaTri('o-hang-xe', d.hangXe);
        datGiaTri('o-dong-xe', d.dongXe);
        datGiaTri('o-mo-ta', d.moTa);

        document.getElementById('khu-vuc-khach-moi').classList.add('hidden');
        document.getElementById('khu-vuc-tim-kiem').classList.add('hidden');

        const tb = document.getElementById('thong-tin-dich-vu-da-chon');
        tb.innerHTML = `<strong>${d.tenKH}</strong>`;
        tb.classList.remove('hidden');

        // Luôn hiện khung chọn dịch vụ — chỉ tích sẵn nếu có lịch hẹn kèm dịch vụ đã chọn trước đó
        document.getElementById('khu-vuc-dich-vu').classList.remove('hidden');
        const dsIdDaChon = d.dichVuIds || [];
        document.querySelectorAll('.o-dich-vu-checkbox').forEach(function (cb) {
            cb.checked = dsIdDaChon.includes(parseInt(cb.value));
        });

        document.querySelectorAll('.nut-chon-lich').forEach(b => b.classList.remove('border-aiblue', 'bg-blue-50'));
    }

    document.querySelectorAll('.nut-chon-lich').forEach(function (btn) {
        btn.addEventListener('click', function () {
            chonTuLichHen(JSON.parse(btn.dataset.json));
            btn.classList.add('border-aiblue', 'bg-blue-50');
        });
    });

    document.getElementById('btn-mo-khach-vang-lai').addEventListener('click', function () {
        ['lich-hen-id-hidden', 'xe-id-hidden', 'khachhang-id-hidden', 'o-bien-so', 'o-hang-xe', 'o-dong-xe']
            .forEach(id => datGiaTri(id, ''));

        document.getElementById('thong-tin-dich-vu-da-chon').classList.add('hidden');
        document.getElementById('khu-vuc-khach-moi').classList.remove('hidden');
        document.getElementById('khu-vuc-tim-kiem').classList.remove('hidden');

        // Khách vãng lai vẫn cần chọn dịch vụ ban đầu — bỏ trống hết checkbox (không có dịch vụ đề xuất sẵn)
        document.getElementById('khu-vuc-dich-vu').classList.remove('hidden');
        document.querySelectorAll('.o-dich-vu-checkbox').forEach(cb => cb.checked = false);

        document.querySelectorAll('.nut-chon-lich').forEach(b => b.classList.remove('border-aiblue', 'bg-blue-50'));
    });

    const oTimKiem = document.getElementById('o-tim-kiem');
    const ketQuaTimKiem = document.getElementById('ket-qua-tim-kiem');
    let timeoutTimKiem;

    oTimKiem.addEventListener('input', function () {
        clearTimeout(timeoutTimKiem);
        const tuKhoa = oTimKiem.value.trim();
        if (tuKhoa.length < 2) { ketQuaTimKiem.classList.add('hidden'); return; }

        timeoutTimKiem = setTimeout(async function () {
            const res = await fetch(`{{ route('cvdv.phieu.tim-kiem') }}?q=${encodeURIComponent(tuKhoa)}`);
            const ds = await res.json();

            ketQuaTimKiem.innerHTML = ds.length === 0
                ? '<p class="text-xs text-slate-400 p-3">Không tìm thấy — điền thông tin bên dưới để tạo mới</p>'
                : ds.map(item => `<div class="px-3 py-2 hover:bg-slate-50 cursor-pointer text-sm border-b border-slate-50 last:border-0"
                        onclick='chonTuLichHen(${JSON.stringify({
                            lichHenId: null, xeId: item.xe_id, khachhangId: item.khachhang_id,
                            bienSo: item.bien_so, hangXe: item.hang_xe, dongXe: item.dong_xe,
                            tenKH: item.ho_ten + " — " + item.sdt, moTa: "", tenDichVu: ""
                        })})'>
                        ${item.hang_xe} ${item.dong_xe} — ${item.bien_so}
                        <span class="text-slate-400 text-xs block">${item.ho_ten} — ${item.sdt}</span>
                    </div>`).join('');
            ketQuaTimKiem.classList.remove('hidden');
        }, 300);
    });

    document.getElementById('btn-phan-loai').addEventListener('click', async function () {
        const moTa = document.getElementById('o-mo-ta').value.trim();
        if (!moTa) { alert('Vui lòng có mô tả sự cố trước'); return; }
        this.innerText = 'Đang phân loại...';
        try {
            const res = await fetch('{{ route("cvdv.phieu.phan-loai-ai") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ mo_ta_su_co: moTa }),
            });
            const data = await res.json();
            document.getElementById('ai-nhom-su-co').innerText = data.nhomSuCo || 'Không xác định';
            document.getElementById('ai-do-tin-cay').innerText = (data.doTinCay || 0) + '%';
            document.getElementById('nhom-su-co-hidden').value = data.nhomSuCo || '';
            document.getElementById('ket-qua-ai').classList.remove('hidden');
        } catch (err) { alert('Không thể phân loại lúc này.'); }
        this.innerHTML = '<span>✦</span> Phân loại sự cố';
    });

    document.getElementById('btn-goi-y-hang-muc').addEventListener('click', async function () {
        const moTa = document.getElementById('o-mo-ta').value.trim();
        if (!moTa) { alert('Vui lòng có mô tả sự cố trước'); return; }
        this.innerText = 'Đang gợi ý...';
        try {
            const res = await fetch('{{ route("cvdv.phieu.goi-y-hang-muc") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ mo_ta_su_co: moTa, nhom_su_co_ai: document.getElementById('nhom-su-co-hidden').value }),
            });
            const data = await res.json();
            const khung = document.getElementById('ket-qua-hang-muc');
            if (data.loi) {
                khung.innerHTML = `<p class="text-sm text-red-500">${data.loi}</p>`;
            } else {
                document.getElementById('ai-danh-sach-hang-muc').innerHTML = (data.hangMuc || []).map(hm =>
                    `<p class="text-sm text-navy">• ${hm.tenDichVu} <span class="text-xs text-slate-500">— ${hm.lyDo}</span></p>`
                ).join('') || '<p class="text-sm text-slate-400">Không có gợi ý.</p>';
            }
            khung.classList.remove('hidden');
        } catch (err) { alert('Không thể lấy gợi ý lúc này.'); }
        this.innerHTML = '<span>✦</span> Gợi ý hạng mục';
    });

    document.getElementById('btn-goi-y-ktv').addEventListener('click', async function () {
        this.innerText = 'Đang gợi ý...';
        try {
            const res = await fetch('{{ route("cvdv.phieu.goi-y-ktv") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({
                    mo_ta_su_co: document.getElementById('o-mo-ta').value.trim(),
                    nhom_su_co_ai: document.getElementById('nhom-su-co-hidden').value,
                }),
            });
            const data = await res.json();
            const khung = document.getElementById('goi-y-ktv-ket-qua');
            if (data.ktvDeXuat) {
                khung.innerHTML = `<span class="font-medium">${data.aiApDung ? '✦ AI gợi ý' : 'Gợi ý'}:</span> ${data.ktvDeXuat.lyDo}`;
                khung.classList.remove('hidden');
                document.getElementById('chon-ktv').value = data.ktvDeXuat.ktvId;
            } else {
                khung.innerHTML = 'Không có kỹ thuật viên nào khả dụng.';
                khung.classList.remove('hidden');
            }
        } catch (err) { alert('Không thể lấy gợi ý lúc này.'); }
        this.innerHTML = '<span>✦</span> Gợi ý AI';
    });
</script>
@endsection