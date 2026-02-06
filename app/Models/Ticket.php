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
        'description',
        'price',
        'quota',
        'valid_days',
        'is_active',
        'terms_conditions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

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
}
