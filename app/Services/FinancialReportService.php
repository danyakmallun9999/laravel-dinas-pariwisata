<?php

namespace App\Services;

use App\Models\TicketOrder;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportService
{
    /**
     * Get financial summary for a date range.
     */
    public function getSummary($startDate, $endDate, $userId = null)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $orders = TicketOrder::whereBetween('paid_at', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'used'])
            ->when($userId, function($q) use ($userId) {
                $q->whereHas('ticket.place', function($subQ) use ($userId) {
                    $subQ->where('created_by', $userId);
                });
            });

        // Gross Revenue (Total paid by customers)
        $grossRevenue = $orders->sum('total_price');

        // Tax & Fees
        $taxAmount = $orders->sum('tax_amount');
        $appFees = $orders->sum('app_fee');

        // Refunds (Consider refunds that happened in this period, regardless of when order was made)
        $refunds = Transaction::where('type', 'refund')
            ->where('status', 'success')
            ->whereBetween('transacted_at', [$startDate, $endDate])
            ->sum('amount');

        // Net Revenue = Gross - Tax - Fees - Refunds
        // Note: verify if total_price includes tax/fees. Usually Gross includes everything.
        // If total_price = Unit Price + Tax + Fee, then Net = Gross - Tax - Fee - Refund.
        $netRevenue = $grossRevenue - $taxAmount - $appFees - $refunds;

        return [
            'gross_revenue' => $grossRevenue,
            'net_revenue' => $netRevenue,
            'tax_amount' => $taxAmount,
            'app_fees' => $appFees,
            'total_refunds' => $refunds,
            'transaction_count' => $orders->count(),
            'tickets_sold' => $orders->sum('quantity'),
        ];
    }

    /**
     * Get daily revenue trend.
     */
    public function getDailyTrend($startDate, $endDate, $userId = null)
    {
        return TicketOrder::whereBetween('paid_at', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'used'])
            ->when($userId, function($q) use ($userId) {
                $q->whereHas('ticket.place', function($subQ) use ($userId) {
                    $subQ->where('created_by', $userId);
                });
            })
            ->selectRaw('DATE(paid_at) as date, SUM(total_price) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get revenue by payment method.
     */
    public function getByPaymentMethod($startDate, $endDate, $userId = null)
    {
        return TicketOrder::whereBetween('paid_at', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'used'])
            ->when($userId, function($q) use ($userId) {
                $q->whereHas('ticket.place', function($subQ) use ($userId) {
                    $subQ->where('created_by', $userId);
                });
            })
            ->select(
                DB::raw('COALESCE(payment_channel, payment_method_detail, payment_method) as payment_method'),
                DB::raw('SUM(total_price) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('COALESCE(payment_channel, payment_method_detail, payment_method)'))
            ->orderByDesc('revenue')
            ->get();
    }

    /**
     * Get revenue by Ticket (Wisata/Event).
     */
    public function getByTicket($startDate, $endDate, $userId = null)
    {
        return TicketOrder::whereBetween('paid_at', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'used'])
            ->when($userId, function($q) use ($userId) {
                $q->whereHas('ticket.place', function($subQ) use ($userId) {
                    $subQ->where('created_by', $userId);
                });
            })
            ->with('ticket.place')
            ->select('ticket_id', DB::raw('SUM(total_price) as revenue'), DB::raw('SUM(quantity) as tickets_sold'))
            ->groupBy('ticket_id')
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($item) {
                $ticket = $item->ticket;
                $placeName = $ticket && $ticket->place ? $ticket->place->name : '';
                $ticketName = $ticket ? $ticket->name : 'Unknown Ticket';

                return [
                    'ticket_name' => $ticketName,
                    'place_name'  => $placeName,
                    'ticket_type' => $ticket ? $ticket->type : '-',
                    'revenue'     => $item->revenue,
                    'tickets_sold' => $item->tickets_sold,
                ];
            });
    }
}
