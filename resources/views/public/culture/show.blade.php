<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen">
        
        <!-- Immersive Hero Section -->
        <div class="relative h-[60vh] md:h-[70vh] w-full overflow-hidden group rounded-b-[2.5rem] md:rounded-b-[4rem] shadow-2xl z-20">
            <img src="{{ asset($culture->image) }}" alt="{{ $culture->name }}" class="w-full h-full object-cover attachment-fixed transform scale-105 group-hover:scale-100 transition-transform duration-[3s] ease-out">
            
            <!-- Cinematic Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-black/20 opacity-90"></div>

            <!-- Hero Content (Centered) -->
            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center z-10">
                <div class="max-w-4xl mx-auto space-y-6 md:space-y-8 animate-fade-in-up mt-10 md:mt-0">
                    
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center justify-center gap-3 md:gap-4">
                        <span class="px-4 py-1.5 md:px-5 md:py-2 rounded-full bg-orange-600/90 backdrop-blur-md border border-white/20 text-white text-xs md:text-sm font-bold tracking-widest uppercase shadow-xl hover:bg-orange-600 transition-colors">
                            Budaya & Tradisi
                        </span>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="font-display text-4xl sm:text-5xl md:text-7xl lg:text-7xl font-black text-white leading-tight drop-shadow-2xl tracking-tight px-4">
                        {{ $culture->name }}
                    </h1>

                    <!-- Location -->
                    <div class="flex items-center justify-center gap-2 text-white/80 font-medium text-base md:text-xl">
                        <span class="material-symbols-outlined text-orange-400 text-xl md:text-2xl">location_on</span>
                        <span class="border-b border-transparent hover:border-white/50 transition-all cursor-pointer">{{ $culture->location }}</span>
                    </div>

                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-6 md:bottom-10 left-1/2 -translate-x-1/2 z-20 animate-bounce cursor-pointer opacity-70 hover:opacity-100 transition-opacity" @click="document.getElementById('content').scrollIntoView({behavior: 'smooth'})">
                <span class="material-symbols-outlined text-white text-3xl md:text-4xl drop-shadow-lg">keyboard_arrow_down</span>
            </div>
        </div>

        <!-- Main Content Section -->
        <div id="content" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
                
                <!-- Left Column: Content (8 cols) -->
                <div class="md:col-span-8 space-y-8">
                    
                    <!-- About Section -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-xl border border-slate-100 dark:border-slate-700/50">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                <span class="material-symbols-outlined text-2xl">menu_book</span>
                            </div>
                            <h2 class="text-2xl font-display font-bold text-slate-800 dark:text-white">Tentang Tradisi</h2>
                        </div>
                        <div class="prose prose-lg prose-slate dark:prose-invert max-w-none font-light leading-relaxed">
                            <p class="whitespace-pre-line">{{ $culture->full_description ?? $culture->description }}</p>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Sidebar (4 cols) -->
                <div class="md:col-span-4 space-y-8">
                    <!-- Sticky Sidebar -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-xl border border-slate-100 dark:border-slate-700/50 sticky top-24">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-4">
                            <span class="material-symbols-outlined text-orange-500">info</span> Informasi Utama
                        </h3>

                        <div class="space-y-6">
                             <!-- Highlight/Date -->
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-base">event</span>
                                    Waktu Pelaksanaan
                                </div>
                                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4 border border-orange-100 dark:border-orange-800/30 group-hover:border-orange-500/30 transition-colors">
                                    <p class="text-orange-800 dark:text-orange-200 font-bold text-sm whitespace-pre-line leading-relaxed">
                                        {{ $culture->highlight }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Location Detail -->
                            <div class="group">
                                <div class="flex items-center gap-3 mb-2 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-base">pin_drop</span>
                                    Lokasi
                                </div>
                                <p class="text-slate-800 dark:text-white font-semibold pl-1">
                                    {{ $culture->location }}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Back -->
            <div class="mt-12 text-center">
                 <a href="{{ route('welcome') }}#culture" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold transition-all">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke Beranda
                 </a>
            </div>

        </div>
    </div>
</x-public-layout>
