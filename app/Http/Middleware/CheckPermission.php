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
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized - Not authenticated.');
        }

        // Split permissions on '|' (pipe) to support OR syntax in route definitions
        // e.g. permission:view all financial reports|view own financial reports
        $allPermissions = [];
        foreach ($permissions as $permission) {
            $allPermissions = array_merge($allPermissions, explode('|', $permission));
        }

        if (!$user->hasAnyPermission($allPermissions)) {
            abort(403, 'Unauthorized - Insufficient permissions.');
        }

        return $next($request);
    }
}
