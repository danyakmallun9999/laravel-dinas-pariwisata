<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Culture extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'image',
        'description',
        'content',
        'category',
        'location',
        'time',
        'youtube_url',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function images()
    {
        return $this->hasMany(CultureImage::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (Str::startsWith($this->image, 'http')) {
            return $this->image;
        }

        if (file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }

        return asset($this->image);
    }
}
