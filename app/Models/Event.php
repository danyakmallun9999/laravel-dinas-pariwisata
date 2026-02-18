<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'location',
        'start_date',
        'end_date',
        'image',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = \Illuminate\Support\Str::slug($event->title).'-'.\Illuminate\Support\Str::random(5);
            }
        });

        static::updating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = \Illuminate\Support\Str::slug($event->title).'-'.\Illuminate\Support\Str::random(5);
            }
        });
    }

    /**
     * Get the user who created this event.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope a query to only include events owned by a specific user.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function getTranslatedTitleAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && !empty($this->title_en)) {
            return $this->title_en;
        }
        return $this->title;
    }

    public function getTranslatedDescriptionAttribute()
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && !empty($this->description_en) && trim(strip_tags($this->description_en)) !== '') {
            return $this->description_en;
        }
        return $this->description;
    }

}
