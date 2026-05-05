<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class RecipeController extends Controller
{
    /** GET /recipes */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'difficulty', 'sort', 'limit']);
        $recipes = $this->queryRecipes($request);

        return Inertia::render('recipes/Index', [
            'recipes' => $recipes,
            'filters' => $filters,
        ]);
    }

    /** GET /recipes/create */
    public function create()
    {
        return Inertia::render('recipes/Create');
    }

    /** POST /recipes */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'image'        => 'nullable|url',
            'description'  => 'required|string',
            'instructions' => 'required|string',
            'calories'     => 'nullable|integer|min:0',
            'difficulty'   => 'required|in:beginner,intermediate,advanced',
        ]);

        Recipe::create($validated);

        Cache::flush();

        return redirect()->route('recipes.index');
    }

    public function show(Recipe $recipe)
    {
        return Inertia::render('recipes/Show', ['recipe' => $recipe]);
    }

    public function apiIndex(Request $request)
    {
        $cacheKey = 'recipes_api_' . md5($request->getQueryString() ?? 'default');

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            return $this->queryRecipes($request);
        });

        return response()->json([
            'data'  => $data,
            'total' => $data->count(),
        ]);
    }

    private function queryRecipes(Request $request)
    {
        $query = Recipe::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($difficulty = $request->input('difficulty')) {
            if (in_array($difficulty, ['beginner', 'intermediate', 'advanced'])) {
                $query->where('difficulty', $difficulty);
            }
        }

        match ($request->input('sort')) {
            'oldest'        => $query->oldest(),
            'calories_asc'  => $query->orderBy('calories'),
            'calories_desc' => $query->orderByDesc('calories'),
            default         => $query->latest(),
        };

        $limit = min((int) ($request->input('limit', 12)), 100);
        $query->limit($limit);

        return $query->get();
    }
}