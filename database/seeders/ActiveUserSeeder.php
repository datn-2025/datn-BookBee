<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ActiveUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $userRole = Role::where('name', 'User')->first();

        // Tạo tài khoản admin
        if (!User::where('email', 'admin@bookbee.com')->exists()) {
            User::create([
                'id' => (string) Str::uuid(),
                'name' => 'Admin BookBee',
                'email' => 'admin@bookbee.com',
                'avatar' => 'https://ui-avatars.com/api/?name=Admin+BookBee&background=random',
                'password' => Hash::make('admin123456'),
                'phone' => '0123456789',
                'status' => 'Hoạt Động',
                'role_id' => $adminRole->id,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tạo tài khoản user
        if (!User::where('email', 'user@bookbee.com')->exists()) {
            User::create([
                'id' => (string) Str::uuid(),
                'name' => 'User BookBee',
                'email' => 'user@bookbee.com',
                'avatar' => 'https://ui-avatars.com/api/?name=User+BookBee&background=random',
                'password' => Hash::make('user123456'),
                'phone' => '0987654321',
                'status' => 'Hoạt Động',
                'role_id' => $userRole->id,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
