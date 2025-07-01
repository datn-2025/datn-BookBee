<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
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

        // Tạo 2 admin mẫu
        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Admin One',
            'email' => 'admin1@example.com',
            'password' => Hash::make('password'), // hoặc thay đổi mật khẩu nếu muốn
            'phone' => '0123456789',
            'status' => 'Hoạt Động',
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'phone' => '0987654321',
            'status' => 'Hoạt Động',
            'role_id' => $adminRole->id,
        ]);

        // Tạo 10 user thường mẫu
        $users = [
            [
                'name' => 'User One',
                'email' => 'user1@example.com',
                'phone' => '0900000001',
            ],
            [
                'name' => 'User Two',
                'email' => 'user2@example.com',
                'phone' => '0900000002',
            ],
            [
                'name' => 'User Three',
                'email' => 'user3@example.com',
                'phone' => '0900000003',
            ],
            [
                'name' => 'User Four',
                'email' => 'user4@example.com',
                'phone' => '0900000004',
            ],
            [
                'name' => 'User Five',
                'email' => 'user5@example.com',
                'phone' => '0900000005',
            ],
            [
                'name' => 'User Six',
                'email' => 'user6@example.com',
                'phone' => '0900000006',
            ],
            [
                'name' => 'User Seven',
                'email' => 'user7@example.com',
                'phone' => '0900000007',
            ],
            [
                'name' => 'User Eight',
                'email' => 'user8@example.com',
                'phone' => '0900000008',
            ],
            [
                'name' => 'User Nine',
                'email' => 'user9@example.com',
                'phone' => '0900000009',
            ],
            [
                'name' => 'User Ten',
                'email' => 'user10@example.com',
                'phone' => '0900000010',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'id' => (string) Str::uuid(),
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'phone' => $user['phone'],
                'status' => 'Hoạt Động',
                'role_id' => $userRole->id,
            ]);
        }
    }
}
