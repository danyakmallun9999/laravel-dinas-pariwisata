<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    // Note: is_admin is NOT in $fillable, so it is automatically guarded
    // from mass assignment. No explicit $guarded needed.

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Check if user is a public user.
     */
    public function isPublicUser(): bool
    {
        return $this->is_admin === false;
    }

    /**
     * Scope a query to only include admin users.
     */
    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    /**
     * Scope a query to only include public users.
     */
    public function scopePublicUser($query)
    {
        return $query->where('is_admin', false);
    }

    /**
     * Get all places owned by this user.
     */
    public function ownedPlaces()
    {
        return $this->hasMany(\App\Models\Place::class, 'created_by');
    }

    /**
     * Get all events owned by this user.
     */
    public function ownedEvents()
    {
        return $this->hasMany(\App\Models\Event::class, 'created_by');
    }

    /**
     * Get all posts owned by this user.
     */
    public function ownedPosts()
    {
        return $this->hasMany(\App\Models\Post::class, 'created_by');
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user can manage all content (not restricted by ownership).
     */
    public function canManageAllContent(): bool
    {
        return $this->hasAnyPermission(['manage all destinations', 'manage all events', 'manage all posts']);
    }
}
