<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationMail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    // Trang tài khoản: Nếu chưa đăng nhập thì chuyển sang trang đăng nhập
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('account.login');
        }
        return view('account.index');
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        // Nếu đã đăng nhập thì chuyển hướng về trang chủ
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('account.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        // Nếu đã đăng nhập thì chuyển hướng về trang chủ
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu tối thiểu 8 ký tự.',
        ]);

        // Kiểm tra trạng thái tài khoản trước khi đăng nhập
        $user = User::where('email', $request->email)->first();
        // dd($user);
        

        if ($user) {
            // Kiểm tra nếu tài khoản bị khóa
            if ($user->status === 'Bị Khóa') {
                Toastr::error('Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.', 'Lỗi');
                return back()->withInput();
            }

            // Kiểm tra nếu tài khoản chưa kích hoạt
            if ($user->status === 'Chưa kích Hoạt') {
                // Gửi lại email kích hoạt nếu cần
                // Mail::to($user->email)->send(new ActivationMail($user));
                Toastr::error('Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email để kích hoạt.', 'Lỗi');
                return back()->withInput();
            }

            // Kiểm tra số lần đăng nhập sai
            $attempts = session('login_attempts_' . $user->id, 0);
            if ($attempts >= 3) {
                $user->status = 'Bị Khóa';
                $user->save();
                Toastr::error('Tài khoản đã bị khóa do đăng nhập sai quá nhiều lần. Vui lòng liên hệ quản trị viên.', 'Lỗi');
                return back()->withInput();
            }
        }

        // Thực hiện đăng nhập
        $credentials = $request->only('email', 'password');
        // dd($credentials);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            // Xóa đếm số lần đăng nhập sai
            if (isset($user)) {
                session()->forget('login_attempts_' . $user->id);
            }

            // Kiểm tra nếu là admin thì chuyển hướng về trang admin
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }
            Toastr::success('Đăng nhập thành công!', 'Thành công');
            return redirect()->intended(route('home'));
        }

        // Tăng số lần đăng nhập sai
        if (isset($user)) {
            session(['login_attempts_' . $user->id => ($attempts ?? 0) + 1]);
            $remainingAttempts = 3 - (($attempts ?? 0) + 1);
            if ($remainingAttempts > 0) {
                Toastr::error("Sai thông tin đăng nhập. Bạn còn $remainingAttempts lần thử.", 'Lỗi');
            } else {
                Toastr::error('Tài khoản của bạn đã bị khóa do đăng nhập sai quá nhiều lần.', 'Lỗi');
            }
        } else {
            Toastr::error('Email hoặc mật khẩu không chính xác.', 'Lỗi');
        }

        return back()->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Toastr::success('Bạn đã đăng xuất thành công!', 'Thành công');
        return redirect()->route('home');
    }

    // Hiển thị form đăng ký
    public function register()
    {
        return view('account.register');
    }

    // Xử lý đăng ký
    public function handleRegister(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập tên đăng nhập.',
            'name.max' => 'Tên đăng nhập tối đa 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Lấy role_id của quyền "User"
        $userRole = Role::where('name', 'User')->first();
        if (!$userRole) {
            Toastr::error('Không tìm thấy quyền User trong hệ thống!', 'Lỗi');
            return back()->withErrors(['role' => 'Không tìm thấy quyền User trong hệ thống!']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'status' => 'Chưa kích Hoạt',
        ]);

        // Tạo activation token và url
        $token = sha1($user->email);
        $activationUrl = route('account.activate', [
            'userId' => $user->id,
            'token' => $token,
            'expires' => now()->addHours(24)->timestamp
        ]);

        // Gửi email kích hoạt
        try {
            Mail::to($user->email)->send(new ActivationMail($activationUrl, $user->name));
            Toastr::success('Đăng ký tài khoản thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.', 'Thành công');
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Không thể gửi email kích hoạt. Vui lòng thử lại sau.', 'Lỗi');
            $user->delete(); // Xóa user nếu không gửi được email
            return back()->withInput();
        }

        return redirect()->route('account.index');
    }

    // Đăng xuất
    // public function logout(Request $request)
    // {
    //     Auth::logout();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     Toastr::success('Đăng xuất thành công!', 'Thành công');

    //     return redirect('/');
    // }

    // Hiển thị form quên mật khẩu
    public function showForgotPasswordForm()
    {
        return view('account.resetpass');
    }

    // Gửi email đặt lại mật khẩu
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ], [
            'email.exists' => 'Không tìm thấy địa chỉ email này trong hệ thống.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.'
        ]);

        $user = User::where('email', $request->email)->first();
        $resetToken = Str::random(64);
        $user->update(['reset_token' => $resetToken]);

        $resetLink = route('account.password.reset', ['token' => $resetToken]);

        try {
            Mail::to($request->email)->send(new ResetPasswordMail($resetLink));
            Toastr::success('Chúng tôi đã gửi email chứa liên kết đặt lại mật khẩu của bạn!', 'Thành công');
        } catch (\Exception $e) {
            Toastr::error('Không thể gửi email đặt lại mật khẩu. Vui lòng thử lại sau.', 'Lỗi');
            return back()->withInput();
        }

        return back();
    }

    // Hiển thị form đặt lại mật khẩu
    public function showResetPasswordForm($token)
    {
        return view('account.reset-password-form', ['token' => $token]);
    }

    // Xử lý đặt lại mật khẩu
    public function handleResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->first();

        if (!$user) {
            Toastr::error('Mã token không hợp lệ!', 'Lỗi');
            return back()->withErrors(['email' => 'Mã token không hợp lệ!']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_token' => null
        ]);

        Toastr::success('Mật khẩu của bạn đã được thay đổi!', 'Thành công');
        return redirect()->route('account.login');
    }

    // hiển thị thông tin người dùng khi đang đăng nhập
    public function showUser(Request $request)
    {
        if (!Auth::check()) {
            Toastr::error('Bạn cần đăng nhập để xem thông tin tài khoản.', 'Lỗi');
            return redirect()->route('account.login');
        }

        $user = Auth::user();
        return view('profile.client', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(), // cho phép email cũ
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('avatar')) {
            $filename = time() . '.' . $request->avatar->extension();

            // Xóa file cũ nếu có
            if ($user->avatar && file_exists(public_path('storage/' . $user->avatar))) {
                unlink(public_path('storage/' . $user->avatar));
            }

            // Lưu file vào storage/app/public/avatars
            $request->avatar->storeAs('avatars', $filename, 'public');

            // ✅ Gán đúng: chỉ lưu 'avatars/filename.jpg'
            $user->avatar = 'avatars/' . $filename;
        }




        $user->save();

        Toastr::success('Cập nhật hồ sơ thành công!', 'Thành công');
        return back();
    }
}
