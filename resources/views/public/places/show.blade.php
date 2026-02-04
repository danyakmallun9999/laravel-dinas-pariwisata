<x-public-layout>
    <div class="bg-white dark:bg-slate-950 min-h-screen">
        
        <!-- Hero Section - Minimalist & Clean -->
        <div class="relative h-[60vh] md:h-[75vh] w-full overflow-hidden bg-slate-100 dark:bg-slate-900">
            @if($place->image_path)
                <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]">
            @else
                <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-800 dark:to-slate-900 flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-slate-400 dark:text-slate-600">image</span>
                </div>
            @endif
            
            <!-- Overlay - Subtle Dark Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent"></div>

            <!-- Hero Content -->
            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center z-10">
                <div class="max-w-4xl mx-auto space-y-4 md:space-y-6">
                    
                    <!-- Category & Rating Badges -->
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <span class="px-4 py-1.5 rounded-lg bg-white text-slate-900 text-[10px] md:text-xs font-bold uppercase tracking-[0.15em] border border-slate-200">
                            {{ $place->category->name ?? __('Tourism.Category.Default') }}
                        </span>
                        @if($place->rating)
                        <div class="flex items-center gap-1.5 bg-slate-950 px-3 py-1.5 rounded-lg border border-slate-800 text-yellow-400 text-[10px] md:text-xs font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined text-xs">star</span>
                            <span>{{ $place->rating }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Title -->
                    <h1 class="font-display text-4xl sm:text-5xl md:text-7xl font-black text-white leading-tight tracking-tight px-4">
                        {{ $place->name }}
                    </h1>

                    <!-- Location -->
                    <div class="flex items-center justify-center gap-2 text-slate-200 font-medium text-base md:text-lg">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        <span>{{ $place->address ?? 'Jepara, Jawa Tengah' }}</span>
                    </div>

                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 animate-bounce cursor-pointer text-slate-300 hover:text-white transition-colors" @click="document.getElementById('content').scrollIntoView({behavior: 'smooth'})">
                <span class="material-symbols-outlined text-3xl md:text-4xl">keyboard_arrow_down</span>
            </div>
        </div>

        <!-- Main Content -->
        <div id="content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <!-- Left Column: Content (8 cols) -->
                <div class="lg:col-span-8 space-y-12">
                    
                    <!-- About Section -->
                    <section x-data="{ expanded: false, isLong: false }" x-init="isLong = $refs.aboutText.scrollHeight > 250" class="bg-white dark:bg-slate-900 rounded-2xl p-8 md:p-10 border border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300">
                                <span class="material-symbols-outlined text-xl">description</span>
                            </div>
                            <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white">{{ __('Places.Detail.About') }}</h2>
                        </div>
                        <div class="relative">
                            <div x-ref="aboutText" 
                                 class="prose prose-lg prose-slate dark:prose-invert max-w-none font-normal leading-relaxed text-slate-700 dark:text-slate-300 overflow-hidden transition-all duration-500 ease-in-out"
                                 :class="expanded ? 'max-h-[5000px]' : 'max-h-[250px]'">
                                <p class="whitespace-pre-line">{{ $place->description }}</p>
                            </div>
                            
                            <!-- Gradient Fade -->
                            <div x-show="isLong && !expanded" 
                                 class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white dark:from-slate-900 to-transparent pointer-events-none"
                                 x-transition:enter="transition opacity duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100">
                            </div>
                        </div>

                        <!-- Toggle Button -->
                        <div x-show="isLong" class="mt-6 flex justify-start">
                            <button @click="expanded = !expanded" 
                                    class="group flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <span x-text="expanded ? 'Tampilkan Lebih Sedikit' : 'Baca Selengkapnya'"></span>
                                <span class="material-symbols-outlined text-lg transition-transform duration-300" :class="expanded ? 'rotate-180' : ''">expand_more</span>
                            </button>
                        </div>
                    </section>

                    <!-- Wahana & Fasilitas Grid -->
                    @if(!empty($place->rides) || !empty($place->facilities))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Wahana Card -->
                        @if(!empty($place->rides) && is_array($place->rides))
                        <section class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden h-full">
                            <!-- Header -->
                            <div class="p-6 md:p-8 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-xl">attractions</span>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white">{{ __('Places.Detail.RidesTitle') }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Places.Detail.RidesSubtitle') }}</p>
                                </div>
                            </div>
                            
                            <!-- List -->
                            <div class="p-6 md:p-8">
                                <ul class="space-y-2">
                                    @foreach($place->rides as $ride)
                                        @if(is_array($ride))
                                            @php
                                                $isHeader = str_ends_with(trim($ride['name']), ':') || empty($ride['price']);
                                                $cleanName = str_replace(':', '', $ride['name']);
                                            @endphp

                                            @if($isHeader)
                                                <li class="pt-3 pb-1 first:pt-0">
                                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                                                        <span class="w-1 h-1 rounded-full bg-blue-500"></span>
                                                        {{ $cleanName }}
                                                    </h4>
                                                </li>
                                            @else
                                                <li class="flex items-start justify-between gap-4 text-sm py-2 px-3 ml-2 border-l-2 border-blue-200 dark:border-blue-900/30 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/50 rounded-r transition-colors">
                                                    <span class="font-medium leading-snug">{{ $ride['name'] }}</span>
                                                    @if(!empty($ride['price']))
                                                        <span class="shrink-0 font-bold text-blue-600 dark:text-blue-400 text-xs">{{ $ride['price'] }}</span>
                                                    @endif
                                                </li>
                                            @endif
                                        @else
                                            <li class="text-sm text-slate-600 dark:text-slate-400 py-1.5">{{ $ride }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </section>
                        @endif

                        <!-- Fasilitas Card -->
                        @if(!empty($place->facilities) && is_array($place->facilities))
                        <section class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden h-full">
                            <!-- Header -->
                            <div class="p-6 md:p-8 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-xl">pool</span>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-lg text-slate-900 dark:text-white">{{ __('Places.Detail.FacilitiesTitle') }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('Places.Detail.FacilitiesSubtitle') }}</p>
                                </div>
                            </div>

                            <!-- Facilities List -->
                            <div class="p-6 md:p-8">
                                <div class="flex flex-wrap gap-2.5">
                                    @foreach($place->facilities as $facility)
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-medium">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            <span>{{ $facility }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                        @endif
                        
                    </div>
                    @endif

                </div>

                <!-- Right Column: Sidebar (4 cols) -->
                <div class="lg:col-span-4 space-y-8">
                    
                    <!-- Info Sidebar - Sticky -->
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 border border-slate-200 dark:border-slate-800 sticky top-24 space-y-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-4">
                            <span class="material-symbols-outlined text-slate-700 dark:text-slate-300">info</span>
                            {{ __('Places.Detail.Sidebar.Title') }}
                        </h3>

                        <!-- Ticket Price -->
                        <div>
                            <div class="flex items-center gap-2 mb-3 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-base">confirmation_number</span>
                                {{ __('Places.Detail.Sidebar.Ticket') }}
                            </div>
                            <p class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-medium text-sm whitespace-pre-line leading-relaxed">
                                {{ $place->ticket_price ?? __('Places.Detail.Sidebar.ContactLabel') }}
                            </p>
                        </div>

                        <!-- Opening Hours -->
                        <div>
                            <div class="flex items-center gap-2 mb-3 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-base">schedule</span>
                                {{ __('Places.Detail.Sidebar.Hours') }}
                            </div>
                            <p class="text-slate-900 dark:text-white font-semibold">
                                {{ $place->opening_hours ?? __('Places.Detail.Sidebar.EveryDay') }}
                            </p>
                        </div>

                        <!-- Contact Info -->
                        @if($place->contact_info)
                        <div>
                            <div class="flex items-center gap-2 mb-3 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-base">call</span>
                                {{ __('Places.Detail.Sidebar.Contact') }}
                            </div>
                            <p class="text-slate-900 dark:text-white font-semibold">
                                {{ $place->contact_info }}
                            </p>
                        </div>
                        @endif

                        <!-- Manager/Ownership -->
                        @if($place->manager || $place->ownership_status)
                        <div>
                            <div class="flex items-center gap-2 mb-3 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-base">badge</span>
                                {{ __('Places.Detail.Sidebar.Manager') }}
                            </div>
                            <div>
                                <p class="text-slate-900 dark:text-white font-semibold text-sm">{{ $place->manager ?? '-' }}</p>
                                @if($place->ownership_status)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $place->ownership_status }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Social Media -->
                        @if($place->social_media)
                        <div>
                            <div class="flex items-center gap-2 mb-3 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-base">share</span>
                                {{ __('Places.Detail.Sidebar.Social') }}
                            </div>
                            <p class="text-slate-700 dark:text-slate-300 text-sm whitespace-pre-line">{{ $place->social_media }}</p>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="pt-6 border-t border-slate-200 dark:border-slate-800 space-y-3">
                            <a href="{{ $place->google_maps_link ?? 'https://www.google.com/maps/dir/?api=1&destination=' . $place->latitude . ',' . $place->longitude }}" target="_blank" 
                               class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                                <img src="https://www.google.com/images/branding/product/2x/maps_96in128dp.png" alt="Google Maps" class="w-5 h-5 object-contain brightness-0 invert">
                                <span>{{ __('Places.Detail.Sidebar.Directions') }}</span>
                            </a>
                            
                            @if($place->website)
                            <a href="{{ $place->website }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-lg bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-bold transition-colors">
                                <span class="material-symbols-outlined">language</span>
                                <span>{{ __('Places.Detail.Sidebar.Website') }}</span>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Gallery Section -->
                    @if($place->images->count() > 0 || $place->image_path)
                    <div x-data="{ 
                        lightboxOpen: false, 
                        activeImage: '', 
                        images: {{ json_encode(
                            collect([$place->image_path])
                                ->concat($place->images->pluck('image_path'))
                                ->filter()
                                ->unique()
                                ->map(fn($path) => asset($path))
                                ->values()
                        ) }},
                        get activeIndex() { return this.images.indexOf(this.activeImage); },
                        prev() {
                            let index = this.activeIndex;
                            this.activeImage = this.images[index - 1 < 0 ? this.images.length - 1 : index - 1];
                        },
                        next() {
                            let index = this.activeIndex;
                            this.activeImage = this.images[index + 1 >= this.images.length ? 0 : index + 1];
                        },
                        openLightbox(img) {
                            this.activeImage = img;
                            this.lightboxOpen = true;
                        }
                    }" class="bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 border border-slate-200 dark:border-slate-800">
                        <div class="flex items-center gap-3 mb-6">
                            <h2 class="text-xl font-display font-bold text-slate-900 dark:text-white">{{ __('Places.Detail.Gallery') }}</h2>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 auto-rows-[120px]">
                            @if($place->image_path)
                            <div class="col-span-2 row-span-2 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 cursor-pointer group" @click="openLightbox('{{ asset($place->image_path) }}')">
                                <img src="{{ asset($place->image_path) }}" class="w-full h-full object-cover group-hover:brightness-90 transition-all duration-500">
                            </div>
                            @endif
                            
                            @foreach($place->images as $image)
                            <div class="rounded-lg overflow-hidden border border-slate-200 dark:border-slate-800 cursor-pointer group" @click="openLightbox('{{ asset($image->image_path) }}')">
                                <img src="{{ asset($image->image_path) }}" class="w-full h-full object-cover group-hover:brightness-90 transition-all duration-500">
                            </div>
                            @endforeach
                        </div>

                        <!-- Lightbox Modal -->
                        <template x-teleport="body">
                            <template x-if="lightboxOpen">
                                <div 
                                    x-show="lightboxOpen" 
                                    style="display: none;"
                                    class="fixed inset-0 z-[20000] flex items-center justify-center bg-black/95 p-4"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    @keydown.escape.window="lightboxOpen = false"
                                    @keydown.arrow-left.window="if(lightboxOpen) prev()"
                                    @keydown.arrow-right.window="if(lightboxOpen) next()"
                                    @click="lightboxOpen = false"
                                >
                                    <!-- Close Button -->
                                    <button @click.stop="lightboxOpen = false" class="absolute top-6 right-6 text-white/60 hover:text-white transition-colors z-[20001] p-2 hover:bg-white/10 rounded-lg">
                                        <span class="material-symbols-outlined text-3xl">close</span>
                                    </button>
                                    
                                    <!-- Prev Button -->
                                    <button @click.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/60 hover:text-white z-[20001] p-3 hover:bg-white/10 rounded-lg transition-colors hidden md:block">
                                        <span class="material-symbols-outlined text-4xl">chevron_left</span>
                                    </button>
                                    
                                    <!-- Next Button -->
                                    <button @click.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/60 hover:text-white z-[20001] p-3 hover:bg-white/10 rounded-lg transition-colors hidden md:block">
                                        <span class="material-symbols-outlined text-4xl">chevron_right</span>
                                    </button>

                                    <!-- Content Wrapper -->
                                    <div class="relative flex flex-col items-center justify-center max-h-screen w-full select-none">
                                         
                                         <!-- Image Counter -->
                                         <div class="absolute -top-14 left-1/2 -translate-x-1/2 text-white/70 font-medium text-sm bg-black/40 px-3 py-1.5 rounded-lg" @click.stop>
                                            <span x-text="activeIndex + 1"></span> / <span x-text="images.length"></span>
                                         </div>

                                         <!-- Image -->
                                         <img 
                                            :src="activeImage" 
                                            class="max-h-[85vh] max-w-full rounded-lg object-contain transition-all duration-300"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-50"
                                            x-transition:enter-end="opacity-100"
                                            @click.stop
                                         >
                                         
                                         <!-- Gallery Navigation -->
                                         <div class="mt-8 flex gap-2 overflow-x-auto max-w-[90vw] p-2" @click.stop>
                                             <template x-for="(img, index) in images">
                                                 <button @click="activeImage = img" class="w-16 h-16 rounded-lg overflow-hidden border-2 transition-all shrink-0" :class="activeImage === img ? 'border-white opacity-100' : 'border-white/30 opacity-40 hover:opacity-60'">
                                                     <img :src="img" class="w-full h-full object-cover">
                                                 </button>
                                             </template>
                                         </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</x-public-layout>