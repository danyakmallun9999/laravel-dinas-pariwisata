<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Tickets.Breadcrumb.Index') }}</span>
            </nav>

            @php
                $placesJSON = $places->map(function($place) {
                    $imagePath = $place->image_path ?? '';
                    $imageUrl = '';
                    if ($imagePath) {
                        if (str_starts_with($imagePath, 'http')) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, 'images/')) {
                            $imageUrl = asset($imagePath);
                        } else {
                            $imageUrl = asset('storage/' . $imagePath);
                        }
                    }
                    
                    // Categorize tickets
                    $tickets = $place->tickets->filter(function($ticket) {
                         return $ticket->is_active;
                    })->map(function($ticket) {
                         return [
                             'id' => $ticket->id,
                             'name' => $ticket->name,
                             'type' => $ticket->type,
                             'description' => $ticket->description,
                             'price' => $ticket->price,
                             'quota' => $ticket->quota,
                             'valid_days' => $ticket->valid_days,
                             'formatted_price' => number_format($ticket->price, 0, ',', '.'),
                         ];
                    })->values();

                    $minPrice = $tickets->min('price');

                    return [
                        'id' => $place->id,
                        'name' => $place->name,
                        'slug' => $place->slug, // Ensure slug is available
                        'description' => $place->description, // Or truncated description
                        'image_url' => $imageUrl,
                        'tickets' => $tickets,
                        'min_price' => $minPrice,
                        'formatted_min_price' => number_format($minPrice, 0, ',', '.'),
                    ];
                });
            @endphp

            <div x-data="{
                search: '',
                places: {{ Js::from($placesJSON) }},
                get filteredPlaces() {
                    if (this.search === '') return this.places;
                    return this.places.filter(place => {
                        return place.name.toLowerCase().includes(this.search.toLowerCase());
                    });
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                }
            }">

                <!-- Header -->
                <div class="mb-10 border-b border-gray-100 dark:border-white/10 pb-8">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                                {{ __('Tickets.Title') }} <span class="text-primary">{{ __('Tickets.TitleHighlight') }}</span>
                            </h1>
                            <p class="text-slate-500 dark:text-slate-400 text-lg max-w-2xl">
                                {{ __('Tickets.Subtitle') }}
                            </p>
                        </div>
                        <a href="{{ route('tickets.my') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-700 dark:text-slate-300 font-semibold text-sm hover:border-primary hover:text-primary transition-all shadow-sm">
                            <i class="fa-solid fa-ticket"></i>
                            <span>{{ __('Tickets.MyTicketsButton') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="bg-white dark:bg-slate-800/50 backdrop-blur-xl p-4 rounded-3xl border border-slate-200 dark:border-white/5 flex flex-col lg:flex-row gap-4 mb-12 relative z-20">
                    <!-- Search -->
                    <div class="flex-1 relative group">
                        <i class="fa-solid fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors"></i>
                        <input 
                            x-model="search"
                            type="text" 
                            placeholder="Cari destinasi wisata..."
                            class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-none bg-slate-50 dark:bg-black/20 focus:bg-white dark:focus:bg-black/40 ring-1 ring-slate-200 dark:ring-white/10 focus:ring-2 focus:ring-primary text-slate-700 dark:text-white font-medium transition-all placeholder:text-slate-400"
                        >
                    </div>

                    <!-- Reset Button -->
                    <button 
                        @click="search = ''"
                        class="px-6 py-3.5 rounded-2xl bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 font-bold text-sm ring-1 ring-slate-200 dark:ring-white/10 hover:bg-slate-50 dark:hover:bg-white/10 transition-all"
                        x-show="search"
                        x-transition
                    >
                        <i class="fa-solid fa-times mr-2"></i>{{ __('Tickets.ResetButton') }}
                    </button>
                </div>

                <!-- Places Grid -->
                <div class="grid grid-cols-1 gap-8 min-h-[50vh]">
                    <template x-for="place in filteredPlaces" :key="place.id">
                        <div x-data="{ expanded: false }" class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-100 dark:border-slate-700">
                            
                            <!-- Main Destination Card -->
                            <div class="flex flex-col md:flex-row">
                                <!-- Image -->
                                <div class="w-full md:w-1/3 h-56 md:h-auto relative overflow-hidden group">
                                     <template x-if="place.image_url">
                                        <img :src="place.image_url" :alt="place.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    </template>
                                    <template x-if="!place.image_url">
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-700 text-slate-400">
                                            <i class="fa-solid fa-image text-4xl"></i>
                                        </div>
                                    </template>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-6 md:p-8 flex-1 flex flex-col justify-between relative">
                                    <div>
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors" x-text="place.name"></h3>
                                            <div class="text-left sm:text-right shrink-0 bg-slate-50 dark:bg-slate-700/50 px-4 py-2 rounded-xl border border-slate-100 dark:border-slate-600">
                                                <div class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-0.5 uppercase tracking-wide">Mulai dari</div>
                                                <div class="text-lg font-bold text-primary">Rp <span x-text="place.formatted_min_price"></span></div>
                                            </div>
                                        </div>
                                        <p class="text-slate-600 dark:text-slate-300 mb-6 line-clamp-3 leading-relaxed" x-text="place.description"></p>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-auto pt-6 border-t border-slate-100 dark:border-slate-700">
                                        <div class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                            <i class="fa-solid fa-ticket text-primary"></i>
                                            <span x-text="place.tickets.length + ' Jenis Tiket Tersedia'"></span>
                                        </div>
                                        <button 
                                            @click="expanded = !expanded"
                                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full font-bold text-sm transition-all duration-300"
                                            :class="expanded ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300' : 'bg-primary text-white hover:bg-primary-dark shadow-lg shadow-primary/20 hover:shadow-primary/40'"
                                        >
                                            <span x-text="expanded ? 'Tutup' : 'Lihat Tiket'"></span>
                                            <i class="fa-solid transition-transform duration-300" :class="expanded ? 'fa-chevron-up rotate-0' : 'fa-chevron-down'"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Expandable Ticket List -->
                            <div x-show="expanded" x-collapse class="border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 p-6 md:p-8">
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6">Pilihan Tiket untuk <span x-text="place.name"></span></h4>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <template x-for="ticket in place.tickets" :key="ticket.id">
                                        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-6 hover:border-primary/50 transition-colors group/ticket">
                                            <div class="flex-1">
                                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                                    <h5 class="font-bold text-slate-800 dark:text-white text-lg group-hover/ticket:text-primary transition-colors" x-text="ticket.name"></h5>
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider border border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400" x-text="ticket.type"></span>
                                                </div>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-3" x-text="ticket.description || 'Tiket masuk reguler'"></p>
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-medium text-slate-600 dark:text-slate-300">
                                                        <i class="fa-regular fa-clock text-slate-400"></i>
                                                        <span x-text="ticket.valid_days + ' Hari'"></span>
                                                    </span>
                                                    <template x-if="ticket.quota">
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 dark:bg-green-900/20 text-xs font-medium text-green-700 dark:text-green-400">
                                                            <i class="fa-solid fa-users text-green-500"></i>
                                                            <span x-text="'Kuota: ' + ticket.quota"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-row sm:flex-col justify-between items-center sm:items-end gap-4 border-t sm:border-t-0 border-slate-100 dark:border-slate-700 pt-4 sm:pt-0 mt-2 sm:mt-0">
                                                <div class="text-primary font-bold text-xl">Rp <span x-text="ticket.formatted_price"></span></div>
                                                <a :href="`/e-tiket/${ticket.id}`" class="px-6 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm text-center hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shadow-lg shadow-slate-900/20">
                                                    Pilih
                                                </a>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="filteredPlaces.length === 0" class="text-center py-24" style="display: none;">
                        <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <i class="fa-solid fa-map-location-dot text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">{{ __('Tickets.NoTicketsFound') }}</h3>
                        <p class="text-slate-500">{{ __('Tickets.NoTicketsSubtitle') }}</p>
                        <button @click="search = ''" class="mt-4 text-primary font-bold hover:underline">{{ __('Tickets.ResetSearchButton') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-public-layout>
