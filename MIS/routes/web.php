<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Redirect root to /login (custom login view)
Route::get('/', function () {
    return view('login'); // Custom login page
});

// Dashboard with optional dynamic page loading
Route::get('/dashboard', function () {
    return view('dashboard', ['page' => 'counter']);
})->middleware(['auth', 'verified']);

Route::get('/dashboard/{page?}', function ($page = 'counter') {
    if (!view()->exists("pages.$page")) {
        abort(404);
    }

    return view('dashboard', ['page' => $page]);
})->middleware(['auth'])->name('dashboard');

// Authenticated user profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Load additional auth routes (login, register, reset, etc.)
require __DIR__.'/auth.php';
