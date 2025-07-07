<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PasswordChangeMail;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    /**
     * Display the admin profile page
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile.index', compact('admin'));
    }

    /**
     * Update admin profile information
     */
    public function updateProfile(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $admin = User::findOrFail($adminId);

        // Nếu thay đổi email, yêu cầu nhập mật khẩu để xác thực
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Nếu email thay đổi, yêu cầu mật khẩu xác thực
        if ($request->email !== $admin->email) {
            $rules['password_confirmation'] = 'required';
        }

        $request->validate($rules, [
            'name.required' => 'Tên không được để trống.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'avatar.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'password_confirmation.required' => 'Vui lòng nhập mật khẩu để xác thực thay đổi email.'
        ]);

        // Nếu thay đổi email, kiểm tra mật khẩu
        if ($request->email !== $admin->email && $request->password_confirmation) {
            if (!Hash::check($request->password_confirmation, $admin->password)) {
                return back()->withErrors(['password_confirmation' => 'Mật khẩu không đúng.']);
            }
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            // Handle avatar upload with better file naming
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
                    Storage::disk('public')->delete($admin->avatar);
                }

                $file = $request->file('avatar');
                $filename = 'admin_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('avatars/admin', $filename, 'public');
                $data['avatar'] = $path;
            }

            $admin->update($data);
            
            // Log activity
            Log::info('Admin profile updated', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'changes' => array_keys($data)
            ]);
            
            Toastr::success('Cập nhật thông tin tài khoản thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating admin profile: ' . $e->getMessage());
            Toastr::error('Có lỗi xảy ra khi cập nhật thông tin!');
        }

        return back();
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $adminId = Auth::guard('admin')->id();
        $admin = User::findOrFail($adminId);

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        try {
            // Update password
            $admin->update(['password' => Hash::make($request->password)]);

            // Send notification email (optional)
            try {
                Mail::to($admin->email)->send(new PasswordChangeMail($admin));
            } catch (\Exception $e) {
                Log::error('Không thể gửi email thông báo đổi mật khẩu: ' . $e->getMessage());
            }

            Toastr::success('Đổi mật khẩu thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating admin password: ' . $e->getMessage());
            Toastr::error('Có lỗi xảy ra khi đổi mật khẩu!');
        }

        return back();
    }
}
