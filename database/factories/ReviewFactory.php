<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->boolean(80) ? $this->faker->paragraph : null, // 80% chance of having a comment
            'status' => $this->faker->randomElement(['visible', 'hidden']),
            'admin_response' => $this->faker->optional()->text(),
        ];
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved'
            ];
        });
    }

    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending'
            ];
        });
    }
}
