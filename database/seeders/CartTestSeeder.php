<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\User;

class CartTestSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy user test
        $user = User::where('email', 'user@bookbee.com')->first();

        if (!$user) {
            $this->command->error('Không tìm thấy user test!');
            return;
        }

        // Lấy 3 sách đầu tiên
        $books = Book::take(3)->get();

        foreach ($books as $book) {
            // Lấy format đầu tiên của sách
            $format = BookFormat::where('book_id', $book->id)->first();

            if ($format) {
                Cart::create([
                    'user_id' => $user->id,
                    'book_format_id' => $format->id,
                    'quantity' => rand(1, 3),
                ]);
            }
        }

        $this->command->info('Đã thêm sản phẩm vào giỏ hàng của user test!');
    }
}
