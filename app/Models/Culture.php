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
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null; // or default image
        }

        if (Str::startsWith($this->image, 'http')) {
            return $this->image;
        }

        // Check if file exists in storage via symlink path logic or filesystem
        // If it starts with 'images/culture', it might be from storage.
        // Static images are in 'images/kuliner-jppr' or 'images/culture' public folder directly.
        
        // If file exists in 'public/storage/' . $this->image
        if (file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }

        return asset($this->image);
    }
}
