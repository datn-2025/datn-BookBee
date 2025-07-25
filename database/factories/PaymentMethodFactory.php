<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'COD (thanh toán khi nhận hàng)',
                'Chuyển khoản ngân hàng',
                'VNPAY',
                'Thanh toán vnpay'
            ])
        ];
    }
}
