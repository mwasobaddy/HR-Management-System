<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Department;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OnboardingService
{
    /**
     * Complete the onboarding process for a tenant.
     */
    public function completeOnboarding(Tenant $tenant, User $user, array $validatedData, Request $request): void
    {
        DB::transaction(function () use ($tenant, $user, $validatedData, $request) {
            // Update Company Profile
            $this->updateCompanyProfile($tenant, $validatedData, $request);

            // Update User (Admin)
            $this->updateAdminUser($user, $validatedData);

            // Create Department if provided
            $this->createDepartmentIfProvided($tenant, $validatedData);

            // Update tenant
            $this->markTenantOnboardingComplete($tenant, $validatedData);
        });
    }

    /**
     * Update or create company profile.
     */
    protected function updateCompanyProfile(Tenant $tenant, array $data, Request $request): void
    {
        $companyProfile = CompanyProfile::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'company_name' => $data['company_name'],
                'address' => $data['address'],
                'address_line_2' => $data['address_line_2'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => $data['country'],
                'postal_code' => $data['postal_code'],
                'phone' => $data['company_phone'],
                'email' => $data['company_email'],
                'fiscal_year_start' => $data['fiscal_year_start'] ?? '01-01',
                'currency' => $data['currency'] ?? 'USD',
                'working_hours' => $data['working_hours'] ?? null,
                'ai_provider' => $data['ai_provider'],
                'ai_model' => $data['ai_model'],
                'ai_api_key' => $data['ai_api_key'],
                'google_calendar_api_key' => $data['google_calendar_api_key'],
                'google_meet_api_key' => $data['google_meet_api_key'],
                'smtp_host' => $data['smtp_host'],
                'smtp_port' => $data['smtp_port'],
                'smtp_username' => $data['smtp_username'],
                'smtp_password' => $data['smtp_password'],
                'smtp_encryption' => $data['smtp_encryption'],
                'smtp_from_address' => $data['smtp_from_address'],
                'smtp_from_name' => $data['smtp_from_name'],
            ]
        );

        // Handle company logo upload if provided
        if ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            $companyProfile->update(['logo' => $logoPath]);
        }
    }

    /**
     * Update the admin user with onboarding data.
     */
    protected function updateAdminUser(User $user, array $data): void
    {
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['personal_email'],
            'work_email' => $data['work_email'],
            'language' => $data['language'] ?? 'en',
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Create department if department name is provided.
     */
    protected function createDepartmentIfProvided(Tenant $tenant, array $data): void
    {
        if (!empty($data['department_name'])) {
            Department::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $data['department_name']
                ],
                [
                    'branch_name' => $data['branch_name'],
                    'description' => null,
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Mark tenant onboarding as completed.
     */
    protected function markTenantOnboardingComplete(Tenant $tenant, array $data): void
    {
        $tenant->update([
            'company_name' => $data['company_name'],
            'onboarding_completed' => true
        ]);
    }
}