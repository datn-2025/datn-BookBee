<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\BookImage;
use App\Models\AttributeValue;
use App\Models\BookAttributeValue;
use App\Models\Category;
use App\Models\Author;
use App\Models\Brand;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Đảm bảo có dữ liệu cần thiết trước khi tạo sách
        $categories = Category::all();
        $authors = Author::all();
        $brands = Brand::all();

        if ($categories->isEmpty() || $authors->isEmpty() || $brands->isEmpty()) {
            $this->command->error('Vui lòng chạy CategorySeeder, AuthorSeeder và BrandSeeder trước!');
            return;
        }

        // Tạo sách cho mỗi danh mục
        foreach ($categories as $category) {
            // Tạo 5 sách cho mỗi danh mục
            for ($i = 0; $i < 5; $i++) {
                $book = Book::create([
                    'id' => (string) Str::uuid(),
                    'title' => 'Sách ' . ($i + 1),
                    'slug' => 'sach-' . ($i + 1) . '-' . Str::random(4),
                    'description' => 'Mô tả sách ' . ($i + 1),
                    'category_id' => $category->id,
                    'author_id' => $authors->random()->id,
                    'brand_id' => $brands->random()->id,
                    'status' => 'available',
                    'cover_image' => 'https://picsum.photos/200/300',
                    'isbn' => 'ISBN' . rand(100000, 999999),
                    'publication_date' => now(),
                    'page_count' => rand(100, 500),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 70% sách có bản bìa cứng
                if (fake()->boolean(70)) {
                    BookFormat::factory()->create([
                        'book_id' => $book->id,
                        'name' => 'Sách Vật Lý',
                    ]);
                }

                // 50% sách có bản ebook
                if (fake()->boolean(50)) {
                    BookFormat::factory()->create([
                        'book_id' => $book->id,
                        'name' => 'Ebook'
                    ]);
                }

                // Tạo 1-3 ảnh cho mỗi sách
                for ($j = 0; $j < rand(1, 3); $j++) {
                    BookImage::create([
                        'book_id' => $book->id,
                        'image_url' => 'books/book-' . fake()->numberBetween(1, 5) . '.jpg'
                    ]);
                }

                // Gắn 3-5 thuộc tính cho mỗi sách
                $attributeValues = AttributeValue::inRandomOrder()
                    ->limit(rand(3, 5))
                    ->get();

                foreach ($attributeValues as $value) {
                    BookAttributeValue::create([
                        'book_id' => $book->id,
                        'attribute_value_id' => $value->id
                    ]);
                }
            }
        }
    }
}
