<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen py-10 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            @php
                $allEvents = $groupedEvents->flatten();
                $locations = $allEvents->pluck('location')->unique()->values();
                $months = $groupedEvents->keys();
            @endphp

            <div x-data="{ 
                search: '', 
                selectedMonth: '', 
                selectedLocation: '',
                get filteredEvents() {
                    return true; // Logic handled in x-show for simplicity/SEO
                }
            }">

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

                        <!-- Month Dropdown (Custom) -->
                        <div class="relative min-w-[220px]" x-data="{ open: false }">
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
                            @click="search = ''; selectedMonth = ''; selectedLocation = ''"
                            class="px-6 py-3 rounded-xl bg-white dark:bg-white/10 text-slate-600 dark:text-slate-300 font-bold text-sm ring-1 ring-gray-200 dark:ring-white/10 hover:bg-gray-100 dark:hover:bg-white/20 transition-colors"
                            x-show="search || selectedMonth || selectedLocation"
                            x-transition
                        >
                            {{ __('Events.Filter.Reset') }}
                        </button>
                    </div>
                </div>

                <!-- Events Grid -->
                @if($allEvents->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 min-h-[400px]">
                        @foreach($allEvents as $event)
                        <a href="{{ route('events.public.show', $event) }}" 
                           class="group block h-full transition-all duration-300"
                           x-show="(selectedMonth === '' || '{{ $event->start_date->translatedFormat('F Y') }}' === selectedMonth) && 
                                   (selectedLocation === '' || '{{ $event->location }}' === selectedLocation) && 
                                   (search === '' || '{{ strtolower($event->title) }}'.toLowerCase().includes(search.toLowerCase()))"
                           x-transition:enter="transition ease-out duration-300"
                           x-transition:enter-start="opacity-0 scale-95"
                           x-transition:enter-end="opacity-100 scale-100"
                        >
                            <div class="h-full flex flex-col bg-white dark:bg-surface-dark border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden hover:border-primary/50 dark:hover:border-primary/50 transition-colors duration-300 shadow-sm hover:shadow-lg">
                                
                                <!-- Image 16:9 -->
                                <div class="aspect-video w-full relative overflow-hidden bg-gray-100 dark:bg-white/5">
                                    @if($event->image)
                                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex items-center justify-center h-full text-gray-300">
                                            <i class="fa-regular fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Simple Date Badge -->
                                    <div class="absolute top-3 right-3 bg-white dark:bg-black/80 px-3 py-1.5 rounded shadow-sm text-center border border-gray-100 dark:border-white/10">
                                        <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $event->start_date->format('M') }}</div>
                                        <div class="text-xl font-bold text-gray-800 dark:text-white leading-none">{{ $event->start_date->format('d') }}</div>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-5 flex flex-col flex-1">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/5 px-2 py-0.5 rounded">{{ __('Events.Badge') }}</span>
                                        <div class="text-xs text-gray-400 flex items-center gap-1">
                                                <i class="fa-regular fa-clock"></i>
                                                {{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '-' }}
                                        </div>
                                    </div>

                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight mb-2 group-hover:text-primary transition-colors line-clamp-2">
                                        {{ $event->title }}
                                    </h3>

                                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 flex items-start gap-1.5">
                                        <i class="fa-solid fa-location-dot text-gray-400 mt-0.5"></i>
                                        <span class="line-clamp-1">{{ $event->location }}</span>
                                    </div>

                                    <div class="mt-auto pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-sm">
                                        <span class="text-gray-400 group-hover:text-primary transition-colors">{{ __('Events.Card.ViewDetail') }}</span>
                                        <i class="fa-solid fa-arrow-right text-gray-300 group-hover:text-primary transition-colors -translate-x-1 group-hover:translate-x-0 duration-300"></i>
                                    </div>
                                </div>

                            </div>
                        </a>
                        @endforeach
                        
                        <!-- No Results Message (Pure Alpine) -->
                        <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-20 hidden" 
                             :class="{ '!block': !document.querySelectorAll('a[x-show]:not([style*=\'display: none\'])').length && (search || selectedMonth || selectedLocation) }">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-white/5 mb-4 text-gray-400">
                                <i class="fa-solid fa-magnifying-glass text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ __('Events.Empty.Title') }}</h3>
                            <p class="text-gray-500">{{ __('Events.Empty.Subtitle') }}</p>
                        </div>
                    </div>
                @else
                    <div class="py-20 text-center border-2 border-dashed border-gray-200 dark:border-white/5 rounded-xl">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-white/5 mb-4 text-gray-400">
                            <i class="fa-regular fa-calendar-xmark text-3xl"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('Events.NoEvents') }}</p>
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-public-layout>
