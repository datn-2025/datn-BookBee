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
    public function index(Request $request)
    {
        $query = User::with('roles')->select('id', 'name', 'avatar', 'email', 'phone', 'status')
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
        $user = User::with('roles')->findOrFail($id);
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
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'status' => 'required|in:Hoạt Động,Bị Khóa,Chưa kích Hoạt',
        ], [
            'roles.required' => 'Vui lòng chọn vai trò',
            'roles.*.exists' => 'Vai trò không tồn tại',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        $user = User::with('roles')->findOrFail($id);

        // Lưu thông tin cũ trước khi cập nhật
        $oldRoles = $user->roles->pluck('name')->implode(', ');
        $oldStatus = $user->status;

        // Cập nhật thông tin
        $user->update([
            'status' => $request->status,
        ]);
        $user->roles()->sync($request->roles);

        // Tải lại thông tin user sau khi cập nhật
        $user->load('roles');

        // Kiểm tra nếu có sự thay đổi thì mới gửi email
        $newRoles = $user->roles->pluck('name')->implode(', ');
        if ($oldRoles !== $newRoles || $oldStatus !== $user->status) {
            try {
                Mail::to($user->email)
                    ->queue(new UserStatusUpdated($user, $oldRoles, $oldStatus));

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
    // public function editRolesPermissions($id)
    // {
    //     $user = User::with(['roles', 'permissions'])->findOrFail($id);
    //     $roles = Role::all();
    //     $permissions = Permission::all();

    //     return view('admin.users.roles-permissions', compact('user', 'roles', 'permissions'));
    // }
    public function editRolesPermissions($id)
{
    $user = User::with(['roles.permissions', 'permissions'])->findOrFail($id);
    $roles = Role::all();

    // Lấy ID các quyền từ role hiện có
    $rolePermissions = $user->roles->flatMap(function ($role) {
        return $role->permissions;
    })->pluck('id')->unique();

    // Chỉ lấy các quyền mà roles chưa có
    $permissions = Permission::whereNotIn('id', $rolePermissions)->get();

    return view('admin.users.roles-permissions', compact('user', 'roles', 'permissions'));
}
    // public function updateRolesPermissions(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);

    //     // Gán vai trò
    //     $roles = $request->input('roles', []);
    //     $user->roles()->sync($roles);

    //     // Gán quyền trực tiếp
    //     $permissions = $request->input('permissions', []);
    //     $user->permissions()->sync($permissions);

    //     return redirect()->route('admin.users.index')->with('success', 'Cập nhật vai trò & quyền thành công.');
    // }
    public function updateRolesPermissions(Request $request, $id)
{
    $user = User::with(['roles', 'permissions'])->findOrFail($id);

    // Vai trò cũ trước khi cập nhật
    $oldRoleIds = $user->roles->pluck('id')->toArray();

    // Vai trò mới từ form
    $newRoleIds = $request->input('roles', []);
    $user->roles()->sync($newRoleIds); // Cập nhật vai trò mới

    // Lấy danh sách quyền thuộc các vai trò cũ
    $oldRolePermissions = Role::whereIn('id', $oldRoleIds)
        ->with('permissions')
        ->get()
        ->flatMap->permissions
        ->pluck('id')
        ->unique();

    // Xoá các quyền thủ công trước đây nếu chúng thuộc các vai trò cũ
    $user->permissions()->detach($oldRolePermissions);

    // Gán quyền thủ công được chọn từ form
    $directPermissions = $request->input('permissions', []);
    $user->permissions()->syncWithoutDetaching($directPermissions);

    return redirect()->route('admin.users.index')->with('success', 'Cập nhật vai trò & quyền thành công.');
}
}
