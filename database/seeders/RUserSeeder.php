<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();

        if (!$adminRole || !$userRole) {
            $this->command->error('Vui lòng seed bảng roles trước khi seed users!');
            return;
        }

        // Vô hiệu hóa ràng buộc để xóa dữ liệu (nếu cần làm sạch bảng)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2 Admin cố định
        $admins = [
            [
                'name' => 'Admin One',
                'email' => 'admin1@example.com',
                'phone' => '0123456789',
            ],
            [
                'name' => 'Admin Two',
                'email' => 'admin2@example.com',
                'phone' => '0987654321',
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::updateOrInsert(
                ['email' => $admin['email']],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $admin['name'],
                    'password' => Hash::make('password'),
                    'phone' => $admin['phone'],
                    'status' => 'Hoạt Động',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $userModel = User::where('email', $admin['email'])->first();
            if ($userModel) {
                $userModel->roles()->syncWithoutDetaching([$adminRole->id]);
            }
        }

        // 10 User thường
        $users = [
            ['name' => 'User One', 'email' => 'user1@example.com', 'phone' => '0900000001'],
            ['name' => 'User Two', 'email' => 'user2@example.com', 'phone' => '0900000002'],
            ['name' => 'User Three', 'email' => 'user3@example.com', 'phone' => '0900000003'],
            ['name' => 'User Four', 'email' => 'user4@example.com', 'phone' => '0900000004'],
            ['name' => 'User Five', 'email' => 'user5@example.com', 'phone' => '0900000005'],
            ['name' => 'User Six', 'email' => 'user6@example.com', 'phone' => '0900000006'],
            ['name' => 'User Seven', 'email' => 'user7@example.com', 'phone' => '0900000007'],
            ['name' => 'User Eight', 'email' => 'user8@example.com', 'phone' => '0900000008'],
            ['name' => 'User Nine', 'email' => 'user9@example.com', 'phone' => '0900000009'],
            ['name' => 'User Ten', 'email' => 'user10@example.com', 'phone' => '0900000010'],
        ];

        foreach ($users as $user) {
            $userObj = User::updateOrInsert(
                ['email' => $user['email']],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'phone' => $user['phone'],
                    'status' => 'Hoạt Động',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $userModel = User::where('email', $user['email'])->first();
            if ($userModel) {
                $userModel->roles()->syncWithoutDetaching([$userRole->id]);
            }
        }
    }
}
