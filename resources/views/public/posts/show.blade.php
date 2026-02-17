<x-public-layout>
    {{-- Scrollbar-hide utility for mobile --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @push('seo')
        <x-seo 
            :title="$post->title . ' - Jelajah Jepara'"
            :description="Str::limit(strip_tags($post->content), 150)"
            :image="$post->image_path ? asset($post->image_path) : asset('images/logo-kura.png')"
            type="article"
        />
    @endpush
    <div class="bg-white dark:bg-background-dark min-h-screen -mt-20 pt-24 sm:pt-32">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 sm:pb-12">
            
            <!-- Breadcrumb -->
            <nav class="flex justify-center text-[11px] sm:text-xs md:text-sm text-gray-500/80 sm:text-gray-500 mb-3 sm:mb-6 space-x-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors" wire:navigate>{{ __('Nav.Home') }}</a>
                <span>/</span>
                <a href="{{ route('posts.index') }}" class="text-gray-400 hover:text-primary transition-colors" wire:navigate>{{ __('Nav.News') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 truncate max-w-[200px]">{{ $post->translated_title }}</span>
            </nav>

            <!-- Header Section -->
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-2xl sm:text-4xl md:text-6xl font-black text-gray-900 dark:text-white leading-tight mb-4 sm:mb-6 tracking-tight font-serif">
                    {{ $post->translated_title }}
                </h1>
                
                <div class="flex flex-col md:flex-row items-center justify-center gap-3 sm:gap-6 border-b border-gray-100 dark:border-gray-800 pb-5 mb-5 sm:pb-8 sm:mb-8">
                    <!-- Author Info -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <img src="{{ asset('images/logo-kura.png') }}" class="w-6 h-6 object-contain" alt="Admin">
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $post->author ?? __('News.Header.Department') }}</p>
                            <p class="text-xs text-gray-500">
                                Published {{ $post->published_at ? $post->published_at->format('M d, Y') : '-' }} • {{ ceil(str_word_count(strip_tags($post->translated_content)) / 200) }} {{ __('News.ReadTime') }}
                            </p>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="hidden md:block w-px h-8 bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Share Buttons -->
                    <!-- Share Buttons -->
                    <!-- Share Buttons -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500 mr-2 hidden md:inline">Share:</span>
                        <x-share-modal :url="route('posts.show', $post)" :title="$post->translated_title" :text="Str::limit(strip_tags($post->translated_content), 100)">
                            <button class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Bagikan artikel ini">
                                <i class="fa-solid fa-share-nodes"></i>
                            </button>
                        </x-share-modal>
                    </div>
                </div>
            </div>

            <!-- Hero Image -->
            <div class="relative w-full aspect-video md:aspect-[21/9] rounded-xl sm:rounded-2xl overflow-hidden mb-6 sm:mb-12 shadow-md sm:shadow-2xl">
                <img src="{{ asset($post->image_path) }}" alt="{{ $post->translated_title }}" class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent pointer-events-none"></div>
                <!-- Image Credit -->
                @if($post->image_credit)
                <div class="absolute bottom-4 right-4 px-3 py-1 bg-black/50 backdrop-blur-sm rounded-full text-[10px] text-white/80">
                    Photo: {{ $post->image_credit }}
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-12">
                <!-- Main Content (Left) -->
                <div class="lg:col-span-8">
                    <!-- Intro Blockquote -->


                    <!-- Article Body -->
                    <article class="tinymce-content text-gray-700 dark:text-gray-300">
                        {!! \App\Services\ContentSanitizer::sanitizeAllowHtml($post->translated_content) !!}
                    </article>

                    <!-- Tags -->
                    <div class="mt-8 sm:mt-12 flex flex-wrap gap-2">
                        <span class="text-sm font-bold text-gray-400 mr-2">Tags:</span>
                        <a href="#" class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full hover:bg-primary hover:text-white transition-colors">#Jepara</a>
                        <a href="#" class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full hover:bg-primary hover:text-white transition-colors">#Pariwisata</a>
                        <a href="#" class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full hover:bg-primary hover:text-white transition-colors">#{{ $post->type }}</a>
                    </div>
                    <!-- Statistics Section -->
                    <div class="mt-10 pt-6 sm:mt-16 sm:pt-8 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-6 sm:mb-8">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">analytics</span>
                                Statistik Pembaca
                            </h3>
                            <span class="text-[10px] sm:text-xs text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 sm:px-3 py-1 rounded-full">
                                Real-time Data
                            </span>
                        </div>
                        
                        <!-- Post Stats Cards -->
                        <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-6 sm:mb-8">
                            <!-- Total Views -->
                            <div class="p-3 sm:p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl sm:rounded-2xl border border-blue-100 dark:border-blue-900/30 transition-all hover:shadow-md">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-3 mb-2">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-blue-100 dark:bg-blue-800/50 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                        <i class="fa-regular fa-eye text-[10px] sm:text-xs"></i>
                                    </div>
                                    <p class="text-[10px] sm:text-sm text-blue-600 dark:text-blue-400 font-medium">Total Views</p>
                                </div>
                                <p class="text-xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['total_views']) }}</p>
                            </div>
                            
                            <!-- Views Today -->
                            <div class="p-3 sm:p-6 bg-purple-50 dark:bg-purple-900/20 rounded-xl sm:rounded-2xl border border-purple-100 dark:border-purple-900/30 transition-all hover:shadow-md">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-3 mb-2">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-purple-100 dark:bg-purple-800/50 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                        <i class="fa-solid fa-chart-line text-[10px] sm:text-xs"></i>
                                    </div>
                                    <p class="text-[10px] sm:text-sm text-purple-600 dark:text-purple-400 font-medium">Hari Ini</p>
                                </div>
                                <p class="text-xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['views_today']) }}</p>
                            </div>
                            
                            <!-- Unique Visitors -->
                            <div class="p-3 sm:p-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl sm:rounded-2xl border border-emerald-100 dark:border-emerald-900/30 transition-all hover:shadow-md">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-3 mb-2">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-100 dark:bg-emerald-800/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                        <i class="fa-solid fa-users text-[10px] sm:text-xs"></i>
                                    </div>
                                    <p class="text-[10px] sm:text-sm text-emerald-600 dark:text-emerald-400 font-medium">Unique</p>
                                </div>
                                <p class="text-xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ number_format($stats['unique_visitors']) }}</p>
                            </div>
                        </div>

                        <!-- Article Views Chart -->
                        <div class="bg-white dark:bg-surface-dark rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-800 shadow-sm mb-10 sm:mb-16">
                            <h4 class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 sm:mb-6">Grafik Kunjungan (30 Hari Terakhir)</h4>
                            <div class="relative h-48 sm:h-64 w-full">
                                <canvas id="viewsChart"></canvas>
                            </div>
                        </div>

                        <!-- Tourism Stats Section - Redesigned Minimalist -->
                        <!-- Tourism Stats Section - Redesigned Minimalist -->
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl sm:rounded-3xl overflow-hidden">
                            <div class="p-5 sm:p-8 md:p-10">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-5 sm:mb-8 gap-3 sm:gap-4">
                                    <div>
                                        <h3 class="text-xl sm:text-2xl font-serif font-bold text-gray-900 flex items-center gap-2 sm:gap-3">
                                            <span class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                                <i class="fa-solid fa-map-location-dot text-blue-600 text-sm"></i>
                                            </span>
                                            Wisata Jepara {{ $tourismStats['year'] }}
                                        </h3>
                                        <p class="text-gray-500 text-sm mt-2 ml-11">Data statistik resmi pariwisata terkini.</p>
                                    </div>
                                    <div class="px-4 py-1.5 rounded-full bg-green-50 border border-green-100 text-green-700 text-xs font-bold flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                                        Live Data
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 sm:gap-8 mb-5 sm:mb-8">
                                    <!-- Total Tiket Terjual -->
                                    <div class="bg-gray-50/50 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100">
                                        <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1 sm:mb-2">Tiket Terjual</p>
                                        <div class="flex items-end gap-1 sm:gap-2">
                                            <p class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tight">{{ number_format($tourismStats['total_sold']) }}</p>
                                            <span class="text-xs sm:text-sm text-gray-500 mb-0.5 sm:mb-1.5">tiket</span>
                                        </div>
                                    </div>

                                    <!-- Total Pengunjung (Check-in) -->
                                    <div class="bg-blue-50/50 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-blue-100">
                                        <p class="text-[10px] sm:text-xs font-bold text-blue-400 uppercase tracking-wider mb-1 sm:mb-2">Total Pengunjung</p>
                                        <div class="flex items-end gap-1 sm:gap-2">
                                            <p class="text-2xl sm:text-4xl font-black text-blue-600 tracking-tight">{{ number_format($tourismStats['total_visitors']) }}</p>
                                            <span class="text-xs sm:text-sm text-blue-500 mb-0.5 sm:mb-1.5">orang</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Area -->
                                <div>
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="text-sm font-bold text-gray-900">Tren Kunjungan Bulanan</h4>
                                    </div>
                                    <div class="relative h-48 sm:h-64 w-full">
                                        <canvas id="tourismChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            const viewsChartEl = document.getElementById('viewsChart');
            const tourismChartEl = document.getElementById('tourismChart');
            
            if (!viewsChartEl || !tourismChartEl) return;

            // Configuration for charts
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.color = '#64748b'; // Slate-500
            Chart.defaults.scale.grid.color = 'rgba(226, 232, 240, 0.6)'; // Slate-200

            // 1. Article Views Chart (Line)
            const viewsCtx = viewsChartEl.getContext('2d');
            const viewsData = @json($viewsGraph);
            
            new Chart(viewsCtx, {
                type: 'line',
                data: {
                    labels: viewsData.map(d => {
                        const date = new Date(d.date);
                        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                    }),
                    datasets: [{
                        label: 'Pembaca',
                        data: viewsData.map(d => d.count),
                        borderColor: '#3b82f6', // Blue-500
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.1)'); // Blue-500 low opacity
                            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
                            return gradient;
                        },
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#f8fafc',
                            bodyColor: '#f8fafc',
                            padding: 10,
                            cornerRadius: 6,
                            displayColors: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });

            // 2. Tourism Stats Chart (Bar)
            const tourismCtx = tourismChartEl.getContext('2d');
            const tourismData = @json($tourismStats['monthly_data']);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            // Fill missing months with 0
            const monthlyVisitors = new Array(12).fill(0);
            tourismData.forEach(d => {
                monthlyVisitors[d.month - 1] = d.visitors;
            });

            new Chart(tourismCtx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Pengunjung',
                        data: monthlyVisitors,
                        backgroundColor: '#3b82f6', // Blue-500
                        borderRadius: 4,
                        barThickness: 12, // Thinner bars for elegance
                        hoverBackgroundColor: '#2563eb', // Blue-600
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#ffffff',
                            bodyColor: '#cbd5e1',
                            padding: 10,
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('id-ID').format(context.raw) + ' Tiket';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        }
                    }
                }
            });
        });
    </script>
    @endpush

                <!-- Sidebar (Right) -->
                <div class="lg:col-span-4 space-y-6 sm:space-y-12">
                    
                    <!-- Related News Widget -->
                    <div class="bg-white dark:bg-surface-dark rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-800 shadow-sm">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">feed</span>
                            {{ __('News.RelatedTitle') }}
                        </h3>
                        <div class="space-y-6">
                            @foreach($relatedPosts as $related)
                            <a href="{{ route('posts.show', $related) }}" class="group flex gap-4 items-start" wire:navigate>
                                <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                    <img src="{{ asset($related->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                </div>
                                <div>
                                    <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider block mb-1">
                                        {{ $related->published_at ? $related->published_at->format('M d, Y') : '' }}
                                    </span>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-primary transition-colors line-clamp-2">
                                        {{ $related->translated_title }}
                                    </h4>
                                </div>
                            </a>
                            @endforeach
                            @if($relatedPosts->isEmpty())
                                <p class="text-sm text-gray-500 italic">{{ __('News.NoRelated') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Must Visit Widget -->
                    <div class="bg-blue-50 dark:bg-blue-900/10 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-blue-100 dark:border-blue-900/20">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">explore</span>
                            {{ __('News.MustVisitTitle') }}
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($recommendedPlaces as $place)
                            @if(!$place->slug) @continue @endif
                            <div class="relative group rounded-xl overflow-hidden aspect-[16/9] shadow-md">
                                <img src="{{ asset($place->image_path) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <h4 class="text-white font-bold text-lg leading-tight mb-0.5">{{ $place->name }}</h4>
                                    <p class="text-white/80 text-xs">{{ $place->category?->name }}</p>
                                </div>
                                <a href="{{ route('places.show', $place) }}" class="absolute inset-0 z-10" wire:navigate></a>
                            </div>
                            @endforeach
                        </div>
                        
                        <a href="{{ route('explore.map') }}" class="mt-6 block w-full py-3 text-center text-sm font-bold text-blue-600 hover:text-blue-700 hover:bg-blue-100/50 rounded-xl transition-colors">
                            {{ __('News.ViewFullMap') }}
                        </a>
                    </div>

                    <!-- CTA Section -->
                    <div class="rounded-2xl overflow-hidden relative aspect-square bg-gray-900 flex items-center justify-center text-center p-6 group">
                         <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=600&q=80" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700">
                         <div class="relative z-10">
                             <h4 class="text-white font-serif text-2xl font-bold mb-2">{{ __('News.CTA.Title') }}</h4>
                             <p class="text-white/80 text-sm mb-4">{{ __('News.CTA.Subtitle') }}</p>
                             <a href="{{ route('places.index') }}" class="inline-block px-6 py-2 bg-white text-gray-900 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-primary hover:text-white transition-colors" wire:navigate>
                                 {{ __('News.CTA.Button') }}
                             </a>
                         </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-public-layout>
