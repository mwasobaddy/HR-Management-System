<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the database with subscription plans
        $this->seed([
            \Database\Seeders\SubscriptionPlanSeeder::class,
            \Database\Seeders\TenantPermissionsSeeder::class,
        ]);
    }

    public function test_free_subscription_can_be_created_successfully()
    {
        Notification::fake();

        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        $subscriptionData = [
            'plan_id' => $freePlan->id,
            'company_name' => 'Test Company',
            'domain' => 'testcompany',
            'email' => 'admin@testcompany.com',
            'admin_name' => 'Test Admin',
        ];

        $response = $this->post(route('subscribe.store'), $subscriptionData);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Account created! Check your email for login instructions.');

        // Verify tenant was created
        $this->assertDatabaseHas('tenants', [
            'company_name' => 'Test Company',
            'slug' => 'testcompany',
            'plan_id' => $freePlan->id,
            'subscription_status' => 'trial',
            'database_type' => 'shared',
        ]);

        $tenant = Tenant::where('slug', 'testcompany')->first();
        $this->assertNotNull($tenant);

        // Verify domain was created
        $this->assertDatabaseHas('domains', [
            'tenant_id' => $tenant->id,
            'domain' => '127.0.0.1.testcompany.localhost',
        ]);

        // Verify admin user was created
        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenant->id,
            'name' => 'Test Admin',
            'email' => 'admin@testcompany.com',
            'employee_id' => 'EMP001',
            'is_active' => true,
        ]);

        $user = User::where('email', 'admin@testcompany.com')->first();
        $this->assertNotNull($user);

        // Verify user has super-admin role
        $this->assertTrue($user->hasRole('super-admin'));

        // Verify company profile was created

        $tenant = Tenant::where('slug', 'testcompany')->first();
        $this->assertNotNull($tenant);

        // Verify domain was created
        $this->assertDatabaseHas('domains', [
            'tenant_id' => $tenant->id,
            'domain' => '127.0.0.1.testcompany.localhost',
        ]);

        // Verify admin user was created
        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenant->id,
            'name' => 'Test Admin',
            'email' => 'admin@testcompany.com',
            'employee_id' => 'EMP001',
            'is_active' => true,
        ]);

        $user = User::where('email', 'admin@testcompany.com')->first();
        $this->assertNotNull($user);

        // Verify user has super-admin role
        $this->assertTrue($user->hasRole('super-admin'));

        // Verify company profile was created
        $this->assertDatabaseHas('company_profiles', [
            'tenant_id' => $tenant->id,
            'company_name' => 'Test Company',
            'timezone' => 'UTC',
            'currency' => 'USD',
        ]);

        // Verify welcome email was sent
        Notification::assertSentTo($user, \App\Notifications\WelcomeCredentials::class);
    }

    public function test_subscription_show_page_works()
    {
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        $response = $this->get(route('subscribe', ['plan' => $freePlan->id]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('subscribe')
            ->has('plan')
            ->where('plan.id', $freePlan->id)
            ->where('plan.name', $freePlan->name)
            ->where('plan.price', $freePlan->price_monthly)
        );
    }

    public function test_subscription_show_requires_plan_parameter()
    {
        $response = $this->get(route('subscribe'));

        $response->assertRedirect('/billing');
        $response->assertSessionHas('error', 'Please select a plan first.');
    }

    public function test_subscription_show_validates_plan_exists()
    {
        $response = $this->get(route('subscribe', ['plan' => 999]));

        $response->assertRedirect('/billing');
        $response->assertSessionHas('error', 'Invalid plan selected.');
    }

    public function test_paid_subscription_requires_payment_info()
    {
        $paidPlan = SubscriptionPlan::where('slug', '!=', 'free')->first();

        $subscriptionData = [
            'plan_id' => $paidPlan->id,
            'company_name' => 'Test Company',
            'domain' => 'testcompany',
            'email' => 'admin@testcompany.com',
            'admin_name' => 'Test Admin',
            // Missing payment info
        ];

        $response = $this->post(route('subscribe.store'), $subscriptionData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['payment_type', 'card_number', 'expiry', 'cvv']);
    }

    public function test_domain_must_be_unique()
    {
        Notification::fake();

        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        // Create first tenant
        $this->post(route('subscribe.store'), [
            'plan_id' => $freePlan->id,
            'company_name' => 'Test Company 1',
            'domain' => 'testcompany',
            'email' => 'admin1@testcompany.com',
            'admin_name' => 'Test Admin 1',
        ]);

        // Try to create second tenant with same domain
        $response = $this->post(route('subscribe.store'), [
            'plan_id' => $freePlan->id,
            'company_name' => 'Test Company 2',
            'domain' => 'testcompany',
            'email' => 'admin2@testcompany.com',
            'admin_name' => 'Test Admin 2',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['domain']);
    }

    public function test_email_must_be_unique_across_system()
    {
        Notification::fake();

        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        // Create first tenant
        $this->post(route('subscribe.store'), [
            'plan_id' => $freePlan->id,
            'company_name' => 'Test Company 1',
            'domain' => 'testcompany1',
            'email' => 'admin@testcompany.com',
            'admin_name' => 'Test Admin 1',
        ]);

        // Try to create second tenant with same email
        $response = $this->post(route('subscribe.store'), [
            'plan_id' => $freePlan->id,
            'company_name' => 'Test Company 2',
            'domain' => 'testcompany2',
            'email' => 'admin@testcompany.com', // Same email
            'admin_name' => 'Test Admin 2',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_invalid_domain_format_is_rejected()
    {
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        $subscriptionData = [
            'plan_id' => $freePlan->id,
            'company_name' => 'Test Company',
            'domain' => 'test-company@invalid', // Invalid characters
            'email' => 'admin@testcompany.com',
            'admin_name' => 'Test Admin',
        ];

        $response = $this->post(route('subscribe.store'), $subscriptionData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['domain']);
    }

    public function test_subscription_creation_handles_exceptions_gracefully()
    {
        $freePlan = SubscriptionPlan::where('slug', 'free')->first();

        // Mock the tenant service to throw an exception
        $this->mock(\App\Services\TenantCreationService::class, function ($mock) {
            $mock->shouldReceive('createTenant')->andThrow(new \Exception('Database error'));
        });

        $subscriptionData = [
            'plan_id' => $freePlan->id,
            'company_name' => 'Test Company',
            'domain' => 'testcompany',
            'email' => 'admin@testcompany.com',
            'admin_name' => 'Test Admin',
        ];

        $response = $this->post(route('subscribe.store'), $subscriptionData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['error' => 'Failed to create account. Please try again or contact support.']);
    }
}
