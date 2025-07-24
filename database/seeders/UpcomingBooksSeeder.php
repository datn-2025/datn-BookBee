<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\BookImage;
use App\Models\BookFormat;
use App\Models\Author;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UpcomingBooksSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ nếu có
        Book::where('status', 'Sắp Ra Mắt')
            ->whereIn('slug', [
                'cuon-sach-sap-ra-mat-1',
                'cuon-sach-sap-ra-mat-2', 
                'cuon-sach-sap-ra-mat-3',
                'cuon-sach-sap-ra-mat-4'
            ])->delete();

        // Lấy dữ liệu cần thiết
        $author = Author::first() ?? Author::create([
            'id' => Str::uuid(),
            'name' => 'Tác giả mẫu',
            'slug' => 'tac-gia-mau',
            'biography' => 'Tiểu sử tác giả mẫu'
        ]);

        $brand = Brand::first() ?? Brand::create([
            'id' => Str::uuid(),
            'name' => 'Nhà xuất bản mẫu',
            'slug' => 'nha-xuat-ban-mau',
            'description' => 'Mô tả nhà xuất bản'
        ]);

        $category = Category::first() ?? Category::create([
            'id' => Str::uuid(),
            'name' => 'Văn học',
            'slug' => 'van-hoc',
            'description' => 'Sách văn học'
        ]);

        // Tạo sách sắp ra mắt
        $upcomingBooks = [
            [
                'title' => 'Cuốn Sách Sắp Ra Mắt 1',
                'slug' => 'cuon-sach-sap-ra-mat-1',
                'description' => 'Đây là một cuốn sách sắp được phát hành với nội dung hấp dẫn và thú vị.',
                'publication_date' => Carbon::now()->addDays(30), // 30 ngày sau
                'isbn' => 'ISBN-UPCOMING-001',
                'page_count' => 200,
                'status' => 'Sắp Ra Mắt'
            ],
            [
                'title' => 'Cuốn Sách Sắp Ra Mắt 2',
                'slug' => 'cuon-sach-sap-ra-mat-2',
                'description' => 'Một tác phẩm mới đầy hứa hẹn từ tác giả nổi tiếng.',
                'publication_date' => Carbon::now()->addDays(45), // 45 ngày sau
                'isbn' => 'ISBN-UPCOMING-002',
                'page_count' => 320,
                'status' => 'Sắp Ra Mắt'
            ],
            [
                'title' => 'Cuốn Sách Sắp Ra Mắt 3',
                'slug' => 'cuon-sach-sap-ra-mat-3',
                'description' => 'Cuốn sách được mong chờ nhất năm với câu chuyện độc đáo.',
                'publication_date' => Carbon::now()->addDays(60), // 60 ngày sau
                'isbn' => 'ISBN-UPCOMING-003',
                'page_count' => 275,
                'status' => 'Sắp Ra Mắt'
            ],
            [
                'title' => 'Cuốn Sách Sắp Ra Mắt 4',
                'slug' => 'cuon-sach-sap-ra-mat-4',
                'description' => 'Phần tiếp theo của series bestseller được yêu thích.',
                'publication_date' => Carbon::now()->addDays(75), // 75 ngày sau
                'isbn' => 'ISBN-UPCOMING-004',
                'page_count' => 400,
                'status' => 'Sắp Ra Mắt'
            ]
        ];

        foreach ($upcomingBooks as $bookData) {
            // Tạo sách
            $book = Book::create([
                'id' => Str::uuid(),
                'title' => $bookData['title'],
                'slug' => $bookData['slug'],
                'description' => $bookData['description'],
                'author_id' => $author->id,
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'status' => $bookData['status'],
                'isbn' => $bookData['isbn'],
                'publication_date' => $bookData['publication_date'],
                'page_count' => $bookData['page_count']
            ]);

            // Tạo liên kết tác giả
            $book->authors()->attach($author->id, [
                'id' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Tạo format giá
            BookFormat::create([
                'id' => Str::uuid(),
                'book_id' => $book->id,
                'format_name' => 'Sách Vật Lý',
                'price' => rand(150000, 350000), // Giá từ 150k đến 350k
                'discount' => rand(0, 20), // Giảm giá 0-20%
                'stock' => rand(50, 200) // Kho từ 50-200 cuốn
            ]);

            // Tạo ảnh mẫu (nếu cần)
            BookImage::create([
                'id' => Str::uuid(),
                'book_id' => $book->id,
                'image_path' => 'images/books/sample-upcoming-book.jpg', // Ảnh mẫu
                'is_primary' => true
            ]);
        }

        $this->command->info('Đã tạo ' . count($upcomingBooks) . ' sách sắp ra mắt thành công!');
    }
}
