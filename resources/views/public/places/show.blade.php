<x-public-layout>
    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans">
        
        <div class="flex flex-col lg:flex-row">
            
            <!-- Left Side: Sticky Visuals (50%) -->
            <div class="lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative h-[50vh] bg-slate-100 dark:bg-slate-900 overflow-hidden">
                <!-- Main Image -->
                @if($place->image_path)
                    <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-slate-200 dark:bg-slate-800 text-slate-400">
                        <span class="material-symbols-outlined text-6xl">image</span>
                    </div>
                @endif
                
                <!-- Overlay Gradient (Mobile Only) -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent lg:hidden"></div>

                <!-- Back Button (Floating) -->
                <a href="{{ route('places.index') }}" class="absolute top-6 left-6 z-20 w-10 h-10 rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white hover:bg-white hover:text-slate-900 transition-all">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>

                <!-- Gallery Preview (Bottom Left) -->
                @if($place->images->count() > 0)
                <div class="absolute bottom-6 left-6 z-20 hidden lg:flex gap-2">
                     <button class="px-4 py-2 rounded-full bg-white/90 backdrop-blur text-slate-900 text-sm font-bold shadow-lg hover:bg-white transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">grid_view</span>
                        Lihat Galeri ({{ $place->images->count() + 1 }})
                     </button>
                </div>
                @endif
            </div>

            <!-- Right Side: Scrollable Content (50%) -->
            <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                <main class="max-w-2xl mx-auto px-6 py-10 md:py-16 lg:px-12 lg:py-20">
                    
                    <!-- Header Section -->
                    <div class="mb-10 animate-fade-in-up">
                        <!-- Breadcrumbs / Badges -->
                        <div class="flex items-center gap-3 mb-4 text-sm">
                            <span class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-xs">
                                {{ $place->category->name ?? 'Destinasi' }}
                            </span>
                             @if($place->rating)
                            <div class="flex items-center gap-1 text-orange-500 font-bold">
                                <span class="material-symbols-outlined text-base">star</span>
                                <span>{{ $place->rating }}</span>
                            </div>
                            @endif
                        </div>

                        <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-tight mb-4">
                            {{ $place->name }}
                        </h1>
                        
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                            <span class="material-symbols-outlined text-xl">location_on</span>
                            <span>{{ $place->address ?? 'Jepara, Jawa Tengah' }}</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="border-slate-100 dark:border-slate-800 mb-10">

                    <!-- Content Body -->
                    <div class="space-y-12">
                        
                        <!-- Description -->
                        <section>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                                Tentang Destinasi
                            </h3>
                            <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed">
                                <p class="whitespace-pre-line">{{ $place->description }}</p>
                            </div>
                        </section>

                        <!-- Highlights / Quick Info Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Tiket Masuk</div>
                                <div class="text-slate-900 dark:text-white font-semibold flex items-center gap-2">
                                    <span class="material-symbols-outlined text-blue-500">confirmation_number</span>
                                    <span>{{ $place->ticket_price ?? 'Hubungi Pengelola' }}</span>
                                </div>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800">
                                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Jam Operasional</div>
                                <div class="text-slate-900 dark:text-white font-semibold flex items-center gap-2">
                                    <span class="material-symbols-outlined text-blue-500">schedule</span>
                                    <span>{{ $place->opening_hours ?? 'Setiap Hari' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Facilities Section -->
                         @if(!empty($place->facilities) && is_array($place->facilities))
                        <section>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                                Fasilitas
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                @foreach($place->facilities as $facility)
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium shadow-sm">
                                        <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
                                        {{ $facility }}
                                    </span>
                                @endforeach
                            </div>
                        </section>
                        @endif

                        <!-- Map / Location Section -->
                        <section>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="w-1 h-6 bg-orange-500 rounded-full"></span>
                                Lokasi & Peta
                            </h3>
                            <a href="{{ $place->google_maps_link ?? 'https://www.google.com/maps/dir/?api=1&destination=' . $place->latitude . ',' . $place->longitude }}" target="_blank" class="group relative block w-full aspect-video rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <!-- Placeholder Map Image (Static for design) -->
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-200 dark:bg-slate-800 group-hover:scale-105 transition-transform duration-500">
                                    <span class="material-symbols-outlined text-6xl text-slate-400">map</span>
                                </div>
                                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <div class="bg-white px-4 py-2 rounded-full shadow-lg font-bold text-slate-900 flex items-center gap-2 transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all">
                                        <span class="material-symbols-outlined text-red-500">near_me</span>
                                        Buka Google Maps
                                    </div>
                                </div>
                            </a>
                        </section>

                        <!-- Contact Info -->
                         @if($place->contact_info)
                        <div class="p-6 rounded-2xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30">
                            <h4 class="font-bold text-blue-900 dark:text-blue-100 mb-2">Kontak Informasi</h4>
                             <p class="text-blue-700 dark:text-blue-300 text-sm mb-4">
                                Jangan ragu untuk menghubungi pengelola untuk informasi reservasi atau pertanyaan lainnya.
                            </p>
                            <div class="flex items-center gap-2 font-mono text-blue-800 dark:text-blue-200 font-bold bg-white dark:bg-slate-900 px-4 py-2 rounded-lg inline-block shadow-sm">
                                <span class="material-symbols-outlined text-blue-500">call</span>
                                {{ $place->contact_info }}
                            </div>
                        </div>
                        @endif

                    </div>

                    <!-- Footer Area -->
                    <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                        <p class="text-slate-400 text-sm italic">
                            Informasi ini dapat berubah sewaktu-waktu. Harap hubungi pengelola untuk kepastian data.
                        </p>
                    </div>

                </main>
            </div>

        </div>
    </div>
</x-public-layout>