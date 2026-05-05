<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'cooking_time' => $this->faker->numberBetween(15, 90),
            'difficulty' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'image' => 'https://picsum.photos/seed/' . rand(1, 1000) . '/800/500',
            'calories' => $this->faker->numberBetween(200, 800),
            'instructions' => collect(range(1, rand(3, 6)))
                ->map(fn($i) => "Step $i: " . $this->faker->sentence(12))
                ->implode("\n"),
        ];
    }
}
