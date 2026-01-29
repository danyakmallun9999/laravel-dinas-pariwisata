<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen">
        
        <!-- Immersive Hero Section -->
        <div class="relative h-[85vh] w-full overflow-hidden group">
            @if($place->image_path)
                <img src="{{ $place->image_path }}" alt="{{ $place->name }}" class="w-full h-full object-cover attachment-fixed transform scale-105 group-hover:scale-100 transition-transform duration-[3s] ease-out">
            @else
                <div class="w-full h-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-8xl text-slate-300">image</span>
                </div>
            @endif
            
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>

            <!-- Hero Content -->
            <div class="absolute bottom-0 left-0 right-0 p-8 md:p-16 lg:p-24 z-10">
                <div class="max-w-7xl mx-auto w-full animate-fade-in-up">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-bold tracking-wider uppercase shadow-lg">
                            {{ $place->category->name ?? 'Destinasi' }}
                        </span>
                        @if($place->rating)
                        <div class="flex items-center gap-1 bg-yellow-400/20 backdrop-blur-md px-3 py-1.5 rounded-full border border-yellow-400/30 text-yellow-300 text-xs font-bold">
                            <span class="material-symbols-outlined text-sm">star</span>
                            <span>{{ $place->rating }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <h1 class="font-display text-5xl md:text-6xl lg:text-7xl font-black text-white mb-6 leading-tight drop-shadow-2xl">
                        {{ $place->name }}
                    </h1>

                    <div class="flex flex-wrap gap-6 text-white/90 font-medium text-lg">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">location_on</span>
                            <span>{{ $place->address ?? 'Jepara, Jawa Tengah' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 -mt-20 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <!-- Left Column: Content (8 cols) -->
                <div class="lg:col-span-8 space-y-12">
                    
                    <!-- About Section -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-xl border border-slate-100 dark:border-slate-700/50">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-2xl">description</span>
                            </div>
                            <h2 class="text-2xl font-display font-bold text-slate-800 dark:text-white">Tentang Destinasi</h2>
                        </div>
                        <div class="prose prose-lg prose-slate dark:prose-invert max-w-none font-light leading-relaxed">
                            <p class="whitespace-pre-line">{{ $place->description }}</p>
                        </div>
                    </div>

                    <!-- Wahana & Fasilitas Grid -->
                    <!-- Wahana & Fasilitas Grid (Bento Style) -->
                    @if(!empty($place->rides) || !empty($place->facilities))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Wahana / Pricing Card -->
                        @if(!empty($place->rides) && is_array($place->rides))
                        <div class="bg-gradient-to-br from-blue-50/50 to-white dark:from-slate-800 dark:to-slate-900 rounded-3xl border border-blue-100 dark:border-slate-700 overflow-hidden relative group h-full">
                            <!-- Header -->
                            <div class="p-6 border-b border-blue-50 dark:border-slate-700/50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl">attractions</span>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-lg text-slate-800 dark:text-white">Wahana & Tur</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Daftar harga dan aktivitas</p>
                                </div>
                            </div>
                            
                            <!-- List -->
                            <div class="p-6 relative">
                                <ul class="space-y-1">
                                    @foreach($place->rides as $ride)
                                        @if(is_array($ride))
                                            @php
                                                // Detect Header: Ends with ':' or has no price/contact info
                                                $isHeader = str_ends_with(trim($ride['name']), ':') || empty($ride['price']);
                                                $cleanName = str_replace(':', '', $ride['name']);
                                            @endphp

                                            @if($isHeader)
                                                <li class="pt-4 pb-2 first:pt-0">
                                                    <h4 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                        {{ $cleanName }}
                                                    </h4>
                                                </li>
                                            @else
                                                <li class="flex items-start justify-between gap-4 text-sm py-1.5 pl-6 border-l-2 border-slate-100 dark:border-slate-700 ml-0.5 hover:bg-slate-50 dark:hover:bg-white/5 pr-2 rounded-r-lg transition-colors">
                                                    <span class="font-medium text-slate-600 dark:text-slate-300 leading-snug">{{ $ride['name'] }}</span>
                                                    @if(!empty($ride['price']))
                                                        <span class="shrink-0 font-bold text-blue-600 dark:text-blue-400 text-xs">{{ $ride['price'] }}</span>
                                                    @endif
                                                </li>
                                            @endif
                                        @else
                                            {{-- Fallback --}}
                                            <li class="text-sm text-slate-600 dark:text-slate-400 py-1">{{ $ride }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                        <!-- Fasilitas Card -->
                        @if(!empty($place->facilities) && is_array($place->facilities))
                        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 overflow-hidden h-full">
                             <!-- Header -->
                             <div class="p-6 border-b border-slate-50 dark:border-slate-700/50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl">pool</span>
                                </div>
                                <div>
                                    <h3 class="font-display font-bold text-lg text-slate-800 dark:text-white">Fasilitas</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Kenyamanan yang tersedia</p>
                                </div>
                            </div>

                            <!-- Chips/Badges -->
                            <div class="p-6">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($place->facilities as $facility)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30 text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                                            <span class="material-symbols-outlined text-sm">check</span>
                                            <span>{{ $facility }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                        
                    </div>
                    @endif

                    <!-- Gallery Section -->
                    @if($place->images->count() > 0 || $place->image_path)
                    <div>
                        <div class="flex items-center gap-3 mb-8">
                             <div class="h-1 w-10 bg-primary rounded-full"></div>
                             <h2 class="text-2xl font-display font-bold text-slate-800 dark:text-white">Galeri Foto</h2>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 auto-rows-[200px]">
                            @if($place->image_path)
                            <div class="md:col-span-2 row-span-2 rounded-2xl overflow-hidden shadow-lg group">
                                <img src="{{ $place->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                            @endif
                            
                            @foreach($place->images as $image)
                            <div class="rounded-2xl overflow-hidden shadow-lg group">
                                <img src="{{ $image->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Sidebar (4 cols) -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Sticky Sidebar -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-xl border border-slate-100 dark:border-slate-700/50 sticky top-24">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-4">
                            <span class="material-symbols-outlined text-primary">info</span> Informasi Utama
                        </h3>

                        <div class="space-y-6">
                            <!-- Ticket Price -->
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-base">confirmation_number</span>
                                    Harga Tiket
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-4 border border-slate-100 dark:border-slate-700 group-hover:border-primary/30 transition-colors">
                                    <p class="text-slate-700 dark:text-slate-200 font-medium text-sm whitespace-pre-line leading-relaxed">
                                        {{ $place->ticket_price ?? 'Hubungi Pengelola' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Opening Hours -->
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-base">schedule</span>
                                    Jam Operasional
                                </div>
                                <p class="text-slate-800 dark:text-white font-semibold pl-1">
                                    {{ $place->opening_hours ?? 'Setiap Hari' }}
                                </p>
                            </div>

                            <!-- Contact info -->
                            @if($place->contact_info)
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-base">call</span>
                                    Kontak
                                </div>
                                <p class="text-slate-800 dark:text-white font-semibold pl-1">
                                    {{ $place->contact_info }}
                                </p>
                            </div>
                            @endif

                             <!-- Manager/Ownership -->
                            @if($place->manager || $place->ownership_status)
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-base">badge</span>
                                    Pengelola
                                </div>
                                <div class="pl-1">
                                    <p class="text-slate-800 dark:text-white font-semibold">{{ $place->manager ?? '-' }}</p>
                                    @if($place->ownership_status)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $place->ownership_status }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Social Media -->
                            @if($place->social_media)
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-base">share</span>
                                    Media Sosial
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 text-sm whitespace-pre-line pl-1">{{ $place->social_media }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 space-y-3">
                             <a href="{{ $place->google_maps_link ?? 'https://www.google.com/maps/dir/?api=1&destination=' . $place->latitude . ',' . $place->longitude }}" target="_blank" 
                               class="flex items-center justify-center gap-2 w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-bold shadow-lg shadow-blue-500/20 transform hover:-translate-y-0.5 transition-all">
                                <img src="https://www.google.com/images/branding/product/2x/maps_96in128dp.png" alt="Google Maps" class="w-5 h-5 object-contain brightness-0 invert">
                                <span>Petunjuk Arah</span>
                            </a>
                            
                            @if($place->website)
                            <a href="{{ $place->website }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3.5 px-6 rounded-xl bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-white font-bold transition-all">
                                <span class="material-symbols-outlined">language</span>
                                <span>Kunjungi Website</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
