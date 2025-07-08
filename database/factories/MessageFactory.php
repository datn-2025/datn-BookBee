<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = \App\Models\Message::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $conversation = Conversation::inRandomOrder()->first();

        $sender = User::inRandomOrder()->first();
        return [
            'id' => Str::uuid(),
            'conversation_id' => optional(Conversation::inRandomOrder()->first())->id ?? Conversation::factory(),
            'sender_id' => optional(User::inRandomOrder()->first())->id ?? User::factory(),
            'content' => $this->faker->sentence,
            'type' => 'text',
            'file_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
