<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'user_id' => User::inRandomOrder()->first()->id,    // lấy ngẫu nhiên user có sẵn
            'book_id' => Book::inRandomOrder()->first()->id,   // lấy ngẫu nhiên sách có sẵn
            'book_format_id' => BookFormat::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'attribute_value_ids' => json_encode([1, 2]), // giả sử attribute id mẫu
            'price' => $this->faker->randomFloat(2, 10000, 500000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
