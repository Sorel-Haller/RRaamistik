<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'title', 'description', 'cooking_time', 'prep_time', 'difficulty', 
        'image', 'servings', 'calories', 'protein', 'carbs', 'fat', 
        'ingredients', 'instructions'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
    ];
}