<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketAnalyticsService
{
    /**
     * Get daily statistics for the dashboard pulse.
     */
    public function getDailyStats()
    {
        $today = Carbon::today();

        // Revenue today (paid orders only)
        $revenue = TicketOrder::whereDate('payed_at', $today)
            ->where('status', 'paid')
            ->sum('total_price');

        // Tickets sold today (paid + pending count as sold for occupancy? active tickets)
        // Usually revenue counts 'paid', but occupancy counts 'valid' tickets for today
        // Let's count sold = paid tickets created today
        $ticketsSold = TicketOrder::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('quantity');

        // Active Visitors (checked in today)
        $activeVisitors = TicketOrder::whereDate('check_in_time', $today)
            ->sum('quantity');

        // Pending Orders (Potential revenue)
        $pendingOrders = TicketOrder::whereDate('created_at', $today)
            ->where('status', 'pending')
            ->count();

        return [
            'revenue' => $revenue,
            'tickets_sold' => $ticketsSold,
            'active_visitors' => $activeVisitors,
            'pending_orders' => $pendingOrders,
        ];
    }

    /**
     * Get sales chart data for the last 30 days.
     */
    public function getSalesChartData($days = 30)
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days - 1);

        $sales = TicketOrder::select(
            DB::raw('DATE(payed_at) as date'),
            DB::raw('SUM(total_price) as total_revenue'),
            DB::raw('SUM(quantity) as total_tickets')
        )
            ->where('status', 'paid')
            ->whereBetween('payed_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Initialize arrays
        $labels = [];
        $revenueData = [];
        $ticketsData = [];

        for ($i = 0; $i < $days; $i++) {
            $dateProps = $startDate->copy()->addDays($i);
            $dateKey = $dateProps->format('Y-m-d');
            $record = $sales->get($dateKey);

            $labels[] = $dateProps->format('d M');
            $revenueData[] = $record ? (float) $record->total_revenue : 0;
            $ticketsData[] = $record ? (int) $record->total_tickets : 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'tickets' => $ticketsData,
        ];
    }

    /**
     * Get monthly sales chart data for the last 12 months (1 Year View).
     */
    public function getMonthlySalesChartData($months = 12)
    {
        $endDate = Carbon::today()->endOfMonth();
        $startDate = Carbon::today()->startOfMonth()->subMonths($months - 1);

        $sales = TicketOrder::select(
            DB::raw('YEAR(payed_at) as year'),
            DB::raw('MONTH(payed_at) as month'),
            DB::raw('SUM(total_price) as total_revenue'),
            DB::raw('SUM(quantity) as total_tickets')
        )
            ->where('status', 'paid')
            ->whereBetween('payed_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Initialize arrays
        $labels = [];
        $revenueData = [];
        $ticketsData = [];

        for ($i = 0; $i < $months; $i++) {
            $dateProps = $startDate->copy()->addMonths($i);
            $year = $dateProps->year;
            $month = $dateProps->month;

            // Find record for this month/year
            $record = $sales->filter(function ($item) use ($year, $month) {
                return $item->year == $year && $item->month == $month;
            })->first();

            $labels[] = $dateProps->format('M Y'); // e.g. "Feb 2025"
            $revenueData[] = $record ? (float) $record->total_revenue : 0;
            $ticketsData[] = $record ? (int) $record->total_tickets : 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'tickets' => $ticketsData,
        ];
    }

    /**
     * Get ticket type breakdown.
     */
    public function getTicketTypeBreakdown()
    {
        return TicketOrder::join('tickets', 'ticket_orders.ticket_id', '=', 'tickets.id')
            ->select('tickets.type', DB::raw('SUM(ticket_orders.quantity) as count'))
            ->where('ticket_orders.status', 'paid')
            ->groupBy('tickets.type')
            ->get();
    }

    /**
     * Get occupancy rate by place.
     */
    public function getOccupancyByPlace()
    {
        // This is complex because quota is per ticket/place per day.
        // Simplified: Get total tickets sold for today per place vs Place capacity (if exists)
        // For now, let's just return tickets sold per place for today
        $today = Carbon::today();

        return DB::table('ticket_orders')
            ->join('tickets', 'ticket_orders.ticket_id', '=', 'tickets.id')
            ->join('places', 'tickets.place_id', '=', 'places.id')
            ->select('places.name', DB::raw('SUM(ticket_orders.quantity) as sold'))
            ->where('ticket_orders.status', 'paid')
            ->whereDate('ticket_orders.visit_date', $today)
            ->groupBy('places.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent transactions.
     */
    public function getRecentTransactions($limit = 5)
    {
        return TicketOrder::with(['ticket.place'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
