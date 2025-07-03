<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangeMail;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProfileClientController extends Controller
{
    // hiển thị thông tin người dùng khi đang đăng nhập
    public function showUser(Request $request)
    {
        if (!Auth::check()) {
            Toastr::error('Bạn cần đăng nhập để xem thông tin tài khoản.', 'Lỗi');
            return redirect()->route('login');
        }

        $user = Auth::user();
        // Truyền $addresses nếu là tab Địa Chỉ
        if ($request->get('type', '1') == 2) {
            $addresses = $user->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        } else {
            $addresses = null;
        }
        return view('clients.profile.profile', compact('user', 'addresses'));
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


    // Hiển thị form đổi mật khẩu
    public function showChangePasswordForm()
    {
        return view('clients.profile.profile');
    }

    // Xử lý đổi mật khẩu
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại.'
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            Toastr::error('Mật khẩu hiện tại không đúng.', 'Lỗi');
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->password);
        $user->save();

        // Gửi email thông báo
        try {
            Mail::to($user->email)->send(new PasswordChangeMail($user->name));
        } catch (\Exception $e) {
            // Log lỗi nhưng không dừng quy trình
            Log::error('Không thể gửi email thông báo đổi mật khẩu: ' . $e->getMessage());
        }

        session()->flash('success', 'Bạn đã thay đổi mật khẩu thành công!');
        return redirect()->route('account.profile');
    }
}