@extends('layouts.app')

@section('title', 'Đăng ký - AutoCare')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-5 py-10">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">

        <h1 class="text-lg md:text-xl font-semibold text-navy mb-1">Tạo tài khoản</h1>
        <p class="text-xs text-slate-400 mb-6">Đặt lịch nhanh hơn, theo dõi lịch sử sửa chữa dễ dàng</p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-lg px-3 py-2 mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('dang-ky.xu-ly') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Họ tên</label>
                <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" placeholder="Nguyễn Văn A"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600 mb-1 block">Số điện thoại</label>
                <input type="text" name="sdt" value="{{ old('sdt') }}" placeholder="0901234567"
                       class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-aiblue">
            </div>

            @php $oMatKhau = [['id' => 'mk1', 'name' => 'password', 'label' => 'Mật khẩu', 'placeholder' => 'Tối thiểu 6 ký tự'],
                              ['id' => 'mk2', 'name' => 'password_confirmation', 'label' => 'Xác nhận mật khẩu', 'placeholder' => 'Nhập lại mật khẩu']]; @endphp

            @foreach ($oMatKhau as $o)
                <div>
                    <label class="text-xs font-medium text-slate-600 mb-1 block">{{ $o['label'] }}</label>
                    <div class="relative">
                        <input type="password" name="{{ $o['name'] }}" id="o-{{ $o['id'] }}" placeholder="{{ $o['placeholder'] }}"
                               class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2.5 pr-10 focus:outline-none focus:border-aiblue">
                        <button type="button" class="nut-xem-mk absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                data-target="o-{{ $o['id'] }}">
                            <svg class="icon-mo w-5 h-5 hidden" viewBox="0 0 24 24" fill="none">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                            <svg class="icon-dong w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.3 5.5A10.8 10.8 0 0112 5c7 0 11 7 11 7a17.5 17.5 0 01-3.2 4M6.5 6.7C3.7 8.5 1 12 1 12s4 7 11 7c1.4 0 2.7-.3 3.9-.8"
                                      stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach

            <button type="submit"
                    class="w-full bg-navy text-white font-semibold text-sm py-2.5 rounded-lg hover:bg-navylight transition">
                Đăng ký
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 my-4">
            Đã có tài khoản? <a href="{{ route('dang-nhap') }}" class="text-aiblue hover:underline">Đăng nhập</a>
        </p>

        <div class="flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-slate-200"></div>
            <span class="text-xs text-slate-400">hoặc</span>
            <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <a href="{{ route('google.chuyen-huong') }}"
           class="w-full flex items-center justify-center gap-2 border border-slate-200 rounded-lg py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
            <svg viewBox="0 0 24 24" class="w-4 h-4">
                <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.4c-.3 1.5-1.1 2.7-2.4 3.6v3h3.9c2.3-2.1 3.6-5.2 3.6-8.8z"/>
                <path fill="#34A853" d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.9-3c-1.1.7-2.4 1.2-4 1.2-3.1 0-5.7-2.1-6.6-4.9H1.4v3.1C3.4 21.3 7.4 24 12 24z"/>
                <path fill="#FBBC05" d="M5.4 14.4c-.2-.7-.4-1.4-.4-2.4s.1-1.7.4-2.4V6.5H1.4C.5 8.2 0 10 0 12s.5 3.8 1.4 5.5l4-3.1z"/>
                <path fill="#EA4335" d="M12 4.8c1.7 0 3.3.6 4.5 1.7l3.4-3.4C17.9 1.2 15.2 0 12 0 7.4 0 3.4 2.7 1.4 6.5l4 3.1c.9-2.8 3.5-4.8 6.6-4.8z"/>
            </svg>
            Bắt đầu với Google
        </a>
    </div>
</div>

<script>
    document.querySelectorAll('.nut-xem-mk').forEach(function (nut) {
        nut.addEventListener('click', function () {
            const oId = nut.dataset.target;
            const o = document.getElementById(oId);
            const iconMo = nut.querySelector('.icon-mo');
            const iconDong = nut.querySelector('.icon-dong');
            const dangAn = o.type === 'password'; // true = TRƯỚC khi bấm đang ẩn
            o.type = dangAn ? 'text' : 'password';
            // Sau khi đổi: trước ẩn (dangAn=true) => giờ hiện => icon mắt MỞ
            iconMo.classList.toggle('hidden', !dangAn);
            iconDong.classList.toggle('hidden', dangAn);
        });
    });
</script>
@endsection