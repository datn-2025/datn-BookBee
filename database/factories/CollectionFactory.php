<?php

namespace Database\Factories;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Collection>
 */
class CollectionFactory extends Factory
{
    protected $model = Collection::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        $name = $this->faker->words(3, true); // ví dụ: "Sách hay mỗi ngày"

        return [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'cover_image' => 'collections/' . Str::slug($name) . '.jpg',
            'description' => $this->faker->paragraph(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
