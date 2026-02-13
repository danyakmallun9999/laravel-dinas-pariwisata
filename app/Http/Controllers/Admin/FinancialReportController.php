<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinancialReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    protected $financialService;
    protected $analyticsService;

    public function __construct(FinancialReportService $financialService, \App\Services\TicketAnalyticsService $analyticsService)
    {
        $this->financialService = $financialService;
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($period && $period !== 'custom') {
            $endDate = Carbon::now()->toDateString();
            switch ($period) {
                case 'day':
                    $startDate = Carbon::now()->toDateString();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek()->toDateString();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth()->toDateString();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear()->toDateString();
                    break;
            }
        } else {
            // Fallback defaults if not provided
            $startDate = $startDate ?: Carbon::now()->startOfMonth()->toDateString();
            $endDate = $endDate ?: Carbon::now()->endOfMonth()->toDateString();
        }

        $user = auth()->user();
        $userIdFilter = null;

        // If user can only view own reports, filter by their ID
        if (!$user->can('view all financial reports') && $user->can('view own financial reports')) {
            $userIdFilter = $user->id;
        }

        $summary = $this->financialService->getSummary($startDate, $endDate, $userIdFilter);
        $dailyTrend = $this->financialService->getDailyTrend($startDate, $endDate, $userIdFilter);
        $paymentMethods = $this->financialService->getByPaymentMethod($startDate, $endDate, $userIdFilter);
        $ticketSales = $this->financialService->getByTicket($startDate, $endDate, $userIdFilter);

        // Preload 365 days data for client-side filtering charts
        $salesChartData = $this->analyticsService->getSalesChartData(365, $userIdFilter);
        $monthlySalesChartData = $this->analyticsService->getMonthlySalesChartData(12, $userIdFilter);

        return view('admin.reports.financial.index', compact(
            'summary',
            'dailyTrend',
            'paymentMethods',
            'ticketSales',
            'startDate',
            'endDate',
            'period',
            'salesChartData',
            'monthlySalesChartData'
        ));
    }

    public function export(Request $request)
    {
        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($period && $period !== 'custom') {
            $endDate = Carbon::now()->toDateString();
            switch ($period) {
                case 'day':
                    $startDate = Carbon::now()->toDateString();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek()->toDateString();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth()->toDateString();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear()->toDateString();
                    break;
            }
        } else {
            $startDate = $startDate ?: Carbon::now()->startOfMonth()->toDateString();
            $endDate = $endDate ?: Carbon::now()->endOfMonth()->toDateString();
        }

        $user = auth()->user();
        $userIdFilter = null;

        if (!$user->can('view all financial reports') && $user->can('view own financial reports')) {
            $userIdFilter = $user->id;
        }

        $transactions = \App\Models\TicketOrder::whereBetween('paid_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('status', 'paid')
            ->when($userIdFilter, function($q) use ($userIdFilter) {
                $q->whereHas('ticket.place', function($subQ) use ($userIdFilter) {
                    $subQ->where('created_by', $userIdFilter);
                });
            })
            ->with(['ticket', 'user'])
            ->get();

        $filename = "financial_report_{$startDate}_{$endDate}.csv";

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, [
                'Order ID',
                'Date',
                'Ticket Name',
                'Customer Name',
                'Customer Email',
                'Quantity',
                'Unit Price',
                'Total Price',
                'Status'
            ]);

            foreach ($transactions as $txn) {
                fputcsv($handle, [
                    $txn->order_number,
                    $txn->created_at->format('Y-m-d H:i:s'),
                    $txn->ticket ? $txn->ticket->name : 'N/A',
                    $txn->customer_name,
                    $txn->customer_email,
                    $txn->quantity,
                    $txn->unit_price,
                    $txn->total_price,
                    $txn->status
                ]);
            }

            fclose($handle);
        }, $filename);
    }
}
