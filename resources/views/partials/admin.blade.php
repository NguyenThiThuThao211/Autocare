<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AutoCare')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                navy: '#0B2545', navylight: '#123B6B',
                aiblue: '#3B82F6', aipurple: '#4438C9', aipurplebg: '#EEF2FF',
            }}}
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 flex min-h-screen">

    {{-- SIDEBAR — chỉ còn menu điều hướng --}}
    <aside class="w-56 bg-navy flex-shrink-0 px-4 py-5">
        <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-aiblue flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 md:w-6 md:h-6">
                    <path d="M5 17h14M5 17a2 2 0 100-4 2 2 0 000 4zm14 0a2 2 0 100-4 2 2 0 000 4zM5 13l1.5-5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.37L19 13M5 13h14"
                          stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="text-white font-semibold text-sm">AutoCare</span>
        </div>

        <nav class="space-y-1">
            @if (auth()->user()->vai_tro === 'Co van dich vu')
                @include('partials.cvdv-sidebar')
            @elseif (auth()->user()->vai_tro === 'Ky thuat vien')
                <a href="{{ route('ktv.phieu') }}" class="block px-3 py-2 rounded-lg bg-navylight text-white text-sm">Phiếu được phân công</a>
            @elseif (auth()->user()->vai_tro === 'Quan ly')
                @include('partials.quanly-sidebar')
            @else
                @yield('sidebar-menu')
            @endif
        </nav>
    </aside>

    {{-- KHU VỰC CHÍNH --}}
    <div class="flex-1 flex flex-col">

        {{-- THANH NGANG TRÊN CÙNG — avatar, tên, chuông thông báo, đăng xuất --}}
        <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-end gap-4">

            {{-- Chuông thông báo --}}
            <div class="relative">
                <button id="btn-thong-bao-nv" type="button" class="relative text-slate-500 hover:text-navy">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5">
                        <path d="M12 3a6 6 0 00-6 6v3.5c0 .6-.2 1.2-.6 1.6L4 16h16l-1.4-1.9c-.4-.4-.6-1-.6-1.6V9a6 6 0 00-6-6z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M9.5 19a2.5 2.5 0 005 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                    @if ($soThongBaoChuaXemNV > 0)
                        <span id="badge-thong-bao-nv" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-semibold rounded-full min-w-[16px] h-4 flex items-center justify-center px-1">
                            {{ $soThongBaoChuaXemNV > 9 ? '9+' : $soThongBaoChuaXemNV }}
                        </span>
                    @endif
                </button>

                <div id="dropdown-thong-bao-nv" class="hidden absolute right-0 top-full mt-3 w-80 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-sm font-semibold text-navy">Thông báo</p>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse ($dsThongBaoGanDayNV as $tb)
                            <div class="px-4 py-3 border-b border-slate-50 last:border-0 {{ !$tb->da_xem ? 'bg-blue-50/40' : '' }}">
                                <p class="text-xs text-slate-700">{{ $tb->noi_dung }}</p>
                                <p class="text-[11px] text-slate-400 mt-1">{{ \Carbon\Carbon::parse($tb->ngay_gui)->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="px-4 py-6 text-xs text-slate-400 text-center">Chưa có thông báo nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Avatar + tên --}}
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-white text-xs font-semibold">
                    {{ mb_substr(auth()->user()->email_sdt ?? 'NV', 0, 1) }}
                </div>
                <div class="text-right leading-tight">
                    <p class="text-xs font-medium text-navy">{{ auth()->user()->email_sdt ?? '' }}</p>
                    <p class="text-[11px] text-slate-400">
                        @php
                            $tenVaiTro = match(auth()->user()->vai_tro ?? '') {
                                'Co van dich vu' => 'Cố vấn dịch vụ',
                                'Ky thuat vien' => 'Kỹ thuật viên',
                                'Quan ly' => 'Quản lý',
                                default => '',
                            };
                        @endphp
                        {{ $tenVaiTro }}
                    </p>
                </div>
            </div>

            {{-- Đăng xuất --}}
            <form method="POST" action="{{ route('dang-xuat') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-500" title="Đăng xuất">
                    <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>
        </header>

        {{-- NỘI DUNG TRANG --}}
        <main class="flex-1 p-6 md:p-8 overflow-y-auto">
            @if (session('thanhcong'))
                <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-2.5 mb-5">{{ session('thanhcong') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-2.5 mb-5">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>

<script>
    (function () {
        const btn = document.getElementById('btn-thong-bao-nv');
        const dropdown = document.getElementById('dropdown-thong-bao-nv');
        const badge = document.getElementById('badge-thong-bao-nv');
        if (!btn) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const dangAn = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden', !dangAn);

            if (dangAn && badge) {
                fetch('{{ route("thong-bao-nv.danh-dau-da-xem") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                });
                badge.remove();
            }
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && e.target !== btn) {
                dropdown.classList.add('hidden');
            }
        });
    })();
</script>
</body>
</html>