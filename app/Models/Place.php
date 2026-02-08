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
        'address',
        'slug',
        'description',
        'description_en',
        'image_path',
        'latitude',
        'longitude',
        'longitude',
        'opening_hours',
        'contact_info',
        'rating',
        'website',
        'google_maps_link',
        'ownership_status',
        'manager',
        'rides',
        'facilities',
        'social_media',
        'kecamatan',
    ];

    public function getDescriptionAttribute($value)
    {
        if (app()->getLocale() === 'en' && !empty($this->attributes['description_en'])) {
            return $this->attributes['description_en'];
        }
        return $value;
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
    ];
}
