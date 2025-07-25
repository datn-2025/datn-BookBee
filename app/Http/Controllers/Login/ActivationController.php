<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function activate(Request $request, $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            Toastr::error('Không tìm thấy tài khoản.', 'Lỗi');
            return redirect()->route('login');
        }        // Kiểm tra token và thời gian hết hạn
        $token = $request->query('token');
        $expires = $request->query('expires');
        
        if (!$token || !$expires || $expires < now()->timestamp) {
            Toastr::error('Liên kết kích hoạt không hợp lệ hoặc đã hết hạn.', 'Lỗi');
            return redirect()->route('login');
        }

        // Chỉ kiểm tra token khớp với email
        if ($token !== sha1($user->email)) {
            Toastr::error('Liên kết kích hoạt không hợp lệ.', 'Lỗi');
            return redirect()->route('login');
        }        if ($user->status === 'Hoạt Động') {
            Toastr::info('Tài khoản đã được kích hoạt trước đó.', 'Thông báo');
            return redirect()->route('login');
        }        $user->status = 'Hoạt Động';
        $user->remember_token = sha1(time() . $user->email);
        $success = $user->save();

        Toastr::success('Kích hoạt tài khoản thành công!', 'Thành công');
        return redirect()->route('login');
    }
}
