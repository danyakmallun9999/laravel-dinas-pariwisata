{{-- Analytical Insights Section --}}
@php
    $dailyCollection = collect($dailyTrend);

    $highestDay  = $dailyCollection->sortByDesc('revenue')->first();
    $lowestDay   = $dailyCollection->where('revenue', '>', 0)->sortBy('revenue')->first();
    $peakTxnDay  = $dailyCollection->sortByDesc('count')->first();
    $avgRevenue  = $dailyCollection->count() > 0 ? $dailyCollection->avg('revenue') : 0;

    $topMethod = $paymentMethods->first();
    $totalMethodRevenue = $summary['gross_revenue'] > 0 ? $summary['gross_revenue'] : 1;
    $topMethodPct = $topMethod ? round(($topMethod->revenue / $totalMethodRevenue) * 100, 1) : 0;

    $insightCards = [];

    if ($highestDay) {
        $insightCards[] = [
            'icon'  => 'fa-solid fa-arrow-trend-up',
            'color' => 'emerald',
            'title' => 'Hari Pendapatan Tertinggi',
            'value' => 'Rp ' . number_format($highestDay->revenue, 0, ',', '.'),
            'sub'   => \Carbon\Carbon::parse($highestDay->date)->translatedFormat('l, d M Y'),
        ];
    }

    if ($lowestDay) {
        $insightCards[] = [
            'icon'  => 'fa-solid fa-arrow-trend-down',
            'color' => 'red',
            'title' => 'Hari Pendapatan Terendah',
            'value' => 'Rp ' . number_format($lowestDay->revenue, 0, ',', '.'),
            'sub'   => \Carbon\Carbon::parse($lowestDay->date)->translatedFormat('l, d M Y'),
        ];
    }

    $insightCards[] = [
        'icon'  => 'fa-solid fa-chart-simple',
        'color' => 'blue',
        'title' => 'Rata-rata Pendapatan / Hari',
        'value' => 'Rp ' . number_format($avgRevenue, 0, ',', '.'),
        'sub'   => $dailyCollection->count() . ' hari aktif',
    ];

    if ($peakTxnDay) {
        $insightCards[] = [
            'icon'  => 'fa-solid fa-bolt',
            'color' => 'amber',
            'title' => 'Transaksi Tertinggi / Hari',
            'value' => number_format($peakTxnDay->count, 0, ',', '.') . ' transaksi',
            'sub'   => \Carbon\Carbon::parse($peakTxnDay->date)->translatedFormat('l, d M Y'),
        ];
    }

    if ($topMethod) {
        $insightCards[] = [
            'icon'  => 'fa-solid fa-crown',
            'color' => 'violet',
            'title' => 'Metode Pembayaran Dominan',
            'value' => $topMethod->payment_method ?: 'Lainnya',
            'sub'   => $topMethodPct . '% dari total pendapatan',
        ];
    }
@endphp

@if(count($insightCards) > 0)
<div class="bg-white rounded-[2rem] border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center border-4 border-amber-50">
            <i class="fa-solid fa-lightbulb text-white text-sm"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-gray-800">Insight Analitik</h3>
            <p class="text-xs text-gray-400">Dihitung otomatis dari data periode ini</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        @foreach($insightCards as $card)
            <div class="flex items-start gap-3 p-4 rounded-2xl bg-{{ $card['color'] }}-50/50 border border-{{ $card['color'] }}-100/50 hover:border-{{ $card['color'] }}-200 transition-all duration-300 group">
                <div class="w-9 h-9 rounded-xl bg-{{ $card['color'] }}-100 text-{{ $card['color'] }}-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                    <i class="{{ $card['icon'] }} text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider mb-1">{{ $card['title'] }}</p>
                    <p class="text-sm font-bold text-gray-800 truncate">{{ $card['value'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $card['sub'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
