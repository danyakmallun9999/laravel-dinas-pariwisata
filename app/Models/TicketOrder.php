<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id', // Added
        'order_number',
        'ticket_number',
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
        'expiry_time',
        'payment_info',
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
        'expiry_time' => 'datetime',
        'payment_info' => 'array',
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
            $orderNumber = 'TKT-'.date('Ymd').'-'.strtoupper(Str::random(6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Generate unique ticket number (atomic, race-safe).
     * Uses DB transaction + lockForUpdate to prevent duplicate generation.
     */
    public function generateTicketNumber()
    {
        if ($this->ticket_number) {
            return $this->ticket_number;
        }

        return DB::transaction(function () {
            // Re-fetch with lock to prevent concurrent generation
            $order = self::lockForUpdate()->find($this->id);

            // Double-check after acquiring lock (another process may have generated it)
            if ($order->ticket_number) {
                return $order->ticket_number;
            }

            $maxRetries = 10;
            for ($i = 0; $i < $maxRetries; $i++) {
                // 12-char random for higher entropy (62^12 ≈ 3.2 × 10^21)
                $random = strtoupper(Str::random(12));
                $ticketNumber = 'TIX-' . $random;

                // Check uniqueness before save
                if (!self::where('ticket_number', $ticketNumber)->exists()) {
                    $order->ticket_number = $ticketNumber;
                    $order->qr_code = $ticketNumber;
                    $order->save();

                    // Update the current instance
                    $this->ticket_number = $ticketNumber;
                    $this->qr_code = $ticketNumber;

                    return $ticketNumber;
                }
            }

            throw new \RuntimeException('Failed to generate unique ticket number after ' . $maxRetries . ' retries');
        });
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
        return match ($this->status) {
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
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'used' => 'Sudah Digunakan',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    /**
     * Get status-specific UI configuration for dynamic rendering.
     */
    public function getStatusConfigAttribute(): array
    {
        $base = [
            'paid' => [
                'bg' => 'bg-green-50 dark:bg-green-900/20',
                'border' => 'border-green-200 dark:border-green-800',
                'iconBg' => 'bg-green-100 dark:bg-green-900/40',
                'iconColor' => 'text-green-500',
                'title' => 'Pembayaran Berhasil',
                'subtitle' => 'Tiket siap digunakan',
                'showQr' => true,
                'animation' => 'scale',
            ],
            'pending' => [
                'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                'border' => 'border-yellow-200 dark:border-yellow-800',
                'iconBg' => 'bg-yellow-100 dark:bg-yellow-900/40',
                'iconColor' => 'text-yellow-500',
                'title' => 'Menunggu Pembayaran',
                'subtitle' => 'Selesaikan pembayaran sebelum waktu habis',
                'showQr' => false,
                'animation' => 'pulse',
            ],
            'cancelled' => [
                'bg' => 'bg-red-50 dark:bg-red-900/20',
                'border' => 'border-red-200 dark:border-red-800',
                'iconBg' => 'bg-red-100 dark:bg-red-900/40',
                'iconColor' => 'text-red-500',
                'title' => 'Pesanan Dibatalkan',
                'subtitle' => 'Tiket tidak dapat digunakan',
                'showQr' => false,
                'animation' => 'fade',
            ],
            'used' => [
                'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                'border' => 'border-blue-200 dark:border-blue-800',
                'iconBg' => 'bg-blue-100 dark:bg-blue-900/40',
                'iconColor' => 'text-blue-500',
                'title' => 'Tiket Sudah Digunakan',
                'subtitle' => $this->check_in_time
                    ? 'Digunakan pada '.$this->check_in_time->translatedFormat('d F Y, H:i')
                    : 'Tiket telah digunakan',
                'showQr' => true,
                'animation' => 'fade',
            ],
        ];

        return $base[$this->status] ?? $base['cancelled'];
    }
}
