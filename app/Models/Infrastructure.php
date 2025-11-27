<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infrastructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'geometry',
        'length_meters',
        'width_meters',
        'condition',
        'description',
        'category_id',
    ];

    protected $casts = [
        'geometry' => 'array',
        'length_meters' => 'decimal:2',
        'width_meters' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
