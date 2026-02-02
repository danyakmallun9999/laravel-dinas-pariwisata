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
                $place->slug = Str::slug($place->name) . '-' . Str::random(5);
            }
        });

        static::updating(function ($place) {
            if (empty($place->slug)) {
                $place->slug = Str::slug($place->name) . '-' . Str::random(5);
            }
        });
    }

    protected $fillable = [
        'category_id',
        'name',
        'address',
        'slug',
        'description',
        'image_path',
        'latitude',
        'longitude',
        'ticket_price',
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
    ];

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
