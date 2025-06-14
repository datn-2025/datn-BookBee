<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Tạo vai trò Admin
        $admin = Role::create([
            'name' => 'Admin',
            'description' => 'Quản trị viên hệ thống'
        ]);
        $admin->permissions()->attach(Permission::all());

        // Tạo vai trò Nhân viên
        $staff = Role::create([
            'name' => 'Nhân viên',
            'description' => 'Nhân viên quản lý'
        ]);

        // Danh sách quyền mà nhân viên không được phép
        $restrictedPermissions = [
            'users.manage',
            'users.force-delete',
            'roles.manage',
            'payment-methods.create',
            'payment-methods.edit',
            'payment-methods.delete',
            'attributes.create',
            'attributes.edit',
            'attributes.delete'
        ];

        // Gán tất cả quyền trừ các quyền bị hạn chế
        $staffPermissions = Permission::whereNotIn('slug', $restrictedPermissions)->get();
        $staff->permissions()->attach($staffPermissions);
    }
}
