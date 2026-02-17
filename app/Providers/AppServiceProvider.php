<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use App\Models\{Place, Event as EventModel, Post, Category, Ticket, User};
use App\Policies\{PlacePolicy, EventPolicy, PostPolicy, CategoryPolicy, TicketPolicy};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Place::class, PlacePolicy::class);
        Gate::policy(EventModel::class, EventPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);

        // Super admin bypass - super admins can do anything
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register NoCaptcha Alias safely
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('NoCaptcha', \Anhskohbo\NoCaptcha\Facades\NoCaptcha::class);

        // AUTH-09: Enforce strong password policy for admin users
        Password::defaults(function () {
            return Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        // AUTH-05: Login event listeners for security monitoring
        Event::listen(Login::class, function ($event) {
            Log::info('Admin login successful', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ]);
        });

        Event::listen(Failed::class, function ($event) {
            Log::warning('Login attempt failed', [
                'email' => $event->credentials['email'] ?? 'unknown',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ]);
        });

        Event::listen(Lockout::class, function ($event) {
            Log::critical('Account locked out due to too many login attempts', [
                'email' => $event->request->input('email'),
                'ip' => $event->request->ip(),
                'user_agent' => $event->request->userAgent(),
            ]);
        });
    }
}
