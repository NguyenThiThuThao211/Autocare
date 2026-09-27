<a href="{{ route('quan-ly.dashboard') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('quan-ly.dashboard') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M3 13h4v8H3v-8zM10 3h4v18h-4V3zM17 8h4v13h-4V8z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Báo cáo
</a>
<a href="{{ route('quan-ly.dich-vu') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('quan-ly.dich-vu*') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Danh mục dịch vụ
</a>
<a href="{{ route('quan-ly.phu-tung') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('quan-ly.phu-tung*') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Tồn kho phụ tùng
</a>
<a href="{{ route('quan-ly.tai-khoan') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('quan-ly.tai-khoan*') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Quản trị tài khoản
</a>