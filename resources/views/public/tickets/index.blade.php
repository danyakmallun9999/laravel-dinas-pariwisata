<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        'slug' => $place->slug,
                        'kecamatan' => $place->kecamatan ?? '',
                        'image_url' => $imageUrl,
                        'tickets' => $tickets,
                        'ticket_count' => $tickets->count(),
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
                    const q = this.search.toLowerCase();
                    return this.places.filter(place => {
                        return place.name.toLowerCase().includes(q) || 
                               (place.kecamatan && place.kecamatan.toLowerCase().includes(q));
                    });
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                }
            }">

                <!-- Header -->
                <div class="mb-5 sm:mb-8">
                    <div class="flex items-start justify-between gap-3 mb-1">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 dark:text-white leading-tight">
                            {{ __('Tickets.Title') }} <span class="text-primary">{{ __('Tickets.TitleHighlight') }}</span>
                        </h1>
                        <a href="{{ route('tickets.my') }}" class="inline-flex items-center gap-1.5 px-3 py-2 sm:px-4 sm:py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-slate-600 dark:text-slate-300 font-semibold text-sm hover:border-primary hover:text-primary transition-all whitespace-nowrap shrink-0">
                            <i class="fa-solid fa-ticket text-xs"></i>
                            <span class="hidden sm:inline">{{ __('Tickets.MyTicketsButton') }}</span>
                            <span class="sm:hidden">Tiket Saya</span>
                        </a>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-2xl">
                        {{ __('Tickets.Subtitle') }}
                    </p>
                </div>

                <!-- Search Bar -->
                <div class="relative mb-5 sm:mb-6">
                    <div class="flex items-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden focus-within:border-primary transition-colors">
                        <i class="fa-solid fa-search ml-4 text-slate-400 text-sm"></i>
                        <input 
                            x-model="search"
                            type="text" 
                            placeholder="Cari destinasi wisata..."
                            class="flex-1 px-3 py-3 border-none bg-transparent text-sm text-slate-700 dark:text-white font-medium placeholder:text-slate-400 focus:ring-0 focus:outline-none"
                        >
                        <button 
                            @click="search = ''"
                            class="mr-3 p-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-400 text-xs hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors"
                            x-show="search"
                            x-transition
                        >
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <div class="mt-2 text-xs text-slate-400 flex items-center gap-1.5" x-show="search" x-transition>
                        <span>Menampilkan <strong class="text-slate-600 dark:text-white" x-text="filteredPlaces.length"></strong> destinasi</span>
                    </div>
                </div>

                <!-- Places List -->
                <div class="space-y-3 sm:space-y-4 min-h-[40vh]">
                    <template x-for="(place, index) in filteredPlaces" :key="place.id">
                        <div 
                            x-data="{ expanded: false }" 
                            class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border transition-colors duration-200"
                            :class="expanded ? 'border-primary/30 dark:border-primary/30' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600'"
                        >
                            <!-- Destination Row -->
                            <div class="cursor-pointer select-none" @click="expanded = !expanded">
                                <!-- Desktop: horizontal layout -->
                                <div class="hidden sm:flex sm:flex-row">
                                    <!-- Image -->
                                    <div class="sm:w-40 md:w-48 relative overflow-hidden shrink-0">
                                        <template x-if="place.image_url">
                                            <img :src="place.image_url" :alt="place.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!place.image_url">
                                            <div class="w-full h-full min-h-[100px] flex items-center justify-center bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500">
                                                <i class="fa-solid fa-mountain-sun text-3xl"></i>
                                            </div>
                                        </template>
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 px-5 py-4 flex items-center min-w-0">
                                        <div class="flex items-center gap-4 w-full">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-lg font-bold text-slate-800 dark:text-white truncate leading-snug transition-colors" :class="expanded && 'text-primary dark:text-primary'" x-text="place.name"></h3>
                                                <div class="flex items-center gap-x-3 mt-1 text-xs text-slate-400">
                                                    <template x-if="place.kecamatan">
                                                        <span class="inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-location-dot text-[10px] text-primary/50"></i>
                                                            <span x-text="place.kecamatan"></span>
                                                        </span>
                                                    </template>
                                                    <span class="inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-ticket text-[10px] text-primary/50"></i>
                                                        <span x-text="place.ticket_count + ' jenis tiket'"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0">
                                                <div class="text-right">
                                                    <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wide leading-none mb-0.5">Mulai dari</div>
                                                    <div class="text-xl font-extrabold text-primary leading-none">Rp <span x-text="place.formatted_min_price"></span></div>
                                                </div>
                                                <div 
                                                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-200 shrink-0"
                                                    :class="expanded ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-400'"
                                                >
                                                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="expanded && 'rotate-180'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile: compact card layout -->
                                <div class="sm:hidden">
                                    <!-- Image - shorter -->
                                    <div class="w-full h-40 relative overflow-hidden">
                                        <template x-if="place.image_url">
                                            <img :src="place.image_url" :alt="place.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!place.image_url">
                                            <div class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500">
                                                <i class="fa-solid fa-mountain-sun text-2xl"></i>
                                            </div>
                                        </template>
                                        <!-- Ticket count pill on image -->
                                        <div class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-full bg-black/50 backdrop-blur-sm text-xs font-semibold text-white">
                                            <i class="fa-solid fa-ticket mr-1 text-[11px]"></i>
                                            <span x-text="place.ticket_count"></span> tiket
                                        </div>
                                    </div>
                                    <!-- Info row below image -->
                                    <div class="px-4 py-3.5 flex items-center gap-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-bold text-slate-800 dark:text-white truncate leading-snug transition-colors" :class="expanded && 'text-primary'" x-text="place.name"></h3>
                                            <template x-if="place.kecamatan">
                                                <div class="flex items-center gap-1 mt-0.5 text-xs text-slate-400">
                                                    <i class="fa-solid fa-location-dot text-[10px] text-primary/50"></i>
                                                    <span x-text="place.kecamatan"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="text-right">
                                                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wide leading-none mb-0.5">Mulai dari</div>
                                                <div class="text-base font-extrabold text-primary leading-tight">Rp <span x-text="place.formatted_min_price"></span></div>
                                            </div>
                                            <div 
                                                class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-200 shrink-0"
                                                :class="expanded ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-400'"
                                            >
                                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="expanded && 'rotate-180'"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expandable Ticket Options -->
                            <div x-show="expanded" x-collapse.duration.250ms>
                                <div class="border-t border-slate-100 dark:border-slate-700/50 bg-slate-50/60 dark:bg-slate-900/30 px-4 sm:px-5 py-4">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-0.5 h-3.5 rounded-full bg-primary"></div>
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pilihan Tiket</span>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="ticket in place.tickets" :key="ticket.id">
                                            <!-- Ticket Card — resembles a physical ticket -->
                                            <div class="group/ticket relative flex overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-primary/40 transition-colors bg-white dark:bg-slate-800">
                                                
                                                <!-- Left: Ticket Icon Strip -->
                                                <div class="hidden sm:flex w-14 shrink-0 items-center justify-center bg-primary/5 dark:bg-primary/10 border-r border-dashed border-slate-200 dark:border-slate-600 relative">
                                                    <i class="fa-solid fa-ticket text-primary text-lg -rotate-45"></i>
                                                    <!-- Top notch -->
                                                    <div class="absolute -top-2 -right-2 w-4 h-4 rounded-full bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-700"></div>
                                                    <!-- Bottom notch -->
                                                    <div class="absolute -bottom-2 -right-2 w-4 h-4 rounded-full bg-slate-50 dark:bg-slate-900/30 border border-slate-200 dark:border-slate-700"></div>
                                                </div>

                                                <!-- Middle: Ticket Info -->
                                                <div class="flex-1 p-4 min-w-0">
                                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
                                                        <div class="flex-1 min-w-0">
                                                            <!-- Mobile ticket icon -->
                                                            <div class="flex items-start gap-3">
                                                                <div class="sm:hidden w-10 h-10 rounded-xl bg-primary/5 dark:bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                                                    <i class="fa-solid fa-ticket text-primary text-sm -rotate-45"></i>
                                                                </div>
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                                                        <h5 class="font-semibold text-slate-800 dark:text-white text-sm group-hover/ticket:text-primary transition-colors" x-text="ticket.name"></h5>
                                                                        <template x-if="ticket.type">
                                                                            <span 
                                                                                class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider"
                                                                                :class="{
                                                                                    'bg-blue-50 text-blue-500 dark:bg-blue-900/30 dark:text-blue-400': ticket.type === 'dewasa' || ticket.type === 'Dewasa',
                                                                                    'bg-amber-50 text-amber-500 dark:bg-amber-900/30 dark:text-amber-400': ticket.type === 'anak' || ticket.type === 'Anak',
                                                                                    'bg-purple-50 text-purple-500 dark:bg-purple-900/30 dark:text-purple-400': ticket.type === 'wisatawan_asing' || ticket.type === 'Wisatawan Asing',
                                                                                    'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400': !['dewasa','Dewasa','anak','Anak','wisatawan_asing','Wisatawan Asing'].includes(ticket.type)
                                                                                }"
                                                                                x-text="ticket.type"
                                                                            ></span>
                                                                        </template>
                                                                    </div>
                                                                    <div class="flex flex-wrap items-center gap-x-3 text-xs text-slate-400">
                                                                        <span class="inline-flex items-center gap-1">
                                                                            <i class="fa-regular fa-clock text-xs"></i>
                                                                            <span x-text="ticket.valid_days + ' hari'"></span>
                                                                        </span>
                                                                        <template x-if="ticket.quota">
                                                                            <span class="inline-flex items-center gap-1 text-emerald-500">
                                                                                <i class="fa-solid fa-users text-xs"></i>
                                                                                <span x-text="ticket.quota + '/hari'"></span>
                                                                            </span>
                                                                        </template>
                                                                        <template x-if="!ticket.quota">
                                                                            <span class="inline-flex items-center gap-1 text-emerald-500">
                                                                                <i class="fa-solid fa-infinity text-xs"></i>
                                                                                Tanpa batas
                                                                            </span>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Price + CTA -->
                                                        <div class="flex items-center justify-between sm:justify-end gap-4 pt-3 sm:pt-0 border-t sm:border-0 border-dashed border-slate-200 dark:border-slate-700/50 shrink-0 ml-12 sm:ml-0">
                                                            <div class="sm:text-right">
                                                                <div class="text-base font-bold text-primary leading-tight">Rp <span x-text="ticket.formatted_price"></span></div>
                                                                <div class="text-xs text-slate-400">/orang</div>
                                                            </div>
                                                            <a 
                                                                :href="`/e-tiket/${ticket.id}`" 
                                                                @click.stop
                                                                class="px-5 py-2 rounded-2xl bg-primary text-white font-semibold text-sm hover:bg-primary-dark transition-colors whitespace-nowrap"
                                                            >
                                                                Beli Tiket
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="filteredPlaces.length === 0" class="text-center py-20" x-cloak>
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center mx-auto mb-3 text-slate-300">
                            <i class="fa-solid fa-map-location-dot text-2xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-700 dark:text-white mb-1">{{ __('Tickets.NoTicketsFound') }}</h3>
                        <p class="text-slate-400 text-sm mb-3">{{ __('Tickets.NoTicketsSubtitle') }}</p>
                        <button @click="search = ''" class="text-primary font-semibold text-sm hover:underline">{{ __('Tickets.ResetSearchButton') }}</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-public-layout>
