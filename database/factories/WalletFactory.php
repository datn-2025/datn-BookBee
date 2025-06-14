<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'user_id' => User::inRandomOrder()->value('id') ?? (string) Str::uuid(),
            'balance' => $this->faker->randomFloat(2, 10000, 10000000),
        ];
    }
}
