<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Recipe;

Route::get('/recipes', function () {
    return Recipe::latest()->get();
});