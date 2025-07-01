<?php

namespace Database\Seeders;

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
            ['name' => 'Admin'],
            ['name' => 'User'],
            ['name' => 'Staff'],
            // You can add more roles here as required
        ];

        // Loop through each role and create a new record in the roles table
        foreach ($roles as $role) {
            Role::create($role);    
        }
    }
}
