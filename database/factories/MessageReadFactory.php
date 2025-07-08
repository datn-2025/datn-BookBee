<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MessageRead>
 */
class MessageReadFactory extends Factory
{
    protected $model = \App\Models\MessageRead::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $message = Message::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        return [
            'id' => Str::uuid(),
            'message_id' => optional(Message::inRandomOrder()->first())->id ?? Message::factory(),
            'user_id' => optional(User::inRandomOrder()->first())->id ?? User::factory(),
            'read_at' => now()->subMinutes(rand(1, 1000)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
