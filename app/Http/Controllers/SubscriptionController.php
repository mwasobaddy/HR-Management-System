<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Rules\UniqueTenantDomain;
use App\Services\TenantCreationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    protected TenantCreationService $tenantService;

    public function __construct(TenantCreationService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Show subscription form for a specific plan.
     */
    public function show(Request $request)
    {
        $planId = $request->query('plan');
        
        if (!$planId) {
            return redirect('/billing')->with('error', 'Please select a plan first.');
        }

        $plan = SubscriptionPlan::find($planId);

        if (!$plan) {
            return redirect('/billing')->with('error', 'Invalid plan selected.');
        }

        return Inertia::render('subscribe', [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price_monthly,
                'billing_cycle' => 'month',
                'max_users' => $plan->max_users,
                'features' => $plan->features ?? [],
            ]
        ]);
    }

    /**
     * Process subscription and create tenant.
     */
    public function store(Request $request)
    {
        $validated = $this->validateSubscription($request);
        
        try {
            $tenant = $this->tenantService->createTenant($validated);

            return redirect('/login')->with('success', 'Account created! Check your email for login credentials.');

        } catch (\Exception $e) {
            \Log::error('Tenant creation failed', [
                'email' => $validated['email'],
                'domain' => $validated['domain'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create account. Please try again or contact support.']);
        }
    }

    /**
     * Validate subscription request.
     *
     * @param Request $request
     * @return array
     */
    protected function validateSubscription(Request $request): array
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'company_name' => 'required|string|max:255',
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\-]+$/',
                new UniqueTenantDomain(),
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'admin_name' => 'required|string|max:255',
            'payment_type' => 'nullable|in:recurring,one-time',
            'card_number' => 'nullable|string|max:19',
            'expiry' => 'nullable|string|max:5',
            'cvv' => 'nullable|string|max:4',
        ]);

        // Validate payment info for paid plans
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        
        if (!$plan->isFree()) {
            $request->validate([
                'payment_type' => 'required|in:recurring,one-time',
                'card_number' => 'required|string|max:19',
                'expiry' => 'required|string|max:5',
                'cvv' => 'required|string|max:4',
            ]);
        }

        return $validated;
    }
}