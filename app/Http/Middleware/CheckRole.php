<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Kiểm tra đúng vai trò trước khi cho vào route — dùng dạng: middleware('role:Co van dich vu')
     */
    public function handle(Request $request, Closure $next, string $vaiTroYeuCau)
    {
        if (!Auth::check() || Auth::user()->vai_tro !== $vaiTroYeuCau) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}