{{-- ApexCharts — Data injection from Blade to JS --}}
<script>
    window.__financialData = {
        dailyLabels:   {!! json_encode(collect($dailyTrend)->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!},
        dailyRevenue:  {!! json_encode(collect($dailyTrend)->pluck('revenue')) !!},
        dailyCounts:   {!! json_encode(collect($dailyTrend)->pluck('count')) !!},
        sparkRevenue:  {!! json_encode(collect($dailyTrend)->pluck('revenue')->values()) !!},
        sparkTickets:  {!! json_encode(collect($dailyTrend)->pluck('count')->values()) !!},
        paymentLabels: {!! json_encode(collect($paymentMethods)->pluck('payment_method')->map(fn($m) => $m ?: 'Lainnya')) !!},
        paymentData:   {!! json_encode(collect($paymentMethods)->pluck('revenue')) !!},
        // New 365-day data for interactive charts
        chartLabels:   {!! json_encode($salesChartData['labels']) !!},
        chartRevenue:  {!! json_encode($salesChartData['revenue']) !!},
        chartTickets:  {!! json_encode($salesChartData['tickets']) !!},
        monthlyLabels:   {!! json_encode($monthlySalesChartData['labels']) !!},
        monthlyRevenue:  {!! json_encode($monthlySalesChartData['revenue']) !!},
        monthlyTickets:  {!! json_encode($monthlySalesChartData['tickets']) !!},
    };
</script>
