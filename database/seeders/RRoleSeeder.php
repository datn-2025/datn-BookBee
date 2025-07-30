<?php

namespace Database\Seeders;
use App\Models\Permission;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add role entries
          $roles = [
            [
                'name' => 'Admin',
                'description' => 'Quản trị viên hệ thống'
            ],
            [
                'name' => 'User',
                'description' => 'Người dùng thông thường'
            ],
            [
                'name' => 'Staff',
                'description' => 'Nhân viên quản lý'
            ]
        ];

        foreach ($roles as $roleData) {
            // Tạo role
            $role = Role::create($roleData);

            // Gán quyền nếu cần
            if ($role->name === 'Admin') {
                $role->permissions()->attach(Permission::all());
            }

            if ($role->name === 'Staff') {
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

                $allowedPermissions = Permission::whereNotIn('slug', $restrictedPermissions)->get();
                $role->permissions()->attach($allowedPermissions);
            }
        }
}
}
