<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // X-Frame-Options: Prevent clickjacking (allow same-origin for TinyMCE)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-Content-Type-Options: Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-XSS-Protection: XSS protection for legacy browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy: Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content-Security-Policy: Prevent XSS, clickjacking, data injection
        // Adjusted to allow TinyMCE editor to function properly
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tiny.cloud https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.tiny.cloud https://cdnjs.cloudflare.com https://fonts.googleapis.com; " .
               "img-src 'self' data: https: blob:; " .
               "font-src 'self' data: https://cdn.tiny.cloud https://cdnjs.cloudflare.com https://fonts.gstatic.com; " .
               "connect-src 'self' https://cdn.tiny.cloud https://api.mymemory.translated.net https://*.midtrans.com; " .
               "frame-src 'self' https://cdn.tiny.cloud https://www.google.com; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'; " .
               "frame-ancestors 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        // Permissions-Policy: Control browser features
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Strict-Transport-Security (HSTS): Force HTTPS in production
        if (app()->environment('production') && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}

