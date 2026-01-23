<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Unauth\PublicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

        // Direct login via signed URL
        Route::get('/auth/login/{user_id}', [\App\Http\Controllers\Auth\AuthController::class, 'login'])
            ->name('auth.login')
            ->middleware('signed');

        // Authenticated routes
        Route::middleware(['auth', 'verified', 'onboarding'])->group(function () {
            Route::get('dashboard', function () {
                return Inertia::render('dashboard');
            })->name('dashboard');
        });

        // Onboarding route (only for authenticated users, skip onboarding check)
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('onboarding', function () {
                $user = auth()->user();
                $tenant = tenant();
                // For now, assume a default plan or get from tenant
                $plan = \App\Models\SubscriptionPlan::where('slug', 'free')->first() ?? \App\Models\SubscriptionPlan::first();

                return Inertia::render('onboarding', [
                    'user' => $user,
                    'tenant' => $tenant,
                    'plan' => $plan,
                ]);
            })->name('onboarding');

            Route::post('onboarding/complete', [OnboardingController::class, 'complete'])
                ->name('onboarding.complete');
        });

        require __DIR__.'/settings.php';
    });
}
