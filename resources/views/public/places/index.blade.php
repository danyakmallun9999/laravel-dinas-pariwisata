<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors" wire:navigate>{{ __('Nav.Home') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Nav.Destinations') }}</span>
            </nav>

            @php
                // Extract unique locations from kecamatan column
                $allPlaces = $places;
                $locations = $allPlaces->pluck('kecamatan')->filter()->unique()->values();
            @endphp

            <div x-data="{
                search: '',
                selectedCategory: '',
                selectedLocation: '',
                currentPage: 1,
                perPage: 9,
                places: {{ Js::from($places->map(function($place) {
                    $place->name = $place->translated_name;
                    $place->description = $place->translated_description;
                    // Ensure localized category name
                    if ($place->category) {
                        $place->category_name = $place->category->name;
                    }
                    return $place;
                })) }},
                get filteredPlaces() {
                    return this.places.filter(place => {
                        const matchesSearch = this.search === '' || place.name.toLowerCase().includes(this.search.toLowerCase());
                        const matchesCategory = this.selectedCategory === '' || (place.category_name === this.selectedCategory);
                        
                        // Location matching (Match exact kecamatan)
                        const matchesLocation = this.selectedLocation === '' || place.kecamatan === this.selectedLocation;

                        return matchesSearch && matchesCategory && matchesLocation;
                    });
                },
                get totalPages() {
                    return Math.ceil(this.filteredPlaces.length / this.perPage);
                },
                get paginatedPlaces() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredPlaces.slice(start, start + this.perPage);
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
            }" x-init="$watch('search', () => currentPage = 1); $watch('selectedCategory', () => currentPage = 1); $watch('selectedLocation', () => currentPage = 1)">
                
                <!-- Simple Header -->
                <div class="mb-10 border-b border-gray-100 dark:border-white/10 pb-8">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                                {{ __('Destinations.Title') }}
                            </h1>
                            <p class="text-slate-500 dark:text-slate-400 text-lg max-w-2xl">
                                {{ __('Destinations.Subtitle') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filter Bar -->
                <div class="bg-white dark:bg-slate-800/50 backdrop-blur-xl p-3 lg:p-4 rounded-3xl border border-slate-200 dark:border-white/5 flex flex-col lg:flex-row gap-3 lg:gap-4 mb-16 relative z-20">
                    
                    <!-- Search -->
                    <div class="flex-1 relative group w-full">
                        <i class="fa-solid fa-search absolute left-4 lg:left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-sm lg:text-base"></i>
                        <input 
                            x-model="search"
                            type="text" 
                            placeholder="{{ __('Destinations.SearchPlaceholder') }}" 
                            class="w-full pl-10 lg:pl-12 pr-4 py-2.5 lg:py-3.5 rounded-2xl border-none bg-slate-50 dark:bg-black/20 focus:bg-white dark:focus:bg-black/40 ring-1 ring-slate-200 dark:ring-white/10 focus:ring-2 focus:ring-primary text-slate-700 dark:text-white font-medium transition-all placeholder:text-slate-400 text-sm lg:text-base"
                        >
                    </div>

                    <!-- Filter Group: Grid on Mobile, Flex/Contents on Desktop -->
                    <div class="grid grid-cols-2 gap-3 lg:contents">
                        <!-- Category Dropdown (Custom) -->
                        <div class="relative min-w-[0] lg:min-w-[220px]" x-data="{ open: false }">
                            <i class="fa-solid fa-layer-group absolute left-3 lg:left-5 top-1/2 -translate-y-1/2 text-slate-400 z-10 pointer-events-none text-sm lg:text-base"></i>
                            
                            <button 
                                @click="open = !open"
                                @click.outside="open = false"
                                class="w-full pl-9 lg:pl-12 pr-7 lg:pr-10 py-2.5 lg:py-3.5 text-left rounded-2xl ring-1 ring-slate-200 dark:ring-white/10 bg-slate-50 dark:bg-black/20 hover:bg-white dark:hover:bg-black/40 focus:ring-2 focus:ring-primary text-sm font-medium flex items-center justify-between transition-all text-slate-700 dark:text-white"
                            >
                                <span x-text="selectedCategory === '' ? '{{ __('Destinations.Filter.AllCategories') }}' : selectedCategory" class="truncate block w-full"></span>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 absolute right-3 lg:right-4" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 translate-y-2"
                                x-transition:enter-end="transform opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100 translate-y-0"
                                x-transition:leave-end="transform opacity-0 translate-y-2"
                                class="absolute z-50 mt-2 w-full min-w-[180px] bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 max-h-80 overflow-y-auto no-scrollbar p-1.5"
                                style="display: none;"
                            >
                                <button 
                                    @click="selectedCategory = ''; open = false"
                                    class="w-full text-left px-4 py-2.5 rounded-xl text-sm transition-all flex items-center justify-between group"
                                    :class="selectedCategory === '' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5'"
                                >
                                    <span>{{ __('Destinations.Filter.AllCategories') }}</span>
                                    <i class="fa-solid fa-check text-primary" x-show="selectedCategory === ''"></i>
                                </button>
                                @foreach($categories as $category)
                                    <button 
                                        @click="selectedCategory = '{{ $category->name }}'; open = false"
                                        class="w-full text-left px-4 py-2.5 rounded-xl text-sm transition-all flex items-center justify-between group"
                                        :class="selectedCategory === '{{ $category->name }}' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5'"
                                    >
                                        <span>{{ $category->name }}</span>
                                        <i class="fa-solid fa-check text-primary" x-show="selectedCategory === '{{ $category->name }}'"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Location Dropdown (Custom) -->
                        <div class="relative min-w-[0] lg:min-w-[220px]" x-data="{ open: false }">
                            <i class="fa-solid fa-location-dot absolute left-3 lg:left-5 top-1/2 -translate-y-1/2 text-slate-400 z-10 pointer-events-none text-sm lg:text-base"></i>
                            
                            <button 
                                @click="open = !open"
                                @click.outside="open = false"
                                class="w-full pl-9 lg:pl-12 pr-7 lg:pr-10 py-2.5 lg:py-3.5 text-left rounded-2xl ring-1 ring-slate-200 dark:ring-white/10 bg-slate-50 dark:bg-black/20 hover:bg-white dark:hover:bg-black/40 focus:ring-2 focus:ring-primary text-sm font-medium flex items-center justify-between transition-all text-slate-700 dark:text-white"
                            >
                                <span x-text="selectedLocation === '' ? '{{ __('Destinations.Filter.AllLocations') }}' : selectedLocation" class="truncate block w-full"></span>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 absolute right-3 lg:right-4" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div 
                                x-show="open" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 translate-y-2"
                                x-transition:enter-end="transform opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100 translate-y-0"
                                x-transition:leave-end="transform opacity-0 translate-y-2"
                                class="absolute z-50 mt-2 right-0 origin-top-right w-full min-w-[180px] bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 max-h-80 overflow-y-auto no-scrollbar p-1.5"
                                style="display: none;"
                            >
                                <button 
                                    @click="selectedLocation = ''; open = false"
                                    class="w-full text-left px-4 py-2.5 rounded-xl text-sm transition-all flex items-center justify-between group"
                                    :class="selectedLocation === '' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5'"
                                >
                                    <span>{{ __('Destinations.Filter.AllLocations') }}</span>
                                    <i class="fa-solid fa-check text-primary" x-show="selectedLocation === ''"></i>
                                </button>
                                @foreach($locations as $loc)
                                    <button 
                                        @click="selectedLocation = '{{ $loc }}'; open = false"
                                        class="w-full text-left px-4 py-2.5 rounded-xl text-sm transition-all flex items-center justify-between group"
                                        :class="selectedLocation === '{{ $loc }}' ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5'"
                                    >
                                        <span>{{ $loc }}</span>
                                        <i class="fa-solid fa-check text-primary" x-show="selectedLocation === '{{ $loc }}'"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <button 
                        @click="search = ''; selectedCategory = ''; selectedLocation = ''"
                        class="px-6 py-2.5 lg:py-3.5 rounded-2xl bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 font-bold text-sm ring-1 ring-slate-200 dark:ring-white/10 hover:bg-slate-50 dark:hover:bg-white/10 transition-all w-full lg:w-auto"
                        x-show="search || selectedCategory || selectedLocation"
                        x-transition
                    >
                        {{ __('Destinations.Filter.Reset') }}
                    </button>
                </div>

                <!-- Modern Grid Layout -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 min-h-[50vh]">
                    <template x-for="place in paginatedPlaces" :key="place.id">
                        <a :href="`/destinasi/${place.slug}`" class="group relative bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-slate-100 dark:border-slate-700 hover:-translate-y-2 flex flex-col h-full"
                           wire:navigate
                           x-transition:enter="transition ease-out duration-300"
                           x-transition:enter-start="opacity-0 scale-95"
                           x-transition:enter-end="opacity-100 scale-100">
                            
                            <!-- Image Section -->
                            <div class="relative h-64 overflow-hidden">
                                <template x-if="place.image_path">
                                    <img :src="place.image_path" :alt="place.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </template>
                                <template x-if="!place.image_path">
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-900 text-slate-300">
                                        <span class="material-symbols-outlined text-5xl">image</span>
                                    </div>
                                </template>
                                
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 rounded-full bg-white/90 dark:bg-slate-900/90 backdrop-blur text-xs font-bold text-slate-800 dark:text-white shadow-sm border border-white/20" 
                                          x-text="place.category_name ? place.category_name : '{{ __('Tourism.Category.Default') }}'">
                                    </span>
                                </div>

                                <!-- Rating Badge -->
                                <div class="absolute bottom-4 right-4 flex items-center gap-1 bg-slate-900/80 backdrop-blur px-2.5 py-1 rounded-lg border border-white/10 text-white font-bold text-xs shadow-lg">
                                    <span class="material-symbols-outlined text-sm text-yellow-400">star</span>
                                    <span x-text="place.rating"></span>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex-1">
                                    <h3 class="text-xl font-display font-bold text-slate-800 dark:text-white mb-2 line-clamp-2 leading-snug group-hover:text-primary transition-colors" x-text="place.name"></h3>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-4 leading-relaxed font-light" x-text="place.description">
                                    </p>
                                </div>
                                
                                <!-- Footer Info -->
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm">
                                    <!-- Address (Short) -->
                                    <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 max-w-[60%]">
                                        <span class="material-symbols-outlined text-lg opacity-70">location_on</span>
                                        <span class="truncate" x-text="place.address ? place.address.split(',')[0] : 'Jepara'"></span>
                                    </div>
                                    
                                    <!-- Price -->
                                    <div class="font-bold text-primary flex items-center gap-1">
                                        <span class="material-symbols-outlined text-lg">payments</span>
                                        <span x-text="place.ticket_price === 'Gratis' || place.ticket_price === 'Free' ? '{{ __('Places.Ticket.Free') }}' : (place.ticket_price && place.ticket_price.length < 15 ? place.ticket_price : '{{ __('Places.Ticket.Entry') }}')"></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                    
                    <!-- Empty State -->
                    <div x-show="filteredPlaces.length === 0" class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-24" style="display: none;">
                        <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <span class="material-symbols-outlined text-4xl">travel_explore</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">{{ __('Destinations.Empty.Title') }}</h3>
                        <p class="text-slate-500">{{ __('Destinations.Empty.Subtitle') }}</p>
                        <button @click="search = ''; selectedCategory = ''; selectedLocation = ''" class="mt-4 text-primary font-bold hover:underline">{{ __('Destinations.Empty.Button') }}</button>
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
