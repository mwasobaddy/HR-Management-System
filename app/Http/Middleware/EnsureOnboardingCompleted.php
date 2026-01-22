<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request and redirect to onboarding if not completed.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for guest users
        if (!$request->user()) {
            return $next($request);
        }

        // Get user's tenant
        $user = $request->user();
        $tenant = Tenant::where('id', $user->tenant_id)->first();

        // Skip for demo tenant
        if ($tenant && $tenant->is_demo) {
            return $next($request);
        }

        // If tenant exists and onboarding not completed, redirect to onboarding
        if ($tenant && !$tenant->onboarding_completed && !$request->routeIs('onboarding*')) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
