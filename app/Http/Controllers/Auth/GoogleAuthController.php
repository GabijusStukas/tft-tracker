<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google’s OAuth page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google, persist JWT cookie and redirect.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            return redirect('/login')->with('error', 'Google authentication failed');
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'name'              => $googleUser->name,
                'password'          => bcrypt(Str::random()),
                'email_verified_at' => now(),
                'avatar'            => $googleUser->avatar,
            ]
        );

        $token = Auth::guard('api')->login($user);

        $ttlMinutes = Auth::guard('api')->factory()->getTTL();

        return redirect()->route('dashboard')->cookie(
            'jwt_token',
            $token,
            $ttlMinutes,
            '/',
            null,
            request()->isSecure(),
            false,
            false,
            'lax'
        );
    }
}
