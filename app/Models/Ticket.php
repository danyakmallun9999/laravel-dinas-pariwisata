<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'name',
        'name_en',
        'type',
        'description',
        'description_en',
        'price',
        'price_weekend',
        'quota',
        'valid_days',
        'is_active',
        'terms_conditions',
        'terms_conditions_en',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_weekend' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'translated_name',
        'translated_description',
        'translated_terms_conditions',
    ];

    public function getTranslatedNameAttribute()
    {
        if (app()->getLocale() == 'en' && !empty($this->name_en)) {
            return $this->name_en;
        }
        return $this->name;
    }

    public function getTranslatedDescriptionAttribute()
    {
        if (app()->getLocale() == 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description;
    }

    public function getTranslatedTermsConditionsAttribute()
    {
        if (app()->getLocale() == 'en' && !empty($this->terms_conditions_en)) {
            return $this->terms_conditions_en;
        }
        return $this->terms_conditions;
    }

    /**
     * Get the place that owns the ticket.
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Get the orders for the ticket.
     */
    public function orders()
    {
        return $this->hasMany(TicketOrder::class);
    }

    /**
     * Scope a query to only include active tickets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available quota for a specific date.
     */
    public function getAvailableQuota($date)
    {
        if ($this->quota === null) {
            return null; // Unlimited
        }

        $bookedQuantity = $this->orders()
            ->where('visit_date', $date)
            ->whereIn('status', ['pending', 'paid'])
            ->sum('quantity');

        return max(0, $this->quota - $bookedQuantity);
    }

    /**
     * Check if ticket is available for booking on a specific date.
     */
    public function isAvailableOn($date, $quantity = 1)
    {
        if (!$this->is_active) {
            return false;
        }

        $available = $this->getAvailableQuota($date);
        
        if ($available === null) {
            return true; // Unlimited quota
        }

        return $available >= $quantity;
    }

    /**
     * Get the applicable price for a specific date.
     */
    public function getPriceForDate($date)
    {
        if ($this->isWeekend($date) && $this->price_weekend !== null) {
            return $this->price_weekend;
        }
        return $this->price;
    }

    /**
     * Check if a date is a weekend (Saturday or Sunday).
     */
    public function isWeekend($date)
    {
        $date = \Carbon\Carbon::parse($date);
        return $date->isWeekend();
    }
}
