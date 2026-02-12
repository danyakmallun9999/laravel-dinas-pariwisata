{{-- Summary Stat Cards with Sparklines --}}
@php
    $trendRevenues = collect($dailyTrend)->pluck('revenue')->values()->toArray();
    $trendCounts   = collect($dailyTrend)->pluck('count')->values()->toArray();
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Pendapatan Kotor --}}
    <div x-data="{ showTip: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-lg transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-28 h-28 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full opacity-40 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-wallet text-white text-lg"></i>
                </div>
                <div class="relative">
                    <button @mouseenter="showTip = true" @mouseleave="showTip = false"
                        class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center text-xs transition-colors">
                        <i class="fa-solid fa-circle-info"></i>
                    </button>
                    <div x-show="showTip" x-transition
                         class="absolute right-0 top-full mt-2 w-52 p-3 bg-gray-800 text-white text-xs rounded-xl shadow-xl z-50 leading-relaxed"
                         style="display:none">
                        Total seluruh transaksi yang masuk pada periode yang dipilih, sebelum potongan apapun.
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1 tracking-tight">
                Rp {{ number_format($summary['gross_revenue'], 0, ',', '.') }}
            </div>
            <p class="text-sm text-gray-500 font-medium">Pendapatan Kotor</p>
            {{-- Sparkline --}}
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div id="sparkGross"></div>
            </div>
        </div>
    </div>

    {{-- Pendapatan Bersih --}}
    <div x-data="{ showTip: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-lg transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-28 h-28 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full opacity-40 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                    <i class="fa-solid fa-money-bill-wave text-white text-lg"></i>
                </div>
                <div class="relative">
                    <button @mouseenter="showTip = true" @mouseleave="showTip = false"
                        class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center text-xs transition-colors">
                        <i class="fa-solid fa-circle-info"></i>
                    </button>
                    <div x-show="showTip" x-transition
                         class="absolute right-0 top-full mt-2 w-52 p-3 bg-gray-800 text-white text-xs rounded-xl shadow-xl z-50 leading-relaxed"
                         style="display:none">
                        Pendapatan setelah dikurangi refund yang terjadi pada periode ini.
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1 tracking-tight">
                Rp {{ number_format($summary['net_revenue'], 0, ',', '.') }}
            </div>
            <p class="text-sm text-gray-500 font-medium">Pendapatan Bersih</p>
            {{-- Refund indicator --}}
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-emerald-600 font-semibold">
                    <i class="fa-solid fa-arrow-right-arrow-left mr-1"></i>Setelah refund
                </span>
                @if($summary['total_refunds'] > 0)
                    <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">
                        -Rp {{ number_format($summary['total_refunds'], 0, ',', '.') }}
                    </span>
                @else
                    <span class="text-xs font-semibold text-gray-400">Rp 0 refund</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Tiket Terjual --}}
    <div x-data="{ showTip: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-lg transition-all duration-500">
        <div class="absolute -right-6 -top-6 w-28 h-28 bg-gradient-to-br from-purple-100 to-purple-50 rounded-full opacity-40 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center shadow-lg shadow-purple-200">
                    <i class="fa-solid fa-ticket text-white text-lg"></i>
                </div>
                <div class="relative">
                    <button @mouseenter="showTip = true" @mouseleave="showTip = false"
                        class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 hover:text-gray-600 flex items-center justify-center text-xs transition-colors">
                        <i class="fa-solid fa-circle-info"></i>
                    </button>
                    <div x-show="showTip" x-transition
                         class="absolute right-0 top-full mt-2 w-52 p-3 bg-gray-800 text-white text-xs rounded-xl shadow-xl z-50 leading-relaxed"
                         style="display:none">
                        Jumlah tiket yang berhasil terjual (status paid) pada periode ini.
                    </div>
                </div>
            </div>
            <div class="text-3xl font-black text-gray-800 mb-1 tracking-tight">
                {{ number_format($summary['tickets_sold'], 0, ',', '.') }}
            </div>
            <p class="text-sm text-gray-500 font-medium">Tiket Terjual</p>
            {{-- Sparkline --}}
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div id="sparkTickets"></div>
            </div>
        </div>
    </div>
</div>
