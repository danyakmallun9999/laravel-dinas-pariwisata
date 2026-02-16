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
        $user = auth('admin')->user();
        $userIdFilter = null;

        // Pengelola wisata only sees data from their own destinations
        if (!$user->can('view all tickets')) {
            $userIdFilter = $user->id;
        }

        $stats = $this->analyticsService->getDailyStats($userIdFilter);
        $salesChart = $this->analyticsService->getSalesChartData(365, $userIdFilter);
        $monthlySalesChart = $this->analyticsService->getMonthlySalesChartData(12, $userIdFilter);
        $ticketTypes = $this->analyticsService->getTicketTypeBreakdown($userIdFilter);
        $occupancy = $this->analyticsService->getOccupancyByPlace($userIdFilter);
        $recentTransactions = $this->analyticsService->getRecentTransactions(5, $userIdFilter);

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
