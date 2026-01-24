<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TenantTestCase;

class PasswordUpdateTest extends TenantTestCase
{
    use RefreshDatabase;

    protected bool $tenancy = true;

    public function test_password_update_page_is_displayed()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->get(route('user-password.edit', [], false));

        $response->assertOk();
    }

    public function test_password_can_be_updated()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->from(route('user-password.edit'))
            ->put(route('user-password.update', [], false), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('user-password.edit'));

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password()
    {
        $user = User::factory()->forTenant($this->tenant)->create();

        $response = $this
            ->actingAs($user)
            ->withServerVariables($this->tenantRequestHeaders())
            ->from(route('user-password.edit'))
            ->put(route('user-password.update', [], false), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrors('current_password')
            ->assertRedirect(route('user-password.edit'));
    }
}
