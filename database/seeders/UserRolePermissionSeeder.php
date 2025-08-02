<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserRolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Gán role cho user đầu tiên
        $user = User::first();
        $role = Role::first();
        $permission = Permission::first();
        if ($user && $role) {
            $user->role_id = $role->id;
            $user->save();
        }
        if ($user && $permission) {
            $user->permissions()->syncWithoutDetaching([$permission->id]);
        }
        if ($role && $permission) {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}