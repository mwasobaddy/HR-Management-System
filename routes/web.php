<?php

use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Unauth\PublicController;
use Illuminate\Support\Facades\Route;

// Wrap central routes in domain groups for Laravel 12
$centralDomains = config('tenancy.central_domains', []);
foreach ($centralDomains as $index => $domain) {
    // When running artisan (e.g. wayfinder:generate), register routes for only the first domain
    // to avoid duplicate named routes being generated for each domain (causes TypeScript redeclaration errors).
    if (app()->runningInConsole() && $index > 0) {
        break;
    }

    Route::domain($domain)->group(function () {
        // Public pages
        Route::get('/', [PublicController::class, 'home'])->name('home');
        Route::get('/pricing', [PublicController::class, 'pricing'])->name('pricing');
        Route::get('/demo', [PublicController::class, 'demo'])->name('demo');
        Route::get('/support', [PublicController::class, 'support'])->name('support');

        // Subscription flow (public)
        Route::get('/subscribe', [SubscriptionController::class, 'show'])->name('subscribe');
        Route::post('/subscribe', [SubscriptionController::class, 'store'])
            ->middleware('throttle:3,1')
            ->name('subscribe.store');
    });
}
