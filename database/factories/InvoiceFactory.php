<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition()
    {
        // Lấy ngẫu nhiên 1 đơn hàng đã tồn tại
        $order = Order::inRandomOrder()->first();

        return [
            'order_id' => $order ? $order->id : Order::factory(),
            'type' => 'sale', // Mặc định là hóa đơn bán hàng
            'invoice_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'total_amount' => $order ? $order->total_amount : $this->faker->randomFloat(2, 100000, 10000000),
        ];
    }
}
