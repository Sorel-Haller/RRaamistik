<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipes extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'title',
        'image',
        'description',
        'ingredients',
        'cooking_time',
        'difficulty',
    ];
}