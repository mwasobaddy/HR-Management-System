<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Public pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/billing', [PublicController::class, 'billing'])->name('billing');
Route::get('/demo', [PublicController::class, 'demo'])->name('demo');
Route::get('/support', [PublicController::class, 'support'])->name('support');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
