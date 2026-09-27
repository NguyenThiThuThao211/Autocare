@extends('layouts.app')

@section('title', 'Đặt lịch hẹn - AutoCare')

@section('content')
<div class="max-w-xl mx-auto px-5 py-8 md:py-12">

    <h1 class="text-lg md:text-2xl font-semibold text-navy mb-1">Đặt lịch hẹn</h1>
    <p class="text-xs text-slate-400 mb-6">Chỉ mất 1 phút — không cần tài khoản</p>

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-3 py-2 mb-4">{{ session('thanhcong') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form id="form-dat-lich" method="POST" action="{{ route('dat-lich.xu-ly') }}" class="bg-white border border-slate-200 rounded-xl p-5 md:p-6 space-y-4">
        @csrf

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Họ tên <span class="text-red-500">*</span></label>
            <input type="text" name="ho_ten" value="{{ old('ho_ten', auth()->user()->khachHang->ho_ten ?? '') }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Số điện thoại <span class="text-red-500">*</span></label>
            <input type="text" name="sdt" value="{{ old('sdt', auth()->user()->khachHang->sdt ?? '') }}" placeholder="0901234567"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        {{-- Xe: chọn có sẵn nếu đã đăng nhập, hoặc nhập hãng/dòng nếu khách mới --}}
        @if (count($dsXeCoSan) > 0)
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Xe của bạn</label>
                <select name="xe_id" id="chon-xe" class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
                    <option value="">-- Xe khác / chưa có trong danh sách --</option>
                    @foreach ($dsXeCoSan as $xe)
                        <option value="{{ $xe->id }}">{{ $xe->hang_xe }} {{ $xe->dong_xe }} — {{ $xe->bien_so }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div id="khu-vuc-xe-moi">
        <p class="text-xs text-slate-400 mb-2">Nếu chưa có xe trong danh sách trên, vui lòng cho biết hãng xe để garage chuẩn bị trước</p>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Hãng xe <span class="text-red-500">*</span></label>
                <input type="text" name="hang_xe" placeholder="Honda"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Dòng xe <span class="text-slate-400">(tùy chọn)</span></label>
                <input type="text" name="dong_xe" placeholder="Civic"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>
        </div>
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Dịch vụ <span class="text-red-500">*</span></label>
            <div class="border border-slate-200 rounded-lg p-3 max-h-48 overflow-y-auto space-y-3">
                @foreach ($danhMucDichVu as $nhom => $dsDichVu)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 mb-1">{{ $nhom }}</p>
                        @foreach ($dsDichVu as $dv)
                            <label class="flex items-center gap-2 text-sm py-1 cursor-pointer">
                                <input type="checkbox" name="dich_vu_ids[]" value="{{ $dv->id }}" class="rounded">
                                {{ $dv->ten_dich_vu }} <span class="text-slate-400 text-xs">({{ number_format($dv->don_gia) }}đ)</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Ngày mong muốn <span class="text-red-500">*</span></label>
            <input type="date" name="ngay" id="chon-ngay" min="{{ date('Y-m-d') }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Khung giờ còn trống <span class="text-red-500">*</span></label>
            <div id="ds-khung-gio" class="grid grid-cols-3 gap-2">
                <p class="col-span-3 text-xs text-slate-400">Chọn ngày trước để xem khung giờ trống</p>
            </div>
        </div>

        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Mô tả sơ bộ vấn đề xe <span class="text-slate-400">(tùy chọn)</span></label>
            <textarea name="mo_ta_so_bo" rows="2" placeholder="VD: xe kêu lạ khi phanh"
                      class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue"></textarea>
            <p class="text-xs text-slate-400 mt-1">Giúp garage chuẩn bị trước khi bạn đến</p>
        </div>

        <button type="submit"
                class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Đặt lịch hẹn
        </button>
    </form>
</div>

<script>
    const oNgay = document.getElementById('chon-ngay');
    const dsKhungGio = document.getElementById('ds-khung-gio');
    let gioDaChon = null;

    oNgay.addEventListener('change', async function () {
        dsKhungGio.innerHTML = '<p class="col-span-3 text-xs text-slate-400">Đang tải...</p>';
        const res = await fetch(`{{ route('dat-lich.khung-gio') }}?ngay=${oNgay.value}`);
        const data = await res.json();

        if (data.khung_gio.length === 0) {
            dsKhungGio.innerHTML = '<p class="col-span-3 text-xs text-red-500">Ngày này đã kín lịch, vui lòng chọn ngày khác</p>';
            return;
        }

        dsKhungGio.innerHTML = '';
        data.khung_gio.forEach(function (gio) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerText = gio;
            btn.className = 'text-sm border border-slate-200 rounded-lg py-2 hover:border-aiblue';
            btn.addEventListener('click', function () {
                document.querySelectorAll('#ds-khung-gio button').forEach(b => {
                    b.classList.remove('bg-navy', 'text-white', 'border-navy');
                });
                btn.classList.add('bg-navy', 'text-white', 'border-navy');
                gioDaChon = gio;

                let hidden = document.getElementById('gio-hidden');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'gio';
                    hidden.id = 'gio-hidden';
                    document.getElementById('form-dat-lich').appendChild(hidden);
                }
                hidden.value = gio;
            });
            dsKhungGio.appendChild(btn);
        });
    });

    // Ẩn ô nhập hãng/dòng xe nếu đã chọn xe có sẵn
    const chonXe = document.getElementById('chon-xe');
    if (chonXe) {
        const khuVucXeMoi = document.getElementById('khu-vuc-xe-moi');
        chonXe.addEventListener('change', function () {
            khuVucXeMoi.style.display = chonXe.value ? 'none' : 'grid';
        });
    }
</script>
@endsection