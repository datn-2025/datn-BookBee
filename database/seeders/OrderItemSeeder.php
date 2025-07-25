<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use App\Models\Book;
use App\Models\BookFormat;
use Illuminate\Support\Str;

class CartSeeder extends Seeder
{
    public function run()
    {
        // Lấy tất cả user để lặp
        $users = User::all();

        foreach ($users as $user) {
            // Tạo 3 sản phẩm ngẫu nhiên cho mỗi user
            for ($i = 0; $i < 3; $i++) {
                $book = Book::inRandomOrder()->first();
                $bookFormat = BookFormat::inRandomOrder()->first();
                $quantity = rand(1, 5);
                $price = 100000; // bạn có thể lấy giá thật nếu có

                Cart::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'book_format_id' => $bookFormat ? $bookFormat->id : null,
                    'quantity' => $quantity,
                    'attribute_value_ids' => json_encode([]), // hoặc tạo attribute giả nếu muốn
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
