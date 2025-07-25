<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{    
    public function run()
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            // Tạo 1 hóa đơn cho mỗi đơn hàng (đảm bảo chỉ 1 invoice/order vì unique)
            Invoice::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'type' => 'sale', // Hóa đơn bán hàng
                    'invoice_date' => now(),
                    'total_amount' => $order->total_amount,
                ]
            );
        }
    }
}
