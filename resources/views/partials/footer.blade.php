<footer class="bg-navy mt-10">
    <div class="max-w-6xl mx-auto px-5 md:px-10 py-10 grid grid-cols-2 md:grid-cols-4 gap-8">

        <div class="col-span-2 md:col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-aiblue flex items-center justify-center">
                <svg viewBox="0 0 24 24" fill="none" class="w-5 h-5 md:w-6 md:h-6">
                    <path d="M5 17h14M5 17a2 2 0 100-4 2 2 0 000 4zm14 0a2 2 0 100-4 2 2 0 000 4zM5 13l1.5-5A2 2 0 018.4 7h7.2a2 2 0 011.9 1.37L19 13M5 13h14"
                          stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
                <span class="text-white font-semibold">AutoCare</span>
            </div>
            <p class="text-slate-400 text-xs md:text-sm">Bảo dưỡng ô tô thông minh, tích hợp AI — minh bạch giá ngay từ đầu.</p>
        </div>

        <div>
            <p class="text-white text-sm font-semibold mb-3">Dịch vụ</p>
            <ul class="space-y-2">
                @foreach ($nhomDichVu as $nhom)
                    <li>
                        <a href="{{ route('dich-vu', ['nhom' => $nhom]) }}" class="text-slate-400 text-xs md:text-sm hover:text-white">
                            {{ $nhom }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <p class="text-white text-sm font-semibold mb-3">Về AutoCare</p>
            <ul class="space-y-2">
                <li><a href="{{ route('ve-garage') }}" class="text-slate-400 text-xs md:text-sm hover:text-white">Giới thiệu</a></li>
                <li><a href="{{ route('danh-gia') }}" class="text-slate-400 text-xs md:text-sm hover:text-white">Đánh giá khách hàng</a></li>
                <li><a href="#" onclick="document.getElementById('btn-mo-chatbot').click(); return false;"
   class="... (giữ nguyên class cũ) ...">
    ✦ Hỏi trợ lý AI
</a></li>
            </ul>
        </div>

        <div>
            <p class="text-white text-sm font-semibold mb-3">Liên hệ</p>
            <p class="text-slate-400 text-xs md:text-sm mb-1">📞 1900 xxxx</p>
            <p class="text-slate-400 text-xs md:text-sm mb-1">📍 Địa chỉ garage</p>
            <p class="text-slate-400 text-xs md:text-sm">🕒 8:00 - 20:00 (T2-CN)</p>
        </div>

    </div>

    <div class="border-t border-white/10 py-4 text-center">
        <p class="text-slate-500 text-xs">&copy; {{ date('Y') }} AutoCare. Đồ án tốt nghiệp.</p>
    </div>
</footer>