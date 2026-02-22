<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image_path',
        'type',
        'published_at',
        'is_published',
        'author',
        'image_credit',
        'title_en',
        'view_count',
        'content_en',
        'stat_widgets',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'stat_widgets' => 'array',
    ];

    public function getTranslatedTitleAttribute()
    {
        if (app()->getLocale() == 'en' && ! empty($this->title_en)) {
            return $this->title_en;
        }

        return $this->title;
    }

    public function getTranslatedContentAttribute()
    {
        if (app()->getLocale() == 'en' && ! empty($this->content_en)) {
            return $this->content_en;
        }

        return $this->content;
    }

    /**
     * Get the user who created this post.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope a query to only include posts owned by a specific user.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Get the visits for the post.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
