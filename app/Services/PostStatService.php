<?php

namespace App\Services;

use App\Models\TicketOrder;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PostStatService
{
    /**
     * Available widget definitions.
     */
    public static function availableWidgets(): array
    {
        return [
            'revenue' => [
                'label' => 'Pendapatan Tiket',
                'icon' => 'fa-solid fa-coins',
                'color' => 'emerald',
                'chart_type' => 'line',
                'description' => 'Total pendapatan dari penjualan tiket wisata',
            ],
            'ticket_sales' => [
                'label' => 'Penjualan Tiket',
                'icon' => 'fa-solid fa-ticket',
                'color' => 'blue',
                'chart_type' => 'bar',
                'description' => 'Jumlah tiket terjual per hari',
            ],
            'top_destinations' => [
                'label' => 'Wisata Terlaris',
                'icon' => 'fa-solid fa-ranking-star',
                'color' => 'amber',
                'chart_type' => 'horizontal_bar',
                'description' => 'Destinasi wisata dengan penjualan tertinggi',
            ],
            'visitor_origins' => [
                'label' => 'Domisili Pengunjung',
                'icon' => 'fa-solid fa-map-location-dot',
                'color' => 'violet',
                'chart_type' => 'doughnut',
                'description' => 'Asal kota/provinsi pengunjung',
            ],
            'visitor_count' => [
                'label' => 'Jumlah Pengunjung',
                'icon' => 'fa-solid fa-users',
                'color' => 'cyan',
                'chart_type' => 'area',
                'description' => 'Jumlah pengunjung yang sudah check-in',
            ],
            'monthly_tourism' => [
                'label' => 'Tren Wisata Bulanan',
                'icon' => 'fa-solid fa-chart-column',
                'color' => 'rose',
                'chart_type' => 'bar',
                'description' => 'Tren kunjungan wisata per bulan',
            ],
            'post_views' => [
                'label' => 'Grafik Pembaca',
                'icon' => 'fa-solid fa-chart-line',
                'color' => 'indigo',
                'chart_type' => 'line',
                'description' => 'Grafik kunjungan artikel per hari',
            ],
        ];
    }

    /**
     * Available period options.
     */
    public static function availablePeriods(): array
    {
        return [
            '1_week' => '1 Minggu',
            '1_month' => '1 Bulan',
            '3_months' => '3 Bulan',
            '6_months' => '6 Bulan',
            '1_year' => '1 Tahun',
            'all_time' => 'Semua Waktu',
        ];
    }

    /**
     * Get data for a specific widget.
     */
    public function getWidgetData(string $type, string $period, ?int $postId = null): array
    {
        $cacheKey = "post_stat_widget:{$type}:{$period}:" . ($postId ?? 'global');
        $cacheTtl = 3600; // 1 hour

        return Cache::remember($cacheKey, $cacheTtl, function () use ($type, $period, $postId) {
            return match ($type) {
                'revenue' => $this->getRevenueData($period),
                'ticket_sales' => $this->getTicketSalesData($period),
                'top_destinations' => $this->getTopDestinationsData($period),
                'visitor_origins' => $this->getVisitorOriginsData($period),
                'visitor_count' => $this->getVisitorCountData($period),
                'monthly_tourism' => $this->getMonthlyTourismData($period),
                'post_views' => $this->getPostViewsData($period, $postId),
                default => ['labels' => [], 'datasets' => [], 'summary' => []],
            };
        });
    }

    /**
     * Get all widget data for a post's configured widgets.
     */
    public function getWidgetsForPost(\App\Models\Post $post): array
    {
        $widgets = $post->stat_widgets ?? [];
        $result = [];

        foreach ($widgets as $widget) {
            $type = $widget['type'] ?? null;
            $period = $widget['period'] ?? '1_month';

            if (!$type || !isset(self::availableWidgets()[$type])) {
                continue;
            }

            $meta = self::availableWidgets()[$type];
            $data = $this->getWidgetData($type, $period, $post->id);

            $result[] = [
                'type' => $type,
                'period' => $period,
                'period_label' => self::availablePeriods()[$period] ?? $period,
                'title' => $widget['title'] ?? $meta['label'],
                'icon' => $meta['icon'],
                'color' => $meta['color'],
                'chart_type' => $meta['chart_type'],
                'data' => $data,
            ];
        }

        return $result;
    }

    /**
     * Convert period string to Carbon date range.
     */
    private function getPeriodDates(string $period): ?Carbon
    {
        return match ($period) {
            '1_week' => now()->subWeek(),
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
            'all_time' => null,
            default => now()->subMonth(),
        };
    }

    /**
     * Revenue data (line chart).
     */
    private function getRevenueData(string $period): array
    {
        $startDate = $this->getPeriodDates($period);

        $query = TicketOrder::query()
            ->whereIn('status', ['paid', 'used'])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $data = $query->groupBy('date')->orderBy('date')->get();

        $totalRevenue = $data->sum('total');
        $avgDaily = $data->count() > 0 ? $totalRevenue / $data->count() : 0;

        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->translatedFormat('d M'))->values()->toArray(),
            'datasets' => [[
                'label' => 'Pendapatan',
                'data' => $data->pluck('total')->map(fn($v) => (float) $v)->values()->toArray(),
                'borderColor' => '#10b981',
                'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                'fill' => true,
                'tension' => 0.4,
            ]],
            'summary' => [
                ['label' => 'Total Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'color' => 'emerald'],
                ['label' => 'Rata-rata/Hari', 'value' => 'Rp ' . number_format($avgDaily, 0, ',', '.'), 'color' => 'blue'],
            ],
        ];
    }

    /**
     * Ticket sales data (bar chart).
     */
    private function getTicketSalesData(string $period): array
    {
        $startDate = $this->getPeriodDates($period);

        $query = TicketOrder::query()
            ->whereIn('status', ['paid', 'used'])
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as total');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $data = $query->groupBy('date')->orderBy('date')->get();

        $totalSold = $data->sum('total');

        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->translatedFormat('d M'))->values()->toArray(),
            'datasets' => [[
                'label' => 'Tiket Terjual',
                'data' => $data->pluck('total')->map(fn($v) => (int) $v)->values()->toArray(),
                'backgroundColor' => '#3b82f6',
                'borderRadius' => 4,
            ]],
            'summary' => [
                ['label' => 'Total Terjual', 'value' => number_format($totalSold) . ' tiket', 'color' => 'blue'],
            ],
        ];
    }

    /**
     * Top destinations data (horizontal bar).
     */
    private function getTopDestinationsData(string $period): array
    {
        $startDate = $this->getPeriodDates($period);

        $query = TicketOrder::query()
            ->join('tickets', 'ticket_orders.ticket_id', '=', 'tickets.id')
            ->join('places', 'tickets.place_id', '=', 'places.id')
            ->whereIn('ticket_orders.status', ['paid', 'used'])
            ->selectRaw('places.name, SUM(ticket_orders.quantity) as total');

        if ($startDate) {
            $query->where('ticket_orders.created_at', '>=', $startDate);
        }

        $data = $query->groupBy('places.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'];

        return [
            'labels' => $data->pluck('name')->toArray(),
            'datasets' => [[
                'label' => 'Pengunjung',
                'data' => $data->pluck('total')->map(fn($v) => (int) $v)->toArray(),
                'backgroundColor' => array_slice($colors, 0, $data->count()),
                'borderRadius' => 4,
            ]],
            'summary' => [],
        ];
    }

    /**
     * Visitor origins data (doughnut).
     */
    private function getVisitorOriginsData(string $period): array
    {
        $startDate = $this->getPeriodDates($period);

        $query = TicketOrder::query()
            ->whereIn('status', ['paid', 'used'])
            ->selectRaw("COALESCE(NULLIF(customer_city, ''), NULLIF(customer_province, ''), 'Lainnya') as origin, SUM(quantity) as total")
            ->where(function ($q) {
                $q->whereNotNull('customer_city')->where('customer_city', '!=', '')
                    ->orWhere(function ($subQ) {
                        $subQ->whereNotNull('customer_province')->where('customer_province', '!=', '');
                    });
            });

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $data = $query->groupBy('origin')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $colors = ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#f97316'];

        return [
            'labels' => $data->pluck('origin')->toArray(),
            'datasets' => [[
                'data' => $data->pluck('total')->map(fn($v) => (int) $v)->toArray(),
                'backgroundColor' => array_slice($colors, 0, $data->count()),
            ]],
            'summary' => [],
        ];
    }

    /**
     * Visitor count data (area chart).
     */
    private function getVisitorCountData(string $period): array
    {
        $startDate = $this->getPeriodDates($period);

        $query = TicketOrder::query()
            ->where('status', 'used')
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as total');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $data = $query->groupBy('date')->orderBy('date')->get();

        $totalVisitors = $data->sum('total');

        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->translatedFormat('d M'))->values()->toArray(),
            'datasets' => [[
                'label' => 'Pengunjung',
                'data' => $data->pluck('total')->map(fn($v) => (int) $v)->values()->toArray(),
                'borderColor' => '#06b6d4',
                'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
                'fill' => true,
                'tension' => 0.4,
            ]],
            'summary' => [
                ['label' => 'Total Pengunjung', 'value' => number_format($totalVisitors) . ' orang', 'color' => 'cyan'],
            ],
        ];
    }

    /**
     * Monthly tourism data (bar chart).
     */
    private function getMonthlyTourismData(string $period): array
    {
        $year = now()->year;

        $data = TicketOrder::query()
            ->whereIn('status', ['paid', 'used'])
            ->whereYear('visit_date', $year)
            ->selectRaw('MONTH(visit_date) as month, SUM(quantity) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyData = array_fill(0, 12, 0);

        foreach ($data as $row) {
            $monthlyData[$row->month - 1] = (int) $row->total;
        }

        $totalYear = array_sum($monthlyData);

        return [
            'labels' => $months,
            'datasets' => [[
                'label' => 'Pengunjung ' . $year,
                'data' => $monthlyData,
                'backgroundColor' => '#f43f5e',
                'borderRadius' => 4,
            ]],
            'summary' => [
                ['label' => 'Total ' . $year, 'value' => number_format($totalYear) . ' pengunjung', 'color' => 'rose'],
            ],
        ];
    }

    /**
     * Post views data (line chart).
     */
    private function getPostViewsData(string $period, ?int $postId): array
    {
        $startDate = $this->getPeriodDates($period) ?? now()->subDays(30);

        $query = Visit::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $startDate);

        if ($postId) {
            $query->where('post_id', $postId);
        }

        $data = $query->groupBy('date')->orderBy('date')->get();

        return [
            'labels' => $data->pluck('date')->map(fn($d) => Carbon::parse($d)->translatedFormat('d M'))->values()->toArray(),
            'datasets' => [[
                'label' => 'Pembaca',
                'data' => $data->pluck('total')->map(fn($v) => (int) $v)->values()->toArray(),
                'borderColor' => '#6366f1',
                'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                'fill' => true,
                'tension' => 0.4,
            ]],
            'summary' => [],
        ];
    }
}
