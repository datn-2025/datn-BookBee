<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Book;
use App\Models\BookFormat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ROrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả các đơn hàng
        $orders = Order::all();

        // Lặp qua từng đơn hàng và tạo các OrderItem
        foreach ($orders as $order) {
            // Giả sử mỗi đơn hàng có từ 1 đến 3 sản phẩm
            $numOfItems = rand(1, 3);
            
            for ($i = 0; $i < $numOfItems; $i++) {
                // Chọn ngẫu nhiên một quyển sách
                $book = Book::inRandomOrder()->first();
                // Chọn ngẫu nhiên một định dạng sách
                $bookFormat = BookFormat::where('book_id', $book->id)->inRandomOrder()->first();
                // Chọn số lượng sản phẩm ngẫu nhiên
                $quantity = rand(1, 5);
                // Giá của sách
                $price = $bookFormat->price;
                // Tính tổng tiền
                $total = $price * $quantity;

                // Tạo OrderItem mới
                $orderItem = OrderItem::create([
                    'id' => (string) Str::uuid(),
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'book_format_id' => $bookFormat->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Giả sử bạn có thêm một số thuộc tính cho OrderItem
                // Thêm các AttributeValue nếu có
                $attributeValues = $book->attributeValues->random(rand(1, 2)); // Lấy ngẫu nhiên 1-2 attributeValue cho sản phẩm
                
                // Dùng phương thức attach() để thêm liên kết giữa OrderItem và AttributeValue
                foreach ($attributeValues as $attributeValue) {
                    // Thêm vào bảng liên kết order_item_attribute_values
                    $orderItem->attributeValues()->attach($attributeValue->id);
                }
            }
        }
    }
}
