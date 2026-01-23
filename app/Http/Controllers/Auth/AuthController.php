<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    /**
     * Handle direct login via signed URL.
     */
    public function login(Request $request)
    {
        // Validate the signed URL
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired login link.');
        }

        $user = User::withoutTenancy()->findOrFail($request->user_id);

        // Log the user in
        Auth::login($user);

        // Redirect to the tenant's dashboard or onboarding
        return redirect('/dashboard');
    }
}
