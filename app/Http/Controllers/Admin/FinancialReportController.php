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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $summary = $this->financialService->getSummary($startDate, $endDate);
        // $dailyTrend is now replaced by salesChartData for the main charts, 
        // but we might keep it if needed for export or other logic. 
        // For the "Trend" charts we use the 365-day preload.
        $dailyTrend = $this->financialService->getDailyTrend($startDate, $endDate); 
        $paymentMethods = $this->financialService->getByPaymentMethod($startDate, $endDate);
        $ticketSales = $this->financialService->getByTicket($startDate, $endDate);

        // Preload 365 days data for client-side filtering charts
        $salesChartData = $this->analyticsService->getSalesChartData(365);
        $monthlySalesChartData = $this->analyticsService->getMonthlySalesChartData(12);

        return view('admin.reports.financial.index', compact(
            'summary',
            'dailyTrend',
            'paymentMethods',
            'ticketSales',
            'startDate',
            'endDate',
            'salesChartData',
            'monthlySalesChartData'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $transactions = \App\Models\TicketOrder::whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('status', 'paid')
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
