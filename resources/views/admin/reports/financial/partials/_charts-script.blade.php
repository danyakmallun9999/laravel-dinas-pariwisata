{{-- ApexCharts — Data injection from Blade to JS --}}
<script>
    (function() {
        // Set data immediately
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
        
        // Dispatch event after DOM is ready (works for both initial load and SPA navigation)
        const dispatchReady = () => {
            // Small delay to ensure charts module is ready
            setTimeout(() => {
                document.dispatchEvent(new CustomEvent('financial-dashboard-data-ready'));
            }, 150);
        };
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', dispatchReady);
        } else {
            // DOM already ready, but wait a tick to ensure all scripts are loaded
            setTimeout(dispatchReady, 100);
        }
        
        // Also dispatch after a longer delay for SPA navigation (Livewire v4)
        setTimeout(() => {
            document.dispatchEvent(new CustomEvent('financial-dashboard-data-ready'));
        }, 300);
    })();
</script>
