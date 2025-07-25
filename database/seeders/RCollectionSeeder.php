<?php

namespace Database\Seeders;

use App\Models\Collection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = [
            [
                'name' => 'Combo Sách Kinh Điển',
                'description' => 'Bộ sách kinh điển giúp bạn phát triển tư duy và kỹ năng sống.',
                'cover_image' => 'collections/kinh-dien.jpg',
                'combo_price' => 299000,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
            ],
            [
                'name' => 'Combo Thiếu Nhi Yêu Thích',
                'description' => 'Tuyển tập truyện thiếu nhi được yêu thích nhất năm.',
                'cover_image' => 'collections/thieu-nhi.jpg',
                'combo_price' => 199000,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
            ],
            [
                'name' => 'Tủ Sách Truyền Cảm Hứng',
                'description' => 'Sách giúp bạn tìm lại động lực và cảm hứng trong cuộc sống.',
                'cover_image' => 'collections/cam-hung.jpg',
                'combo_price' => 259000,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
            ],
        ];
         foreach ($collections as $item) {
            Collection::create([
                'id' => (string) Str::uuid(),
                'name' => $item['name'],
                'description' => $item['description'],
                'cover_image' => $item['cover_image'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(4),
                'status' => 'active',
                'start_date' => $item['start_date'],
                'end_date' => $item['end_date'],
                'combo_price' => $item['combo_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    

}
