<?php

use Illuminate\Support\Facades\Route;
use App\Models\Recipe;

Route::get('/recipes', function () {
    return response()->json([
        'data' => Recipe::latest()->get()
    ]);
});