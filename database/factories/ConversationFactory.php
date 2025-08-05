<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = \App\Models\Conversation::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = User::inRandomOrder()->whereHas('role', fn($q) => $q->where('name', 'User'))->first();
        $admin = User::inRandomOrder()->whereHas('role', fn($q) => $q->where('name', 'Admin'))->first();
        return [
            'id' => Str::uuid(),
            'customer_id' => User::inRandomOrder()->first()?->id ?? Str::uuid(),
            'admin_id' => User::inRandomOrder()->first()?->id ?? Str::uuid(),
            'last_message_at' => now()->subMinutes(rand(0, 1000)),
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
