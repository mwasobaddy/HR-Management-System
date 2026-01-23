<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\WelcomeCredentials;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantCreationService
{
    /**
     * Create a new tenant with all required resources.
     *
     * @param array $data
     * @return Tenant
     * @throws \Exception
     */
    public function createTenant(array $data): Tenant
    {
        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $randomPassword = Str::random(12);

        return DB::transaction(function () use ($data, $plan, $randomPassword) {
            // Create tenant
            $tenant = $this->createTenantRecord($data, $plan);

            // Create domain
            $this->createTenantDomain($tenant, $data['domain']);

            // Initialize tenant context
            tenancy()->initialize($tenant);

            // Create admin user
            $user = $this->createAdminUser($tenant, $data, $randomPassword);

            // Create company profile
            $this->createCompanyProfile($tenant, $data['company_name']);

            // Send welcome email
            $user->notify(new WelcomeCredentials($randomPassword, $tenant));

            return $tenant;
        });
    }

    /**
     * Create the tenant record.
     *
     * @param array $data
     * @param SubscriptionPlan $plan
     * @return Tenant
     */
    protected function createTenantRecord(array $data, SubscriptionPlan $plan): Tenant
    {
        return Tenant::create([
            'id' => Str::uuid(),
            'company_name' => $data['company_name'],
            'slug' => $data['domain'],
            'plan_id' => $plan->id,
            'subscription_status' => $plan->isFree() ? 'trial' : 'active',
            'trial_ends_at' => $plan->isFree() ? now()->addDays(14) : null,
            'subscription_ends_at' => !$plan->isFree() ? now()->addMonth() : null,
            'subscription_type' => $data['payment_type'] ?? null,
            'database_type' => $plan->database_type,
            'onboarding_completed' => false,
            'is_demo' => false,
        ]);
    }

    /**
     * Create tenant domain.
     *
     * @param Tenant $tenant
     * @param string $subdomain
     * @return void
     */
    protected function createTenantDomain(Tenant $tenant, string $subdomain): void
    {
        $tenant->domains()->create([
            'domain' => $subdomain . '.hrms.test',
        ]);
    }

    /**
     * Create admin user for tenant.
     *
     * @param Tenant $tenant
     * @param array $data
     * @param string $password
     * @return User
     */
    protected function createAdminUser(Tenant $tenant, array $data, string $password): User
    {
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $data['admin_name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'employee_id' => 'EMP001',
            'is_active' => true,
        ]);

        // Assign super-admin role
        $user->assignRole('super-admin');

        return $user;
    }

    /**
     * Create company profile for tenant.
     *
     * @param Tenant $tenant
     * @param string $companyName
     * @return CompanyProfile
     */
    protected function createCompanyProfile(Tenant $tenant, string $companyName): CompanyProfile
    {
        return CompanyProfile::create([
            'tenant_id' => $tenant->id,
            'company_name' => $companyName,
            'timezone' => 'UTC',
            'currency' => 'USD',
            'work_hours' => '08:00-17:00',
            'work_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        ]);
    }
}