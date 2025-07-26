<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusFactory extends Factory
{
    protected $model = OrderStatus::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Chờ xác nhận',
                'Đã xác nhận',
                'Đang chuẩn bị',
                'Đang giao hàng',
                'Đã giao thành công',
                'Đã nhận hàng',
                'Thành công',
                'Giao thất bại',
                'Đã hủy',
                'Đang hoàn tiền',
                'Đã hoàn tiền',
            ])
        ];
    }
}
