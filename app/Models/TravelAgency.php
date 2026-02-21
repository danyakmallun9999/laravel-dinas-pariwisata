<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelAgency extends Model
{
    protected $fillable = [
        'name',
        'description',
        'logo_path',
        'contact_wa',
        'website',
        'instagram',
    ];

    public function tourPackages()
    {
        return $this->hasMany(TourPackage::class);
    }
}
