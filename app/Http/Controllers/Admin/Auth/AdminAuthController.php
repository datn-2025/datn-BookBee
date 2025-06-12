<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        // Tìm user theo email
        $user = User::where('email', $request->email)->with('role')->first();

        // Kiểm tra tồn tại user
        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['login' => 'Tài khoản hoặc mật khẩu không đúng']);
        }

        // Kiểm tra quyền admin
        if (!$user->isAdmin()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['login' => 'Bạn không có quyền truy cập vào trang quản trị']);
        }

        // Kiểm tra trạng thái hoạt động
        if (!$user->isActive()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['login' => 'Tài khoản của bạn đã bị khoá hoặc chưa kích hoạt']);
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Tài khoản hoặc mật khẩu không đúng']);
        }

        // Đăng nhập thành công
        Auth::guard('admin')->login($user);
        Toastr::success('Đăng nhập thành công!');
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $guard = Auth::guard('admin');
        if (!$guard->check()) {
            abort(404, 'Bạn chưa đăng nhập');
        }
        $guard->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Toastr::success('Đăng xuất thành công!');
        return redirect()->route('admin.login');
    }
}
