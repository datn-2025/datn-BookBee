<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {        // Create predefined roles
        $roles = [
            // ['id' => fake()->uuid(), 'name' => 'Admin'],
            // ['id' => fake()->uuid(), 'name' => 'User'],
            // ['id' => fake()->uuid(), 'name' => 'Staff']
            ['id' => "1", 'name' => 'Admin'],
            ['id' => "2", 'name' => 'User'],
            ['id' => "3", 'name' => 'Staff']
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
