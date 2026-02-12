{{-- Payment Methods — Doughnut + Interactive Legend --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-200">
            <i class="fa-solid fa-credit-card text-white text-sm"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-800">Metode Pembayaran</h3>
            <p class="text-xs text-gray-500">Distribusi pendapatan</p>
        </div>
    </div>

    @if(count($paymentMethods) > 0)
        @php
            $chartColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
            $totalRevenue = $summary['gross_revenue'] > 0 ? $summary['gross_revenue'] : 1;
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
            {{-- Donut Chart (Left) --}}
            <div class="flex flex-col items-center justify-center border-r border-gray-100 pr-8">
                <div id="paymentMethodChart" class="w-full flex justify-center"></div>
                <div class="text-center mt-4">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Total Pendapatan</p>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Legend & Data List (Right) --}}
            <div class="lg:col-span-2 space-y-3">
                <h4 class="text-sm font-semibold text-gray-600 mb-4 px-3">Rincian per Metode</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($paymentMethods as $index => $method)
                        @php $pct = round(($method->revenue / $totalRevenue) * 100, 1); @endphp
                        <div class="group p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition-all duration-300 border border-transparent hover:border-blue-100">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0 ring-2 ring-white shadow-sm" style="background-color: {{ $chartColors[$index % count($chartColors)] }}"></span>
                                    <div>
                                        <p class="text-sm font-bold text-gray-700 group-hover:text-blue-700 transition-colors">{{ $method->payment_method ?: 'Lainnya' }}</p>
                                        <p class="text-xs text-gray-500">{{ $pct }}% kontribusi</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-800">Rp {{ number_format($method->revenue, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            {{-- Enhanced Progress bar --}}
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full transition-all duration-1000 ease-out group-hover:brightness-110" 
                                         style="width: {{ $pct }}%; background-color: {{ $chartColors[$index % count($chartColors)] }}"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        {{-- Enhanced Empty State --}}
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-credit-card text-2xl text-amber-300"></i>
            </div>
            <p class="text-gray-600 font-medium mb-1">Belum Ada Data</p>
            <p class="text-sm text-gray-400">Ubah periode untuk melihat distribusi pembayaran</p>
        </div>
    @endif
</div>
