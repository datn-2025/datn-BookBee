<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = [
            // Hóa đơn cho đơn hàng 1 của Nguyễn Văn A
            [
                'code' => 'HD' . strtoupper(Str::random(10)),
                'order_id' => 1,
                'user_id' => 2, // Nguyễn Văn A
                'subtotal' => 450000,
                'shipping_fee' => 30000,
                'discount_amount' => 45000, // Từ voucher GIAMGIA10
                'total_amount' => 435000, // 450k - 45k + 30k
                'tax_amount' => 0,
                'status' => 'paid',
                'payment_method_id' => 1, // COD
                'payment_status' => 'paid',
                'issued_at' => Carbon::now()->subDays(15),
                'due_date' => Carbon::now()->subDays(15)->addDays(7),
                'notes' => 'Đã thanh toán khi nhận hàng',
                'billing_name' => 'Nguyễn Văn A',
                'billing_phone' => '0987123456',
                'billing_email' => 'customer1@example.com',
                'billing_address' => 'Số 1, Đường ABC, Phường 1, Quận 1, TP.HCM',
            ],
            
            // Hóa đơn cho đơn hàng 2 của Trần Thị B
            [
                'code' => 'HD' . strtoupper(Str::random(10)),
                'order_id' => 2,
                'user_id' => 3, // Trần Thị B
                'subtotal' => 350000,
                'shipping_fee' => 0, // Miễn phí ship từ voucher FSHIP
                'discount_amount' => 30000, // Từ voucher FSHIP
                'total_amount' => 320000, // 350k - 30k + 0k
                'tax_amount' => 0,
                'status' => 'paid',
                'payment_method_id' => 2, // Chuyển khoản
                'payment_status' => 'paid',
                'issued_at' => Carbon::now()->subDays(3),
                'due_date' => Carbon::now()->subDays(3)->addDays(7),
                'notes' => 'Đã thanh toán qua chuyển khoản ngân hàng',
                'billing_name' => 'Trần Thị B',
                'billing_phone' => '0912345678',
                'billing_email' => 'customer2@example.com',
                'billing_address' => 'Số 45, Đường DEF, Phường 3, Quận 3, TP.HCM',
            ],
            
            // Hóa đơn cho đơn hàng 3 của Lê Văn C (đã hủy)
            [
                'code' => 'HD' . strtoupper(Str::random(10)),
                'order_id' => 3,
                'user_id' => 4, // Lê Văn C
                'subtotal' => 350000,
                'shipping_fee' => 0, // Miễn phí ship từ voucher TRAMGIAO100K
                'discount_amount' => 100000, // Từ voucher TRAMGIAO100K
                'total_amount' => 250000, // 350k - 100k + 0k
                'tax_amount' => 0,
                'status' => 'cancelled',
                'payment_method_id' => 3, // Momo
                'payment_status' => 'refunded',
                'issued_at' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->subDays(5)->addDays(7),
                'cancelled_at' => Carbon::now()->subDays(4),
                'notes' => 'Đơn hàng đã hủy, đã hoàn tiền',
                'billing_name' => 'Lê Văn C',
                'billing_phone' => '0978456123',
                'billing_email' => 'customer3@example.com',
                'billing_address' => 'Số 78, Đường GHI, Phường 4, Quận 4, TP.HCM',
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::updateOrCreate(
                ['order_id' => $invoice['order_id']],
                $invoice
            );
        }
    }
}
