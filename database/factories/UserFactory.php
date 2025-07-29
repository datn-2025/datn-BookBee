<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'avatar' => $this->faker->imageUrl(200, 200, 'people'),
            'password' => bcrypt('password'), // hoặc dùng Hash::make nếu muốn bảo mật hơn
            'phone' => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement(['Hoạt Động', 'Bị Khóa', 'Chưa kích Hoạt']),
            // 'role_id' => Role::inRandomOrder()->value('id'), // Đã chuyển sang n-n, không dùng role_id nữa
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
