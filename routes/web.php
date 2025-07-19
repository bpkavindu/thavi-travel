<?php

use App\Http\Controllers\PhotoSpotController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 🔐 Only logged-in users
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🖼️ Photo Spots (Admin-Only)
    Route::middleware('admin')->group(function () {
        Route::get('/photo-spots/create', [PhotoSpotController::class, 'create'])->name('photo-spots.create');
        Route::post('/photo-spots', [PhotoSpotController::class, 'store'])->name('photo-spots.store');
        Route::get('/photo-spots/{id}/edit', [PhotoSpotController::class, 'edit'])->name('photo-spots.edit');
        Route::put('/photo-spots/{id}', [PhotoSpotController::class, 'update'])->name('photo-spots.update');
        Route::delete('/photo-spots/{id}', [PhotoSpotController::class, 'destroy'])->name('photo-spots.destroy');
    });
});

// 🌍 Public Photo Spots Page
Route::get('/photo-spots', [PhotoSpotController::class, 'index'])->name('photo-spots.index');

// Auth scaffolding
require __DIR__.'/auth.php';
