<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'title',
        'image',
        'description',
        'difficulty',
        'cooking_time',
        'prep_time',
        'servings',
        'calories',
        'protein',
        'carbs',
        'fat',
        'ingredients',
        'instructions',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
    ];
}