{{-- Ticket Sales Breakdown Table — Advanced --}}
{{-- Ticket Sales Breakdown Table — Advanced --}}
<div class="bg-white rounded-[2rem] border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center border-4 border-green-50">
                    <i class="fa-solid fa-receipt text-white text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Rincian Penjualan Tiket</h3>
                    <p class="text-xs text-gray-500">Berdasarkan jenis tiket wisata</p>
                </div>
            </div>
            @if(count($ticketSales) > 0)
                <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    {{ count($ticketSales) }} tiket aktif
                </span>
            @endif
        </div>
    </div>

    @if(count($ticketSales) > 0)
        @php $maxRevenue = collect($ticketSales)->max('revenue') ?: 1; @endphp
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/80">
                    <tr class="text-gray-500 text-xs font-semibold uppercase tracking-wider">
                        <th class="px-6 py-3.5 text-left">#</th>
                        <th class="px-6 py-3.5 text-left">Destinasi / Tiket</th>
                        <th class="px-6 py-3.5 text-center">Terjual</th>
                        <th class="px-6 py-3.5 text-right">Pendapatan</th>
                        <th class="px-6 py-3.5 text-right w-36">Kontribusi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ticketSales as $index => $sale)
                    @php
                        $pct = $summary['gross_revenue'] > 0 ? round(($sale['revenue'] / $summary['gross_revenue']) * 100, 1) : 0;
                        $rankColors = [
                            0 => 'from-amber-400 to-orange-500 text-white shadow-amber-200',
                            1 => 'from-gray-300 to-gray-400 text-white',
                            2 => 'from-amber-600 to-amber-700 text-white',
                        ];
                        $rankClass = $rankColors[$index] ?? 'bg-gray-100 text-gray-600';
                        $isTop3 = $index < 3;
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors duration-200 group">
                        <td class="px-6 py-4">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0
                                {{ $isTop3 ? 'bg-gradient-to-br ' . $rankClass . ' shadow-sm' : 'bg-gray-100 text-gray-600' }}">
                                {{ $index + 1 }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <span class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors duration-200">
                                    {{ $sale['place_name'] ?: '-' }}
                                </span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500">{{ $sale['ticket_name'] }}</span>
                                    <span class="text-[10px] font-medium text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">{{ ucfirst($sale['ticket_type'] ?? '-') }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700">
                                <i class="fa-solid fa-ticket mr-1 text-[10px]"></i>{{ number_format($sale['tickets_sold'], 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-gray-800">Rp {{ number_format($sale['revenue'], 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center gap-2 justify-end">
                                <div class="w-16 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-1.5 rounded-full transition-all duration-700"
                                         style="width: {{ ($sale['revenue'] / $maxRevenue) * 100 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 font-semibold w-10 text-right">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards -->
        <div class="md:hidden space-y-4 p-4">
            @foreach($ticketSales as $index => $sale)
            @php
                $pct = $summary['gross_revenue'] > 0 ? round(($sale['revenue'] / $summary['gross_revenue']) * 100, 1) : 0;
                $rankColors = [
                    0 => 'from-amber-400 to-orange-500 text-white shadow-amber-200',
                    1 => 'from-gray-300 to-gray-400 text-white',
                    2 => 'from-amber-600 to-amber-700 text-white',
                ];
                $rankClass = $rankColors[$index] ?? 'bg-gray-100 text-gray-600';
                $isTop3 = $index < 3;
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm relative overflow-hidden">
                <!-- Rank Ribbon for Top 3 -->
                @if($isTop3)
                    <div class="absolute top-0 right-0 w-16 h-16 pointer-events-none overflow-hidden">
                        <div class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 rotate-45 bg-gradient-to-r {{ $rankClass }} w-24 h-6 flex items-end justify-center pb-1 text-[10px] font-bold shadow-sm">
                            #{{ $index + 1 }}
                        </div>
                    </div>
                @endif

                <div class="flex items-start gap-3 mb-3">
                    @if(!$isTop3)
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0 bg-gray-100 text-gray-600">
                            {{ $index + 1 }}
                        </div>
                    @else
                        <div class="w-8 h-8 flex-shrink-0"></div> <!-- Spacer for alignment if needed, or just let text flow -->
                    @endif
                    
                    <div class="flex-1 min-w-0 {{ $isTop3 ? '-ml-11' : '' }}"> <!-- Adjust margin if top 3 to pull text back since badge is absolute -->
                        <h4 class="text-sm font-bold text-gray-900 line-clamp-1 pr-6">{{ $sale['place_name'] ?: '-' }}</h4>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-xs text-gray-600">{{ $sale['ticket_name'] }}</span>
                            <span class="text-[10px] font-medium text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">{{ ucfirst($sale['ticket_type'] ?? '-') }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-purple-50 rounded-xl p-2.5">
                        <p class="text-[10px] text-purple-600 font-semibold mb-0.5">Terjual</p>
                        <p class="text-sm font-bold text-purple-700">
                            <i class="fa-solid fa-ticket mr-1 text-xs opacity-70"></i>{{ number_format($sale['tickets_sold'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-2.5">
                        <p class="text-[10px] text-blue-600 font-semibold mb-0.5">Pendapatan</p>
                        <p class="text-sm font-bold text-blue-700">
                            Rp {{ number_format($sale['revenue'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 font-medium w-16">Kontribusi</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full"
                                style="width: {{ ($sale['revenue'] / $maxRevenue) * 100 }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-gray-700">{{ $pct }}%</span>
                </div>
            </div>
            @endforeach
        </div>
    @else
        {{-- Enhanced Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 bg-green-50 rounded-2xl flex items-center justify-center mb-4">
                <i class="fa-solid fa-receipt text-3xl text-green-300"></i>
            </div>
            <p class="text-gray-700 font-semibold mb-1">Belum Ada Penjualan Tiket</p>
            <p class="text-sm text-gray-400 max-w-xs mb-4">Tidak ada transaksi tiket pada periode yang dipilih. Coba ubah rentang tanggal.</p>
            <button onclick="document.getElementById('start_date').focus()"
                class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-lg transition-colors">
                <i class="fa-solid fa-calendar-days mr-1"></i>Ubah Periode
            </button>
        </div>
    @endif
</div>
