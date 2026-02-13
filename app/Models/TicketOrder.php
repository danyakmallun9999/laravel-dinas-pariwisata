<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TicketOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id', // Added
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'visit_date',
        'quantity',
        'total_price',
        'tax_amount', // Added
        'app_fee', // Added
        'discount_amount', // Added
        'status',
        'payment_method',
        'qr_code',
        'notes',
        'snap_token',
        'payment_gateway_id',
        'payment_gateway_url',
        'payment_method_detail',
        'payment_channel',
        'paid_at',
        'payed_at',
        'check_in_time',
        'unit_price',
        'customer_city',
        'customer_country',
        'customer_province',
        'payment_gateway_ref',
        'refund_status',
        'refund_amount', // Added
        'refunded_at', // Added
    ];

    protected $casts = [
        'visit_date' => 'date',
        'total_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'app_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'paid_at' => 'datetime',
        'payed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'check_in_time' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = $order->generateOrderNumber();
            }
            if (empty($order->qr_code)) {
                $order->qr_code = $order->generateQRCodeData();
            }
        });
    }

    /**
     * Get the ticket that owns the order.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the order.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Generate unique order number.
     */
    public function generateOrderNumber()
    {
        do {
            $orderNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Generate QR code data.
     */
    public function generateQRCodeData()
    {
        // Return simple string for better density/readability & scanning performance
        return $this->order_number;
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include paid orders.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include used orders.
     */
    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'used' => 'blue',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'used' => 'Sudah Digunakan',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }
}
