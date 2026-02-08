@pushOnce('styles')

@endPushOnce

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Admin Panel</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Ticket Dashboard
                </h2>
            </div>
            <!-- Date Display -->
            <div class="hidden md:flex items-center gap-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Pulse Section (Stats) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-admin.dashboard.stat-card 
                    title="Pendapatan Hari Ini"
                    value="Rp {{ number_format($stats['revenue'], 0, ',', '.') }}"
                    icon="fa-solid fa-money-bill-wave"
                    color="green"
                    subtext="Drastis Naik"
                    trend="12"
                />
                
                <x-admin.dashboard.stat-card 
                    title="Tiket Terjual Hari Ini"
                    value="{{ number_format($stats['tickets_sold']) }}"
                    icon="fa-solid fa-ticket"
                    color="blue"
                    subtext="Target: 500"
                />

                <x-admin.dashboard.stat-card 
                    title="Pengunjung Aktif"
                    value="{{ number_format($stats['active_visitors']) }}"
                    icon="fa-solid fa-users-viewfinder"
                    color="purple"
                    subtext="Real-time di lokasi"
                />

                <x-admin.dashboard.stat-card 
                    title="Pesanan Pending"
                    value="{{ number_format($stats['pending_orders']) }}"
                    icon="fa-solid fa-clock"
                    color="yellow"
                    subtext="Potensi Revenue: Rp {{ number_format($stats['pending_orders'] * 15000, 0, ',', '.') }}" 
                />
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sales Trend Chart -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Tren Penjualan (30 Hari Terakhir)</h3>
                        </div>
                        <div class="relative w-full h-72">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Ticket Type Distribution -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Distribusi Tipe Tiket</h3>
                    </div>
                    <div class="relative w-full h-64 flex items-center justify-center">
                        <canvas id="ticketTypeChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                         @foreach($ticketTypes as $type)
                            <div class="flex justify-between items-center text-sm">
                                <span class="capitalize text-gray-600">{{ $type->type }}</span>
                                <span class="font-bold text-gray-800">{{ $type->count }} ({{ number_format(($type->count / $ticketTypes->sum('count')) * 100, 1) }}%)</span>
                            </div>
                         @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Transactions & Occupancy Leaderboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Orders -->
                <div class="lg:col-span-2">
                    <x-admin.dashboard.recent-transactions :transactions="$recentTransactions" />
                </div>

                <!-- Occupancy Leaderboard -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                     <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Destinasi Terpopuler Hari Ini</h3>
                    </div>
                    <div class="space-y-4">
                        @forelse($occupancy as $index => $place)
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between mb-1">
                                        <span class="font-medium text-gray-700">{{ $place->name }}</span>
                                        <span class="font-bold text-gray-800">{{ $place->sold }} Tiket</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($place->sold / ($occupancy->max('sold') ?: 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 py-4 italic">Belum ada data hari ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Trend Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 400);
            salesGradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            salesGradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: @json($salesChart['labels']),
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: @json($salesChart['revenue']),
                        borderColor: '#3b82f6',
                        backgroundColor: salesGradient,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointHoverBackgroundColor: '#3b82f6',
                        pointHoverBorderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
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
                            grid: { borderDash: [2, 4], color: '#f3f4f6' },
                            ticks: { callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID', {notation: 'compact'}); } }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Ticket Type Chart
            const typeCtx = document.getElementById('ticketTypeChart').getContext('2d');
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($ticketTypes->pluck('type')),
                    datasets: [{
                        data: @json($ticketTypes->pluck('count')),
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
</x-app-layout>
