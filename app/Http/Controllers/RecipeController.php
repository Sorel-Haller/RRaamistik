<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class RecipeController extends Controller
{
    /**
     * Display a listing of recipes
     */
    public function index(Request $request)
    {
        $cacheKey = 'recipes_' . md5(json_encode($request->all()));

        $recipes = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = Recipe::query();

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
            'filters' => $request->only([
                'search',
                'difficulty',
                'sort',
                'limit',
            ]),
        ]);
    }

    /**
     * Show create page
     */
    public function create()
    {
        return Inertia::render('recipes/Create');
    }

    /**
     * Store new recipe
     */
    public function store(Request $request)
    {
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'nullable|string',
        'description' => 'required|string',

        'difficulty' => 'required|in:beginner,intermediate,advanced',

        'cooking_time' => 'required|integer|min:1',
        'prep_time' => 'nullable|integer',
        'servings' => 'nullable|integer',

        'calories' => 'nullable|integer',
        'protein' => 'nullable|string',
        'carbs' => 'nullable|string',
        'fat' => 'nullable|string',

        'ingredients' => 'nullable|array',
        'instructions' => 'nullable|array',
    ]);

        Recipe::create($data);

        Cache::flush();

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Recipe created successfully!');
    }

    /**
     * Show single recipe page
     */
    public function show(Recipe $recipe)
    {
        return Inertia::render('recipes/Show', [
            'recipe' => $recipe,
        ]);
    }
}