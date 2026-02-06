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
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'visit_date',
        'quantity',
        'total_price',
        'status',
        'payment_method',
        'qr_code',
        'notes',
        'xendit_invoice_id',
        'xendit_invoice_url',
        'xendit_payment_method',
        'xendit_payment_channel',
        'paid_at',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
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
        return json_encode([
            'order_number' => $this->order_number,
            'ticket_id' => $this->ticket_id,
            'customer_name' => $this->customer_name,
            'visit_date' => $this->visit_date->format('Y-m-d'),
            'quantity' => $this->quantity,
        ]);
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
