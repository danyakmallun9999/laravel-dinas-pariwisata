<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen -mt-20 pt-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Nav.Home') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Nav.Events') }}</span>
            </nav>
            
            @php
                $allEvents = $groupedEvents->flatten();
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
                        'image_url' => $event->image ? asset($event->image) : null,
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
                currentPage: 1,
                perPage: 9,
                events: {{ Js::from($allEventsJSON) }},
                get filteredEvents() {
                    return this.events.filter(event => {
                        const matchesSearch = this.search === '' || event.title.toLowerCase().includes(this.search.toLowerCase());
                        const matchesYear = this.selectedYear === '' || event.start_date_year === this.selectedYear;
                        const matchesMonth = this.selectedMonth === '' || event.start_date_month_name === this.selectedMonth;
                        return matchesSearch && matchesYear && matchesMonth;
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
            }" x-init="$watch('search', () => currentPage = 1); $watch('selectedYear', () => currentPage = 1); $watch('selectedMonth', () => currentPage = 1);">

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
                                class="w-full pl-11 pr-10 py-3 text-left rounded-xl ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-black/20 focus:ring-2 focus:ring-primary text-sm font-medium flex items-center justify-between gap-3"
                            >
                                <span x-text="selectedYear === '' ? '{{ __('Events.Filter.AllYears') }}    ' : selectedYear" class="truncate"></span>
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
                                        {{ __('Events.Filter.AllYears') }}
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
                                class="w-full pl-11 pr-10 py-3 text-left rounded-xl ring-1 ring-gray-200 dark:ring-white/10 bg-white dark:bg-black/20 focus:ring-2 focus:ring-primary text-sm font-medium flex items-center justify-between gap-3"
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



                        <!-- Reset Button -->
                        <button 
                            @click="search = ''; selectedYear = ''; selectedMonth = ''"
                            class="px-6 py-3 rounded-xl bg-white dark:bg-white/10 text-slate-600 dark:text-slate-300 font-bold text-sm ring-1 ring-gray-200 dark:ring-white/10 hover:bg-gray-100 dark:hover:bg-white/20 transition-colors"
                            x-show="search || selectedYear || selectedMonth"
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
                               class="group block h-full transition-all duration-300 hover:-translate-y-1"
                               x-transition:enter="transition ease-out duration-300"
                               x-transition:enter-start="opacity-0 scale-95"
                               x-transition:enter-end="opacity-100 scale-100"
                            >
                                <div class="h-full flex flex-col bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                                    
                                    <!-- Image -->
                                    <div class="relative w-full h-48 overflow-hidden bg-stone-100 dark:bg-slate-800">
                                        <template x-if="event.image_url">
                                            <img :src="event.image_url" :alt="event.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        </template>
                                        <template x-if="!event.image_url">
                                            <img src="{{ asset('images/agenda/logo-agenda.png') }}" :alt="event.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        </template>
                                        
                                        <!-- Glassmorphic Date Badge (Top-Left) -->
                                        <div class="absolute top-4 left-4 bg-white/90 dark:bg-black/70 backdrop-blur-sm px-3 py-2 rounded-xl text-center shadow-lg">
                                            <div class="text-xl font-bold text-stone-800 dark:text-white leading-none" x-text="event.start_date_day"></div>
                                            <div class="text-[10px] font-semibold tracking-widest text-stone-500 dark:text-stone-400 uppercase mt-0.5" x-text="event.start_date_month"></div>
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-5 flex flex-col flex-1 space-y-2">
                                        <h3 class="text-lg md:text-xl font-semibold text-stone-900 dark:text-white leading-snug line-clamp-2 group-hover:text-primary transition-colors duration-300" x-text="event.title">
                                        </h3>

                                        <div class="flex items-center gap-2 text-stone-600 dark:text-stone-400 text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-stone-400 dark:text-stone-500"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                            <span class="line-clamp-1" x-text="event.location || 'Jepara'"></span>
                                        </div>

                                        <p class="text-sm text-stone-500 dark:text-stone-400 line-clamp-2 pt-1" x-text="event.description ? event.description.replace(/<[^>]*>/g, '').substring(0, 100) + '...' : ''"></p>

                                        <div class="pt-3 mt-auto">
                                            <span class="text-sm font-medium text-amber-700 dark:text-amber-500 group-hover:text-amber-800 dark:group-hover:text-amber-400 transition-colors duration-300 inline-flex items-center gap-1">
                                                {{ __('Events.Card.ViewDetail') }} →
                                            </span>
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
