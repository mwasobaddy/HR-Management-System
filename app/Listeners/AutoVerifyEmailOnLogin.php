<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class AutoVerifyEmailOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Check if email_verified_at is null and set it to now
        if (is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => now()]);
        }
    }
}
