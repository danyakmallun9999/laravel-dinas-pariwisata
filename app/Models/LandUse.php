<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'geometry',
        'area_hectares',
        'owner',
        'description',
    ];

    protected $casts = [
        'geometry' => 'array',
        'area_hectares' => 'decimal:4',
    ];
}
