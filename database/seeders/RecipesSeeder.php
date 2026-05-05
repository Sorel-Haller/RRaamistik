<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;

class RecipesSeeder extends Seeder
{
    public function run(): void
    {
        Recipe::factory()->count(50)->create();
    }
}
