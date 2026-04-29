<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MarkerController;
use App\Mail\Timetable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('/weather/search', [WeatherController::class, 'search']);

    Route::get('/markers',             [MarkerController::class, 'index'])->name('markers.index');
    Route::post('/markers',            [MarkerController::class, 'store'])->name('markers.store');
    Route::put('/markers/{marker}',    [MarkerController::class, 'update'])->name('markers.update');
    Route::delete('/markers/{marker}', [MarkerController::class, 'destroy'])->name('markers.destroy');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/{post}/edit',   [PostController::class, 'edit'])->name('posts.edit');

    Route::post('/add-comment/{post}', [CommentController::class, 'store'])->name('comments.add');

    Route::get('/products',              [ProductController::class, 'index'])->name('products.index');
    Route::delete('/products/{products}',[ProductController::class, 'destroy'])->name('products.destroy');
});


Route::get('/mailable', function () {

    $startDate = now()->startOfWeek();
    $endDate   = now()->endOfWeek();

    $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
        'from'          => $startDate->toIsoString(),
        'thru'          => $endDate->toIsoString(),
        'lang'          => 'ET',
        'page'          => 0,
        'schoolId'      => 38,
        'size'          => 50,
        'studentGroups' => '39248890-7051-4182-9a9a-8a82259b862b',
    ]);

    $timetableEvents = collect($response['content'] ?? [])
        ->sortBy(fn($event) => $event['date'] . ' ' . $event['timeStart'])
        ->groupBy(fn($event) =>
            Carbon::parse($event['date'])->locale('et_EE')->dayName
        );

    return new Timetable($timetableEvents, $startDate, $endDate);
});


require __DIR__.'/settings.php';
require __DIR__.'/posts.php';
require __DIR__.'/authors.php';
require __DIR__.'/auth.php';