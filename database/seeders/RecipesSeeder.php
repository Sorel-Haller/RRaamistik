<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipes;

class RecipesSeeder extends Seeder
{
    public function run(): void
    {
        Recipes::insert([
            [
                'title' => 'Spaghetti Carbonara',
                'image' => 'https://picsum.photos/400/300?1',
                'description' => 'Classic Italian pasta with eggs, cheese, pancetta and pepper.',
                'cooking_time' => 25,
                'difficulty' => 'intermediate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Chicken Curry',
                'image' => 'https://picsum.photos/400/300?2',
                'description' => 'Spicy and creamy chicken curry with aromatic spices.',
                'cooking_time' => 40,
                'difficulty' => 'advanced',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Avocado Toast',
                'image' => 'https://picsum.photos/400/300?3',
                'description' => 'Simple and healthy avocado toast with lemon and salt.',
                'cooking_time' => 10,
                'difficulty' => 'beginner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Beef Stir Fry',
                'image' => 'https://picsum.photos/400/300?4',
                'description' => 'Quick beef stir fry with vegetables and soy sauce.',
                'cooking_time' => 20,
                'difficulty' => 'intermediate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pancakes',
                'image' => 'https://picsum.photos/400/300?5',
                'description' => 'Fluffy pancakes perfect for breakfast.',
                'cooking_time' => 15,
                'difficulty' => 'beginner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Grilled Salmon',
                'image' => 'https://picsum.photos/400/300?6',
                'description' => 'Delicious grilled salmon with herbs and lemon.',
                'cooking_time' => 30,
                'difficulty' => 'intermediate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}