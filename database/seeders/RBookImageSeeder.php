<?php

namespace Database\Seeders;

use App\Models\BookImage;
use Illuminate\Database\Seeder;

class RBookImageSeeder extends Seeder
{
    public function run(): void
    {
        $bookImages = [
            // Hình ảnh cho sách Đắc Nhân Tâm (book_id = 1)
            [
                'book_id' => 1,
                'image_path' => 'books/dac-nhan-tam.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            [
                'book_id' => 1,
                'image_path' => 'books/dac-nhan-tam-2.jpg',
                'is_primary' => 0,
                'display_order' => 2,
            ],
            
            // Hình ảnh cho sách Nhà Giả Kim (book_id = 2)
            [
                'book_id' => 2,
                'image_path' => 'books/nha-gia-kim.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách Đời Ngắn Đừng Ngủ Dài (book_id = 3)
            [
                'book_id' => 3,
                'image_path' => 'books/doi-ngan-dung-ngu-dai.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách Tôi Tài Giỏi Bạn Cũng Thế (book_id = 4)
            [
                'book_id' => 4,
                'image_path' => 'books/toi-tai-gioi-ban-cung-the.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách Đừng Bao Giờ Đi Ăn Một Mình (book_id = 5)
            [
                'book_id' => 5,
                'image_path' => 'books/dung-bao-gio-di-an-mot-minh.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách Dạy Con Làm Giàu (book_id = 6)
            [
                'book_id' => 6,
                'image_path' => 'books/day-con-lam-giau.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách 7 Vùng Tâm Lý (book_id = 7)
            [
                'book_id' => 7,
                'image_path' => 'books/7-vung-tam-ly.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách Tiền Không Mua Được Gì (book_id = 8)
            [
                'book_id' => 8,
                'image_path' => 'books/tien-khong-mua-duoc-gi.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách Dạy Con Làm Giàu - Tập 2 (book_id = 9)
            [
                'book_id' => 9,
                'image_path' => 'books/day-con-lam-giau-tap-2.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
            
            // Hình ảnh cho sách Đắc Nhân Tâm (bản đặc biệt) (book_id = 10)
            [
                'book_id' => 10,
                'image_path' => 'books/dac-nhan-tam-dac-biet.jpg',
                'is_primary' => 1,
                'display_order' => 1,
            ],
        ];

        foreach ($bookImages as $image) {
            BookImage::updateOrCreate(
                [
                    'book_id' => $image['book_id'],
                    'image_path' => $image['image_path']
                ],
                $image
            );
        }
    }
}
