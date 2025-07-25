<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserStatusUpdated;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')->select('id', 'name', 'avatar', 'email', 'phone', 'role_id', 'status')
            ->where('id', '!=', Auth::id()); // Loại bỏ tài khoản đang đăng nhập

        // Tìm kiếm theo text
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(10)->appends($request->only(['search', 'status']));

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        // Lấy lịch sử mua hàng của user, join với trạng thái đơn hàng và thông tin người dùng
        $listDonHang = $user->orders()
            ->with(['orderStatus', 'address', 'paymentStatus'])
            ->get()
            ->map(function ($order) {
                return (object)[
                    'id' => $order->id,
                    'order_code' => $order->id, // Sử dụng id làm mã đơn hàng
                    'shipping_name' => $order->address->recipient_name ?? 'N/A',
                    'shipping_phone' => $order->address->phone ?? 'N/A',
                    'created_at' => $order->created_at->format('d/m/Y H:i'),
                    'total_amount' => $order->total_amount,
                    'orderStatus' => $order->orderStatus,
                    'paymentStatus' => $order->paymentStatus
                ];
            });
        return view('admin.users.show', compact('user', 'listDonHang'));
    }

    public function edit($id)
    {
        $user = User::with('role')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Hoạt Động,Bị Khóa,Chưa kích Hoạt',
        ], [
            'role_id.required' => 'Vui lòng chọn vai trò',
            'role_id.exists' => 'Vai trò không tồn tại',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        $user = User::with('role')->findOrFail($id);

        // Lưu thông tin cũ trước khi cập nhật
        $oldRole = $user->role ? $user->role->name : 'Chưa phân quyền';
        $oldStatus = $user->status;

        // Cập nhật thông tin
        $user->update([
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);

        // Tải lại thông tin user sau khi cập nhật
        $user->load('role');

        // Kiểm tra nếu có sự thay đổi thì mới gửi email
        if ($oldRole !== $user->role->name || $oldStatus !== $user->status) {
            try {
                Mail::to($user->email)
                    ->queue(new UserStatusUpdated($user, $oldRole, $oldStatus));

                // Log thành công vào queue
                Log::info('Đã thêm email thông báo vào queue cho user: ' . $user->email);
            } catch (\Exception $e) {
                // Log lỗi nhưng vẫn cho phép tiếp tục
                Log::error('Không thể thêm email vào queue: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.users.index', $user->id)
            ->with('success', 'Cập nhật thành công');
    }
}
