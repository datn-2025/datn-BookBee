<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\Address;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealisticOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy users có role User
        $users = User::whereHas('role', function($q) {
            $q->where('name', 'User');
        })->take(5)->get();

        if ($users->isEmpty()) {
            $this->command->info('Không có user nào với role User. Tạo user mới...');
            $users = User::factory(3)->create();
        }

        // Lấy các trạng thái cần thiết
        $orderStatuses = OrderStatus::all();
        $paymentStatuses = PaymentStatus::all();
        $paymentMethods = PaymentMethod::all();

        if ($orderStatuses->isEmpty() || $paymentStatuses->isEmpty() || $paymentMethods->isEmpty()) {
            $this->command->error('Thiếu dữ liệu cơ bản. Chạy các seeder sau trước:');
            $this->command->error('- OrderStatusSeeder');
            $this->command->error('- PaymentStatusSeeder');
            $this->command->error('- PaymentMethodSeeder');
            return;
        }

        foreach ($users as $user) {
            // Tạo địa chỉ nếu chưa có
            $address = $user->addresses()->first();
            if (!$address) {
                $address = Address::factory()->create(['user_id' => $user->id]);
            }

            // Tạo 2-4 đơn hàng cho mỗi user (thực tế hơn)
            $numOrders = rand(2, 4);
            
            for ($i = 0; $i < $numOrders; $i++) {
                $order = Order::create([
                    'id' => (string) Str::uuid(),
                    'order_code' => 'ORD' . date('Ymd') . strtoupper(Str::random(6)),
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                    'total_amount' => 0, // Sẽ tính sau
                    'shipping_fee' => rand(0, 1) ? 30000 : 0, // 50% có phí ship
                    'order_status_id' => $orderStatuses->random()->id,
                    'payment_method_id' => $paymentMethods->random()->id,
                    'payment_status_id' => $paymentStatuses->random()->id,
                    'delivery_method' => collect(['delivery', 'ebook', 'pickup'])->random(),
                    'note' => $this->getRandomNote(),
                    'created_at' => now()->subDays(rand(1, 30)), // Đơn hàng trong 30 ngày qua
                ]);

                // Tạo 1-3 sản phẩm cho mỗi đơn hàng (thực tế)
                $numItems = rand(1, 3);
                $totalAmount = 0;

                $selectedBooks = Book::inRandomOrder()->take($numItems)->get();

                foreach ($selectedBooks as $book) {
                    $bookFormat = BookFormat::where('book_id', $book->id)->first();
                    
                    if (!$bookFormat) {
                        continue; // Bỏ qua nếu không có format
                    }
                    
                    // Số lượng thực tế: 1-2 cuốn (hiếm khi mua nhiều)
                    $quantity = rand(1, 2);
                    $price = $bookFormat->price;
                    $itemTotal = $price * $quantity;
                    
                    OrderItem::create([
                        'id' => (string) Str::uuid(),
                        'order_id' => $order->id,
                        'book_id' => $book->id,
                        'book_format_id' => $bookFormat->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                        'is_combo' => false,
                        'item_type' => 'book',
                    ]);
                    
                    $totalAmount += $itemTotal;
                }

                // Cập nhật tổng tiền đơn hàng (bao gồm phí ship)
                $order->update([
                    'total_amount' => $totalAmount + $order->shipping_fee
                ]);

                $this->command->info("Tạo đơn hàng {$order->order_code} với {$numItems} sản phẩm, tổng " . $order->orderItems->sum('quantity') . " cuốn");
            }
        }

        $this->command->info('Đã tạo dữ liệu đơn hàng thực tế thành công!');
        
        // Hiển thị thống kê
        $totalOrders = Order::count();
        $avgItemsPerOrder = Order::with('orderItems')->get()->avg(function($order) {
            return $order->orderItems->sum('quantity');
        });
        
        $this->command->info("Thống kê:");
        $this->command->info("- Tổng số đơn hàng: {$totalOrders}");
        $this->command->info("- Trung bình sản phẩm/đơn hàng: " . round($avgItemsPerOrder, 1));
    }

    /**
     * Tạo ghi chú ngẫu nhiên cho đơn hàng
     */
    private function getRandomNote(): ?string
    {
        $notes = [
            null, // 40% không có ghi chú
            null,
            'Giao hàng giờ hành chính',
            'Gọi trước khi giao',
            'Để ở bảo vệ nếu không có người',
            'Giao hàng cuối tuần',
            'Đóng gói cẩn thận',
        ];

        return collect($notes)->random();
    }
}