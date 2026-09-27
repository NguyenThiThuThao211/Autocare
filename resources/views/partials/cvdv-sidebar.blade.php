<a href="{{ route('cvdv.dashboard') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('cvdv.dashboard') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M3 13h4v8H3v-8zM10 3h4v18h-4V3zM17 8h4v13h-4V8z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Dashboard
</a>
<a href="{{ route('cvdv.lich-hen') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('cvdv.lich-hen') || request()->routeIs('cvdv.phieu.tao-moi') || request()->routeIs('cvdv.phan-cong') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M8 7V3m8 4V3M3 11h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Quản lý lịch hẹn
</a>
<a href="{{ route('cvdv.phieu.danh-sach') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('cvdv.phieu.danh-sach') || request()->routeIs('cvdv.phieu.chi-tiet') || request()->routeIs('cvdv.bao-gia.*') || request()->routeIs('cvdv.hoa-don.*') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Quản lý phiếu sửa chữa
</a>
<a href="{{ route('cvdv.khach-hang') }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('cvdv.khach-hang*') ? 'bg-navylight text-white' : 'text-slate-300 hover:bg-navylight' }}">
    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 flex-shrink-0"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    Danh sách khách hàng
</a>