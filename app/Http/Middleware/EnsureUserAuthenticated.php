<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via the 'web' guard
        if (!Auth::guard('web')->check()) {
            // Store the intended URL
            session()->put('url.intended', $request->url());
            
            // Redirect to login page
            return redirect()->route('auth.google.login');
        }

        // Ensure the user is not an admin (public users only)
        $user = Auth::guard('web')->user();
        if ($user && $user->isAdmin()) {
            Auth::guard('web')->logout();
            return redirect()->route('welcome')->with('error', 'This page is for public users only.');
        }

        return $next($request);
    }
}
