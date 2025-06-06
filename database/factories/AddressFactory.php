<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recipient_name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'address_detail' => $this->faker->streetAddress,
            'ward' => $this->faker->city,
            'district' => $this->faker->city,
            'city' => $this->faker->city,
            'is_default' => false
        ];
    }
}
