<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Complete the onboarding process for the user's tenant.
     */
    public function complete(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get and update tenant
        $tenant = Tenant::find($user->tenant_id);
        
        if ($tenant) {
            $tenant->update(['onboarding_completed' => true]);
        }

        return redirect()->route('dashboard')->with('success', 'Welcome to your dashboard!');
    }
}
