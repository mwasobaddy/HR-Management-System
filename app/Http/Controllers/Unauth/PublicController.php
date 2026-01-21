<?php

namespace App\Http\Controllers\Unauth;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    /**
     * Show the home page (marketing landing).
     */
    public function home(): Response
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price_monthly')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'price_monthly' => $plan->price_monthly,
                    'price_yearly' => $plan->price_yearly,
                    'max_users' => $plan->max_users,
                    'max_job_posts' => $plan->max_job_posts,
                    'features' => $plan->features,
                ];
            });

        return Inertia::render('unauth/home', [
            'plans' => $plans,
        ]);
    }

    /**
     * Show the pricing page for tier selection.
     */
    public function pricing(): Response
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price_monthly')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => number_format($plan->price_monthly, 2),
                    'billing_cycle' => 'month',
                    'max_users' => $plan->max_users,
                    'features' => $plan->features,
                ];
            });

        return Inertia::render('unauth/pricing', [
            'plans' => $plans,
        ]);
    }

    /**
     * Show the demo page with credentials.
     */
    public function demo(): Response
    {
        $demoAccounts = [
            [
                'email' => 'demo-admin@hrms.com',
                'password' => 'demo123',
                'role' => 'Admin',
                'description' => 'Full system access with administrative privileges',
            ],
            [
                'email' => 'demo-hr@hrms.com',
                'password' => 'demo123',
                'role' => 'HR Manager',
                'description' => 'Manage employees, attendance, and HR operations',
            ],
            [
                'email' => 'demo-manager@hrms.com',
                'password' => 'demo123',
                'role' => 'Department Manager',
                'description' => 'View and manage department employees',
            ],
            [
                'email' => 'demo-employee1@hrms.com',
                'password' => 'demo123',
                'role' => 'Employee',
                'description' => 'Basic employee access to personal information',
            ],
            [
                'email' => 'demo-employee2@hrms.com',
                'password' => 'demo123',
                'role' => 'Employee',
                'description' => 'Basic employee access to personal information',
            ],
        ];

        return Inertia::render('unauth/demo', [
            'demoAccounts' => $demoAccounts,
        ]);
    }

    /**
     * Show the support page.
     */
    public function support(): Response
    {
        return Inertia::render('unauth/support');
    }
}
