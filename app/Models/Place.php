<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Place extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($place) {
            if (empty($place->slug)) {
                $place->slug = Str::slug($place->name).'-'.Str::random(5);
            }
        });

        static::updating(function ($place) {
            if (empty($place->slug)) {
                $place->slug = Str::slug($place->name).'-'.Str::random(5);
            }
        });
    }

    protected $fillable = [
        'category_id',
        'name',
        'name_en',
        'address',
        'slug',
        'description',
        'description_en',
        'image_path',
        'latitude',
        'longitude',
        // 'longitude', // Duplicate removed
        'opening_hours',
        'contact_info',
        'rating',
        'rating',
        // 'website', // Removed
        'google_maps_link',
        'google_maps_link',
        'ownership_status',
        'manager',
        'rides',
        'facilities',
        'social_media',
        'kecamatan',
        'created_by',
    ];

    // Accessor for Translated Name
    public function getTranslatedNameAttribute()
    {
        if (app()->getLocale() == 'en' && !empty($this->name_en)) {
            return $this->name_en;
        }
        return $this->name;
    }

    // Accessor for Translated Description
    // Renamed from getDescriptionAttribute to avoid conflict with raw attribute access in admin
    // BUT we need to check if existing code relies on $place->description automatically translating.
    // The previous implementation overrode getDescriptionAttribute.
    // To be safe and consistent with Post, let's use explicit translated_description
    // AND keep description as raw, OR keep the override if we want auto-magic.
    // However, the user plan said "Update/Rename getDescriptionAttribute to getTranslatedDescriptionAttribute".
    // So let's do that to avoid admin form issues where it shows English content in Indonesian field.

    public function getTranslatedDescriptionAttribute()
    {
        if (app()->getLocale() == 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function activeTickets()
    {
        return $this->tickets()->where('is_active', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function images()
    {
        return $this->hasMany(PlaceImage::class);
    }

    protected $casts = [
        'rides' => 'array',
        'facilities' => 'array',
        'social_media' => 'array',
    ];

    protected $appends = [
        'translated_name',
        'translated_description',
    ];

    /**
     * Get the user who created this place.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope a query to only include places owned by a specific user.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}
