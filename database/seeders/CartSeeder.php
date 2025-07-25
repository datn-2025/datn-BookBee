<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use App\Models\Book;
use App\Models\BookFormat;

class CartSeeder extends Seeder
{
    public function run()
    {
        // Lấy user test
        $user = User::where('email', 'user@bookbee.com')->first();
        if (!$user) {
            $this->command->error('Không tìm thấy user test!');
            return;
        }

        // Lấy một số sách và định dạng ngẫu nhiên
        $books = Book::take(3)->get();
        $formats = BookFormat::take(2)->get();
        $attributes = Attribute::take(3)->get();
        // Tạo giỏ hàng cho user
        foreach ($books as $book) {
            foreach ($formats as $format) {
                foreach ($attributes as $attribute) {
                    Cart::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'book_format_id' => $format->id,
                        'attribute_value_ids' => $attribute->id,
                        'quantity' => rand(1, 3),
                        'price' => $format->price
                    ]);
                }
            }
        }

        $this->command->info('Đã tạo dữ liệu giỏ hàng test thành công!');
    }
}
