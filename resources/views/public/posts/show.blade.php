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

                        @if(!empty($statWidgets))
                        {{-- Dynamic Stat Widgets --}}
                        <div class="space-y-6 sm:space-y-8 mt-6 sm:mt-8">
                            @foreach($statWidgets as $index => $widget)
                            <div class="bg-white dark:bg-surface-dark rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-gray-100 dark:border-gray-800 shadow-sm">
                                {{-- Widget Header --}}
                                <div class="flex items-center justify-between mb-4 sm:mb-5">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl flex items-center justify-center
                                            @switch($widget['color'])
                                                @case('emerald') bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400 @break
                                                @case('blue') bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400 @break
                                                @case('amber') bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400 @break
                                                @case('violet') bg-violet-100 text-violet-600 dark:bg-violet-900/40 dark:text-violet-400 @break
                                                @case('cyan') bg-cyan-100 text-cyan-600 dark:bg-cyan-900/40 dark:text-cyan-400 @break
                                                @case('rose') bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400 @break
                                                @case('indigo') bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400 @break
                                                @default bg-gray-100 text-gray-600 @break
                                            @endswitch
                                        ">
                                            <i class="{{ $widget['icon'] }} text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">{{ $widget['title'] }}</h4>
                                            <p class="text-[10px] sm:text-xs text-gray-500">{{ $widget['period_label'] }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] sm:text-xs text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-0.5 rounded-full">
                                        Live Data
                                    </span>
                                </div>

                                {{-- Summary Cards --}}
                                @if(!empty($widget['data']['summary']))
                                <div class="grid grid-cols-{{ count($widget['data']['summary']) }} gap-2 sm:gap-3 mb-4">
                                    @foreach($widget['data']['summary'] as $summary)
                                    <div class="p-3 sm:p-4 rounded-xl border
                                        @switch($summary['color'] ?? 'gray')
                                            @case('emerald') bg-emerald-50 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-900/30 @break
                                            @case('blue') bg-blue-50 border-blue-100 dark:bg-blue-900/10 dark:border-blue-900/30 @break
                                            @case('amber') bg-amber-50 border-amber-100 dark:bg-amber-900/10 dark:border-amber-900/30 @break
                                            @case('cyan') bg-cyan-50 border-cyan-100 dark:bg-cyan-900/10 dark:border-cyan-900/30 @break
                                            @case('rose') bg-rose-50 border-rose-100 dark:bg-rose-900/10 dark:border-rose-900/30 @break
                                            @default bg-gray-50 border-gray-100 dark:bg-gray-800 dark:border-gray-700 @break
                                        @endswitch
                                    ">
                                        <p class="text-[10px] sm:text-xs text-gray-500 font-medium mb-1">{{ $summary['label'] }}</p>
                                        <p class="text-base sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $summary['value'] }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Chart Canvas --}}
                                <div class="relative h-48 sm:h-64 w-full">
                                    <canvas id="statWidget{{ $index }}"></canvas>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

    @if(!empty($statWidgets))
    @push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.color = '#64748b';
            Chart.defaults.scale.grid.color = 'rgba(226, 232, 240, 0.6)';

            const widgets = @json($statWidgets);

            widgets.forEach((widget, index) => {
                const el = document.getElementById('statWidget' + index);
                if (!el) return;
                const ctx = el.getContext('2d');

                let chartType = 'line';
                let chartOptions = {
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
                        y: { beginAtZero: true, border: { display: false }, ticks: { precision: 0 } },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                };

                switch (widget.chart_type) {
                    case 'bar':
                        chartType = 'bar';
                        break;
                    case 'horizontal_bar':
                        chartType = 'bar';
                        chartOptions.indexAxis = 'y';
                        break;
                    case 'doughnut':
                        chartType = 'doughnut';
                        chartOptions.scales = {};
                        chartOptions.plugins.legend = {
                            position: 'bottom',
                            labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } }
                        };
                        break;
                    case 'area':
                        chartType = 'line';
                        break;
                    default:
                        chartType = 'line';
                }

                new Chart(ctx, {
                    type: chartType,
                    data: {
                        labels: widget.data.labels || [],
                        datasets: (widget.data.datasets || []).map(ds => ({
                            ...ds,
                            borderWidth: ds.borderWidth || 2,
                            pointRadius: ds.pointRadius || (chartType === 'line' ? 3 : 0),
                            pointHoverRadius: ds.pointHoverRadius || (chartType === 'line' ? 5 : 0),
                            pointBackgroundColor: ds.pointBackgroundColor || '#ffffff',
                            pointBorderColor: ds.borderColor || ds.backgroundColor?.[0] || '#3b82f6',
                            pointBorderWidth: ds.pointBorderWidth || 2,
                        })),
                    },
                    options: chartOptions
                });
            });
        });
    </script>
    @endpush
    @endif

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
