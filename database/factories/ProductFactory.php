<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(12),
            'price' => $this->faker->randomFloat(2, 10, 500), // Hind vahemikus 10-500
            'sku' => strtoupper($this->faker->unique()->bothify('??-###')), // Genereerib suvalise SKU nt AB-123
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'image' => 'https://picsum.photos/seed/' . rand(1, 1000) . '/400/300',
        ];
    }
}
