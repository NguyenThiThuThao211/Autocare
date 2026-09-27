{{-- Nút chatbot nổi — luôn hiện ở góc phải dưới trên mọi trang khách hàng --}}
<button id="btn-mo-chatbot"
        class="fixed bottom-5 right-5 z-50 w-14 h-14 rounded-full bg-aipurple shadow-lg
               flex items-center justify-center hover:scale-105 transition">
    <svg viewBox="0 0 24 24" fill="none" class="w-6 h-6" id="icon-chat-mo">
        <path d="M12 3C6.5 3 2 6.6 2 11c0 2.4 1.3 4.5 3.4 6L4 21l4.3-1.7c1.1.3 2.4.5 3.7.5 5.5 0 10-3.6 10-8s-4.5-8-10-8z"
              stroke="white" stroke-width="1.6" stroke-linejoin="round"/>
        <circle cx="8.5" cy="11" r="1" fill="white"/>
        <circle cx="12" cy="11" r="1" fill="white"/>
        <circle cx="15.5" cy="11" r="1" fill="white"/>
    </svg>
    <svg viewBox="0 0 24 24" fill="none" class="w-6 h-6 hidden" id="icon-chat-dong">
        <path d="M6 6l12 12M18 6L6 18" stroke="white" stroke-width="2" stroke-linecap="round"/>
    </svg>
</button>

{{-- Khung chat nhỏ — ẩn mặc định --}}
<div id="khung-chatbot"
     class="fixed bottom-24 right-5 z-50 w-[90vw] max-w-sm h-[70vh] max-h-[520px]
            bg-white rounded-2xl shadow-2xl hidden flex-col overflow-hidden border border-slate-200">

    {{-- Header khung chat --}}
    <div class="bg-navy px-4 py-3 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-aiblue flex items-center justify-center">
            <span class="text-white text-xs">✦</span>
        </div>
        <div>
            <p class="text-white text-sm font-semibold">Trợ lý AutoCare</p>
            <p class="text-slate-300 text-xs">Luôn sẵn sàng hỗ trợ</p>
        </div>
    </div>

    {{-- Khung tin nhắn --}}
    <div id="danh-sach-tin-nhan" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
        <div class="bg-white rounded-xl rounded-tl-sm px-3 py-2 max-w-[85%] text-sm shadow-sm">
            Xin chào! Tôi có thể giúp gì cho xe của bạn hôm nay?
        </div>
    </div>

    {{-- Ô nhập --}}
    <form id="form-chat" class="border-t border-slate-200 p-3 flex gap-2">
        @csrf
        <input type="text" id="o-nhap-chat" placeholder="Nhập câu hỏi..."
               class="flex-1 text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-aiblue">
        <button type="submit" class="bg-navy text-white rounded-lg px-4 py-2 text-sm hover:bg-navylight">
            Gửi
        </button>
    </form>
</div>

<script>
(function () {
    const btnMo = document.getElementById('btn-mo-chatbot');
    const khung = document.getElementById('khung-chatbot');
    const iconMo = document.getElementById('icon-chat-mo');
    const iconDong = document.getElementById('icon-chat-dong');
    const form = document.getElementById('form-chat');
    const oNhap = document.getElementById('o-nhap-chat');
    const dsTinNhan = document.getElementById('danh-sach-tin-nhan');

    btnMo.addEventListener('click', function () {
        const dangAn = khung.classList.contains('hidden');
        khung.classList.toggle('hidden', !dangAn);
        khung.classList.toggle('flex', dangAn);
        iconMo.classList.toggle('hidden', dangAn);
        iconDong.classList.toggle('hidden', !dangAn);
        if (dangAn) oNhap.focus();
    });

    function themTinNhan(noiDung, laKhach) {
        const div = document.createElement('div');
        if (laKhach) {
            div.className = 'bg-navy text-white rounded-xl rounded-tr-sm px-3 py-2 max-w-[85%] text-sm ml-auto';
        } else {
            div.className = 'bg-white rounded-xl rounded-tl-sm px-3 py-2 max-w-[85%] text-sm shadow-sm';
        }
        div.innerText = noiDung;
        dsTinNhan.appendChild(div);
        dsTinNhan.scrollTop = dsTinNhan.scrollHeight;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const tinNhan = oNhap.value.trim();
        if (!tinNhan) return;

        themTinNhan(tinNhan, true);
        oNhap.value = '';

        const dangGoDiv = document.createElement('div');
        dangGoDiv.id = 'dang-go';
        dangGoDiv.className = 'bg-white rounded-xl rounded-tl-sm px-3 py-2 max-w-[85%] text-sm shadow-sm text-slate-400 italic';
        dangGoDiv.innerText = 'Đang trả lời...';
        dsTinNhan.appendChild(dangGoDiv);
        dsTinNhan.scrollTop = dsTinNhan.scrollHeight;

        try {
            const res = await fetch('{{ route("api.chatbot") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                },
                body: JSON.stringify({ tin_nhan: tinNhan }),
            });
            const data = await res.json();
            document.getElementById('dang-go').remove();
            themTinNhan(data.tra_loi, false);
        } catch (err) {
            document.getElementById('dang-go').remove();
            themTinNhan('Có lỗi xảy ra, vui lòng thử lại.', false);
        }
    });
})();
</script>