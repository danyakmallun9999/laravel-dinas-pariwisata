<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // ISO-01: Use $request->user() to resolve from the guard set by auth middleware
        // auth() without guard resolves default 'web' guard — wrong for admin routes
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized - Not authenticated.');
        }

        if (!$user->hasAnyPermission($permissions)) {
            abort(403, 'Unauthorized - Insufficient permissions.');
        }

        return $next($request);
    }
}
