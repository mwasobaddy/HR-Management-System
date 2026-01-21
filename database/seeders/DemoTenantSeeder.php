<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\Department;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing demo tenant and its data if it exists
        $existingTenant = Tenant::find('demo');
        if ($existingTenant) {
            $this->command->info('Removing existing demo tenant and its data...');
            // Delete will cascade to all related tenant data thanks to foreign keys
            $existingTenant->delete();
        }

        // Get the Free plan (demo uses Free tier with Pro features enabled)
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        // Create demo tenant
        $tenant = Tenant::create([
            'id' => 'demo',
            'company_name' => 'Demo Company Inc.',
            'slug' => 'demo',
            'plan_id' => $freePlan->id,
            'subscription_status' => 'active',
            'trial_ends_at' => now()->addYears(10), // Never expires
            'subscription_ends_at' => null,
            'onboarding_completed' => true,
            'database_type' => 'shared',
            'is_demo' => true,
            'data' => json_encode([
                'features_override' => [
                    'has_all_features' => true, // Enable all Pro features for demo
                ],
            ]),
        ]);

        // Create domain for demo tenant
        $tenant->domains()->create([
            'domain' => 'demo.localhost',
        ]);

        $this->command->info('Demo tenant created. Now initializing tenant context and creating data...');

        // Initialize tenant context for single-database tenancy
        tenancy()->initialize($tenant);

        // Create company profile
        CompanyProfile::create([
            'company_name' => 'Demo Company Inc.',
            'address' => '123 Tech Street',
            'city' => 'San Francisco',
            'state' => 'California',
            'country' => 'United States',
            'postal_code' => '94102',
            'phone' => '+1 (555) 123-4567',
            'email' => 'info@democompany.com',
            'website' => 'https://democompany.com',
            'industry' => 'Technology',
            'company_size' => 50,
            'timezone' => 'America/Los_Angeles',
            'currency' => 'USD',
            'work_start_time' => '09:00:00',
            'work_end_time' => '17:00:00',
            'work_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        ]);

        // Create departments
        $engineering = Department::create([
            'name' => 'Engineering',
            'description' => 'Software development and technical operations',
            'is_active' => true,
        ]);

        $hr = Department::create([
            'name' => 'Human Resources',
            'description' => 'Employee relations and HR operations',
            'is_active' => true,
        ]);

        $sales = Department::create([
            'name' => 'Sales',
            'description' => 'Sales and business development',
            'is_active' => true,
        ]);

        $marketing = Department::create([
            'name' => 'Marketing',
            'description' => 'Marketing and brand management',
            'is_active' => true,
        ]);

        // Create demo users with specific roles
        $admin = User::create([
            'name' => 'Demo Admin',
            'email' => 'demo-admin@hrms.com',
            'email_verified_at' => now(),
            'password' => Hash::make('demo123'),
            'role' => 'admin',
            'employee_id' => 'EMP001',
            'phone' => '+1 (555) 100-0001',
            'is_active' => true,
        ]);

        $hrManager = User::create([
            'name' => 'Demo HR Manager',
            'email' => 'demo-hr@hrms.com',
            'email_verified_at' => now(),
            'password' => Hash::make('demo123'),
            'role' => 'hr_manager',
            'department_id' => $hr->id,
            'employee_id' => 'EMP002',
            'phone' => '+1 (555) 100-0002',
            'is_active' => true,
        ]);

        $manager = User::create([
            'name' => 'Demo Department Manager',
            'email' => 'demo-manager@hrms.com',
            'email_verified_at' => now(),
            'password' => Hash::make('demo123'),
            'role' => 'manager',
            'department_id' => $engineering->id,
            'employee_id' => 'EMP003',
            'phone' => '+1 (555) 100-0003',
            'is_active' => true,
        ]);

        $employee1 = User::create([
            'name' => 'Demo Employee One',
            'email' => 'demo-employee1@hrms.com',
            'email_verified_at' => now(),
            'password' => Hash::make('demo123'),
            'role' => 'employee',
            'department_id' => $engineering->id,
            'employee_id' => 'EMP004',
            'phone' => '+1 (555) 100-0004',
            'is_active' => true,
        ]);

        $employee2 = User::create([
            'name' => 'Demo Employee Two',
            'email' => 'demo-employee2@hrms.com',
            'email_verified_at' => now(),
            'password' => Hash::make('demo123'),
            'role' => 'employee',
            'department_id' => $sales->id,
            'employee_id' => 'EMP005',
            'phone' => '+1 (555) 100-0005',
            'is_active' => true,
        ]);

        // Update department managers
        $engineering->update(['manager_id' => $manager->id]);
        $hr->update(['manager_id' => $hrManager->id]);

        // Create additional sample employees
        $sampleEmployees = [
            ['name' => 'John Smith', 'email' => 'john.smith@democompany.com', 'role' => 'employee', 'department_id' => $engineering->id, 'employee_id' => 'EMP006'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@democompany.com', 'role' => 'employee', 'department_id' => $engineering->id, 'employee_id' => 'EMP007'],
            ['name' => 'Michael Brown', 'email' => 'michael.brown@democompany.com', 'role' => 'employee', 'department_id' => $sales->id, 'employee_id' => 'EMP008'],
            ['name' => 'Emily Davis', 'email' => 'emily.davis@democompany.com', 'role' => 'employee', 'department_id' => $sales->id, 'employee_id' => 'EMP009'],
            ['name' => 'David Wilson', 'email' => 'david.wilson@democompany.com', 'role' => 'employee', 'department_id' => $marketing->id, 'employee_id' => 'EMP010'],
            ['name' => 'Jennifer Martinez', 'email' => 'jennifer.martinez@democompany.com', 'role' => 'employee', 'department_id' => $marketing->id, 'employee_id' => 'EMP011'],
            ['name' => 'Robert Anderson', 'email' => 'robert.anderson@democompany.com', 'role' => 'employee', 'department_id' => $hr->id, 'employee_id' => 'EMP012'],
            ['name' => 'Lisa Taylor', 'email' => 'lisa.taylor@democompany.com', 'role' => 'employee', 'department_id' => $engineering->id, 'employee_id' => 'EMP013'],
            ['name' => 'James Thomas', 'email' => 'james.thomas@democompany.com', 'role' => 'manager', 'department_id' => $sales->id, 'employee_id' => 'EMP014'],
            ['name' => 'Maria Garcia', 'email' => 'maria.garcia@democompany.com', 'role' => 'manager', 'department_id' => $marketing->id, 'employee_id' => 'EMP015'],
        ];

        foreach ($sampleEmployees as $emp) {
            User::create([
                'name' => $emp['name'],
                'email' => $emp['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => $emp['role'],
                'department_id' => $emp['department_id'],
                'employee_id' => $emp['employee_id'],
                'phone' => '+1 (555) ' . rand(100, 999) . '-' . rand(1000, 9999),
                'is_active' => true,
            ]);
        }

        // Update sales and marketing managers
        $salesManager = User::where('email', 'james.thomas@democompany.com')->first();
        $marketingManager = User::where('email', 'maria.garcia@democompany.com')->first();

        $sales->update(['manager_id' => $salesManager->id]);
        $marketing->update(['manager_id' => $marketingManager->id]);

        tenancy()->end();

        $totalUsers = 5 + count($sampleEmployees);
        $this->command->info("Demo tenant created successfully with {$totalUsers} users!");
        $this->command->info('Demo accounts: demo-admin@hrms.com, demo-hr@hrms.com, demo-manager@hrms.com, demo-employee1@hrms.com, demo-employee2@hrms.com');
        $this->command->info('Password for all demo accounts: demo123');
    }
}

