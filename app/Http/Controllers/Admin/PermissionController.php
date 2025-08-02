<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('module')->get()->groupBy('module');
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'module' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        $slug = Str::slug($request->name);

        Permission::create([
            'name' => $request->name,
            'slug' => $slug,
            'module' => $request->module,
            'description' => $request->description
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Thêm quyền thành công');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'module' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        $slug = Str::slug($request->name);

        $permission->update([
            'name' => $request->name,
            'slug' => $slug,
            'module' => $request->module,
            'description' => $request->description
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Cập nhật quyền thành công');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Xóa quyền thành công');
    }
}
