<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Tests\TestCase;

class AutoVerifyEmailTest extends TestCase
{
    public function test_email_is_automatically_verified_on_login_when_null()
    {
        // Create a user with null email_verified_at
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Fire the login event
        event(new Login('web', $user, false));

        // Refresh the user from database
        $user->refresh();

        // Assert that email_verified_at is now set
        $this->assertNotNull($user->email_verified_at);
        $this->assertInstanceOf(\Carbon\CarbonImmutable::class, $user->email_verified_at);
    }

    public function test_email_verification_is_not_changed_when_already_verified()
    {
        $verifiedAt = now()->subDays(1);

        // Create a user with existing email_verified_at
        $user = User::factory()->create([
            'email_verified_at' => $verifiedAt,
        ]);

        // Fire the login event
        event(new Login('web', $user, false));

        // Refresh the user from database
        $user->refresh();

        // Assert that email_verified_at remains unchanged (compare timestamps)
        $this->assertEquals($verifiedAt->toDateTimeString(), $user->email_verified_at->toDateTimeString());
    }
}
