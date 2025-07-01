<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RAuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Nguyễn Nhật Ánh',
                'biography' => 'Tác giả nổi tiếng với các tác phẩm về tuổi thơ và thiếu nhi.',
                'image' => 'authors/nguyen-nhat-anh.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Tony Buổi Sáng',
                'biography' => 'Tác giả ẩn danh với phong cách truyền cảm hứng cho giới trẻ.',
                'image' => 'authors/tony-buoi-sang.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Dale Carnegie',
                'biography' => 'Bậc thầy về nghệ thuật giao tiếp và phát triển bản thân.',
                'image' => 'authors/dale-carnegie.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Nguyễn Ngọc Tư',
                'biography' => 'Tác giả nổi bật với các tác phẩm mang màu sắc đồng quê Nam Bộ.',
                'image' => 'authors/nguyen-ngoc-tu.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Paulo Coelho',
                'biography' => 'Nhà văn Brazil nổi tiếng với tác phẩm "Nhà Giả Kim".',
                'image' => 'authors/paulo-coelho.jpg'
            ],
        ];

        foreach ($authors as $author) {
            Author::updateOrCreate(
                ['id' => $author['id']],
                $author
            );
        }
    }
}
