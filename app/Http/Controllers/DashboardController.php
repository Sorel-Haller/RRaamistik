<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     * Default city is Tallinn; cached for 1 hour.
     */
    public function __invoke(Request $request)
    {
        $weather = Cache::remember('weather_v2_tallinn', now()->addHour(), function () {
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q'     => 'Tallinn',
                'appid' => config('services.weather.key'),
                'units' => 'metric',
            ]);

            return $response->successful() ? $response->json() : null;
        });

        return Inertia::render('Dashboard', [
            'weather' => $weather,
        ]);
    }
}