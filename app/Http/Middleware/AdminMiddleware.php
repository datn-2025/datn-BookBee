<?php

namespace App\Http\Middleware;

use Brian2694\Toastr\Facades\Toastr;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('admin');
        if (!$guard->check()) {
            Toastr::error('Bạn chưa đăng nhập', 'Lỗi');
            return redirect()->route('admin.login');
        }
        
        $user = $guard->user();
        if (!$user->isAdmin()) {
            $guard->logout();
            Toastr::error('Bạn không có quyền truy cập', 'Lỗi');
          return redirect()->route('admin.login');
        }

        if (!$user->isActive()) {
            $guard->logout();
            Toastr::error('Tài khoản của bạn đã bị khóa hoặc chưa được kích hoạt', 'Lỗi');
             return redirect()->route('admin.login');
        }
        
        return $next($request);
    }
}
