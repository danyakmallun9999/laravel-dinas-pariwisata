<x-public-layout>
    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans">
        
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row">
                
                <!-- Left Side: Sticky Visuals (50%) -->
                <div class="lg:w-1/2 lg:h-[93vh] lg:sticky lg:top-24 relative h-[60vh] bg-white dark:bg-slate-950 overflow-hidden group flex flex-col lg:pb-6" 
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
                        }
                     }">
                    
                    <!-- Back Button (Separated) -->
                    <div class="px-4 lg:px-6 pt-4 pb-2">
                        <a href="{{ route('places.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-primary hover:text-white transition-all shadow-sm">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                    </div>
    
                    <!-- Main Image Area -->
                    <div class="flex-1 relative p-4 lg:p-6 pb-0 lg:pb-0 pt-0 lg:pt-0 flex items-end justify-center overflow-hidden perspective-[1000px]">
                        <div class="relative w-full h-full rounded-3xl overflow-hidden text-transparent">
                            <template x-if="activeImage">
                                <img :src="activeImage" 
                                     alt="{{ $place->name }}" 
                                     class="w-full h-full object-cover transition-all duration-500 ease-in-out transform origin-center"
                                     :class="isFlipping ? '[transform:rotateY(90deg)] opacity-75 scale-95' : '[transform:rotateY(0deg)] opacity-100 scale-100'">
                            </template>
                            <template x-if="!activeImage">
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-6xl">image</span>
                                </div>
                            </template>
                        </div>
                    </div>
    
                    <!-- Thumbnails / Gallery List -->
                    <div class="w-full px-4 lg:px-6 pb-6 pt-3 flex items-center gap-3 overflow-x-auto no-scrollbar scroll-smooth">
                        @foreach($uniqueGalleryImages as $imgPath)
                            <button @click="changeImage('{{ asset($imgPath) }}')" 
                                    :class="activeImage === '{{ asset($imgPath) }}' ? 'ring-2 ring-blue-500 scale-105' : 'opacity-70 hover:opacity-100'"
                                    class="relative w-16 h-16 lg:w-20 lg:h-20 flex-shrink-0 rounded-xl overflow-hidden transition-all duration-300">
                                <img src="{{ asset($imgPath) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                </div>
    
                <!-- Right Side: Scrollable Content (50%) -->
                <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                    <main class="max-w-3xl mx-auto px-6 py-12 md:py-16 lg:px-16 lg:py-24">
                        
                        <!-- Top Meta: Category & Rating -->
                        <div class="flex flex-wrap items-center gap-3 mb-6 animate-fade-in-up">
                            <span class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-xs border border-blue-100 dark:border-blue-800">
                                {{ $place->category->name ?? 'Destinasi' }}
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
                                {{ $place->name }}
                            </h1>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                                <span class="material-symbols-outlined text-xl flex-shrink-0">location_on</span>
                                <span class="font-light">{{ $place->address ?? 'Jepara, Jawa Tengah' }}</span>
                            </div>
                        </div>
    
                        <!-- Horizontal Divider -->
                        <hr class="border-slate-100 dark:border-slate-800 mb-10">
    
                        <!-- Content Body -->
                        <div class="space-y-12 animate-fade-in-up delay-200">
                            
                            <!-- Description -->
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-4 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                                    Tentang Destinasi
                                </h3>
                                <div x-data="{ expanded: false }">
                                    <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed text-justify transition-all duration-300"
                                         :class="expanded ? '' : 'line-clamp-4 mask-image-b'">
                                        <p class="whitespace-pre-line">{{ $place->description }}</p>
                                    </div>
                                    @if(strlen($place->description) > 300)
                                        <button @click="expanded = !expanded" 
                                                class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                            <span x-text="expanded ? 'Sembunyikan' : 'Baca Selengkapnya'"></span>
                                            <span class="material-symbols-outlined text-lg transition-transform duration-300" 
                                                  :class="expanded ? 'rotate-180' : ''">expand_more</span>
                                        </button>
                                    @endif
                                </div>
                            </section>
    
                            <!-- Key Information Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Ticket -->
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-800/50 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">Tiket Masuk</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex-shrink-0 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                            <span class="material-symbols-outlined text-xl">confirmation_number</span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1">
                                            @if($place->ticket_price)
                                                <div class="flex flex-col gap-1.5">
                                                    @foreach(explode("\n", $place->ticket_price) as $price)
                                                        @if(trim($price))
                                                            <div class="relative pl-3 text-sm leading-relaxed text-slate-700 dark:text-slate-200">
                                                                <span class="absolute left-0 top-1.5 w-1.5 h-1.5 rounded-full bg-blue-400/60"></span>
                                                                {{ trim($price) }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="block text-lg leading-tight">Hubungi Pengelola</span>
                                                <span class="text-xs text-slate-500 font-normal">Harga dapat berubah sewaktu-waktu</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Opening Hours -->
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-800/50 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">Jam Operasional</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex-shrink-0 flex items-center justify-center text-blue-600 dark:text-blue-400">
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
                                                <span class="block text-lg leading-tight">Setiap Hari</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Ownership -->
                                @if($place->ownership_status)
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-800/50 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Status Kepemilikan</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                            <span class="material-symbols-outlined text-sm">domain</span>
                                        </div>
                                        <span>{{ $place->ownership_status }}</span>
                                    </div>
                                </div>
                                @endif
                                <!-- Manager -->
                                @if($place->manager)
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-800/50 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">Pengelola</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
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
                                    Wahana & Aktivitas
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
                                    Fasilitas Tersedia
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
                                 <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Informasi Kontak & Media Sosial</h3>
                                 <div class="flex flex-wrap gap-4">
                                    @if($place->social_media)
                                        <a href="{{ $place->social_media }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-pink-500 text-white font-bold hover:bg-pink-600 transition-colors shadow-lg hover:shadow-pink-500/30">
                                            <i class="fa-brands fa-instagram text-xl"></i>
                                            <span>Instagram / Social Media</span>
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
                                    Lokasi Peta
                                </h3>
                                <a href="{{ $place->google_maps_link ?? 'https://www.google.com/maps/dir/?api=1&destination=' . $place->latitude . ',' . $place->longitude }}" target="_blank" class="block w-full aspect-video rounded-3xl overflow-hidden bg-slate-100 dark:bg-slate-800 border-[6px] border-white dark:border-slate-800 shadow-xl relative group">
                                    <!-- Leaflet Map Container -->
                                    <div id="mini-map" class="absolute inset-0 w-full h-full z-0"></div>
                                    
                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center z-10 pointer-events-none">
                                        <div class="bg-white px-6 py-3 rounded-full shadow-2xl font-bold text-slate-900 flex items-center gap-3 transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                            <span class="material-symbols-outlined text-red-500">near_me</span>
                                            Buka Google Maps
                                        </div>
                                    </div>
                                </a>
                            </section>
    
                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const lat = {{ $place->latitude ?? -6.5817 }};
                                    const lng = {{ $place->longitude ?? 110.6685 }};
                                    
                                    const map = L.map('mini-map', {
                                        center: [lat, lng],
                                        zoom: 15, // Zoomed in a bit more for detail
                                        zoomControl: false,
                                        dragging: false,
                                        scrollWheelZoom: false,
                                        doubleClickZoom: false,
                                        boxZoom: false,
                                        attributionControl: false,
                                        keyboard: false
                                    });
    
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '© OpenStreetMap contributors'
                                    }).addTo(map);
    
                                    // Marker removed as per request
                                    
                                    // Invalidate size to ensure render
                                    setTimeout(() => {
                                        map.invalidateSize();
                                    }, 100);
                                });
                            </script>
                            @endpush
    
                        </div>
    
                        <!-- Footer Area -->
                        <div class="mt-20 pt-10 border-t border-slate-100 dark:border-slate-800 text-center">
                            <p class="text-slate-400 text-sm">
                                &copy; {{ date('Y') }} Dinas Pariwisata & Kebudayaan Kabupaten Jepara
                            </p>
                        </div>
    
                    </main>
                </div>
    
            </div>
        </div>
    </div>
</x-public-layout>