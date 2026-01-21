<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for small businesses getting started with HR management',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'max_users' => 15,
                'max_job_posts' => 15,
                'has_onboarding_framework' => false,
                'has_ai_features' => false,
                'has_api_access' => false,
                'has_payroll' => false,
                'has_subdomain' => false,
                'has_custom_domain' => false,
                'database_type' => 'shared',
                'is_active' => true,
                'features' => json_encode([
                    'Basic employee management',
                    'Basic attendance tracking',
                    'Standard reports',
                    'Email support',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plus',
                'slug' => 'plus',
                'description' => 'Enhanced features for growing teams',
                'price_monthly' => 49.99,
                'price_yearly' => 499.99, // ~2 months free
                'max_users' => 50,
                'max_job_posts' => 65,
                'has_onboarding_framework' => true,
                'has_ai_features' => false,
                'has_api_access' => false,
                'has_payroll' => false,
                'has_subdomain' => false,
                'has_custom_domain' => false,
                'database_type' => 'shared',
                'is_active' => true,
                'features' => json_encode([
                    'Full employee management',
                    'Onboarding framework',
                    'Advanced attendance & leave management',
                    'Custom reports',
                    'Priority email support',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Advanced features with AI and dedicated infrastructure',
                'price_monthly' => 149.99,
                'price_yearly' => 1499.99, // ~2 months free
                'max_users' => 250,
                'max_job_posts' => -1, // Unlimited
                'has_onboarding_framework' => true,
                'has_ai_features' => true,
                'has_api_access' => true,
                'has_payroll' => true,
                'has_subdomain' => true,
                'has_custom_domain' => false,
                'database_type' => 'dedicated',
                'is_active' => true,
                'features' => json_encode([
                    'All Plus features',
                    'AI-powered CV vetting',
                    'Candidate screening',
                    'Subdomain ({company}.hrms.obseque.com)',
                    'Full API access',
                    'Payroll processing',
                    'Performance management',
                    'Unlimited job postings',
                    'Priority support',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Custom solutions for large organizations',
                'price_monthly' => 0.00, // Custom pricing
                'price_yearly' => 0.00, // Custom pricing
                'max_users' => -1, // Unlimited
                'max_job_posts' => -1, // Unlimited
                'has_onboarding_framework' => true,
                'has_ai_features' => true,
                'has_api_access' => true,
                'has_payroll' => true,
                'has_subdomain' => true,
                'has_custom_domain' => true,
                'database_type' => 'dedicated',
                'is_active' => true,
                'features' => json_encode([
                    'All Pro features',
                    'Custom domain support',
                    'White-label branding',
                    'Custom integrations',
                    'SLA guarantees',
                    'Dedicated account manager',
                    'Custom feature development',
                    'Unlimited everything',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscription_plans')->insert($plans);
    }
}
