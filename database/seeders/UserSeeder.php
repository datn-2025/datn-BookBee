<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();

        // Tạo 2 admin users
        $admins = User::factory(2)->create();
        foreach ($admins as $admin) {
            $admin->role_id = $adminRole->id;
            $admin->save();
        }

        // Tạo 10 user thường
        $users = User::factory(10)->create();
        foreach ($users as $user) {
            $user->role_id = $userRole->id;
            $user->save();
        }
    }
}
