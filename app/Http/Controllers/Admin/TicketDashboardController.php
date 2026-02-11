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
        $salesChart = $this->analyticsService->getSalesChartData(365);
        $monthlySalesChart = $this->analyticsService->getMonthlySalesChartData(12);
        $ticketTypes = $this->analyticsService->getTicketTypeBreakdown();
        $occupancy = $this->analyticsService->getOccupancyByPlace();
        $recentTransactions = $this->analyticsService->getRecentTransactions();

        return view('admin.tickets.dashboard', compact(
            'stats',
            'salesChart',
            'monthlySalesChart',
            'ticketTypes',
            'occupancy',
            'recentTransactions'
        ));
    }
}
