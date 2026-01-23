<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DirectLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_via_signed_url()
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);

        // Create a signed login URL
        $loginUrl = URL::temporarySignedRoute(
            'auth.login',
            Carbon::now()->addMinutes(60),
            ['user_id' => $user->id]
        );

        // Make request to the signed URL
        $response = $this->get($loginUrl);

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');

        // User should be authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::user()->id);
    }

    public function test_expired_signed_url_is_rejected()
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);

        // Create an expired signed URL
        $loginUrl = URL::temporarySignedRoute(
            'auth.login',
            Carbon::now()->subMinutes(1), // Already expired
            ['user_id' => $user->id]
        );

        // Make request to the expired signed URL
        $response = $this->get($loginUrl);

        // Should return 403
        $response->assertForbidden();

        // User should not be authenticated
        $this->assertFalse(Auth::check());
    }

    public function test_invalid_signature_is_rejected()
    {
        $tenant = Tenant::factory()->create();

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now(),
        ]);

        // Create a URL with invalid signature
        $loginUrl = route('auth.login', ['user_id' => $user->id]).'?signature=invalid';

        // Make request to the invalid URL
        $response = $this->get($loginUrl);

        // Should return 403
        $response->assertForbidden();

        // User should not be authenticated
        $this->assertFalse(Auth::check());
    }

    public function test_nonexistent_user_id_is_rejected()
    {
        // Create a signed URL with nonexistent user ID
        $loginUrl = URL::temporarySignedRoute(
            'auth.login',
            Carbon::now()->addMinutes(60),
            ['user_id' => 99999]
        );

        // Make request to the signed URL
        $response = $this->get($loginUrl);

        // Should return 404 (ModelNotFoundException)
        $response->assertNotFound();

        // User should not be authenticated
        $this->assertFalse(Auth::check());
    }
}
