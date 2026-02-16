<?php

namespace App\Models;

/**
 * ISO-03: Dedicated Admin model for the 'admin' auth guard.
 * 
 * Uses a global scope to ensure that only users with is_admin=true
 * can authenticate via the admin guard. This provides type-safety
 * so Auth::guard('admin')->attempt() can never resolve a public user.
 */
class Admin extends User
{
    protected $table = 'users';

    /**
     * Tell Spatie Permission which guard this model uses.
     */
    protected $guard_name = 'admin';

    /**
     * Override fillable to allow is_admin mass assignment for admin creation.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'email_verified_at',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('admin_only', function ($query) {
            $query->where('is_admin', true);
        });
    }
}
