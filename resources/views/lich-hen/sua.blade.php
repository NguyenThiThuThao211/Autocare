@extends('layouts.app')

@section('title', 'Sửa lịch hẹn - AutoCare')

@section('content')
<div class="max-w-md mx-auto px-5 py-8">
    <h1 class="text-xl font-semibold text-navy mb-6">Sửa lịch hẹn</h1>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('lich-hen.sua', $lichHen->id) }}" id="form-sua-lich" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Chọn ngày mới</label>
            <input type="date" id="chon-ngay" min="{{ date('Y-m-d') }}"
                   value="{{ \Carbon\Carbon::parse($lichHen->ngay_gio)->format('Y-m-d') }}"
                   class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600 mb-1 block">Khung giờ còn trống</label>
            <div id="ds-khung-gio" class="grid grid-cols-3 gap-2">
                <p class="col-span-3 text-xs text-slate-400">Chọn ngày để xem khung giờ</p>
            </div>
        </div>
        <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
            Lưu thay đổi
        </button>
    </form>
</div>

<script>
    const oNgay = document.getElementById('chon-ngay');
    const dsKhungGio = document.getElementById('ds-khung-gio');

    async function taiKhungGio() {
        dsKhungGio.innerHTML = '<p class="col-span-3 text-xs text-slate-400">Đang tải...</p>';
        const res = await fetch(`{{ route('dat-lich.khung-gio') }}?ngay=${oNgay.value}`);
        const data = await res.json();

        dsKhungGio.innerHTML = data.khung_gio.length === 0
            ? '<p class="col-span-3 text-xs text-red-500">Ngày này đã kín lịch</p>' : '';

        data.khung_gio.forEach(function (gio) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerText = gio;
            btn.className = 'text-sm border border-slate-200 rounded-lg py-2 hover:border-aiblue';
            btn.addEventListener('click', function () {
                document.querySelectorAll('#ds-khung-gio button').forEach(b => b.classList.remove('bg-navy', 'text-white', 'border-navy'));
                btn.classList.add('bg-navy', 'text-white', 'border-navy');

                document.getElementById('form-sua-lich').querySelectorAll('input[name="ngay"], input[name="gio"]').forEach(el => el.remove());
                const f = document.getElementById('form-sua-lich');
                f.insertAdjacentHTML('beforeend', `<input type="hidden" name="ngay" value="${oNgay.value}"><input type="hidden" name="gio" value="${gio}">`);
            });
            dsKhungGio.appendChild(btn);
        });
    }
    oNgay.addEventListener('change', taiKhungGio);
    taiKhungGio();
</script>
@endsection