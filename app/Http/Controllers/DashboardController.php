<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $city   = $request->query('city', 'Tallinn'); // 👈 change default city here
        $apiKey = config('services.openweathermap.key');
        $weather = null;

        if ($apiKey) {
            $cacheKey = 'weather_' . strtolower(trim($city));

            $weather = Cache::remember($cacheKey, 600, function () use ($city, $apiKey) {
                try {
                    $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                        'q'     => $city,
                        'appid' => $apiKey,
                        'units' => 'metric',
                        'lang'  => 'et',
                    ]);

                    return $response->successful() ? $response->json() : null;
                } catch (\Exception $e) {
                    return null;
                }
            });

            if ($weather === null) {
                Cache::forget($cacheKey);
            }
        }

        return Inertia::render('Dashboard', [
            'weather' => $weather,
            'city'    => $city,
            'markers' => \App\Models\Marker::all(),
        ]);
    }
}