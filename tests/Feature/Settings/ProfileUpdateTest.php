<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;

class ProfileUpdateTest extends TenantTestCase
{
    use RefreshDatabase;

    protected bool $tenancy = true;

    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->get(route('profile.edit', [], false));

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->patch(route('profile.update', [], false), [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->patch(route('profile.update', [], false), [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->delete(route('profile.destroy', [], false), [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->from(route('profile.edit'))
            ->delete(route('profile.destroy', [], false), [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect(route('profile.edit'));

        $this->assertNotNull($user->fresh());
    }
}
