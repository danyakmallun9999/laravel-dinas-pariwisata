<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen -mt-20 pt-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Nav.Home') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Nav.Events') }}</span>
            </nav>
            
            @php
                $allEvents = $groupedEvents->flatten();
                $locations = $allEvents->pluck('location')->unique()->values();
                // Extract unique years
                $years = $allEvents->pluck('start_date')->map(fn($date) => $date->format('Y'))->unique()->sortDesc()->values();
                // Extract unique month names (localized)
                $months = $allEvents->pluck('start_date')->map(fn($date) => $date->translatedFormat('F'))->unique()->values();

                $allEventsJSON = $allEvents->map(function($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'slug' => $event->slug,
                        'description' => $event->description,
                        'location' => $event->location,
                        'image_url' => $event->image ? Storage::url($event->image) : null,
                        'start_date_month' => $event->start_date->format('M'),
                        'start_date_day' => $event->start_date->format('d'),
                        'start_date_year' => $event->start_date->format('Y'),
                        'start_date_month_name' => $event->start_date->translatedFormat('F'),
                        'start_date_full' => $event->start_date->translatedFormat('F Y'),
                        'start_time' => $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '-',
                    ];
                });
            @endphp

            <div x-data="{ 
                search: '', 
                selectedYear: '', 
                selectedMonth: '', 
                selectedLocation: '',
                currentPage: 1,
                perPage: 9,
                events: {{ Js::from($allEventsJSON) }},
                get filteredEvents() {
                    return this.events.filter(event => {
                        const matchesSearch = this.search === '' || event.title.toLowerCase().includes(this.search.toLowerCase());
                        const matchesYear = this.selectedYear === '' || event.start_date_year === this.selectedYear;
                        const matchesMonth = this.selectedMonth === '' || event.start_date_month_name === this.selectedMonth;
                        const matchesLocation = this.selectedLocation === '' || event.location === this.selectedLocation;
                        return matchesSearch && matchesYear && matchesMonth && matchesLocation;
                    });
                },
                get totalPages() {
                    return Math.ceil(this.filteredEvents.length / this.perPage);
                },
                get paginatedEvents() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredEvents.slice(start, start + this.perPage);
                },
                get pages() {
                    let pages = [];
                    let start = Math.max(1, this.currentPage - 2);
                    let end = Math.min(this.totalPages, start + 4);
                    if (end - start < 4) start = Math.max(1, end - 4);
                    for (let i = start; i <= end; i++) {
                        if (i > 0) pages.push(i);
                    }
                    return pages;
                }
            }" x-init="$watch('search', () => currentPage = 1); $watch('selectedYear', () => currentPage = 1); $watch('selectedMonth', () => currentPage = 1); $watch('selectedLocation', () => currentPage = 1)">

                <!-- Header & Filters -->
                <div class="mb-10 border-b border-gray-100 dark:border-white/10 pb-8">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                                {{ __('Events.Title') }} <span class="text-slate-400 font-light">{{ date('Y') }}</span>
                            </h1>
                            <p class="text-slate-500 dark:text-slate-400 text-lg">
                                {{ __('Events.Subtitle', ['count' => $allEvents->count()]) }}
                            </p>
                        </div>
                    </div>

                    <!-- Advanced Filter Bar -->
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-2xl flex flex-col lg:flex-row gap-4">
                        
                        <!-- Search -->
                        <div class="flex-1 relative">
                            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input 
                                x-model="search"
                                type="text" 
                                placeholder="{{ __('Events.SearchPlaceholder') }}" 
                                class="w-full pl-11 pr-4 py-3 rounded-xl border-none ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-black/20 focus:ring-2 focus:ring-primary text-sm font-medium"
                            >
                        </div>

                        <!-- Year Dropdown (Custom) -->
                        <div class="relative min-w-[180px]" x-data="{ open: false }">
                            <i class="fa-regular fa-calendar-days absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10 pointer-events-none"></i>
                            
                            <button 
                                @click="open = !open"
                                @click.outside="open = false"
                                class="w-full pl-11 pr-10 py-3 text-left rounded-xl ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-black/20 focus:ring-2 focus:ring-primary text-sm font-medium flex items-center justify-between"
                            >
                                <span x-text="selectedYear === '' ? 'Semua Tahun    ' : selectedYear" class="truncate"></span>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-full bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-gray-100 dark:border-white/10 max-h-60 overflow-y-auto no-scrollbar"
                                style="display: none;"
                            >
                                <div class="p-1">
                                    <button 
                                        @click="selectedYear = ''; open = false"
                                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                        :class="selectedYear === '' ? 'text-primary font-bold bg-primary/5' : 'text-slate-600 dark:text-slate-300'"
                                    >
                                        Semua Tahun
                                    </button>
                                    @foreach($years as $year)
                                        <button 
                                            @click="selectedYear = '{{ $year }}'; open = false"
                                            class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                            :class="selectedYear === '{{ $year }}' ? 'text-primary font-bold bg-primary/5' : 'text-slate-600 dark:text-slate-300'"
                                        >
                                            {{ $year }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Month Dropdown (Custom) -->
                        <div class="relative min-w-[180px]" x-data="{ open: false }">
                            <i class="fa-regular fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10 pointer-events-none"></i>
                            
                            <button 
                                @click="open = !open"
                                @click.outside="open = false"
                                class="w-full pl-11 pr-10 py-3 text-left rounded-xl ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-black/20 focus:ring-2 focus:ring-primary text-sm font-medium flex items-center justify-between"
                            >
                                <span x-text="selectedMonth === '' ? '{{ __('Events.Filter.AllMonths') }}' : selectedMonth" class="truncate"></span>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-full bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-gray-100 dark:border-white/10 max-h-60 overflow-y-auto no-scrollbar"
                                style="display: none;"
                            >
                                <div class="p-1">
                                    <button 
                                        @click="selectedMonth = ''; open = false"
                                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                        :class="selectedMonth === '' ? 'text-primary font-bold bg-primary/5' : 'text-slate-600 dark:text-slate-300'"
                                    >
                                        {{ __('Events.Filter.AllMonths') }}
                                    </button>
                                    @foreach($months as $month)
                                        <button 
                                            @click="selectedMonth = '{{ $month }}'; open = false"
                                            class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                            :class="selectedMonth === '{{ $month }}' ? 'text-primary font-bold bg-primary/5' : 'text-slate-600 dark:text-slate-300'"
                                        >
                                            {{ $month }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Location Dropdown (Custom) -->
                        <div class="relative min-w-[220px]" x-data="{ open: false }">
                            <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10 pointer-events-none"></i>
                            
                            <button 
                                @click="open = !open"
                                @click.outside="open = false"
                                class="w-full pl-11 pr-10 py-3 text-left rounded-xl ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-black/20 focus:ring-2 focus:ring-primary text-sm font-medium flex items-center justify-between"
                            >
                                <span x-text="selectedLocation === '' ? '{{ __('Events.Filter.AllLocations') }}' : selectedLocation" class="truncate"></span>
                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-full bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-gray-100 dark:border-white/10 max-h-60 overflow-y-auto no-scrollbar"
                                style="display: none;"
                            >
                                <div class="p-1">
                                    <button 
                                        @click="selectedLocation = ''; open = false"
                                        class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                        :class="selectedLocation === '' ? 'text-primary font-bold bg-primary/5' : 'text-slate-600 dark:text-slate-300'"
                                    >
                                        {{ __('Events.Filter.AllLocations') }}
                                    </button>
                                    @foreach($locations as $loc)
                                        <button 
                                            @click="selectedLocation = '{{ $loc }}'; open = false"
                                            class="w-full text-left px-4 py-2.5 rounded-lg text-sm transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                                            :class="selectedLocation === '{{ $loc }}' ? 'text-primary font-bold bg-primary/5' : 'text-slate-600 dark:text-slate-300'"
                                        >
                                            {{ $loc }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Reset Button -->
                        <button 
                            @click="search = ''; selectedYear = ''; selectedMonth = ''; selectedLocation = ''"
                            class="px-6 py-3 rounded-xl bg-white dark:bg-white/10 text-slate-600 dark:text-slate-300 font-bold text-sm ring-1 ring-gray-200 dark:ring-white/10 hover:bg-gray-100 dark:hover:bg-white/20 transition-colors"
                            x-show="search || selectedYear || selectedMonth || selectedLocation"
                            x-transition
                        >
                            {{ __('Events.Filter.Reset') }}
                        </button>
                    </div>
                </div>

                <!-- Events Grid -->
                <div class="min-h-[400px]">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <template x-for="event in paginatedEvents" :key="event.id">
                            <a :href="`/calendar-of-events/${event.slug}`" 
                               class="group block h-full transition-all duration-300"
                               x-transition:enter="transition ease-out duration-300"
                               x-transition:enter-start="opacity-0 scale-95"
                               x-transition:enter-end="opacity-100 scale-100"
                            >
                                <div class="h-full flex flex-col bg-white dark:bg-surface-dark border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden hover:border-primary/50 dark:hover:border-primary/50 transition-colors duration-300 shadow-sm hover:shadow-lg">
                                    
                                    <!-- Image 16:9 -->
                                    <div class="aspect-video w-full relative overflow-hidden bg-gray-100 dark:bg-white/5">
                                        <template x-if="event.image_url">
                                            <img :src="event.image_url" :alt="event.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        </template>
                                        <template x-if="!event.image_url">
                                            <div class="flex items-center justify-center h-full text-gray-300">
                                                <i class="fa-regular fa-image text-3xl"></i>
                                            </div>
                                        </template>
                                        
                                        <!-- Simple Date Badge -->
                                        <div class="absolute top-3 right-3 bg-white dark:bg-black/80 px-3 py-1.5 rounded shadow-sm text-center border border-gray-100 dark:border-white/10">
                                            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide" x-text="event.start_date_month"></div>
                                            <div class="text-xl font-bold text-gray-800 dark:text-white leading-none" x-text="event.start_date_day"></div>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-5 flex flex-col flex-1">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/5 px-2 py-0.5 rounded">{{ __('Events.Badge') }}</span>
                                            <div class="text-xs text-gray-400 flex items-center gap-1">
                                                    <i class="fa-regular fa-clock"></i>
                                                    <span x-text="event.start_time"></span>
                                            </div>
                                        </div>

                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2 group-hover:text-primary transition-colors line-clamp-2" x-text="event.title">
                                        </h3>

                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 flex items-start gap-1.5">
                                            <i class="fa-solid fa-location-dot text-gray-400 mt-0.5"></i>
                                            <span class="line-clamp-1" x-text="event.location"></span>
                                        </div>

                                        <div class="mt-auto pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-sm">
                                            <span class="text-gray-400 group-hover:text-primary transition-colors">{{ __('Events.Card.ViewDetail') }}</span>
                                            <i class="fa-solid fa-arrow-right text-gray-300 group-hover:text-primary transition-colors -translate-x-1 group-hover:translate-x-0 duration-300"></i>
                                        </div>
                                    </div>

                                </div>
                            </a>
                        </template>
                    </div>
                    
                    <!-- No Results Message -->
                    <div x-show="filteredEvents.length === 0" class="text-center py-20" style="display: none;">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-white/5 mb-4 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ __('Events.Empty.Title') }}</h3>
                        <p class="text-gray-500">{{ __('Events.Empty.Subtitle') }}</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="totalPages > 1" class="mt-12 flex justify-center items-center gap-2 pb-12">
                    <button 
                        @click="currentPage > 1 ? (currentPage--, window.scrollTo({ top: 0, behavior: 'smooth' })) : null"
                        :disabled="currentPage === 1"
                        class="size-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 disabled:opacity-50 disabled:cursor-not-allowed hover:border-primary hover:text-primary transition-all shadow-sm"
                    >
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>

                    <template x-for="page in pages" :key="page">
                        <button 
                            @click="currentPage = page; window.scrollTo({ top: 0, behavior: 'smooth' })"
                            x-text="page"
                            class="size-10 rounded-xl border font-bold text-sm transition-all shadow-sm"
                            :class="currentPage === page ? 'bg-primary border-primary text-white' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-primary hover:text-primary'"
                        ></button>
                    </template>

                    <button 
                        @click="currentPage < totalPages ? (currentPage++, window.scrollTo({ top: 0, behavior: 'smooth' })) : null"
                        :disabled="currentPage === totalPages"
                        class="size-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 disabled:opacity-50 disabled:cursor-not-allowed hover:border-primary hover:text-primary transition-all shadow-sm"
                    >
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>


            </div>

        </div>
    </div>
</x-public-layout>
