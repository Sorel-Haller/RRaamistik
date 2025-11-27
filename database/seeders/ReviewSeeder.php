<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review; 
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            Review::factory()
            ->count(rand(5, 8))
            ->create(['product_id' => $product->id]);
        });
    }
}
