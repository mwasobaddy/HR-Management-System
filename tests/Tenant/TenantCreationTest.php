<?php

use App\Models\Department;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantCreationService;
use Database\Seeders\TenantPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tenant creation service creates tenant with all resources', function () {
    // Seed permissions and roles
    $seeder = new TenantPermissionsSeeder;
    $seeder->run();

    // Create a subscription plan first
    $plan = SubscriptionPlan::create([
        'name' => 'Basic Plan',
        'slug' => 'basic',
        'price_monthly' => 29.99,
        'features' => ['feature1', 'feature2'],
    ]);

    $service = new TenantCreationService;

    $tenant = $service->createTenant([
        'company_name' => 'Test Company',
        'domain' => 'testcompany',
        'plan_id' => $plan->id,
        'admin_name' => 'Test Admin',
        'email' => 'admin@testcompany.com',
        'payment_type' => 'monthly',
    ]);

    expect($tenant)->toBeInstanceOf(Tenant::class);
    expect($tenant->company_name)->toBe('Test Company');
    expect($tenant->domains->first()->domain)->toBe('testcompany.lvh.me');

    // Verify tenant has required attributes
    expect($tenant->id)->not->toBeNull();
    expect($tenant->slug)->toBe('testcompany');
});

test('tenant data isolation works correctly', function () {
    // Create first tenant
    $tenant1 = Tenant::factory()->create(['id' => 'tenant1']);
    $tenant1->domains()->create(['domain' => 'tenant1.localhost']);
    tenancy()->initialize($tenant1);

    // Create a department in tenant context
    $department = Department::create([
        'name' => 'Engineering',
        'description' => 'Software development team',
    ]);

    expect($department->tenant_id)->toBe($tenant1->id);

    // Create another tenant and verify isolation
    $tenant2 = Tenant::factory()->create(['id' => 'tenant2']);
    $tenant2->domains()->create(['domain' => 'tenant2.localhost']);
    tenancy()->initialize($tenant2);

    // Should not see the department from the first tenant
    $departments = Department::all();
    expect($departments)->toHaveCount(0);

    // Switch back to first tenant
    tenancy()->initialize($tenant1);
    $departments = Department::all();
    expect($departments)->toHaveCount(1);
    expect($departments->first()->name)->toBe('Engineering');
});

test('user permissions are properly assigned on tenant creation', function () {
    // Seed permissions and roles
    $seeder = new TenantPermissionsSeeder;
    $seeder->run();

    // Create a subscription plan first
    $plan = SubscriptionPlan::create([
        'name' => 'Premium Plan',
        'slug' => 'premium',
        'price_monthly' => 49.99,
        'features' => ['feature1', 'feature2', 'feature3'],
    ]);

    $service = new TenantCreationService;

    $tenant = $service->createTenant([
        'company_name' => 'Permission Test Company',
        'domain' => 'permissiontest',
        'plan_id' => $plan->id,
        'admin_name' => 'Permission Admin',
        'email' => 'admin@permissiontest.com',
        'payment_type' => 'monthly',
    ]);

    tenancy()->initialize($tenant);

    $adminUser = User::where('email', 'admin@permissiontest.com')->first();

    expect($adminUser)->not->toBeNull();
    expect($adminUser->hasRole('super-admin'))->toBeTrue();
    expect($adminUser->hasPermissionTo('view users'))->toBeTrue();
    expect($adminUser->hasPermissionTo('create departments'))->toBeTrue();
});
