<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Admin Panel</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Ticket Dashboard
                </h2>
            </div>
            <!-- Date Display with Alpine -->
            <div x-data="{ showDateInfo: false }" class="relative">
                <button @click="showDateInfo = !showDateInfo" 
                        class="hidden md:flex items-center gap-2 text-sm text-gray-600 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300">
                    <i class="fa-regular fa-calendar text-blue-500"></i>
                    <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400" :class="showDateInfo && 'rotate-180'" style="transition: transform 0.2s"></i>
                </button>
                <div x-show="showDateInfo" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     @click.away="showDateInfo = false"
                     class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 p-4 z-50">
                    <div class="text-xs text-gray-500 uppercase tracking-wider mb-2">Waktu Server</div>
                    <div class="text-lg font-bold text-gray-800" x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID'), 1000)" x-text="time"></div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="text-xs text-gray-500">Data terakhir diupdate</div>
                        <div class="text-sm text-gray-700 font-medium">Baru saja</div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="dashboardData()" x-init="initAnimations()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Pulse Section (Stats) with GSAP animation targets -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Revenue Card -->
                <div class="stat-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-lg transition-all duration-500">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-green-100 to-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-green-200">
                                <i class="fa-solid fa-money-bill-wave text-white text-lg"></i>
                            </div>
                            @if($stats['revenue'] > 0)
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                                <i class="fa-solid fa-arrow-trend-up mr-1"></i>+12%
                            </span>
                            @endif
                        </div>
                        <div class="stat-value text-3xl font-black text-gray-800 mb-1" data-value="{{ $stats['revenue'] }}">
                            Rp {{ number_format($stats['revenue'], 0, ',', '.') }}
                        </div>
                        <div class="text-sm text-gray-500 font-medium">Pendapatan Hari Ini</div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <span class="text-xs text-green-600 font-semibold">Drastis naik dari kemarin</span>
                        </div>
                    </div>
                </div>

                <!-- Tickets Sold Card -->
                <div class="stat-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-lg transition-all duration-500">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-200">
                                <i class="fa-solid fa-ticket text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="stat-value text-3xl font-black text-gray-800 mb-1">
                            {{ number_format($stats['tickets_sold']) }}
                        </div>
                        <div class="text-sm text-gray-500 font-medium">Tiket Terjual Hari Ini</div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ min(($stats['tickets_sold'] / 500) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 font-medium">{{ number_format(($stats['tickets_sold'] / 500) * 100, 0) }}% target</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Visitors Card -->
                <div class="stat-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-lg transition-all duration-500">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center shadow-lg shadow-purple-200">
                                <i class="fa-solid fa-users-viewfinder text-white text-lg"></i>
                            </div>
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                        </div>
                        <div class="stat-value text-3xl font-black text-gray-800 mb-1">
                            {{ number_format($stats['active_visitors']) }}
                        </div>
                        <div class="text-sm text-gray-500 font-medium">Pengunjung Aktif</div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <span class="text-xs text-purple-600 font-semibold"><i class="fa-solid fa-location-dot mr-1"></i>Real-time di lokasi</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders Card -->
                <div class="stat-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-lg transition-all duration-500">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-200">
                                <i class="fa-solid fa-clock text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="stat-value text-3xl font-black text-gray-800 mb-1">
                            {{ number_format($stats['pending_orders']) }}
                        </div>
                        <div class="text-sm text-gray-500 font-medium">Pesanan Pending</div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <span class="text-xs text-amber-600 font-semibold">Potensi: Rp {{ number_format($stats['pending_orders'] * 15000, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section with Alpine.js interactivity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sales Trend Chart -->
                <div class="lg:col-span-2 chart-card">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Tren Penjualan</h3>
                                <p class="text-xs text-gray-500">30 hari terakhir</p>
                            </div>
                            <div x-data="{ activeTab: 'revenue' }" class="flex bg-gray-100 rounded-lg p-1">
                                <button @click="activeTab = 'revenue'; updateChart('revenue')" 
                                        :class="activeTab === 'revenue' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500'"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200">
                                    Revenue
                                </button>
                                <button @click="activeTab = 'tickets'; updateChart('tickets')" 
                                        :class="activeTab === 'tickets' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500'"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all duration-200">
                                    Tiket
                                </button>
                            </div>
                        </div>
                        <div class="relative w-full h-72">
                            <canvas id="salesChart"></canvas>
                        </div>
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-800">Rp {{ number_format(array_sum($salesChart['revenue']), 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Total 30 Hari</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-800">Rp {{ number_format(array_sum($salesChart['revenue']) / 30, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Rata-rata/Hari</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-gray-800">{{ number_format(array_sum($salesChart['tickets'])) }}</div>
                                <div class="text-xs text-gray-500">Total Tiket</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Type Distribution -->
                <div class="chart-card">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Distribusi Tiket</h3>
                                <p class="text-xs text-gray-500">Berdasarkan tipe</p>
                            </div>
                        </div>
                        <div class="relative w-48 h-48 mx-auto">
                            <canvas id="ticketTypeChart"></canvas>
                            <!-- Center text -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <div class="text-2xl font-black text-gray-800">{{ $ticketTypes->sum('count') }}</div>
                                <div class="text-xs text-gray-500">Total</div>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="mt-6 space-y-3">
                            @php $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']; @endphp
                            @foreach($ticketTypes as $index => $type)
                                <div class="flex items-center justify-between group hover:bg-gray-50 px-2 py-1 rounded-lg transition-colors">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full" style="background-color: {{ $colors[$index % count($colors)] }}"></span>
                                        <span class="text-sm text-gray-600 capitalize">{{ $type->type }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-gray-800">{{ $type->count }}</span>
                                        <span class="text-xs text-gray-400">({{ $ticketTypes->sum('count') > 0 ? number_format(($type->count / $ticketTypes->sum('count')) * 100, 0) : 0 }}%)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions & Occupancy Leaderboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders -->
                <div class="lg:col-span-2 table-card">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Transaksi Terbaru</h3>
                                    <p class="text-xs text-gray-500">5 pesanan terakhir</p>
                                </div>
                                <a href="{{ route('admin.tickets.orders') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 text-gray-600 text-xs font-semibold uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3 text-left">Order</th>
                                        <th class="px-6 py-3 text-left">Pelanggan</th>
                                        <th class="px-6 py-3 text-left">Destinasi</th>
                                        <th class="px-6 py-3 text-right">Total</th>
                                        <th class="px-6 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-mono font-bold text-gray-700">{{ Str::limit($transaction->order_number, 12) }}</div>
                                            <div class="text-xs text-gray-400">{{ $transaction->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-800">{{ $transaction->customer_name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600">{{ $transaction->ticket->place->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm font-bold text-gray-800">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($transaction->status == 'paid')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Paid
                                                </span>
                                            @elseif($transaction->status == 'pending')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5 animate-pulse"></span>Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                            <i class="fa-solid fa-inbox text-3xl mb-2"></i>
                                            <p>Belum ada transaksi hari ini</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Occupancy Leaderboard -->
                <div class="leaderboard-card">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Top Destinasi</h3>
                                <p class="text-xs text-gray-500">Hari ini</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-200">
                                <i class="fa-solid fa-trophy text-white"></i>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @forelse($occupancy as $index => $place)
                                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm shadow-sm
                                        {{ $index == 0 ? 'bg-gradient-to-br from-amber-400 to-orange-500 text-white' : '' }}
                                        {{ $index == 1 ? 'bg-gradient-to-br from-gray-300 to-gray-400 text-white' : '' }}
                                        {{ $index == 2 ? 'bg-gradient-to-br from-amber-600 to-amber-700 text-white' : '' }}
                                        {{ $index > 2 ? 'bg-gray-100 text-gray-600' : '' }}">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between mb-1">
                                            <span class="font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">{{ Str::limit($place->name, 20) }}</span>
                                            <span class="font-bold text-gray-800">{{ $place->sold }}</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-1000 ease-out
                                                {{ $index == 0 ? 'bg-gradient-to-r from-amber-400 to-orange-500' : 'bg-blue-500' }}" 
                                                 style="width: {{ ($place->sold / ($occupancy->max('sold') ?: 1)) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-chart-simple text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-400 text-sm">Belum ada data hari ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GSAP & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        // Chart.js instances
        let salesChartInstance = null;
        const salesData = {
            labels: @json($salesChart['labels']),
            revenue: @json($salesChart['revenue']),
            tickets: @json($salesChart['tickets'])
        };

        // Alpine.js data
        function dashboardData() {
            return {
                initAnimations() {
                    // GSAP entrance animations using fromTo for reliability
                    gsap.fromTo('.stat-card', 
                        { y: 30, opacity: 0 },
                        { duration: 0.8, y: 0, opacity: 1, stagger: 0.1, ease: 'power3.out' }
                    );

                    gsap.fromTo('.chart-card', 
                        { y: 30, opacity: 0 },
                        { duration: 0.8, y: 0, opacity: 1, delay: 0.3, stagger: 0.1, ease: 'power3.out' }
                    );

                    gsap.fromTo('.table-card', 
                        { y: 30, opacity: 0 },
                        { duration: 0.8, y: 0, opacity: 1, delay: 0.5, ease: 'power3.out' }
                    );

                    gsap.fromTo('.leaderboard-card', 
                        { y: 30, opacity: 0 },
                        { duration: 0.8, y: 0, opacity: 1, delay: 0.6, ease: 'power3.out' }
                    );
                }
            }
        }

        // Update chart function (called by Alpine)
        function updateChart(type) {
            if (!salesChartInstance) return;
            
            const data = type === 'revenue' ? salesData.revenue : salesData.tickets;
            const label = type === 'revenue' ? 'Revenue (Rp)' : 'Tiket Terjual';
            const color = type === 'revenue' ? '#3b82f6' : '#10b981';
            
            salesChartInstance.data.datasets[0].data = data;
            salesChartInstance.data.datasets[0].label = label;
            salesChartInstance.data.datasets[0].borderColor = color;
            
            // Update gradient
            const ctx = salesChartInstance.ctx;
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, type === 'revenue' ? 'rgba(59, 130, 246, 0.15)' : 'rgba(16, 185, 129, 0.15)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            salesChartInstance.data.datasets[0].backgroundColor = gradient;
            
            salesChartInstance.update('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Sales Trend Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 400);
            salesGradient.addColorStop(0, 'rgba(59, 130, 246, 0.15)');
            salesGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            salesChartInstance = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesData.labels,
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: salesData.revenue,
                        borderColor: '#3b82f6',
                        backgroundColor: salesGradient,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 3,
                        pointHoverBackgroundColor: '#3b82f6',
                        pointHoverBorderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f9fafb',
                            bodyColor: '#f9fafb',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { 
                                color: '#f3f4f6',
                                drawBorder: false
                            },
                            ticks: { 
                                color: '#9ca3af',
                                font: { size: 11 },
                                callback: function(value) { 
                                    return 'Rp ' + value.toLocaleString('id-ID', {notation: 'compact'}); 
                                } 
                            }
                        },
                        x: {
                            border: { display: false },
                            grid: { display: false },
                            ticks: { 
                                color: '#9ca3af',
                                font: { size: 11 },
                                maxRotation: 0
                            }
                        }
                    }
                }
            });

            // Ticket Type Doughnut Chart
            const typeCtx = document.getElementById('ticketTypeChart').getContext('2d');
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($ticketTypes->pluck('type')),
                    datasets: [{
                        data: @json($ticketTypes->pluck('count')),
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f9fafb',
                            bodyColor: '#f9fafb',
                            padding: 12,
                            cornerRadius: 8
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
