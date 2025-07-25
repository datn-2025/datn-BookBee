<?php

namespace Database\Factories;

use App\Models\BookFormat;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFormatFactory extends Factory
{
    protected $model = BookFormat::class;

    public function definition(): array
    {
        $prices = [
            'Ebook' => [30000, 80000],       // Giá ebook thường thấp hơn
            'Sách Vật Lý' => [50000, 150000],  // Giá cao nhất
        ];

        $format = $this->faker->randomElement(array_keys($prices));
        $priceRange = $prices[$format];
        $isPhysical = in_array($format, ['Sách Vật Lý']);

        return [
            'book_id' => Book::factory(),
            'format_name' => $format,
            'price' => $this->faker->numberBetween($priceRange[0], $priceRange[1]),
            'discount' => $this->faker->optional(0.3)->numberBetween(5, 25), // 30% chance of having a discount
            'stock' => $isPhysical ? $this->faker->numberBetween(10, 100) : null, // Chỉ format vật lý mới có stock
            'file_url' => !$isPhysical ? 'ebooks/book.pdf' : null,
            'sample_file_url' => !$isPhysical ? 'ebooks/sample.pdf' : null,
            'allow_sample_read' => !$isPhysical // Chỉ ebook mới cho phép đọc thử
        ];
    }
}
