<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\WelcomeCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    /**
     * Show subscription form for a specific plan
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
            'plan' => $plan
        ]);
    }

    /**
     * Process subscription and create tenant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'admin_name' => 'required|string|max:255',
            'payment_type' => 'nullable|in:recurring,one-time',
            'card_number' => 'nullable|string|max:19',
            'expiry' => 'nullable|string|max:5',
            'cvv' => 'nullable|string|max:4',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Validate payment info for paid plans
        if ($plan->price > 0) {
            $request->validate([
                'payment_type' => 'required|in:recurring,one-time',
                'card_number' => 'required|string|max:19',
                'expiry' => 'required|string|max:5',
                'cvv' => 'required|string|max:4',
            ]);
        }

        // Generate random password
        $randomPassword = Str::random(12);

        DB::beginTransaction();
        try {
            // Create tenant
            $tenant = Tenant::create([
                'id' => Str::uuid(),
                'name' => Str::slug($validated['company_name']),
                'subscription_plan_id' => $plan->id,
                'trial_ends_at' => $plan->price == 0 ? now()->addDays(14) : null,
                'subscription_type' => $validated['payment_type'] ?? null,
                'is_demo' => false,
                'onboarding_completed' => false,
            ]);

            // Create domain
            $tenant->domains()->create([
                'domain' => Str::slug($validated['company_name']) . '.hrms.test',
            ]);

            // Initialize tenant context
            tenancy()->initialize($tenant);

            // Create admin user with random password
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['email'],
                'password' => Hash::make($randomPassword),
                'role' => 'admin',
                'employee_id' => 'EMP001',
                'is_active' => true,
            ]);

            // Create company profile
            CompanyProfile::create([
                'tenant_id' => $tenant->id,
                'company_name' => $validated['company_name'],
                'timezone' => 'UTC',
                'currency' => 'USD',
                'work_hours' => '08:00-17:00',
                'work_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            ]);

            // Send welcome email with credentials
            $user->notify(new WelcomeCredentials($randomPassword, $tenant));

            DB::commit();

            return redirect('/login')->with('success', 'Account created! Check your email for login credentials.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create account: ' . $e->getMessage()]);
        }
    }
}
