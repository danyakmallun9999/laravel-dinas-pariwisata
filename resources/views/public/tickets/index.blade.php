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
                $ticketsJSON = $tickets->map(function($ticket) {
                    $imagePath = $ticket->place->image_path ?? '';
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
                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->name,
                        'description' => $ticket->description,
                        'price' => $ticket->price,
                        'valid_days' => $ticket->valid_days,
                        'quota' => $ticket->quota,
                        'place_name' => $ticket->place->name,
                        'place_slug' => $ticket->place->slug,
                        'image_url' => $imageUrl,
                    ];
                });
            @endphp

            <div x-data="{
                search: '',
                currentPage: 1,
                perPage: 9,
                tickets: {{ Js::from($ticketsJSON) }},
                get filteredTickets() {
                    return this.tickets.filter(ticket => {
                        const matchesSearch = this.search === '' || 
                            ticket.name.toLowerCase().includes(this.search.toLowerCase()) ||
                            ticket.place_name.toLowerCase().includes(this.search.toLowerCase());
                        return matchesSearch;
                    });
                },
                get totalPages() {
                    return Math.ceil(this.filteredTickets.length / this.perPage);
                },
                get paginatedTickets() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredTickets.slice(start, start + this.perPage);
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
                },
                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                }
            }" x-init="$watch('search', () => currentPage = 1)">

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
                            placeholder="{{ __('Tickets.SearchPlaceholder') }}" 
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

                <!-- Tickets Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 min-h-[50vh]">
                    <template x-for="ticket in paginatedTickets" :key="ticket.id">
                        <a :href="`/e-tiket/${ticket.id}`" 
                           class="group relative bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-slate-100 dark:border-slate-700 hover:-translate-y-2 flex flex-col h-full"
                           x-transition:enter="transition ease-out duration-300"
                           x-transition:enter-start="opacity-0 scale-95"
                           x-transition:enter-end="opacity-100 scale-100">
                            
                            <!-- Image Section -->
                            <div class="relative h-56 overflow-hidden">
                                <template x-if="ticket.image_url">
                                    <img :src="ticket.image_url" :alt="ticket.place_name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                </template>
                                <template x-if="!ticket.image_url">
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/20 to-indigo-500/20 text-primary">
                                        <i class="fa-solid fa-ticket text-5xl"></i>
                                    </div>
                                </template>
                                
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent to-transparent"></div>
                                
                                <!-- Price Badge -->
                                <div class="absolute bottom-4 left-4 flex items-center gap-2">
                                    <span class="px-4 py-2 rounded-xl bg-primary text-white font-bold text-lg shadow-lg">
                                        Rp <span x-text="formatPrice(ticket.price)"></span>
                                    </span>
                                </div>

                                <!-- Valid Days Badge -->
                                <div class="absolute top-4 right-4 px-3 py-1.5 rounded-lg bg-white/90 dark:bg-slate-900/90 backdrop-blur text-xs font-bold text-slate-700 dark:text-white shadow-sm border border-white/20">
                                    <i class="fa-solid fa-calendar-check mr-1 text-primary"></i>
                                    <span x-text="ticket.valid_days + ' {{ __('Tickets.Card.Day') }}'"></span>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex-1">
                                    <!-- Place Name -->
                                    <div class="text-xs font-bold uppercase tracking-wider text-primary mb-2" x-text="ticket.place_name"></div>
                                    
                                    <!-- Ticket Name -->
                                    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-3 line-clamp-2 leading-snug group-hover:text-primary transition-colors" x-text="ticket.name"></h3>
                                    
                                    <!-- Description -->
                                    <p class="text-slate-500 dark:text-slate-400 text-sm line-clamp-2 mb-4 leading-relaxed" x-text="ticket.description || 'Nikmati pengalaman wisata terbaik di Jepara'"></p>
                                </div>
                                
                                <!-- Footer -->
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm">
                                    <template x-if="ticket.quota">
                                        <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                            <i class="fa-solid fa-users"></i>
                                            <span x-text="'{{ __('Tickets.Card.Quota') }}: ' + ticket.quota + '/{{ __('Tickets.Card.Day') }}'"></span>
                                        </div>
                                    </template>
                                    <template x-if="!ticket.quota">
                                        <div class="flex items-center gap-1.5 text-green-600">
                                            <i class="fa-solid fa-infinity"></i>
                                            <span>{{ __('Tickets.Card.Unlimited') }}</span>
                                        </div>
                                    </template>
                                    
                                    <div class="flex items-center gap-2 text-primary font-semibold group-hover:gap-3 transition-all">
                                        <span>{{ __('Tickets.Card.Book') }}</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                    
                    <!-- Empty State -->
                    <div x-show="filteredTickets.length === 0" class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-24" style="display: none;">
                        <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                            <i class="fa-solid fa-ticket text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">{{ __('Tickets.NoTicketsFound') }}</h3>
                        <p class="text-slate-500">{{ __('Tickets.NoTicketsSubtitle') }}</p>
                        <button @click="search = ''" class="mt-4 text-primary font-bold hover:underline">{{ __('Tickets.ResetSearchButton') }}</button>
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="totalPages > 1" class="mt-12 flex justify-center items-center gap-2 pb-12">
                    <button 
                        @click="currentPage > 1 ? (currentPage--, window.scrollTo({ top: 0, behavior: 'smooth' })) : null"
                        :disabled="currentPage === 1"
                        class="size-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 disabled:opacity-50 disabled:cursor-not-allowed hover:border-primary hover:text-primary transition-all shadow-sm"
                    >
                        <i class="fa-solid fa-chevron-left"></i>
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
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
