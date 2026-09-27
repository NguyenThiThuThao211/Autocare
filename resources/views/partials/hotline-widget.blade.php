{{-- Nút hotline nổi — cạnh chatbot, luôn hiện trên mọi trang khách hàng --}}
<button id="btn-mo-hotline"
        class="fixed bottom-5 right-24 z-50 w-14 h-14 rounded-full bg-navy shadow-lg
               flex items-center justify-center hover:scale-105 transition">
    <svg viewBox="0 0 24 24" fill="none" class="w-6 h-6">
        <path d="M4 4h4l2 5-2.5 1.5a11 11 0 005 5L14 13l5 2v4a2 2 0 01-2 2A16 16 0 012 6a2 2 0 012-2z"
              stroke="white" stroke-width="1.6" stroke-linejoin="round" fill="none"/>
    </svg>
</button>

<div id="khung-hotline"
     class="fixed bottom-24 right-24 z-50 w-72 bg-white rounded-2xl shadow-2xl hidden border border-slate-200 overflow-hidden">
    <div class="bg-navy px-4 py-3">
        <p class="text-white text-sm font-semibold">Liên hệ garage</p>
        <p class="text-slate-300 text-xs">Không duyệt được báo giá online? Gọi ngay cho chúng tôi</p>
    </div>
    <div class="p-4">
        <a href="tel:1900xxxx" class="flex items-center gap-3 bg-slate-50 rounded-xl p-3 mb-3 hover:bg-slate-100">
            <span class="w-10 h-10 rounded-full bg-navy flex items-center justify-center text-white">📞</span>
            <div>
                <p class="text-sm font-semibold text-navy">1900 xxxx</p>
                <p class="text-xs text-slate-400">Bấm để gọi ngay</p>
            </div>
        </a>
        <p class="text-xs text-slate-400 text-center">Hỗ trợ 8:00 - 20:00 (T2 - CN)</p>
    </div>
</div>

<script>
(function () {
    const btn = document.getElementById('btn-mo-hotline');
    const khung = document.getElementById('khung-hotline');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        khung.classList.toggle('hidden');
    });

    document.addEventListener('click', function (e) {
        if (!khung.contains(e.target) && e.target !== btn) {
            khung.classList.add('hidden');
        }
    });
})();
</script>