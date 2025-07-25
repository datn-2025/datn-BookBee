<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RWishlistSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('role', function($query) {
            $query->where('name', 'User');
        })->get();

        // Kiểm tra nếu không có user nào
        if ($users->isEmpty()) {
            $this->command->error('Không có người dùng nào trong hệ thống. Vui lòng seed bảng users trước!');
            return;
        }

        foreach ($users as $user) {
            // Mỗi user có từ 0 đến 2 sách trong wishlist
            $books = Book::inRandomOrder()->limit(rand(0, 2))->get();

            foreach ($books as $book) {
                Wishlist::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'book_id' => $book->id
                ]);
            }
        }
    }
}
