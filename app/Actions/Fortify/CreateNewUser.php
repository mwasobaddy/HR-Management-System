<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\CompanyProfile;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user with tenant.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'company_name' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            // Get the Free plan by default
            $freePlan = SubscriptionPlan::where('slug', 'free')->first();

            // Create tenant
            $tenant = Tenant::create([
                'id' => Str::uuid()->toString(),
                'company_name' => $input['company_name'],
                'slug' => Str::slug($input['company_name']) . '-' . Str::random(6),
                'plan_id' => $freePlan->id,
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(14), // 14 day trial
                'database_type' => 'shared',
                'onboarding_completed' => false,
            ]);

            // Create domain for tenant (for Free/Plus, we use main domain)
            $tenant->domains()->create([
                'domain' => config('app.url'), // Will be hrms.obseque.com in production
            ]);

            // Initialize tenant context to create user within tenant scope
            tenancy()->initialize($tenant);

            // Create admin user for the tenant
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'role' => 'admin', // First user is always admin
                'employee_id' => 'EMP001',
                'is_active' => true,
            ]);

            // Create default company profile
            CompanyProfile::create([
                'company_name' => $input['company_name'],
                'timezone' => config('app.timezone'),
                'currency' => 'USD',
            ]);

            tenancy()->end();

            return $user;
        });
    }
}
