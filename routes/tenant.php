<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Tenant root: redirect authenticated users to dashboard, guests to login
    Route::get('/', function () {
        return auth()->check()
            ? redirect()->route('dashboard')
            : redirect()->route('login');
    })->name('tenant.home');

    // Direct login via signed URL (tenant domain)
    Route::get('/auth/login/{user_id}', [AuthController::class, 'login'])
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
