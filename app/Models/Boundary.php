<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boundary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'geometry',
        'description',
        'area_hectares',
    ];

    protected $casts = [
        'geometry' => 'array',
        'area_hectares' => 'decimal:4',
    ];
}
