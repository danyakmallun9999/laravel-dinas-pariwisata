<x-public-layout>
    @push('seo')
        <x-seo 
            :title="$place->translated_name . ' - Jelajah Jepara'"
            :description="$place->translated_description ? Str::limit(strip_tags($place->translated_description), 150) : 'Jelajahi ' . $place->translated_name . ' di Jepara.'"
            :image="$place->image_path ? asset($place->image_path) : asset('images/logo-kura.png')"
            type="article"
        />
    @endpush
    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans -mt-20 pt-4">
        
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row">
                
                <!-- Left Side: Sticky Visuals (50%) -->
                <div class="lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative bg-white dark:bg-slate-950 z-10 group flex flex-col lg:overflow-hidden p-4 lg:pl-16 lg:pr-8 lg:pt-24" 
                     @php
                         $uniqueGalleryImages = collect([]);
                         if ($place->image_path) {
                             $uniqueGalleryImages->push($place->image_path);
                         }
                         foreach($place->images as $img) {
                             $uniqueGalleryImages->push($img->image_path);
                         }
                         $uniqueGalleryImages = $uniqueGalleryImages->unique()->values();
                     @endphp
                     x-data="{ 
                        activeImage: '{{ $uniqueGalleryImages->first() ? asset($uniqueGalleryImages->first()) : '' }}',
                        isFlipping: false,
                        lightboxOpen: false,
                        lightboxIndex: 0,
                        images: [
                            @foreach($uniqueGalleryImages as $imgPath)
                                '{{ asset($imgPath) }}',
                            @endforeach
                        ],
                        changeImage(url) {
                            if (this.activeImage === url) return;
                            this.isFlipping = true;
                            setTimeout(() => {
                                this.activeImage = url;
                                this.isFlipping = false;
                            }, 300);
                        },
                        openLightbox(url) {
                            this.lightboxIndex = this.images.indexOf(url);
                            if (this.lightboxIndex === -1) this.lightboxIndex = 0;
                            this.lightboxOpen = true;
                            document.body.style.overflow = 'hidden';
                        },
                        closeLightbox() {
                            this.lightboxOpen = false;
                            document.body.style.overflow = '';
                        },
                        lightboxPrev() {
                            this.lightboxIndex = (this.lightboxIndex - 1 + this.images.length) % this.images.length;
                        },
                        lightboxNext() {
                            this.lightboxIndex = (this.lightboxIndex + 1) % this.images.length;
                        }
                     }"
                     @keydown.escape.window="if (lightboxOpen) closeLightbox()"
                     @keydown.left.window="if (lightboxOpen) lightboxPrev()"
                     @keydown.right.window="if (lightboxOpen) lightboxNext()">
                    
                    <!-- Breadcrumbs -->
                    <div class="mb-6">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                <li class="inline-flex items-center">
                                    <a href="{{ route('welcome') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                        <span class="material-symbols-outlined text-lg mr-1">home</span>
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                        <a href="{{ route('places.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                            Destinasi
                                        </a>
                                    </div>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                        <span class="text-sm font-medium text-slate-900 dark:text-white line-clamp-1 max-w-[150px] md:max-w-xs">
                                            {{ $place->translated_name }}
                                        </span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
    
                    <!-- Main Image Area -->
                    <div class="relative w-full aspect-[4/3] lg:aspect-auto lg:h-[60vh] overflow-hidden perspective-[1000px]">
                        <div class="relative w-full h-full rounded-3xl overflow-hidden text-transparent cursor-pointer" @click="openLightbox(activeImage)">
                            <template x-if="activeImage">
                                <img :src="activeImage" 
                                     alt="{{ $place->translated_name }}" 
                                     class="w-full h-full object-cover transition-all duration-500 ease-in-out transform origin-center"
                                     :class="isFlipping ? '[transform:rotateY(90deg)] opacity-75 scale-95' : '[transform:rotateY(0deg)] opacity-100 scale-100'">
                            </template>
                            <template x-if="!activeImage">
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-6xl">image</span>
                                </div>
                            </template>
                            <!-- Zoom hint overlay -->
                            <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-colors flex items-center justify-center">
                                <div class="bg-white/90 backdrop-blur px-4 py-2 rounded-full font-bold text-sm text-slate-700 shadow-lg opacity-0 hover:opacity-100 transition-opacity flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">zoom_in</span>
                                    Lihat Detail
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Thumbnails / Gallery List -->
                    <div class="w-full px-4 lg:px-6 pb-6 pt-3 flex items-center gap-3 overflow-x-auto scrollbar-hide scroll-smooth">
                        @foreach($uniqueGalleryImages as $imgPath)
                            <button @click="changeImage('{{ asset($imgPath) }}')" 
                                    :class="activeImage === '{{ asset($imgPath) }}' ? 'ring-2 ring-primary scale-105' : 'opacity-70 hover:opacity-100'"
                                    class="relative w-20 h-14 lg:w-24 lg:h-16 flex-shrink-0 rounded-xl overflow-hidden transition-all duration-300">
                                <img src="{{ asset($imgPath) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>

                    <!-- Lightbox Modal -->
                    <div x-show="lightboxOpen" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[9999] flex items-center justify-center"
                         style="position: fixed;">
                        
                        <!-- Backdrop (click to close) -->
                        <div class="absolute inset-0 bg-black/95 backdrop-blur-sm" @click="closeLightbox()"></div>
                        
                        <!-- Close Button -->
                        <button @click="closeLightbox()" 
                                class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all duration-200 hover:scale-110">
                            <span class="material-symbols-outlined text-2xl">close</span>
                        </button>

                        <!-- Image Counter -->
                        <div class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 bg-white/10 backdrop-blur-sm text-white text-sm font-medium px-4 py-2 rounded-full">
                            <span x-text="(lightboxIndex + 1) + ' / ' + images.length"></span>
                        </div>

                        <!-- Prev Button -->
                        <button x-show="images.length > 1" @click="lightboxPrev()" 
                                class="absolute left-2 sm:left-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all duration-200 hover:scale-110">
                            <span class="material-symbols-outlined text-2xl">chevron_left</span>
                        </button>

                        <!-- Next Button -->
                        <button x-show="images.length > 1" @click="lightboxNext()" 
                                class="absolute right-2 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all duration-200 hover:scale-110">
                            <span class="material-symbols-outlined text-2xl">chevron_right</span>
                        </button>

                        <!-- Lightbox Image -->
                        <div class="relative z-10 w-full h-full max-w-[90vw] max-h-[80vh] flex items-center justify-center pointer-events-none">
                            <img :src="images[lightboxIndex]" 
                                 :alt="'{{ $place->translated_name }} - Foto ' + (lightboxIndex + 1)"
                                 class="w-auto h-auto max-w-full max-h-full object-contain rounded-lg shadow-2xl select-auto pointer-events-auto">
                        </div>

                        <!-- Thumbnail Strip -->
                        <div x-show="images.length > 1" class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2 bg-black/40 backdrop-blur-sm rounded-2xl p-2 max-w-[90vw] overflow-x-auto scrollbar-hide">
                            <template x-for="(img, idx) in images" :key="idx">
                                <button @click="lightboxIndex = idx" 
                                        :class="lightboxIndex === idx ? 'ring-2 ring-white scale-110 opacity-100' : 'opacity-50 hover:opacity-80'"
                                        class="w-14 h-10 sm:w-16 sm:h-11 flex-shrink-0 rounded-lg overflow-hidden transition-all duration-200">
                                    <img :src="img" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
    
                <!-- Right Side: Scrollable Content (50%) -->
                <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                    <main class="max-w-3xl mx-auto px-6 py-12 md:py-16 lg:px-16 lg:py-24">
                        
                        <!-- Top Meta: Category & Rating -->
                        <div class="flex flex-wrap items-center gap-3 mb-6 animate-fade-in-up">
                            <span class="px-3 py-1 rounded-full bg-primary/5 dark:bg-primary/10 text-primary dark:text-primary font-bold uppercase tracking-wider text-xs border border-primary/20 dark:border-primary/20">
                                {{ $place->category->name ?? __('Places.Category.Default') }}
                            </span>
                            @if($place->rating)
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-800/30">
                                <span class="material-symbols-outlined text-base fill-current">star</span>
                                <span class="text-sm font-bold">{{ $place->rating }}</span>
                            </div>
                            @endif
                        </div>
    
                        <!-- Title & Address -->
                        <div class="mb-10 animate-fade-in-up delay-100">
                            <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-tight mb-4">
                                {{ $place->translated_name }}
                            </h1>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                                <span class="material-symbols-outlined text-xl flex-shrink-0">location_on</span>
                                <span class="font-light">{{ $place->address ?? __('Places.Address.Default') }}</span>
                            </div>
                        </div>
    
                        <!-- Horizontal Divider -->
                        <hr class="border-slate-100 dark:border-slate-800 mb-10">
    
                        <!-- Content Body -->
                        <div class="space-y-12 animate-fade-in-up delay-200">
                            
                            <!-- Description -->
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-4 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                                    {{ __('Places.About') }}
                                </h3>
                                <div x-data="{ expanded: false }">
                                    <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed text-justify transition-all duration-300"
                                         :class="expanded ? '' : 'line-clamp-4 mask-image-b'">
                                        <p class="whitespace-pre-line">{{ $place->translated_description }}</p>
                                    </div>
                                    @if(strlen($place->translated_description) > 300)
                                        <button @click="expanded = !expanded" 
                                                class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-primary dark:text-primary hover:text-primary/80 dark:hover:text-primary/80 transition-colors">
                                            <span x-text="expanded ? '{{ __('Places.ShowLess') }}' : '{{ __('Places.ReadMore') }}'"></span>
                                            <span class="material-symbols-outlined text-lg transition-transform duration-300" 
                                                  :class="expanded ? 'rotate-180' : ''">expand_more</span>
                                        </button>
                                    @endif
                                </div>
                            </section>
    
                            <!-- Key Information Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Ticket Section - Premium Design -->
                                <div class="md:col-span-2 rounded-2xl bg-gradient-to-br from-primary/5 via-white to-sky-50/50 dark:from-primary/10 dark:via-slate-900 dark:to-slate-900 border border-primary/20 dark:border-primary/30 overflow-hidden">
                                    <!-- Header -->
                                    <div class="px-5 py-4 bg-gradient-to-r from-primary to-sky-500 dark:from-primary dark:to-sky-600">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white text-xl">confirmation_number</span>
                                            </div>
                                            <div>
                                                <h4 class="text-white font-bold text-base">{{ __('Places.Ticket.Title') }}</h4>
                                                <p class="text-white/70 text-xs">{{ __('Places.Ticket.Subtitle') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Ticket Cards -->
                                    <div class="p-5">
                                        @if($place->activeTickets->isNotEmpty())
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                @foreach($place->activeTickets as $index => $ticket)
                                                    <div class="group relative bg-white dark:bg-slate-800/80 rounded-xl border border-slate-200/80 dark:border-slate-700/50 overflow-hidden hover:shadow-lg hover:shadow-primary/10 hover:border-primary/40 transition-all duration-300">
                                                        <!-- Decorative Element -->
                                                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-primary/10 to-transparent rounded-bl-full pointer-events-none"></div>
                                                        
                                                        <!-- Ticket Content -->
                                                        <div class="relative p-4">
                                                            <!-- Ticket Type Badge -->
                                                            <div class="flex items-start justify-between mb-3">
                                                                <div class="flex items-center gap-2">
                                                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary/20 to-sky-100 dark:from-primary/30 dark:to-sky-900/30 flex items-center justify-center">
                                                                        <span class="material-symbols-outlined text-primary text-sm">{{ $index === 0 ? 'person' : 'child_care' }}</span>
                                                                    </div>
                                                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Places.Ticket.Type') }} {{ $index + 1 }}</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Ticket Name -->
                                                            <h5 class="font-bold text-slate-800 dark:text-white text-base mb-1 leading-snug group-hover:text-primary transition-colors">
                                                                {{ $ticket->name }}
                                                            </h5>
                                                            
                                                            <!-- Description -->
                                                            @if($ticket->description)
                                                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mb-3">{{ $ticket->description }}</p>
                                                            @else
                                                                <p class="text-xs text-slate-400 dark:text-slate-500 mb-3">{{ __('Places.Ticket.DescriptionDefault') }}</p>
                                                            @endif
                                                            
                                                            <!-- Divider with dots -->
                                                            <div class="relative my-3">
                                                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-2 h-4 bg-slate-100 dark:bg-slate-900 rounded-r-full"></div>
                                                                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 h-4 bg-slate-100 dark:bg-slate-900 rounded-l-full"></div>
                                                                <div class="border-t-2 border-dashed border-slate-200 dark:border-slate-600 mx-4"></div>
                                                            </div>
                                                            
                                                            <!-- Price & Action -->
                                                            <div class="flex items-end justify-between gap-3 mt-3">
                                                                <div>
                                                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-0.5">{{ __('Places.Ticket.Price') }}</span>
                                                                    <div class="flex flex-col">
                                                                        <div class="flex items-baseline gap-1">
                                                                            <span class="text-xs text-slate-500">{{ __('Places.Ticket.Normal') }}</span>
                                                                            <span class="text-lg font-extrabold text-primary dark:text-primary">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        @if($ticket->price_weekend)
                                                                            <div class="flex items-baseline gap-1">
                                                                                <span class="text-[10px] text-rose-500 font-semibold">{{ __('Places.Ticket.Weekend') }}</span>
                                                                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Rp {{ number_format($ticket->price_weekend, 0, ',', '.') }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <a href="{{ route('tickets.show', $ticket->id) }}" 
                                                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary to-sky-500 hover:from-primary/90 hover:to-sky-500/90 text-white font-bold text-xs shadow-md shadow-primary/20 hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300">
                                                                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                                                                    {{ __('Places.Ticket.Buy') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="bg-white dark:bg-slate-800/80 rounded-xl p-5 border border-slate-200/80 dark:border-slate-700/50 text-center">
                                                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-amber-500 text-xl">info</span>
                                                </div>
                                                <span class="block text-base font-semibold text-slate-800 dark:text-white mb-1">{{ __('Places.Ticket.Contact') }}</span>
                                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ __('Places.Ticket.ContactInfo') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Opening Hours -->
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">{{ __('Places.Hours') }}</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary dark:text-primary">
                                            <span class="material-symbols-outlined text-xl">schedule</span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1">
                                            @if($place->opening_hours)
                                                <div class="flex flex-col gap-1.5">
                                                    @foreach(explode("\n", $place->opening_hours) as $hours)
                                                        @if(trim($hours))
                                                            <div class="text-sm leading-relaxed text-slate-700 dark:text-slate-200">
                                                                {{ trim($hours) }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="block text-lg leading-tight">{{ __('Places.Hours.Everyday') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Ownership -->
                                @if($place->ownership_status)
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('Places.Ownership') }}</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center text-primary dark:text-primary">
                                            <span class="material-symbols-outlined text-sm">domain</span>
                                        </div>
                                        <span>{{ $place->ownership_status }}</span>
                                    </div>
                                </div>
                                @endif
                                <!-- Manager -->
                                @if($place->manager)
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">{{ __('Places.Manager') }}</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 dark:bg-primary/20 flex items-center justify-center text-primary dark:text-primary">
                                            <span class="material-symbols-outlined text-sm">badge</span>
                                        </div>
                                        <span>{{ $place->manager }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
    
                             <!-- Wahana (Rides) -->
                            @if(!empty($place->rides) && is_array($place->rides))
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-purple-500 rounded-full"></span>
                                    {{ __('Places.Rides') }}
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($place->rides as $ride)
                                        <div class="flex items-start gap-3 p-4 rounded-xl bg-purple-50 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-800/30">
                                            <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 mt-0.5">attractions</span>
                                            <div class="flex flex-col">
                                                @if(is_array($ride))
                                                    <span class="text-slate-700 dark:text-slate-300 font-medium text-sm">{{ $ride['name'] ?? '' }}</span>
                                                    @if(!empty($ride['price']))
                                                        <span class="text-slate-500 dark:text-slate-400 text-xs">{{ $ride['price'] }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-slate-700 dark:text-slate-300 font-medium text-sm">{{ $ride }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                            @endif
    
                            <!-- Facilities Section -->
                            @if(!empty($place->facilities) && is_array($place->facilities))
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                                    {{ __('Places.Facilities') }}
                                </h3>
                                <div class="flex flex-wrap gap-3">
                                    @foreach($place->facilities as $facility)
                                        <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium shadow-sm hover:shadow-md transition-all cursor-default">
                                            <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                            {{ $facility }}
                                        </span>
                                    @endforeach
                                </div>
                            </section>
                            @endif
    
                            <!-- Social Media & Contact -->
                            @if($place->social_media || $place->contact_info)
                            <section class="p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-white dark:from-slate-900 dark:to-slate-950 border border-slate-200 dark:border-slate-800">
                                 <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">{{ __('Places.Contact') }}</h3>
                                 <div class="flex flex-wrap gap-4">
                                    @if($place->social_media && is_array($place->social_media))
                                        @foreach($place->social_media as $social)
                                            <a href="{{ $social['url'] ?? '#' }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-pink-500 text-white font-bold hover:bg-pink-600 transition-colors shadow-lg hover:shadow-pink-500/30">
                                                @php
                                                    $platform = strtolower($social['platform'] ?? '');
                                                    $icon = 'fa-link';
                                                    if (str_contains($platform, 'instagram') || $platform === 'ig') $icon = 'fa-instagram';
                                                    elseif (str_contains($platform, 'facebook') || $platform === 'fb') $icon = 'fa-facebook';
                                                    elseif (str_contains($platform, 'tiktok')) $icon = 'fa-tiktok';
                                                    elseif (str_contains($platform, 'youtube')) $icon = 'fa-youtube';
                                                    elseif (str_contains($platform, 'twitter') || $platform === 'x') $icon = 'fa-x-twitter';
                                                    elseif (str_contains($platform, 'whatsapp') || $platform === 'wa') $icon = 'fa-whatsapp';
                                                @endphp
                                                <i class="fa-brands {{ $icon }} text-xl"></i>
                                                <span>{{ $social['platform'] ?? 'Social Media' }}</span>
                                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                                            </a>
                                        @endforeach
                                    @elseif(is_string($place->social_media))
                                         <a href="{{ $place->social_media }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-pink-500 text-white font-bold hover:bg-pink-600 transition-colors shadow-lg hover:shadow-pink-500/30">
                                            <i class="fa-brands fa-instagram text-xl"></i>
                                            <span>Social Media</span>
                                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                                        </a>
                                    @endif
    
                                    @if($place->contact_info)
                                        <div class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                                            <span class="material-symbols-outlined text-slate-500">call</span>
                                            <span>{{ $place->contact_info }}</span>
                                        </div>
                                    @endif
                                 </div>
                            </section>
                            @endif
    
                            <!-- Map / Location Section -->
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                                    {{ __('Places.Map') }}
                                </h3>
                                 <div class="relative w-full h-[400px] md:h-auto md:aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm group">
                                     <iframe 
                                        width="100%" 
                                        height="100%" 
                                        frameborder="0" 
                                        scrolling="no" 
                                        marginheight="0" 
                                        marginwidth="0" 
                                        src="https://maps.google.com/maps?q={{ $place->latitude && $place->longitude ? $place->latitude . ',' . $place->longitude : urlencode($place->translated_name . ' Jepara') }}&t=&z=14&ie=UTF8&iwloc=&output=embed">
                                     </iframe>
                                     
                                     <!-- Click Overlay to Open External Map -->
                                     <a href="https://www.google.com/maps/search/?api=1&query={{ $place->latitude && $place->longitude ? $place->latitude . ',' . $place->longitude : urlencode($place->translated_name . ' Jepara') }}" 
                                        target="_blank"
                                        class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center z-10"
                                        title="{{ __('Events.Detail.MapsLink') }}">
                                         <div class="bg-white/90 backdrop-blur text-slate-900 px-4 py-2 rounded-full font-bold text-sm shadow-lg opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all flex items-center gap-2">
                                             <span class="material-symbols-outlined text-red-500">map</span>
                                             {{ __('Events.Detail.MapsLink') }}
                                         </div>
                                     </a>
                                 </div>
                            </section>
    
                        </div>
    
                        <!-- Footer Area -->
                        <div class="mt-20 pt-10 border-t border-slate-100 dark:border-slate-800 text-center">
                            <p class="text-slate-400 text-sm">
                                {{ __('Places.Footer', ['year' => date('Y')]) }}
                            </p>
                        </div>
    
                    </main>
                </div>
    
            </div>
        </div>
    </div>
</x-public-layout>