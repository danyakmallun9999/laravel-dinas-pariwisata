<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TicketAnalyticsService;
use Illuminate\Http\Request;

class TicketDashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(TicketAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $stats = $this->analyticsService->getDailyStats();
        $salesChart = $this->analyticsService->getSalesChartData();
        $ticketTypes = $this->analyticsService->getTicketTypeBreakdown();
        $occupancy = $this->analyticsService->getOccupancyByPlace();
        $recentTransactions = $this->analyticsService->getRecentTransactions();

        return view('admin.tickets.dashboard', compact(
            'stats',
            'salesChart',
            'ticketTypes',
            'occupancy',
            'recentTransactions'
        ));
    }
}
