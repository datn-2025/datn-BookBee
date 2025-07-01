<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class ROrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Chờ xác nhận', 'color' => '#3498db', 'description' => 'Đơn hàng mới tạo, đang chờ xác nhận từ cửa hàng', 'status' => 1],
            ['name' => 'Đã xác nhận', 'color' => '#2ecc71', 'description' => 'Đơn hàng đã được xác nhận', 'status' => 1],
            ['name' => 'Đang đóng gói', 'color' => '#f39c12', 'description' => 'Đơn hàng đang được đóng gói', 'status' => 1],
            ['name' => 'Đang giao hàng', 'color' => '#9b59b6', 'description' => 'Đơn hàng đang được vận chuyển', 'status' => 1],
            ['name' => 'Đã giao hàng', 'color' => '#27ae60', 'description' => 'Đơn hàng đã được giao thành công', 'status' => 1],
            ['name' => 'Đã hủy', 'color' => '#e74c3c', 'description' => 'Đơn hàng đã bị hủy', 'status' => 1],
            ['name' => 'Hoàn trả', 'color' => '#95a5a6', 'description' => 'Đơn hàng đã được hoàn trả', 'status' => 1],
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
