<x-public-layout>
    <div class="bg-background-light dark:bg-background-dark min-h-screen -mt-20">
        <!-- Hero Section -->
        <div class="relative h-[60vh] md:h-[70vh] w-full overflow-hidden">
            @if($place->image_path)
                <img src="{{ $place->image_path }}" alt="{{ $place->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-300 dark:bg-gray-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-gray-400">image</span>
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-background-light dark:from-background-dark via-black/40 to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 lg:p-16">
                <div class="max-w-7xl mx-auto w-full">
                    <span class="inline-block px-3 py-1 mb-4 rounded-full bg-primary/90 backdrop-blur text-white text-sm font-bold shadow-lg">
                        {{ $place->category->name ?? 'Destinasi Wisata' }}
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 drop-shadow-lg leading-tight">
                        {{ $place->name }}
                    </h1>
                    <div class="flex items-center gap-4 text-white/90">
                        @if($place->rating)
                        <div class="flex items-center gap-1 bg-black/30 backdrop-blur px-3 py-1 rounded-full border border-white/10">
                            <span class="material-symbols-outlined text-yellow-400 text-sm">star</span>
                            <span class="font-bold">{{ $place->rating }}</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-1 bg-black/30 backdrop-blur px-3 py-1 rounded-full border border-white/10">
                            <span class="material-symbols-outlined text-sm">payments</span>
                            <span>{{ $place->ticket_price == 'Gratis' ? 'Gratis' : $place->ticket_price }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 md:py-16 -mt-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <!-- Main Content (Left) -->
                <div class="lg:col-span-2 space-y-10">
                    <!-- Description -->
                    <div class="prose prose-lg dark:prose-invert max-w-none">
                        <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">Tentang Destinasi</h2>
                        <p class="text-text-light/80 dark:text-text-dark/80 leading-relaxed whitespace-pre-line">
                            {{ $place->description }}
                        </p>
                    </div>

                    <!-- Gallery (Placeholder for now) -->
                    <div>
                        <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-6">Galeri Foto</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Main Image -->
                            <div class="aspect-square rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-800 cursor-pointer group">
                                @if($place->image_path)
                                <img src="{{ $place->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @endif
                            </div>
                            
                            <!-- Gallery Images -->
                            @foreach($place->images as $image)
                            <div class="aspect-square rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-800 cursor-pointer group">
                                <img src="{{ $image->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right) -->
                <div class="space-y-8">
                    <!-- Info Card -->
                    <div class="bg-white dark:bg-surface-dark rounded-2xl p-6 shadow-xl border border-surface-light dark:border-white/5 sticky top-24">
                        <h3 class="text-xl font-bold text-text-light dark:text-text-dark mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">info</span> Informasi Utama
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Address -->
                            <div class="flex gap-4">
                                <div class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined">location_on</span>
                                </div>
                                <div>
                                    <p class="text-xs text-text-light/50 font-bold uppercase tracking-wider mb-1">Lokasi</p>
                                    <p class="text-text-light dark:text-text-dark font-medium">{{ $place->address ?? 'Jepara' }}</p>
                                </div>
                            </div>

                            <!-- Opening Hours -->
                            <div class="flex gap-4">
                                <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined">schedule</span>
                                </div>
                                <div>
                                    <p class="text-xs text-text-light/50 font-bold uppercase tracking-wider mb-1">Jam Operasional</p>
                                    <p class="text-text-light dark:text-text-dark font-medium">{{ $place->opening_hours ?? 'Setiap Hari' }}</p>
                                </div>
                            </div>

                            <!-- Ticket -->
                            <div class="flex gap-4">
                                <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 text-green-600 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined">confirmation_number</span>
                                </div>
                                <div>
                                    <p class="text-xs text-text-light/50 font-bold uppercase tracking-wider mb-1">Harga Tiket</p>
                                    <p class="text-text-light dark:text-text-dark font-medium">{{ $place->ticket_price ?? 'Hubungi Pengelola' }}</p>
                                </div>
                            </div>

                            <!-- Contact -->
                            <div class="flex gap-4">
                                <div class="size-10 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined">call</span>
                                </div>
                                <div>
                                    <p class="text-xs text-text-light/50 font-bold uppercase tracking-wider mb-1">Kontak</p>
                                    <p class="text-text-light dark:text-text-dark font-medium">{{ $place->contact_info ?: '-' }}</p>
                                </div>
                            </div>

                             <!-- Notes -->
                            @if($place->notes)
                            <div class="flex gap-4">
                                <div class="size-10 rounded-full bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined">lightbulb</span>
                                </div>
                                <div>
                                    <p class="text-xs text-text-light/50 font-bold uppercase tracking-wider mb-1">Catatan</p>
                                    <p class="text-text-light dark:text-text-dark font-medium text-sm">{{ $place->notes }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Action Button -->
                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3 pt-4 border-t border-surface-light dark:border-white/5">
                                @if($place->website)
                                <a href="{{ $place->website }}" target="_blank" class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold transition-all shadow-lg shadow-primary/20 group">
                                    <span class="material-symbols-outlined">language</span>
                                    <span>Website</span>
                                </a>
                                @endif
                                
                                <a href="{{ $place->google_maps_link ?? 'https://www.google.com/maps/dir/?api=1&destination=' . $place->latitude . ',' . $place->longitude }}" target="_blank" 
                                   class="flex items-center justify-center size-12 rounded-xl bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all shadow-md hover:shadow-lg"
                                   title="Buka di Google Maps">
                                    <img src="https://www.google.com/images/branding/product/2x/maps_96in128dp.png" alt="Google Maps" class="w-6 h-6 object-contain">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
