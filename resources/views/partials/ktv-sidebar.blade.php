<a href="{{ route('ktv.phieu') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('ktv.phieu*') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0">
        <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"
              stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Phiếu được phân công
</a>