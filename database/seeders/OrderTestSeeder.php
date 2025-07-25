<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\User;
use App\Models\Address;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use Illuminate\Support\Str;

class OrderTestSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy user test
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Lấy địa chỉ test
        $address = Address::where('user_id', $user->id)->first();
        if (!$address) {
            $address = Address::factory()->create(['user_id' => $user->id]);
        }

        // Lấy sách và định dạng sách
        $book = Book::first();
        $bookFormat = BookFormat::first();

        if (!$book || !$bookFormat) {
            $this->call([BookSeeder::class, BookFormatSeeder::class]);
            $book = Book::first();
            $bookFormat = BookFormat::first();
        }

        // Lấy trạng thái đơn hàng và thanh toán
        $orderStatus = OrderStatus::where('name', 'Chờ xác nhận')->first();
        $paymentStatus = PaymentStatus::where('name', 'Chờ thanh toán')->first();
        $paymentMethod = PaymentMethod::where('name', 'Thanh toán khi nhận hàng')->first();

        // Tạo đơn hàng test
        $order = Order::create([
            'id' => (string) Str::uuid(),
            'order_code' => 'ORD' . date('Ymd') . strtoupper(Str::random(4)),
            'user_id' => $user->id,
            'address_id' => $address->id,
            'total_amount' => 150000,
            'shipping_fee' => 30000,
            'order_status_id' => $orderStatus->id,
            'payment_method_id' => $paymentMethod->id,
            'payment_status_id' => $paymentStatus->id,
            'note' => 'Đơn hàng test'
        ]);

        // Tạo chi tiết đơn hàng
        OrderItem::create([
            'id' => (string) Str::uuid(),
            'order_id' => $order->id,
            'book_id' => $book->id,
            'book_format_id' => $bookFormat->id,
            'quantity' => 2,
            'price' => 60000,
            'total' => 120000
        ]);

        $this->command->info('Đã tạo đơn hàng test thành công!');
    }
}
