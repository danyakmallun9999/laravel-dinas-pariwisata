<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSetting extends Model
{
    protected $fillable = [
        'type',
        'media_paths',
        'title_id',
        'title_en',
        'subtitle_id',
        'subtitle_en',
        'badge_id',
        'badge_en',
        'button_text_id',
        'button_text_en',
        'button_link',
    ];

    protected $casts = [
        'media_paths' => 'array',
    ];
}
