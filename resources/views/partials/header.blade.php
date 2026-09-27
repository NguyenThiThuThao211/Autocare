<header class="bg-navy sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-5 md:px-10 py-4 md:py-5 flex items-center justify-between">

        <a href="{{ route('trang-chu-cong-khai') }}" class="flex items-center gap-2.5">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-aiblue flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 md:w-6 md:h-6">
                    <path d="M5 17h14M5 17a2 2 0 100-4 2 2 0 000 4zm14 0a2 2 0 100-4 2 2 0 000 4zM5 13l1.5-5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.37L19 13M5 13h14"
                          stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="text-white font-semibold text-base md:text-xl">AutoCare</span>
        </a>

        <nav class="hidden md:flex items-center gap-8">
            <div class="relative group">
                <button class="text-slate-200 text-sm hover:text-white flex items-center gap-1">
                    Dịch vụ <span class="text-xs">▾</span>
                </button>
                <div class="absolute left-0 top-full pt-3 hidden group-hover:block">
                    <div class="bg-white rounded-xl shadow-lg w-72 p-4">
                        <p class="text-xs text-slate-400 mb-2 px-2">Nhóm dịch vụ</p>
                        @foreach ($nhomDichVu as $nhom)
                            <a href="{{ route('dich-vu', ['nhom' => $nhom]) }}"
                               class="block px-2 py-2 rounded-lg text-sm text-navy hover:bg-slate-50">
                                {{ $nhom }}
                            </a>
                        @endforeach
                        <div class="mt-3 pt-3 border-t border-slate-100 bg-aipurplebg rounded-lg p-3">
                            <p class="text-xs font-semibold text-aipurple mb-1">✦ Ưu đãi tháng này</p>
                            <p class="text-xs text-slate-600">Giảm 10% gói bảo dưỡng định kỳ cho khách hàng mới</p>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('ve-garage') }}" class="text-slate-200 text-sm hover:text-white">Về AutoCare</a>
            <a href="{{ route('danh-gia') }}" class="text-slate-200 text-sm hover:text-white">Đánh giá</a>
        </nav>

        @guest
            <div class="flex items-center gap-2 md:gap-3">
                <a href="{{ route('dang-nhap') }}"
                   class="text-white text-xs md:text-sm border border-white/60 rounded-md px-3 py-1.5 md:px-4 md:py-2 hover:bg-white/10">
                    Đăng nhập
                </a>
                <a href="{{ route('dang-ky') }}"
                   class="hidden sm:inline-block text-navy text-xs md:text-sm bg-white rounded-md px-3 py-1.5 md:px-4 md:py-2 hover:bg-slate-100">
                    Đăng ký
                </a>
            </div>
        @else
            <div class="flex items-center gap-3 md:gap-4">
                <div class="relative">
                    <button id="btn-thong-bao" type="button" class="relative text-white hover:text-blue-200">
                        <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 md:w-6 md:h-6">
                            <path d="M12 3a6 6 0 00-6 6v3.5c0 .6-.2 1.2-.6 1.6L4 16h16l-1.4-1.9c-.4-.4-.6-1-.6-1.6V9a6 6 0 00-6-6z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M9.5 19a2.5 2.5 0 005 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        @if ($soThongBaoChuaXem > 0)
                            <span id="badge-thong-bao" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-semibold rounded-full min-w-[16px] h-4 flex items-center justify-center px-1">
                                {{ $soThongBaoChuaXem > 9 ? '9+' : $soThongBaoChuaXem }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown thông báo — ẩn mặc định, cuộn được nếu nhiều --}}
                    <div id="dropdown-thong-bao" class="hidden absolute right-0 top-full mt-3 w-80 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-navy">Thông báo</p>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse ($dsThongBaoGanDay as $tb)
                                <div class="px-4 py-3 border-b border-slate-50 last:border-0 {{ !$tb->da_xem ? 'bg-blue-50/40' : '' }}">
                                    <p class="text-xs text-slate-700">{{ $tb->noi_dung }}</p>
                                    <p class="text-[11px] text-slate-400 mt-1">{{ \Carbon\Carbon::parse($tb->ngay_gui)->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="px-4 py-6 text-xs text-slate-400 text-center">Chưa có thông báo nào.</p>
                            @endforelse
                        </div>
                        <a href="{{ route('thong-bao') }}" class="block text-center text-xs text-aiblue py-2.5 border-t border-slate-100 hover:bg-slate-50">
                            Xem tất cả
                        </a>
                    </div>
                </div>
                <a href="{{ route('trang-chu') }}" class="flex items-center gap-2 text-white text-xs md:text-sm hover:text-blue-200">
                    <span class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-aiblue flex items-center justify-center text-xs font-semibold">
                        {{ mb_substr(auth()->user()->khachHang->ho_ten ?? 'KH', 0, 1) }}
                    </span>
                    <span class="hidden sm:inline">{{ auth()->user()->khachHang->ho_ten ?? 'Tài khoản' }}</span>
                </a>
                <form method="POST" action="{{ route('dang-xuat') }}">
                    @csrf
                    <button type="submit" class="text-white text-xs md:text-sm border border-white/60 rounded-md px-3 py-1.5 md:px-4 md:py-2 hover:bg-white/10">
                        Đăng xuất
                    </button>
                </form>
            </div>
        @endguest
    </div>

<script>
    (function () {
        const btn = document.getElementById('btn-thong-bao');
        const dropdown = document.getElementById('dropdown-thong-bao');
        const badge = document.getElementById('badge-thong-bao');
        if (!btn) return;

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const dangAn = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden', !dangAn);

            if (dangAn && badge) {
                // Mở dropdown -> đánh dấu đã đọc qua AJAX, ẩn badge ngay
                fetch('{{ route("thong-bao.danh-dau-da-xem") }}', {
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
</header>