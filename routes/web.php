<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Unauth\PublicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Public pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/pricing', [PublicController::class, 'pricing'])->name('pricing');
Route::get('/demo', [PublicController::class, 'demo'])->name('demo');
Route::get('/support', [PublicController::class, 'support'])->name('support');

// Subscription flow (public)
Route::get('/subscribe', [SubscriptionController::class, 'show'])->name('subscribe');
Route::post('/subscribe', [SubscriptionController::class, 'store'])->name('subscribe.store');

// Authenticated routes
Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Onboarding route (only for authenticated users, skip onboarding check)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('onboarding', function () {
        return Inertia::render('onboarding');
    })->name('onboarding');
    
    Route::post('onboarding/complete', [OnboardingController::class, 'complete'])
        ->name('onboarding.complete');
});

require __DIR__.'/settings.php';
