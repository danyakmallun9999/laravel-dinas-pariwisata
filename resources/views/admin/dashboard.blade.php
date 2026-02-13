@pushOnce('styles')


@endPushOnce

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Admin Panel</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
            </div>
            <!-- Date Display -->
            <div class="hidden md:flex items-center gap-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner -->
            <!-- Welcome Banner (Compact & Elegant) -->
            <!-- Welcome Banner (Compact & Elegant) -->
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            Selamat Datang, <span class="text-blue-600">{{ Auth::user()->name }}</span>! 👋
                        </h1>
                        <p class="text-gray-500 text-sm mt-1">
                            Siap untuk mengelola pariwisata Jepara hari ini?
                        </p>
                    </div>
                    <div class="hidden md:block">
                         <a href="{{ route('welcome') }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-2xl text-sm font-bold transition-all border border-gray-200 hover:border-blue-200">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            Lihat Website
                        </a>
                    </div>
                </div>
            </div>

            <!-- Visitation Trends Chart -->
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-bold text-gray-800">Grafik Jumlah Pengunjung</h3>
                            <span class="text-xs text-gray-500 bg-white px-3 py-1 rounded-full border border-gray-200" id="chartPeriodLabel">
                                 {{ date('Y') }}
                            </span>
                        </div>
                        
                        <!-- Toggle Buttons -->
                        <div class="bg-gray-200/50 p-1 rounded-xl flex items-center self-start sm:self-auto">
                            <button onclick="switchChartMode('monthly')" id="btn-monthly" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm bg-white text-blue-600">
                                Bulanan
                            </button>
                            <button onclick="switchChartMode('yearly')" id="btn-yearly" class="px-4 py-1.5 rounded-lg text-xs font-bold text-gray-500 hover:text-gray-700 transition-all">
                                Tahunan
                            </button>
                        </div>
                    </div>
                    <div class="relative w-full h-80">
                        <canvas id="visitationChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @unlessrole('pengelola_wisata')
                @canany('create destinations')
                <a href="{{ route('admin.places.create') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-blue-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-blue-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 border border-blue-100">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-blue-600 transition-colors text-center">Tambah Destinasi</span>
                    </div>
                </a>
                @endcanany
                @endunlessrole

                @canany('create posts')
                <a href="{{ route('admin.posts.create') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-purple-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-purple-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 border border-purple-100">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-purple-600 transition-colors text-center">Tulis Berita</span>
                    </div>
                </a>
                @endcanany

                @canany('create events')
                <a href="{{ route('admin.events.create') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-orange-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-orange-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-300 border border-orange-100">
                            <i class="fa-solid fa-calendar-plus"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-orange-600 transition-colors text-center">Agenda Baru</span>
                    </div>
                </a>
                @endcanany

                <!-- Check-in Scanner Shortcut (Hidden for Pengelola Wisata) -->
                @unlessrole('pengelola_wisata')
                @canany('scan tickets')
                <a href="{{ route('admin.scan.index') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-emerald-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-emerald-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 border border-emerald-100">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-emerald-600 transition-colors text-center">Check-in Tiket</span>
                    </div>
                </a>
                @endcanany
                @endunlessrole
            </div>

            <!-- Admin Wisata Enhanced Dashboard -->
            @if(auth()->user()->hasAnyPermission(['view all destinations', 'view own destinations', 'view all tickets']))
            <div class="space-y-6">
                <!-- Key Metrics with Growth Indicators -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Visitors -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-1 rounded-[2.5rem] border border-blue-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-500/30">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                @php
                                    $visitorGrowth = $stats['visitors_last_month'] > 0 
                                        ? (($stats['visitors_this_month'] - $stats['visitors_last_month']) / $stats['visitors_last_month']) * 100 
                                        : 0;
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $visitorGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    <i class="fa-solid fa-{{ $visitorGrowth >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ number_format(abs($visitorGrowth), 1) }}%
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($stats['total_visitors']) }}</h3>
                            <p class="text-sm text-gray-600 font-medium">Total Pengunjung</p>
                            <p class="text-xs text-gray-500 mt-2">{{ number_format($stats['visitors_this_month']) }} bulan ini</p>
                        </div>
                    </div>

                    <!-- Total Revenue -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-1 rounded-[2.5rem] border border-emerald-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30">
                                    <i class="fa-solid fa-sack-dollar"></i>
                                </div>
                                @php
                                    $revenueGrowth = $stats['revenue_last_month'] > 0 
                                        ? (($stats['revenue_this_month'] - $stats['revenue_last_month']) / $stats['revenue_last_month']) * 100 
                                        : 0;
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $revenueGrowth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    <i class="fa-solid fa-{{ $revenueGrowth >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ number_format(abs($revenueGrowth), 1) }}%
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-1">Rp {{ number_format($stats['ticket_revenue'] / 1000000, 1) }}M</h3>
                            <p class="text-sm text-gray-600 font-medium">Total Pendapatan</p>
                            <p class="text-xs text-gray-500 mt-2">Rp {{ number_format($stats['revenue_this_month'] / 1000, 0) }}K bulan ini</p>
                        </div>
                    </div>

                    <!-- Total Bookings -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-1 rounded-[2.5rem] border border-purple-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-purple-500/30">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">
                                    {{ $stats['ticket_orders_count'] > 0 ? number_format(($stats['ticket_orders_paid'] / $stats['ticket_orders_count']) * 100, 0) : 0 }}% paid
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($stats['ticket_orders_count']) }}</h3>
                            <p class="text-sm text-gray-600 font-medium">Total Pesanan</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $stats['ticket_orders_pending'] }} menunggu pembayaran</p>
                        </div>
                    </div>

                    <!-- Average Order Value -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-1 rounded-[2.5rem] border border-amber-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-amber-500/30">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                    <i class="fa-solid fa-star"></i> AVG
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-1">Rp {{ number_format($stats['average_order_value'] / 1000, 0) }}K</h3>
                            <p class="text-sm text-gray-600 font-medium">Rata-rata Transaksi</p>
                            <p class="text-xs text-gray-500 mt-2">Per pesanan tiket</p>
                        </div>
                    </div>

                    <!-- Today's Revenue -->
                    <div class="bg-gradient-to-br from-cyan-50 to-teal-50 p-1 rounded-[2.5rem] border border-cyan-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-cyan-500/30">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-700">
                                    Today
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-1">Rp {{ number_format($stats['revenue_today'] / 1000, 0) }}K</h3>
                            <p class="text-sm text-gray-600 font-medium">Pendapatan Hari Ini</p>
                            <p class="text-xs text-cyan-500 mt-2">Total hari ini</p>
                        </div>
                    </div>

                    <!-- Today's Visitors -->
                    <div class="bg-gradient-to-br from-rose-50 to-pink-50 p-1 rounded-[2.5rem] border border-rose-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-rose-500/30">
                                    <i class="fa-solid fa-person-walking-luggage"></i>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                    Today
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-1">{{ number_format($stats['visitors_today']) }}</h3>
                            <p class="text-sm text-gray-600 font-medium">Pengunjung Hari Ini</p>
                            <p class="text-xs text-rose-500 mt-2">Orang berkunjung</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Top Tickets -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Transactions -->
                    <div class="lg:col-span-2 bg-gradient-to-br from-blue-50 to-indigo-50 p-1 rounded-[2.5rem] border border-blue-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800">Transaksi Terakhir</h3>
                                </div>
                                <a href="{{ route('admin.tickets.orders') }}" class="group flex items-center gap-2 text-sm text-blue-600 font-bold hover:text-blue-700 transition-colors">
                                    Lihat Semua
                                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                            
                            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 font-bold tracking-wider">Pelanggan</th>
                                            <th class="px-6 py-4 font-bold tracking-wider">Tiket</th>
                                            <th class="px-6 py-4 font-bold tracking-wider">Total</th>
                                            <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($stats['recent_orders'] ?? [] as $order)
                                        <tr class="hover:bg-blue-50/50 transition-colors group cursor-default">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 text-xs font-bold">
                                                        {{ substr($order->user->name ?? 'G', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-gray-800">{{ $order->user->name ?? 'Guest' }}</div>
                                                        <div class="text-xs text-gray-500 font-medium">{{ $order->created_at->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-700">{{ $order->ticket->name ?? '-' }}</div>
                                                <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                                    <i class="fa-solid fa-location-dot text-gray-300 text-[10px]"></i>
                                                    {{ $order->ticket->place->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-black text-gray-800">
                                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusClasses = match($order->status) {
                                                        'paid' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                        'used' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                        default => 'bg-gray-100 text-gray-700 border-gray-200'
                                                    };
                                                    $statusIcon = match($order->status) {
                                                        'paid' => 'fa-check',
                                                        'used' => 'fa-ticket-simple',
                                                        default => 'fa-circle-question'
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusClasses }}">
                                                    <i class="fa-solid {{ $statusIcon }} text-[10px]"></i>
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center gap-3">
                                                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 text-xl">
                                                        <i class="fa-solid fa-receipt"></i>
                                                    </div>
                                                    <span class="text-gray-500 font-medium">Belum ada transaksi</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling Tickets -->
                    <div class="bg-gradient-to-br from-indigo-50 to-violet-50 p-1 rounded-[2.5rem] border border-indigo-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-gray-800">Tiket Terlaris</h3>
                            </div>
                            
                            <div class="space-y-3">
                                @forelse($stats['top_tickets'] ?? [] as $ticket)
                                @php
                                    $rankColor = match($loop->iteration) {
                                        1 => 'bg-yellow-100 text-yellow-700 border-yellow-200 shadow-yellow-200/50',
                                        2 => 'bg-gray-100 text-gray-700 border-gray-200 shadow-gray-200/50',
                                        3 => 'bg-orange-100 text-orange-700 border-orange-200 shadow-orange-200/50',
                                        default => 'bg-blue-50 text-blue-600 border-blue-100 shadow-blue-100/50'
                                    };
                                @endphp
                                <div class="group flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md hover:scale-[1.02] transition-all duration-300">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <div class="w-10 h-10 flex-shrink-0 rounded-xl {{ $rankColor }} flex items-center justify-center font-black text-sm border shadow-sm group-hover:rotate-6 transition-transform">
                                            #{{ $loop->iteration }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-gray-800 text-sm line-clamp-1 group-hover:text-blue-600 transition-colors">{{ $ticket->name }}</h4>
                                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                                <i class="fa-solid fa-location-dot text-gray-300"></i>
                                                <span class="truncate">{{ $ticket->place->name ?? '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0 ml-4">
                                        <span class="block font-black text-gray-800 text-lg leading-none">{{ number_format($ticket->total_sold) }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Terjual</span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8 text-gray-500 text-sm">
                                    Belum ada data penjualan
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Revenue Trend & Visitor Origins -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Revenue Trend -->
                    <div class="lg:col-span-2 bg-gradient-to-br from-emerald-50 to-teal-50 p-1 rounded-[2.5rem] border border-emerald-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800">Tren Pendapatan</h3>
                                </div>
                                <div class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                                    7 Hari Terakhir
                                </div>
                            </div>
                            <div class="h-[300px] relative">
                                <canvas id="revenueTrendChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Origins -->
                    <div class="bg-gradient-to-br from-rose-50 to-orange-50 p-1 rounded-[2.5rem] border border-rose-100">
                        <div class="p-6 rounded-[2rem] bg-white/80 backdrop-blur-sm h-full">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg">
                                        <i class="fa-solid fa-map-location-dot"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800">Asal Pengunjung</h3>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                @forelse($stats['top_visitor_origins'] ?? [] as $origin)
                                <div class="flex items-center justify-between p-3 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 text-xs font-bold border border-gray-100 flex-shrink-0">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-gray-800 text-sm truncate">{{ $origin->customer_city ?? 'Lainnya' }}</h4>
                                            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                                @php
                                                    $maxVisitors = $stats['top_visitor_origins']->max('total_visitors') ?: 1;
                                                    $width = ($origin->total_visitors / $maxVisitors) * 100;
                                                @endphp
                                                <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $width }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="block font-black text-gray-800 text-sm">{{ number_format($origin->total_visitors) }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Orang</span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 text-2xl mx-auto mb-3">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <p class="text-gray-500 text-sm">Belum ada data alamat</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Places -->
                @canany('view all destinations', 'view own destinations')
                    @php
                        $isSinglePlaceManager = !auth()->user()->can('view all destinations') && $stats['places_count'] === 1;
                    @endphp

                    @if($isSinglePlaceManager)
                        <!-- Single Place Manager View -->
                        <a href="{{ route('admin.places.index') }}" class="group bg-blue-600 p-1 rounded-[2.5rem] border border-blue-500 hover:scale-[1.02] transition-transform duration-300">
                            <div class="p-6 rounded-[2rem] border border-white/10 bg-gradient-to-br from-blue-600 to-indigo-700 h-full relative overflow-hidden">
                                <!-- Decoration -->
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full -ml-12 -mb-12 blur-xl"></div>
                                
                                <div class="relative z-10 flex flex-col h-full justify-between">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-xl backdrop-blur-sm">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </div>
                                        <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-white/20 text-white backdrop-blur-md border border-white/10">Lola Wisata</span>
                                    </div>
                                    
                                    <div>
                                        <h3 class="text-2xl font-bold text-white mb-1">Kelola Wisata Saya</h3>
                                        <p class="text-blue-100 text-sm">Update info, foto, dan tiket destinasi Anda satu pintu.</p>
                                    </div>

                                    <div class="mt-4 flex items-center gap-2 text-white/80 text-xs font-medium">
                                        <span>Klik untuk edit</span>
                                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @else
                        <!-- Standard View -->
                        <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                            <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl border border-blue-200">
                                        <i class="fa-solid fa-map-marked-alt"></i>
                                    </div>
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 border border-blue-100">+{{ $stats['places_count'] > 0 ? 'Active' : '0' }}</span>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['places_count'] }}</h3>
                                <p class="text-sm text-gray-500 font-medium">Destinasi Wisata</p>
                            </div>
                        </div>
                    @endif
                @endcanany

                <!-- Posts -->
                @canany('view all posts', 'view own posts')
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl border border-purple-200">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 border border-purple-100">News</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['posts_count'] }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Berita & Artikel</p>
                    </div>
                </div>
                @endcanany

                <!-- Events -->
                @canany('view all events', 'view own events')
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl border border-orange-200">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 border border-orange-100">Events</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['events_count'] }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Agenda Kegiatan</p>
                    </div>
                </div>
                @endcanany

                <!-- Categories -->
                @canany('manage categories')
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center text-xl border border-pink-200">
                                <i class="fa-solid fa-tags"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-pink-50 text-pink-600 border border-pink-100">Types</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ \App\Models\Category::count() }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Kategori Wisata</p>
                    </div>
                </div>
                @endcanany
            </div>

            <!-- Berita & Agenda Breakdown (Admin Berita Only) -->
            @if(auth()->user()->hasAnyPermission(['view all posts', 'view own posts', 'view all events', 'view own events']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Post Status Breakdown -->
                @if(auth()->user()->hasAnyPermission(['view all posts', 'view own posts']))
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Status Berita</h3>
                            <a href="{{ route('admin.posts.index') }}" class="text-sm text-purple-600 hover:underline">Kelola</a>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 hover:border-green-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center border border-green-100">
                                        <i class="fa-solid fa-check-circle text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Published</p>
                                        <h4 class="text-2xl font-bold text-gray-800">{{ $stats['posts_published'] }}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">{{ $stats['posts_count'] > 0 ? round(($stats['posts_published'] / $stats['posts_count']) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 hover:border-amber-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                                        <i class="fa-solid fa-file-pen text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Draft</p>
                                        <h4 class="text-2xl font-bold text-gray-800">{{ $stats['posts_draft'] }}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">{{ $stats['posts_count'] > 0 ? round(($stats['posts_draft'] / $stats['posts_count']) * 100) : 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Event Timeline Breakdown -->
                @if(auth()->user()->hasAnyPermission(['view all events', 'view own events']))
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Timeline Agenda</h3>
                            <a href="{{ route('admin.events.index') }}" class="text-sm text-orange-600 hover:underline">Kelola</a>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 hover:border-blue-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                                        <i class="fa-solid fa-clock text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Upcoming</p>
                                        <h4 class="text-2xl font-bold text-gray-800">{{ $stats['events_upcoming'] }}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">{{ $stats['events_count'] > 0 ? round(($stats['events_upcoming'] / $stats['events_count']) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 hover:border-gray-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-600 flex items-center justify-center border border-gray-100">
                                        <i class="fa-solid fa-history text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Past Events</p>
                                        <h4 class="text-2xl font-bold text-gray-800">{{ $stats['events_past'] }}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">{{ $stats['events_count'] > 0 ? round(($stats['events_past'] / $stats['events_count']) * 100) : 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Ticket Orders Status (Admin Wisata Only) -->
            @can('view all tickets')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Status Pesanan Tiket</h3>
                            <a href="{{ route('admin.tickets.orders') }}" class="text-sm text-emerald-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 hover:border-emerald-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                                        <i class="fa-solid fa-check-double text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Paid</p>
                                        <h4 class="text-2xl font-bold text-gray-800">{{ $stats['ticket_orders_paid'] }}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">{{ $stats['ticket_orders_count'] > 0 ? round(($stats['ticket_orders_paid'] / $stats['ticket_orders_count']) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-gray-100 hover:border-amber-200 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                                        <i class="fa-solid fa-clock text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Pending</p>
                                        <h4 class="text-2xl font-bold text-gray-800">{{ $stats['ticket_orders_pending'] }}</h4>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-400">{{ $stats['ticket_orders_count'] > 0 ? round(($stats['ticket_orders_pending'] / $stats['ticket_orders_count']) * 100) : 0 }}%</span>
                                </div>
                            </div>
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-50 to-blue-50 border border-emerald-100">
                                <p class="text-xs font-medium text-gray-500 mb-1">Total Revenue</p>
                                <h4 class="text-2xl font-bold text-emerald-600">Rp {{ number_format($stats['ticket_revenue'], 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan

            <!-- Content Grid 1: Chart & Upcoming Events -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Statistics Chart -->
                @canany('view all destinations', 'manage categories')
                <div class="lg:col-span-2 bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Distribusi Kategori Wisata</h3>
                            <a href="{{ route('admin.categories.index') }}" class="text-sm text-blue-600 hover:underline">Kelola Kategori</a>
                        </div>
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <!-- Chart -->
                            <div class="relative w-48 h-48 md:w-56 md:h-56 flex-shrink-0">
                                <canvas id="categoriesChart"></canvas>
                                <!-- Center Text -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-3xl font-bold text-gray-800">{{ $stats['places_count'] }}</span>
                                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Total</span>
                                </div>
                            </div>

                            <!-- Custom Legend -->
                            <div class="flex-1 w-full grid grid-cols-2 gap-3">
                                @foreach($stats['categories'] as $category)
                                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white transition-colors border border-transparent hover:border-gray-100">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $category->color }}"></span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700 truncate">{{ $category->name }}</span>
                                            <span class="text-xs font-bold text-gray-500">{{ $category->places_count }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200/50 rounded-full h-1 mt-1">
                                            <div class="bg-gray-300 h-1 rounded-full" style="width: {{ $stats['places_count'] > 0 ? ($category->places_count / $stats['places_count']) * 100 : 0 }}%; background-color: {{ $category->color }}"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endcanany

                <!-- Upcoming Events Widget -->
                @canany('view all events', 'view own events')
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Agenda Terdekat</h3>
                            <a href="{{ route('admin.events.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($stats['upcoming_events'] as $event)
                                <div class="flex gap-4 items-start group">
                                    <div class="flex flex-col items-center bg-white rounded-2xl p-2 min-w-[3.5rem] border border-gray-100 group-hover:border-blue-200 transition-colors">
                                        <span class="text-xs font-bold text-red-500 uppercase">{{ $event->start_date->isoFormat('MMM') }}</span>
                                        <span class="text-xl font-bold text-gray-800">{{ $event->start_date->isoFormat('DD') }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 line-clamp-1 group-hover:text-blue-600 transition-colors">{{ $event->title }}</h4>
                                        <p class="text-xs text-gray-500 mb-1 line-clamp-1">{{ $event->location }}</p>
                                        <span class="text-[0.65rem] bg-white border border-gray-100 text-gray-500 px-2 py-0.5 rounded-full inline-block">
                                            {{ $event->start_date->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-400">
                                    <i class="fa-regular fa-calendar-xmark text-3xl mb-2"></i>
                                    <p class="text-sm">Belum ada agenda terdekat.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endcanany
            </div>



            <!-- Content Grid 2: Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Places -->
                @canany('view all destinations', 'view own destinations')
                @if(!(!auth()->user()->can('view all destinations') && $stats['places_count'] === 1))
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Destinasi Terbaru</h3>
                            <a href="{{ route('admin.places.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($stats['recent_places'] as $place)
                            <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white transition-colors group border border-transparent hover:border-gray-100">
                                @if($place->image_path)
                                    <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-12 h-12 rounded-xl object-cover group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 truncate">{{ $place->name }}</h4>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white border border-white/20 whitespace-nowrap" style="background-color: {{ $place->category->color }}">
                                            {{ $place->category->name }}
                                        </span>
                                        <span class="whitespace-nowrap">• {{ $place->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.places.edit', $place) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors bg-white rounded-lg border border-gray-100 opacity-0 group-hover:opacity-100">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                            @empty
                                <p class="text-gray-500 text-sm italic">Belum ada data destinasi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
                @endcanany

                <!-- Recent Posts -->
                @canany('view all posts', 'view own posts')
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Berita & Artikel Terbaru</h3>
                            <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($stats['recent_posts'] as $post)
                            <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white transition-colors group border border-transparent hover:border-gray-100">
                                <div class="flex flex-col items-center justify-center w-12 h-12 rounded-xl bg-white text-indigo-600 border border-gray-100">
                                    <i class="fa-solid {{ $post->type == 'news' ? 'fa-newspaper' : 'fa-bullhorn' }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 truncate">{{ $post->title }}</h4>
                                    <p class="text-xs text-gray-500">
                                        {{ $post->published_at ? $post->published_at->format('d M Y') : 'Draft' }} • 
                                        <span class="{{ $post->is_published ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ $post->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </p>
                                </div>
                                <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors bg-white rounded-lg border border-gray-100 opacity-0 group-hover:opacity-100">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                            @empty
                                <p class="text-gray-500 text-sm italic">Belum ada berita terbaru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endcanany
            </div>

        </div>
    </div>

    <!-- Chart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriesCtx = document.getElementById('categoriesChart');
            if (categoriesCtx) {
                new Chart(categoriesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($stats['categories']->pluck('name')->values()),
                        datasets: [{
                            data: @json($stats['categories']->pluck('places_count')->values()),
                            backgroundColor: @json($stats['categories']->pluck('color')->values()),
                            borderWidth: 0,
                            hoverOffset: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%', 
                        plugins: {
                            legend: {
                                display: false // We use custom legend
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.parsed || 0;
                                        let total = context.chart._metasets[context.datasetIndex].total;
                                        let percentage = ((value / total) * 100).toFixed(1) + '%';
                                        return label + ': ' + value + ' (' + percentage + ')';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Visitation Trends Chart with Toggle
            const visitationCtx = document.getElementById('visitationChart');
            
            // Prepare Data
            const monthlyData = @json($stats['visitation_trends'] ?? []);
            const yearlyData = @json($stats['yearly_visitation_trends'] ?? []);
            let visitationChart = null;

            // Initialize Chart if data exists
            if (visitationCtx && (monthlyData.labels || yearlyData.labels)) {
                
                // Helper to init chart
                const initChart = (data) => {
                    return new Chart(visitationCtx, {
                        type: 'line',
                        data: JSON.parse(JSON.stringify(data)), // Deep copy to avoid reference issues
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        padding: 20,
                                        font: { size: 11 }
                                    },
                                    onClick: function(e, legendItem, legend) {
                                        const index = legendItem.datasetIndex;
                                        const ci = legend.chart;
                                        if (ci.isDatasetVisible(index)) {
                                            ci.hide(index);
                                            legendItem.hidden = true;
                                        } else {
                                            ci.show(index);
                                            legendItem.hidden = false;
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    padding: 12,
                                    cornerRadius: 8,
                                    titleFont: { size: 13, weight: 'bold' }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { borderDash: [2, 2], color: '#f1f5f9' },
                                    ticks: { precision: 0 }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                };

                // Initial Render (Monthly)
                if (monthlyData.labels) {
                    visitationChart = initChart(monthlyData);
                } else if (yearlyData.labels) {
                    // Fallback if no monthly data
                    visitationChart = initChart(yearlyData);
                    document.getElementById('chartPeriodLabel').innerText = '5 Tahun Terakhir';
                    toggleActiveButton('yearly');
                }

                // Global function for button click
                window.switchChartMode = function(mode) {
                    if (!visitationChart) return;

                    const btnMonthly = document.getElementById('btn-monthly');
                    const btnYearly = document.getElementById('btn-yearly');
                    const label = document.getElementById('chartPeriodLabel');

                    let newData;

                    if (mode === 'monthly') {
                        newData = monthlyData;
                        label.innerText = '{{ date("Y") }}';
                        
                        btnMonthly.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                        btnMonthly.classList.remove('text-gray-500', 'hover:text-gray-700');
                        
                        btnYearly.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                        btnYearly.classList.add('text-gray-500', 'hover:text-gray-700');
                    } else {
                        newData = yearlyData;
                        label.innerText = '5 Tahun Terakhir';

                        btnYearly.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                        btnYearly.classList.remove('text-gray-500', 'hover:text-gray-700');
                        
                        btnMonthly.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                        btnMonthly.classList.add('text-gray-500', 'hover:text-gray-700');
                    }

                    if (newData && newData.labels) {
                        visitationChart.data.labels = newData.labels;
                        visitationChart.data.datasets = newData.datasets;
                        visitationChart.update();
                    }
                };
            }

            // Revenue Trend Chart
            const revenueCtx = document.getElementById('revenueTrendChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: @json($stats['weekly_revenue']['labels'] ?? []),
                        datasets: [{
                            label: 'Pendapatan',
                            data: @json($stats['weekly_revenue']['data'] ?? []),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#10b981',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { borderDash: [2, 2], color: '#f1f5f9' },
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt';
                                        if (value >= 1000) return 'Rp ' + (value / 1000) + 'rb';
                                        return 'Rp ' + value;
                                    }
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
