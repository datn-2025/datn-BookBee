<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    // Hiển thị form thêm nhân viên
    public function create()
    {
        $roles = Role::whereNotIn('name', ['User', 'Khách hàng'])->get();
        return view('admin.staff.create', compact('roles'));
    }

    // Lưu nhân viên mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Hoạt Động,Bị Khóa,Chưa kích Hoạt',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        $user->save();
        return redirect()->route('admin.staff.index')->with('success', 'Thêm nhân viên thành công!');
    }
    // Hiển thị danh sách nhân viên
    public function index(Request $request)
    {
        $query = User::with('role')
            ->whereHas('role', function($q) {
                $q->whereNotIn('name', ['User', 'Khách hàng']);
            })
            ->select('id', 'name', 'avatar', 'email', 'phone', 'status', 'role_id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%") ;
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staffs = $query->paginate(10)->appends($request->only(['search', 'status']));
        return view('admin.staff.index', compact('staffs'));
    }

    // Hiển thị chi tiết nhân viên
    public function show($id)
    {
        $staff = User::with('role')->whereHas('role', function($q) {
            $q->whereNotIn('name', ['User', 'Khách hàng']);
        })->findOrFail($id);
        return view('admin.staff.show', compact('staff'));
    }

    // Hiển thị form chỉnh sửa nhân viên
    public function edit($id)
    {
        $staff = User::with('role')->whereHas('role', function($q) {
            $q->whereNotIn('name', ['User', 'Khách hàng']);
        })->findOrFail($id);
        $roles = Role::whereNotIn('name', ['User', 'Khách hàng'])->get();
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    // Cập nhật thông tin nhân viên
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Hoạt Động,Bị Khóa,Chưa kích Hoạt',
        ]);
        $staff = User::whereHas('role', function($q) {
            $q->whereNotIn('name', ['User', 'Khách hàng']);
        })->findOrFail($id);
        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'status' => $request->status,
        ]);
        return redirect()->route('admin.staff.index')->with('success', 'Cập nhật thành công');
    }

    // Xóa nhân viên
    public function destroy($id)
    {
        $staff = User::whereHas('role', function($q) {
            $q->whereNotIn('name', ['User', 'Khách hàng']);
        })->findOrFail($id);
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Xóa nhân viên thành công');
    }

    // Phân quyền cho nhân viên
    // public function editRolesPermissions($id)
    // {
    //     $staff = User::with(['role', 'permissions'])->whereHas('role', function($q) {
    //         $q->whereNotIn('name', ['User', 'Khách hàng']);
    //     })->findOrFail($id);
    //     $roles = Role::all();
    //     $rolePermissionIds = $staff->role ? $staff->role->permissions->pluck('id')->toArray() : [];
    //     $permissions = Permission::when($rolePermissionIds, function($q) use ($rolePermissionIds) {
    //         return $q->whereNotIn('id', $rolePermissionIds);
    //     })->get();
    //     return view('admin.staff.roles-permissions', compact('staff', 'roles', 'permissions'));
    // }

    // public function updateRolesPermissions(Request $request, $id)
    // {
    //     $staff = User::whereHas('role', function($q) {
    //         $q->whereNotIn('name', ['User', 'Khách hàng']);
    //     })->findOrFail($id);
    //     $request->validate([
    //         'role_id' => 'required|exists:roles,id',
    //         'permissions' => 'array',
    //         'permissions.*' => 'exists:permissions,id',
    //     ]);
    //     $staff->role_id = $request->role_id;
    //     $staff->save();
    //     $staff->permissions()->sync($request->input('permissions', []));
    //     return redirect()->route('admin.staff.index')->with('success', 'Cập nhật vai trò & quyền thành công.');
    // }
}
