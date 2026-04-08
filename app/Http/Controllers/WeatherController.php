<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class WeatherController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->query('city', 'Tallinn');
        $weather = null;
        $error = null;

        $apiKey = config('services.openweathermap.key');

        if ($apiKey) {
            $cacheKey = 'weather_' . strtolower(trim($city));

            $weather = Cache::remember($cacheKey, 600, function () use ($city, $apiKey, &$error) {
                try {
                    $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                        'q' => $city,
                        'appid' => $apiKey,
                        'units' => 'metric',
                        'lang' => 'et',
                    ]);

                    if ($response->successful()) {
                        return $response->json();
                    }

                    $error = 'Linna ei leitud. Palun proovi uuesti.';
                    return null;
                } catch (\Exception $e) {
                    $error = 'Ilmateenusega ühendamine ebaõnnestus.';
                    return null;
                }
            });

            if ($weather === null) {
                Cache::forget($cacheKey);
            }
        } else {
            $error = 'OpenWeatherMap API võti on seadistamata. Lisa OPENWEATHERMAP_API_KEY .env faili.';
        }

        return Inertia::render('Weather/Index', [
            'weather' => $weather,
            'city' => $city,
            'error' => $error,
        ]);
    }
}