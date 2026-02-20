<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CultureImage extends Model
{
    use HasFactory;

    protected $fillable = ['culture_id', 'image_path'];

    public function culture()
    {
        return $this->belongsTo(Culture::class);
    }
}
