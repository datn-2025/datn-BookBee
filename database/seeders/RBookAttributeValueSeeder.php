<?php

namespace Database\Seeders;

use App\Models\BookAttributeValue;
use Illuminate\Database\Seeder;

class RBookAttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        $bookAttributes = [
            // Tôi Thấy Hoa Vàng Trên Cỏ Xanh
            ['book_id' => 1, 'attribute_value_id' => 1, 'extra_price' => 0], // Kích thước 13x20.5cm
            ['book_id' => 1, 'attribute_value_id' => 4, 'extra_price' => 0], // Tiếng Việt
            ['book_id' => 1, 'attribute_value_id' => 7, 'extra_price' => 0], // Bìa mềm
            ['book_id' => 1, 'attribute_value_id' => 14, 'extra_price' => 0], // Mọi lứa tuổi
            
            // Nhà Giả Kim
            ['book_id' => 2, 'attribute_value_id' => 2, 'extra_price' => 0], // Kích thước 14.5x20.5cm
            ['book_id' => 2, 'attribute_value_id' => 4, 'extra_price' => 0], // Tiếng Việt
            ['book_id' => 2, 'attribute_value_id' => 7, 'extra_price' => 0], // Bìa mềm
            ['book_id' => 2, 'attribute_value_id' => 13, 'extra_price' => 0], // Trên 16 tuổi
            
            // Đắc Nhân Tâm
            ['book_id' => 3, 'attribute_value_id' => 3, 'extra_price' => 0], // Kích thước 16x24cm
            ['book_id' => 3, 'attribute_value_id' => 4, 'extra_price' => 0], // Tiếng Việt
            ['book_id' => 3, 'attribute_value_id' => 8, 'extra_price' => 10000], // Bìa cứng (+10k)
            ['book_id' => 3, 'attribute_value_id' => 14, 'extra_price' => 0], // Mọi lứa tuổi
            
            // Sapiens
            ['book_id' => 4, 'attribute_value_id' => 3, 'extra_price' => 0], // Kích thước 16x24cm
            ['book_id' => 4, 'attribute_value_id' => 4, 'extra_price' => 0], // Tiếng Việt
            ['book_id' => 4, 'attribute_value_id' => 8, 'extra_price' => 15000], // Bìa cứng (+15k)
            ['book_id' => 4, 'attribute_value_id' => 13, 'extra_price' => 0], // Trên 16 tuổi
            
            // Tuổi Trẻ Đáng Giá Bao Nhiêu
            ['book_id' => 5, 'attribute_value_id' => 1, 'extra_price' => 0], // Kích thước 13x20.5cm
            ['book_id' => 5, 'attribute_value_id' => 4, 'extra_price' => 0], // Tiếng Việt
            ['book_id' => 5, 'attribute_value_id' => 7, 'extra_price' => 0], // Bìa mềm
            ['book_id' => 5, 'attribute_value_id' => 12, 'extra_price' => 0], // Trên 12 tuổi
        ];

        foreach ($bookAttributes as $item) {
            BookAttributeValue::updateOrCreate(
                [
                    'book_id' => $item['book_id'],
                    'attribute_value_id' => $item['attribute_value_id']
                ],
                $item
            );
        }
    }
}
