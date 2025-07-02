<?php

namespace Database\Seeders;

use App\Models\BookFormat;
use Illuminate\Database\Seeder;

class RBookFormatSeeder extends Seeder
{
    public function run(): void
    {
        $formats = [
            [
                'book_id' => 1, // Tôi Thấy Hoa Vàng Trên Cỏ Xanh
                'format' => 'Bìa mềm',
                'price' => 100000,
                'discount_percent' => 10,
                'quantity' => 50,
                'status' => 1,
            ],
            [
                'book_id' => 1, // Tôi Thấy Hoa Vàng Trên Cỏ Xanh
                'format' => 'Bìa cứng',
                'price' => 150000,
                'discount_percent' => 5,
                'quantity' => 30,
                'status' => 1,
            ],
            [
                'book_id' => 2, // Nhà Giả Kim
                'format' => 'Bìa mềm',
                'price' => 120000,
                'discount_percent' => 15,
                'quantity' => 45,
                'status' => 1,
            ],
            [
                'book_id' => 3, // Đắc Nhân Tâm
                'format' => 'Bìa mềm',
                'price' => 110000,
                'discount_percent' => 10,
                'quantity' => 60,
                'status' => 1,
            ],
            [
                'book_id' => 4, // Sapiens
                'format' => 'Bìa cứng',
                'price' => 250000,
                'discount_percent' => 20,
                'quantity' => 35,
                'status' => 1,
            ],
            [
                'book_id' => 5, // Tuổi Trẻ Đáng Giá Bao Nhiêu
                'format' => 'Bìa mềm',
                'price' => 90000,
                'discount_percent' => 5,
                'quantity' => 40,
                'status' => 1,
            ],
        ];

        foreach ($formats as $format) {
            BookFormat::updateOrCreate(
                [
                    'book_id' => $format['book_id'],
                    'format' => $format['format']
                ],
                $format
            );
        }
    }
}
