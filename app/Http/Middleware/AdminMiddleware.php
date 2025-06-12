<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('admin');
        if (!$guard->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Bạn chưa đăng nhập');
        }
        
        $user = $guard->user();
        if (!$user->isAdmin()) {
            $guard->logout();
          return redirect()->route('admin.login')
                ->with('error', 'Bạn không có quyền truy cập vào trang quản trị');
        }

        if (!$user->isActive()) {
            $guard->logout();
             return redirect()->route('admin.login')
                ->with('error', 'Tài khoản của bạn đã bị khóa hoặc chưa được kích hoạt');
        }
        
        return $next($request);
    }
}
