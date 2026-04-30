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
            'prep_time' => $this->faker->numberBetween(5, 30),
            'difficulty' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'image' => 'https://picsum.photos/800/500?' . rand(1, 1000),
            'servings' => $this->faker->numberBetween(1, 6),
            
            'calories' => $this->faker->numberBetween(200, 800),
            'protein' => $this->faker->numberBetween(10, 50) . 'g',
            'carbs' => $this->faker->numberBetween(20, 100) . 'g',
            'fat' => $this->faker->numberBetween(5, 40) . 'g',

            'ingredients' => collect(range(1, rand(3, 8)))->map(fn() => [
                'amount' => rand(1, 500) . $this->faker->randomElement(['g', 'ml', ' pcs', ' units']),
                'name' => $this->faker->word()
            ])->toArray(),

            'instructions' => collect(range(1, rand(2, 5)))->map(fn($i) => [
                'title' => 'Samm ' . $i,
                'description' => $this->faker->sentence(10)
            ])->toArray(),
        ];
    }

}
