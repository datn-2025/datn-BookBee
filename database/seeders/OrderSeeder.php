<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'User');
        })->get();

        foreach ($users as $user) {
            $address = $user->addresses()->inRandomOrder()->first();

            // Nếu user chưa có địa chỉ, bạn có thể tạo địa chỉ mới hoặc bỏ qua user này
            if (!$address) {
                // Tạo địa chỉ mới cho user (nếu bạn có AddressFactory)
                $address = \App\Models\Address::factory()->create([
                    'user_id' => $user->id,
                ]);
            }

            Order::factory(rand(1, 3))->create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'order_status_id' => OrderStatus::inRandomOrder()->first()->id,
            ]);
        }
    }
}
