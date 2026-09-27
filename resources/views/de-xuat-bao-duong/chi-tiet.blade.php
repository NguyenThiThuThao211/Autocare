@extends('layouts.app')

@section('title', 'Đề xuất bảo dưỡng - AutoCare')

@section('content')
<div class="max-w-md mx-auto px-5 py-10 md:py-16">

    @if (session('thanhcong'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('thanhcong') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
    @endif

    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-aipurplebg flex items-center justify-center text-2xl">✦</div>
        <h1 class="text-lg md:text-xl font-semibold text-navy">Đề xuất bảo dưỡng</h1>
        <p class="text-sm text-slate-400">{{ $xe->hang_xe }} {{ $xe->dong_xe }} — {{ $xe->bien_so }}</p>
    </div>

    @if (!($deXuat['coDeXuat'] ?? false))
        <div class="bg-white border border-slate-200 rounded-xl p-6 text-center">
            <p class="text-sm text-slate-400">Xe của bạn hiện chưa có đề xuất bảo dưỡng nào.</p>
            <a href="{{ route('trang-chu') }}" class="text-aiblue text-sm hover:underline mt-3 inline-block">← Về trang chủ</a>
        </div>
    @elseif ($daXuLy)
        <div class="bg-white border border-slate-200 rounded-xl p-6 text-center">
            <p class="text-sm text-slate-500">Bạn đã xử lý đề xuất này rồi.</p>
            <a href="{{ route('trang-chu') }}" class="text-aiblue text-sm hover:underline mt-3 inline-block">← Về trang chủ</a>
        </div>
    @else
        <div class="bg-aipurplebg border border-indigo-200 rounded-xl p-4 mb-5">
            <p class="text-sm font-semibold text-aipurple mb-1">{{ $deXuat['dichVuSapDenHan'] ?? '' }}</p>
            <p class="text-xs text-slate-600">{{ $deXuat['giaiThich'] ?? '' }}</p>
        </div>

        <form method="POST" action="{{ route('de-xuat-bao-duong.chap-nhan', $xe->id) }}?{{ request()->getQueryString() }}" id="form-chap-nhan" class="space-y-4 mb-4">
            @csrf
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Chọn ngày</label>
                <input type="date" name="ngay" id="chon-ngay-de-xuat" min="{{ date('Y-m-d') }}"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Khung giờ còn trống</label>
                <div id="ds-khung-gio-de-xuat" class="grid grid-cols-3 gap-2">
                    <p class="col-span-3 text-xs text-slate-400">Chọn ngày trước để xem khung giờ trống</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
                Chấp nhận & đặt lịch
            </button>
        </form>

        <form method="POST" action="{{ route('de-xuat-bao-duong.tu-choi', $xe->id) }}?{{ request()->getQueryString() }}"
              onsubmit="return confirm('Từ chối đề xuất này?');">
            @csrf
            <button type="submit" class="w-full text-sm text-slate-400 hover:text-slate-600 py-2">
                Từ chối đề xuất
            </button>
        </form>
    @endif
</div>

<script>
    const oNgayDeXuat = document.getElementById('chon-ngay-de-xuat');
    const dsKhungGioDeXuat = document.getElementById('ds-khung-gio-de-xuat');

    if (oNgayDeXuat) {
        oNgayDeXuat.addEventListener('change', async function () {
            dsKhungGioDeXuat.innerHTML = '<p class="col-span-3 text-xs text-slate-400">Đang tải...</p>';
            const res = await fetch(`{{ route('dat-lich.khung-gio') }}?ngay=${oNgayDeXuat.value}`);
            const data = await res.json();

            if (data.khung_gio.length === 0) {
                dsKhungGioDeXuat.innerHTML = '<p class="col-span-3 text-xs text-red-500">Ngày này đã kín lịch, chọn ngày khác</p>';
                return;
            }

            dsKhungGioDeXuat.innerHTML = '';
            data.khung_gio.forEach(function (gio) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerText = gio;
                btn.className = 'text-sm border border-slate-200 rounded-lg py-2 hover:border-aiblue';
                btn.addEventListener('click', function () {
                    document.querySelectorAll('#ds-khung-gio-de-xuat button').forEach(b => b.classList.remove('bg-navy', 'text-white', 'border-navy'));
                    btn.classList.add('bg-navy', 'text-white', 'border-navy');

                    let hidden = document.getElementById('gio-de-xuat-hidden');
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'gio';
                        hidden.id = 'gio-de-xuat-hidden';
                        document.getElementById('form-chap-nhan').appendChild(hidden);
                    }
                    hidden.value = gio;
                });
                dsKhungGioDeXuat.appendChild(btn);
            });
        });
    }
</script>
@endsection