<?php

namespace App\Http\Controllers;

use App\Models\Recipes;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class RecipesController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'recipes_' . md5(json_encode($request->all()));

        $recipes = Cache::remember($cacheKey, 60, function () use ($request) {

            $query = Recipes::query();

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            }

            if ($request->difficulty && $request->difficulty !== 'all') {
                $query->where('difficulty', $request->difficulty);
            }

            if ($request->sort === 'latest') {
                $query->latest();
            }

            if ($request->sort === 'time') {
                $query->orderBy('cooking_time');
            }

            if ($request->limit) {
                $query->limit((int) $request->limit);
            }

            return $query->get();
        });

        return Inertia::render('recipes/Index', [
            'recipes' => $recipes ?? [],
            'filters' => $request->only(['search', 'difficulty', 'sort', 'limit']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|string',
            'description' => 'required|string',
            'cooking_time' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        Recipes::create($data);

        Cache::flush();

        return redirect()->back()->with('success', 'Recipe created!');
    }
}