<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class RPaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $methods = [
            ['name' => 'Thanh toán khi nhận hàng'],
            ['name' => 'Chuyển khoản ngân hàng'],
            ['name' => 'Ví điện tử'],
            ['name' => 'Thanh toán vnpay'],       
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['name' => $method['name']],
                $method
            );
        }
    }
}
