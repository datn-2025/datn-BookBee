<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserStatusUpdated;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    // Hiển thị form chỉnh sửa vai trò và quyền trực tiếp cho user (1-n role)
    // public function editRolesPermissions($id)
    // {
    //     $user = User::with(['role', 'permissions'])->findOrFail($id);
    //     $roles = \App\Models\Role::all();
    //     // Lấy các permission mà role hiện tại chưa có
    //     $rolePermissionIds = $user->role ? $user->role->permissions->pluck('id')->toArray() : [];
    //     $permissions = \App\Models\Permission::when($rolePermissionIds, function($q) use ($rolePermissionIds) {
    //         return $q->whereNotIn('id', $rolePermissionIds);
    //     })->get();
    //     return view('admin.users.roles-permissions', compact('user', 'roles', 'permissions'));
    // }

    // // Cập nhật vai trò (role_id) và quyền trực tiếp cho user
    // public function updateRolesPermissions(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);
    //     $request->validate([
    //         'role_id' => 'required|exists:roles,id',
    //         'permissions' => 'array',
    //         'permissions.*' => 'exists:permissions,id',
    //     ], [
    //         'role_id.required' => 'Vui lòng chọn vai trò',
    //         'role_id.exists' => 'Vai trò không tồn tại',
    //     ]);

    //     $user->role_id = $request->role_id;
    //     $user->save();

    //     // Gán quyền trực tiếp
    //     $user->permissions()->sync($request->input('permissions', []));

    //     return redirect()->route('admin.users.index')->with('success', 'Cập nhật vai trò & quyền thành công.');
    // }

    public function index(Request $request)
    {
        $query = User::with('role')
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['User', 'Khách hàng']);
            })
            ->select('id', 'name', 'avatar', 'email', 'phone', 'status', 'role_id')
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


        $user = User::findOrFail($id);
        // Lưu thông tin cũ trước khi cập nhật
        $oldRole = $user->role ? $user->role->name : '';
        $oldStatus = $user->status;

        // Cập nhật thông tin
        $user->update([
            'status' => $request->status,
            'role_id' => $request->role_id,
        ]);

        // Tải lại thông tin user sau khi cập nhật
        $user->load('role');

        // Kiểm tra nếu có sự thay đổi thì mới gửi email
        $newRole = $user->role ? $user->role->name : '';
        if ($oldRole !== $newRole || $oldStatus !== $user->status) {
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
    // [DEPRECATED] Legacy n-n user-role logic removed. User now has a single role (1-n).
    // public function editRolesPermissions($id) { /* ...removed... */ }
    // [DEPRECATED] Legacy n-n user-role logic removed. User now has a single role (1-n).
    // public function editRolesPermissions($id) { /* ...removed... */ }
    // [DEPRECATED] Legacy n-n user-role logic removed. User now has a single role (1-n).
    // public function updateRolesPermissions(Request $request, $id) { /* ...removed... */ }
    // [DEPRECATED] Legacy n-n user-role logic removed. User now has a single role (1-n).
    // public function updateRolesPermissions(Request $request, $id) { /* ...removed... */ }
}
