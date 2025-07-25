<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookCollection;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookCollection>
 */
class BookCollectionFactory extends Factory
{
    protected $model = BookCollection::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            return [
            'id' => (string) Str::uuid(),
            'book_id' => Book::inRandomOrder()->value('id') ?? Str::uuid(), // nếu chưa có dữ liệu, tạo UUID giả
            'collection_id' => Collection::inRandomOrder()->value('id') ?? Str::uuid(),
            'order_column' => $this->faker->numberBetween(1, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
    }
}
