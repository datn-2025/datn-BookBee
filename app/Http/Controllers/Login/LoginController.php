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
            return redirect()->route('login');
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
    $user = User::where('email', $request->email)->with('role')->first();

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

        // Kiểm tra mật khẩu
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            // Xóa đếm số lần đăng nhập sai nếu đăng nhập thành công
            if (isset($user)) {
                session()->forget('login_attempts_' . $user->id);
            }

            // Kiểm tra quyền admin
            $user = Auth::user();
            if ($user->isAdmin() || ($user->role && strtolower($user->role->name) === 'admin')) {
                return redirect()->intended(route('admin.dashboard'));
            }
            	Toastr()->success('Đăng nhập thành công!');
            return redirect()->intended(route('home'));
        }

        // Tăng số lần đăng nhập sai
        if (isset($user)) {
            session(['login_attempts_' . $user->id => ($attempts ?? 0) + 1]);
            $remainingAttempts = 3 - (($attempts ?? 0) + 1);
            if ($remainingAttempts > 0) {
                	Toastr()->error("Sai thông tin đăng nhập. Bạn còn $remainingAttempts lần thử.");
            } else {
                Toastr()->error('Tài khoản của bạn đã bị khóa do đăng nhập sai quá nhiều lần.');
            }
        } else {
            Toastr()->error('Email hoặc mật khẩu không chính xác.');
        }

        return back()->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

    	Toastr()->success('Bạn đã đăng xuất thành công!');
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập tên đăng nhập.',
            'name.max' => 'Tên đăng nhập tối đa 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại. Vui lòng đăng ký bằng email khác!',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu tối thiểu 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $userRole = Role::where('name', 'User')->first();
        if (!$userRole) {
            session()->flash('error', 'Không tìm thấy quyền User trong hệ thống!');
            return back()->withErrors(['role' => 'Không tìm thấy quyền User trong hệ thống!']);
        }

        $email = $request->input('email');
        $name = $request->input('name');
        $password = $request->input('password');
        $token = sha1($email);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => 'Chưa kích Hoạt',
            'activation_token' => $token,
            'activation_expires' => now()->addHours(24),
            'role_id' => $userRole->getKey(),
        ]);


        $activationUrl = route('account.activate', [
            'userId' => $user->id,
            'token' => $token,
        ]);
        // dd($activationUrl);


        try {
            Mail::to($user->email)->send(new ActivationMail($activationUrl, $user->name));
            session()->flash('success', 'Đăng ký tài khoản thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.');
        } catch (\Exception $e) {
            $user->delete();
            session()->flash('error', 'Không thể gửi email kích hoạt. Vui lòng thử lại sau.');
            return back()->withInput();
        }

        return redirect()->route('login');
    }
    public function activate(Request $request)
    {
        $userId = $request->input('userId');
        $token = $request->input('token');
        $user = User::find($userId);

        if (!$user || !isset($user->activation_token) || $user->activation_token !== $token || !isset($user->activation_expires) || now()->greaterThan($user->activation_expires)) {
            return redirect()->route('login')->with('error', 'Liên kết không hợp lệ hoặc đã hết hạn.');
        }

        $user->status = 'Hoạt Động';
        $user->activation_token = null;
        $user->activation_expires = null;
        $user->save();
        return redirect()->route('login')->with('success', 'Tài khoản đã được kích hoạt thành công.');
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
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email này không tồn tại trong hệ thống.'
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user && $user->status === 'Bị Khóa') {
            session()->flash('error', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.');
            return back()->withInput();
        }

        $resetToken = Str::random(64);
        $user->reset_token = $resetToken;
        $user->save();


        $resetLink = route('password.reset', ['token' => $resetToken, 'email' => $email]);


        try {
            Mail::to($user->email)->send(new ResetPasswordMail($resetLink));
            session()->flash('success', 'Chúng tôi đã gửi email chứa liên kết đặt lại mật khẩu của bạn!');
            return back();
        } catch (\Exception $e) {
            $user->reset_token = null;
            $user->save();
            session()->flash('error', 'Không thể gửi email đặt lại mật khẩu. Vui lòng thử lại sau.');
            return back()->withInput();
        }
    }

    // Hiển thị form đặt lại mật khẩu
    public function showResetPasswordForm($token, $email)
    {
        return view('account.reset-password-form', ['token' => $token, 'email' => $email]);
    }

    // Xử lý đặt lại mật khẩu
    public function handleResetPassword(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            // 'email.required' => 'Vui lòng nhập email.',
            // 'email.email' => 'Email không đúng định dạng.',
            // 'email.exists' => 'Email không tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $email = $request->input('email');
        $token = $request->input('token');
        $user = User::where('email', $email)
            ->where('reset_token', $token)
            ->first();
        // dd($user);

        $password = $request->input('password');
        if (!$user) {
            session()->flash('error', 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn!');
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn!']);
        }

        $user->password = Hash::make($password);
        $user->reset_token = null;
        $user->save();

        session()->flash('success', 'Mật khẩu đã được thay đổi thành công. Vui lòng đăng nhập lại.');
        return redirect()->route('login');
    }

}
