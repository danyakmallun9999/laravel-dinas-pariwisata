<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelAgency extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'business_type',
        'nib',
        'address',
        'description',
        'logo_path',
        'contact_wa',
        'website',
        'instagram',
    ];

    public function places()
    {
        return $this->belongsToMany(Place::class);
    }
}
