<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing admin tenant if it exists
        $existingTenant = Tenant::where('slug', 'obseque')->first();
        if ($existingTenant) {
            $this->command->info('Removing existing admin tenant...');
            $existingTenant->delete();
        }

        // Get the Pro plan (premium)
        $proPlan = SubscriptionPlan::where('slug', 'pro')->first();
        if (! $proPlan) {
            $this->command->error('Pro plan not found. Please run SubscriptionPlanSeeder first.');

            return;
        }

        // Generate fake payment details
        $fakeCardNumber = '4111111111111111'; // Fake Visa number
        $fakeExpiry = '12/28';
        $fakeCvv = '123';

        $this->command->info('Creating admin tenant for Obseque...');

        // Create admin tenant
        $tenant = Tenant::create([
            'company_name' => 'Obseque',
            'slug' => 'obseque',
            'plan_id' => $proPlan->id,
            'subscription_status' => 'active',
            'subscription_type' => 'recurring',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addYear(),
            'onboarding_completed' => true,
            'database_type' => 'dedicated',
            'is_demo' => false,
            'data' => json_encode([
                'payment_method' => 'card',
                'card_number' => $fakeCardNumber,
                'card_expiry' => $fakeExpiry,
                'card_cvv' => $fakeCvv,
                'billing_address' => [
                    'street' => '123 Tech Street',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'zip' => '94105',
                    'country' => 'US',
                ],
                'subscription_amount' => $proPlan->price_yearly,
                'currency' => 'USD',
                'payment_status' => 'paid',
                'last_payment_date' => now(),
                'next_billing_date' => now()->addYear(),
            ]),
        ]);

        // Create domain for admin tenant
        $tenant->domains()->create([
            'domain' => 'obseque.lvh.me',
        ]);

        // Initialize tenant context to create user and company profile
        tenancy()->initialize($tenant);

        // Create admin user
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kelvin Ramsiel',
            'email' => 'kelvinramsiel@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('default'),
            'employee_id' => 'OWNER001',
            'phone' => '+1 (555) 123-4567',
            'is_active' => true,
        ]);

        // Assign tech-admin role
        $user->assignRole('tech-admin');

        // Create company profile
        CompanyProfile::create([
            'tenant_id' => $tenant->id,
            'company_name' => 'Obseque',
            'industry' => 'Technology',
            'company_size' => 150,
            'website' => 'https://obseque.com',
            'address' => '123 Tech Street',
            'address_line_2' => null,
            'city' => 'San Francisco',
            'state' => 'CA',
            'country' => 'US',
            'postal_code' => '94105',
            'phone' => '+1 (555) 123-4567',
            'email' => 'contact@obseque.com',
            'registration_number' => 'REG123456',
            'tax_number' => 'TAX789012',
            'fiscal_year_start' => '01-01',
            'timezone' => 'America/Los_Angeles',
            'currency' => 'USD',
        ]);

        $this->command->info('Admin tenant created successfully!');
        $this->command->info('Email: kelvinramsiel@gmail.com');
        $this->command->info('Password: default');
        $this->command->info('Domain: obseque.localhost:8000');
        $this->command->info('Role: tech-admin');
    }
}
