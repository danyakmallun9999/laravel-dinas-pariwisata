<x-public-layout>
    <div class="bg-white dark:bg-slate-900 min-h-screen font-sans">
        
        <!-- Immersive Hero Section -->
        <div class="relative h-[85vh] w-full overflow-hidden group">
            <img src="{{ asset($culture->image) }}" alt="{{ $culture->name }}" class="absolute inset-0 w-full h-full object-cover transform scale-105 group-hover:scale-100 transition-transform duration-[10s] ease-out">
            
            <!-- Cinematic Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/80"></div>
            <div class="absolute inset-0 bg-black/20"></div>

            <!-- Hero Content -->
            <div class="absolute bottom-0 left-0 right-0 p-8 md:p-16 pb-24 md:pb-32 z-20 text-center">
                <div class="max-w-5xl mx-auto space-y-6 animate-fade-in-up">
                    
                    <!-- Decorative Subtitle -->
                    <p class="font-script text-3xl md:text-5xl text-accent/90 mb-2 drop-shadow-lg" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                        Heritage & Traditions
                    </p>
                    
                    <!-- Main Title -->
                    <h1 class="font-playfair text-5xl md:text-7xl lg:text-8xl font-bold text-white leading-tight drop-shadow-2xl tracking-wide">
                        {{ $culture->name }}
                    </h1>

                    <!-- Divider -->
                    <div class="w-24 h-1 bg-accent mx-auto rounded-full shadow-lg"></div>

                    <!-- Location & Short Desc -->
                    <div class="flex flex-col items-center gap-3 text-white/90">
                        <div class="flex items-center gap-2 text-lg font-medium bg-black/30 backdrop-blur-sm px-4 py-1 rounded-full border border-white/10">
                            <span class="material-symbols-outlined text-accent">location_on</span>
                            <span>{{ $culture->location }}</span>
                        </div>
                        <p class="text-lg md:text-2xl font-light max-w-2xl mx-auto leading-relaxed drop-shadow-md font-serif italic text-white/80">
                             {{ $culture->description }}
                        </p>
                    </div>

                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 animate-bounce cursor-pointer opacity-80 hover:opacity-100 transition-opacity" @click="document.getElementById('content').scrollIntoView({behavior: 'smooth'})">
                <span class="material-symbols-outlined text-white text-4xl drop-shadow-lg">keyboard_arrow_down</span>
            </div>
        </div>

        <!-- Main Content Section (Floating Card) -->
        <div id="content" class="relative z-30 -mt-20 px-4 sm:px-6 lg:px-8 pb-20">
             <div class="max-w-6xl mx-auto bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-t-[3rem] shadow-2xl border-t border-white/20 p-8 md:p-16">
                 
                 <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
                     
                     <!-- Left Column: Main Article (8 cols) -->
                     <div class="md:col-span-8">
                         <!-- Section Header -->
                         <div class="flex flex-col items-start mb-8">
                             <div class="flex items-center gap-4 mb-4">
                                 <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                                     <span class="material-symbols-outlined text-2xl">menu_book</span>
                                 </div>
                                 <h2 class="font-playfair text-3xl font-bold text-slate-800 dark:text-white">
                                     Tentang Tradisi
                                 </h2>
                             </div>
                             <div class="w-20 h-1 bg-accent rounded-full"></div>
                         </div>

                         <!-- Article Content -->
                         <div class="prose prose-lg prose-slate dark:prose-invert max-w-none font-light leading-loose text-slate-600 dark:text-slate-300 first-letter:float-left first-letter:text-6xl first-letter:pr-3 first-letter:font-playfair first-letter:text-accent first-letter:font-bold">
                             <p class="whitespace-pre-line text-justify">{{ $culture->full_description ?? $culture->description }}</p>
                         </div>
                     </div>

                     <!-- Right Column: Info Sidebar (4 cols) -->
                     <div class="md:col-span-4 space-y-8">
                         <!-- Info Card -->
                         <div class="bg-gray-50 dark:bg-slate-700/50 rounded-3xl p-8 border border-gray-100 dark:border-slate-600 sticky top-32">
                             <h3 class="font-playfair text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2 pb-4 border-b border-gray-200 dark:border-slate-600">
                                 <span class="material-symbols-outlined text-accent">info</span> Informasi Utama
                             </h3>

                             <div class="space-y-6">
                                  <!-- Time/Event -->
                                 <div class="group">
                                     <div class="flex items-center gap-3 mb-2 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                         <span class="material-symbols-outlined text-sm">event</span>
                                         Waktu Pelaksanaan
                                     </div>
                                     <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-600 shadow-sm group-hover:border-accent/50 transition-colors">
                                         <p class="text-accent font-bold text-sm whitespace-pre-line leading-relaxed">
                                             {{ $culture->highlight }}
                                         </p>
                                     </div>
                                 </div>
                                 
                                 <!-- Location Detail -->
                                 <div>
                                     <div class="flex items-center gap-3 mb-2 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                         <span class="material-symbols-outlined text-sm">map</span>
                                         Lokasi
                                     </div>
                                     <p class="text-slate-700 dark:text-slate-200 font-medium pl-1">
                                         {{ $culture->location }}
                                     </p>
                                 </div>
                             </div>
                         </div>
                     </div>

                 </div>

                 <!-- Navigation Footer -->
                 <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-700/50 flex flex-col md:flex-row items-center justify-between gap-6">
                     <a href="{{ route('welcome') }}#culture" class="group inline-flex items-center gap-3 px-6 py-3 rounded-full bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all duration-300">
                        <span class="w-8 h-8 rounded-full bg-white dark:bg-slate-600 flex items-center justify-center shadow-sm group-hover:-translate-x-1 transition-transform">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                        </span>
                        <span class="font-medium tracking-wide">Kembali ke Beranda</span>
                     </a>
                     
                     <div class="flex items-center gap-4">
                         <span class="text-sm font-serif italic text-slate-400">Bagikan:</span>
                         <x-share-modal :url="request()->url()" :title="$culture->name" :text="Str::limit(strip_tags($culture->description), 100)">
                            <button class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all group" title="Bagikan Budaya Ini">
                                <i class="fa-solid fa-share-nodes text-lg group-hover:rotate-12 transition-transform"></i>
                                <span>Bagikan</span>
                            </button>
                         </x-share-modal>
                     </div>
                 </div>

             </div>
        </div>
    </div>
</x-public-layout>
