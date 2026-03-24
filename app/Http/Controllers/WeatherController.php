<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    /**
     * Search weather by city name.
     * Results are cached per city for 1 hour to reduce API calls.
     */
    public function search(Request $request)
    {
        $request->validate([
            'city' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $city = trim($request->input('city'));
        $cacheKey = 'weather_city_' . strtolower(str_replace(' ', '_', $city));

        $data = Cache::remember($cacheKey, now()->addHour(), function () use ($city) {
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q'     => $city,
                'appid' => config('services.weather.key'),
                'units' => 'metric',
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });

        if (is_null($data) || isset($data['cod']) && $data['cod'] !== 200) {
            // Clear bad cache entry so next request retries
            Cache::forget($cacheKey);

            return response()->json([
                'error' => 'City not found or weather service unavailable.',
            ], 404);
        }

        return response()->json($data);
    }
}