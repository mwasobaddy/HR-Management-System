<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteOnboardingRequest;
use App\Models\Tenant;
use App\Services\OnboardingService;

class OnboardingController extends Controller
{
    public function __construct(
        protected OnboardingService $onboardingService
    ) {}

    /**
     * Complete the onboarding process for the user's tenant.
     */
    public function complete(CompleteOnboardingRequest $request)
    {
        $user = $request->user();
        $tenant = Tenant::find($user->tenant_id);

        if (!$tenant) {
            return redirect()->route('login')->withErrors(['error' => 'Tenant not found']);
        }

        try {
            $this->onboardingService->completeOnboarding(
                $tenant,
                $user,
                $request->validated(),
                $request
            );

            return redirect()->route('dashboard')->with('success', 'Welcome to your dashboard!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to complete onboarding. Please try again.']);
        }
    }
}
