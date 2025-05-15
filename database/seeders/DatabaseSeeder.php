<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            PaymentMethodSeeder::class,
            PaymentStatusSeeder::class,
            ProductImageSeeder::class,
            OrderStatusSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class
        ]);
    }
}
