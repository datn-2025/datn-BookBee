<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+6 months');

        return [
            'code' => strtoupper(Str::random(8)),
            'description' => $this->faker->sentence,
            'discount_percent' => $this->faker->randomFloat(2, 5, 50),
            'max_discount' => $this->faker->randomFloat(2, 50000, 500000),
            'min_order_value' => $this->faker->randomFloat(2, 100000, 1000000),
            'valid_from' => $startDate,
            'valid_to' => $endDate,
            'quantity' => $this->faker->numberBetween(50, 1000),
            'status' => $this->faker->randomElement(['active', 'inactive'])
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'valid_from' => now(),
                'valid_to' => now()->addMonths(3)
            ];
        });
    }

    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
                'valid_from' => now()->subMonths(2),
                'valid_to' => now()->subDays(1)
            ];
        });
    }
}
