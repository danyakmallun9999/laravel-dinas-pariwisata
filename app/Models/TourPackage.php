<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    protected $fillable = [
        'place_id',
        'travel_agency_id',
        'name',
        'description',
        'price_start',
        'price_end',
        'duration_days',
        'duration_nights',
        'itinerary',
        'inclusions',
    ];

    protected $casts = [
        'price_start' => 'decimal:2',
        'price_end' => 'decimal:2',
        'itinerary' => 'array',
        'inclusions' => 'array',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function travelAgency()
    {
        return $this->belongsTo(TravelAgency::class);
    }
}
