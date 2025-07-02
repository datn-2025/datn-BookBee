<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);
        
        // Lấy ID có sẵn từ các bảng liên quan
        $brandId = Brand::pluck('id')->random();
        $categoryId = Category::pluck('id')->random();
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'brand_id' => $brandId,
            'category_id' => $categoryId,
            'status' => $this->faker->randomElement(['Còn Hàng', 'Hết Hàng Tồn Kho', 'Sắp Ra Mắt', 'Ngừng Kinh Doanh']),
            'cover_image' => 'books/book-' . $this->faker->numberBetween(1, 5) . '.jpg',
            'isbn' => $this->faker->isbn13(),
            'publication_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'page_count' => $this->faker->numberBetween(100, 1000)
        ];
    }
}
