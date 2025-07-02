<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookFormat;
use App\Models\BookImage;
use App\Models\AttributeValue;
use App\Models\BookAttributeValue;
use App\Models\Category;
use App\Models\Author;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RBookSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $authors = Author::all();
        $brands = Brand::all();
        $attributeValues = AttributeValue::all()->groupBy('attribute_id');

        if ($categories->isEmpty() || $authors->isEmpty() || $brands->isEmpty() || $attributeValues->isEmpty()) {
            $this->command->error('Vui lòng seed đầy đủ Categories, Authors, Brands, AttributeValues trước khi seed Books!');
            return;
        }

        $books = [
            [
                'title' => 'Đắc Nhân Tâm',
                'slug' => Str::slug('Đắc Nhân Tâm') . '-' . Str::random(4),
                'description' => 'Cuốn sách kinh điển về nghệ thuật giao tiếp và thuyết phục.',
                'category' => 'Sách Kinh Tế',
                'author' => 'Dale Carnegie',
                'brand' => 'NXB Trẻ',
                'cover_image' => 'books/dac-nhan-tam.jpg',
                'isbn' => '8934974123456',
                'page_count' => 320,
            ],
            [
                'title' => 'Nhà Giả Kim',
                'slug' => Str::slug('Nhà Giả Kim') . '-' . Str::random(4),
                'description' => 'Hành trình khám phá bản thân và giấc mơ cuộc đời.',
                'category' => 'Sách Văn Học',
                'author' => 'Paulo Coelho',
                'brand' => 'NXB Văn học',
                'cover_image' => 'books/nha-gia-kim.jpg',
                'isbn' => '8934974126789',
                'page_count' => 240,
            ],
            [
                'title' => 'Totto-chan bên cửa sổ',
                'slug' => Str::slug('Totto-chan bên cửa sổ') . '-' . Str::random(4),
                'description' => 'Câu chuyện cảm động về tuổi thơ và giáo dục nhân văn.',
                'category' => 'Sách Thiếu Nhi',
                'author' => 'Tetsuko Kuroyanagi',
                'brand' => 'NXB Kim Đồng',
                'cover_image' => 'books/totto-chan.jpg',
                'isbn' => '8934974129999',
                'page_count' => 208,
            ],
        ];

        foreach ($books as $item) {
            $category = Category::where('name', $item['category'])->first();
            $author = Author::where('name', $item['author'])->first();
            $brand = Brand::where('name', $item['brand'])->first();

            if (!$category || !$author || !$brand) {
                $this->command->warn("Thiếu dữ liệu danh mục/tác giả/NXB cho sách {$item['title']}, bỏ qua...");
                continue;
            }

            $book = Book::create([
                'id' => (string) Str::uuid(),
                'title' => $item['title'],
                'slug' => $item['slug'],
                'description' => $item['description'],
                'category_id' => $category->id,
                'author_id' => $author->id,
                'brand_id' => $brand->id,
                'status' => 'available',
                'cover_image' => $item['cover_image'],
                'isbn' => $item['isbn'],
                'publication_date' => now(),
                'page_count' => $item['page_count'],
            ]);

            // Thêm định dạng sách
            BookFormat::create([
                'book_id' => $book->id,
                'format_name' => 'Bìa cứng',
                'price' => rand(150000, 300000),
            ]);

            BookFormat::create([
                'book_id' => $book->id,
                'format_name' => 'Ebook',
                'price' => rand(80000, 150000), 
            ]);

            // Thêm hình ảnh phụ
            BookImage::create([
                'book_id' => $book->id,
                'image_url' => 'books/phu-' . Str::random(4) . '.jpg'
            ]);

            // Gắn thuộc tính cho sách (mỗi nhóm lấy 1 giá trị ngẫu nhiên)
            foreach ($attributeValues as $group) {
                $value = $group->random();
                BookAttributeValue::create([
                    'book_id' => $book->id,
                    'attribute_value_id' => $value->id,
                ]);
            }
        }
    }
}
