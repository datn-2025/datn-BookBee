<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = WalletTransaction::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'wallet_id' => Wallet::inRandomOrder()->value('id') ?? (string) Str::uuid(),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'type' => $this->faker->randomElement(['deposit', 'withdraw']),
            'description' => $this->faker->sentence(),
            'related_order_id' => Order::inRandomOrder()->value('id'),
        ];
    }
}
