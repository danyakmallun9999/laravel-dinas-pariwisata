<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        // Store the previous URL so user returns to it after login
        $previousUrl = url()->previous();
        if ($previousUrl && $previousUrl !== route('auth.google.login')) {
            session()->put('url.intended', $previousUrl);
        }

        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // MED-03: Wrap in DB transaction with locks to prevent account linking race condition
            $user = \Illuminate\Support\Facades\DB::transaction(function () use ($googleUser) {
                // Prioritize google_id match first
                $user = User::where('google_id', $googleUser->getId())->lockForUpdate()->first();

                if (!$user) {
                    // Only link by email if the account has no Google ID yet
                    $existingUser = User::where('email', $googleUser->getEmail())
                        ->whereNull('google_id')
                        ->lockForUpdate()
                        ->first();

                    if ($existingUser) {
                        // Block admin accounts from being linked via Google
                        if ($existingUser->isAdmin()) {
                            return null; // Signal to redirect with error
                        }

                        $existingUser->update([
                            'google_id' => $googleUser->getId(),
                            'avatar' => $googleUser->getAvatar(),
                        ]);
                        $user = $existingUser;
                    } else {
                        // Create new public user
                        $user = User::create([
                            'name' => $googleUser->getName(),
                            'email' => $googleUser->getEmail(),
                            'google_id' => $googleUser->getId(),
                            'avatar' => $googleUser->getAvatar(),
                            'email_verified_at' => now(),
                        ]);
                    }
                }

                return $user;
            });

            // Handle admin block signal from transaction
            if ($user === null) {
                return redirect()->route('welcome')
                    ->with('error', __('Admin users cannot login via Google. Please use the admin login page.'));
            }

            // Ensure user is not an admin
            if ($user->isAdmin()) {
                return redirect()->route('welcome')
                    ->with('error', __('Admin users cannot login via Google. Please use the admin login page.'));
            }

            // Log the user in using the 'web' guard
            Auth::guard('web')->login($user);

            // Regenerate session to prevent session fixation
            request()->session()->regenerate();

            // ISO-05: Sanitize intended URL — never redirect Google users to admin routes
            $intended = session('url.intended', route('tickets.my'));
            if (str_starts_with(parse_url($intended, PHP_URL_PATH) ?? '', '/admin')) {
                session()->forget('url.intended');
            }

            // Redirect to intended URL or default to my tickets page
            return redirect()->intended(route('tickets.my'));

        } catch (\Exception $e) {
            return redirect()->route('auth.google.login')
                ->with('error', __('Unable to login with Google. Please try again.'));
        }
    }

    /**
     * Show the login page for public users.
     */
    public function showLoginPage()
    {
        return view('auth.public-login');
    }

    /**
     * Log the user out of the application (public users only).
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        // Completely destroy session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Forget remember-me cookie if present
        $cookie = cookie()->forget(Auth::guard('web')->getRecallerName());

        return redirect()->route('welcome')
            ->with('success', __('You have been logged out successfully.'))
            ->withCookie($cookie);
    }
}
