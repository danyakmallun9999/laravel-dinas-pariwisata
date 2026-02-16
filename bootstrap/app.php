<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.user' => \App\Http\Middleware\EnsureUserAuthenticated::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'midtrans.ip' => \App\Http\Middleware\MidtransIpWhitelist::class,
        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        
        $middleware->validateCsrfTokens(except: [
            '/webhooks/midtrans',  // Bypass CSRF for Midtrans payment notifications
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
