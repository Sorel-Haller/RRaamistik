<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentsController;
use App\Mail\Timetable;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which contains the "web" middleware group. Enjoy building your app!
|
*/

// Home route
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Dashboard route
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('posts', PostController::class);
    Route::resource('authors', PostController::class);
    Route::post('/add-comment/{post}', [CommentsController::class, 'store'])->name('comments.add');
});

// Posts routes
Route::prefix('posts')->group(function () {

    // Edit and delete posts
    Route::get('/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::delete('/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Comments tied to posts
    Route::get('/{post}/comments', [CommentsController::class, 'index'])->name('posts.comments.index');
    Route::post('/{post}/comments', [CommentsController::class, 'store'])->name('posts.comments.store');
});


// Include additional route files
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/posts.php';
require __DIR__.'/authors.php';

// Test route (can be removed later)
Route::get('/test', function () {
    return 'tere';
});

Route::get('/mailable', function () {

    $startDate = Carbon::now();
    $endDate = Carbon::now()->addWeek();

    $timetableEvents = collect([
        [
            'title' => 'Math lesson',
            'date' => '2026-03-12',
            'time' => '10:00'
        ],
        [
            'title' => 'Science lesson',
            'date' => '2026-03-13',
            'time' => '12:00'
        ]
    ]);

    return new Timetable($timetableEvents, $startDate, $endDate);
});