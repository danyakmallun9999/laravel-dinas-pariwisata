<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen">
        
        <!-- Immersive Hero Section -->
        <div class="relative h-[60vh] md:h-[70vh] w-full overflow-hidden group rounded-b-[2.5rem] md:rounded-b-[4rem] shadow-2xl z-20">
            <img src="{{ asset($culinary->image) }}" alt="{{ $culinary->name }}" class="w-full h-full object-cover attachment-fixed transform scale-105 group-hover:scale-100 transition-transform duration-[3s] ease-out">
            
            <!-- Cinematic Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-black/20 opacity-90"></div>

            <!-- Hero Content (Centered) -->
            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center z-10">
                <div class="max-w-4xl mx-auto space-y-6 md:space-y-8 animate-fade-in-up mt-10 md:mt-0">
                    
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center justify-center gap-3 md:gap-4">
                        <span class="px-4 py-1.5 md:px-5 md:py-2 rounded-full bg-orange-600/90 backdrop-blur-md border border-white/20 text-white text-xs md:text-sm font-bold tracking-widest uppercase shadow-xl hover:bg-orange-600 transition-colors">
                            Kuliner Khas
                        </span>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="font-display text-4xl sm:text-5xl md:text-7xl lg:text-7xl font-black text-white leading-tight drop-shadow-2xl tracking-tight px-4">
                        {{ $culinary->name }}
                    </h1>

                    <!-- Location / Short Desc -->
                    <div class="flex items-center justify-center gap-2 text-white/80 font-medium text-base md:text-xl max-w-2xl mx-auto">
                        <p class="text-center line-clamp-2">{{ $culinary->description }}</p>
                    </div>

                </div>
            </div>
            
            <!-- Scroll Indicator -->
            <div class="absolute bottom-6 md:bottom-10 left-1/2 -translate-x-1/2 z-20 animate-bounce cursor-pointer opacity-70 hover:opacity-100 transition-opacity" @click="document.getElementById('content').scrollIntoView({behavior: 'smooth'})">
                <span class="material-symbols-outlined text-white text-3xl md:text-4xl drop-shadow-lg">keyboard_arrow_down</span>
            </div>
        </div>

        <!-- Main Content Section -->
        <div id="content" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10 text-center">
             
             <!-- About Section -->
             <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 md:p-12 shadow-xl border border-slate-100 dark:border-slate-700/50">
                 <div class="flex flex-col items-center gap-4 mb-8">
                     <div class="w-16 h-16 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                         <span class="material-symbols-outlined text-3xl">restaurant</span>
                     </div>
                     <h2 class="text-3xl font-display font-bold text-slate-800 dark:text-white">Tentang Hidangan</h2>
                     <div class="w-24 h-1 bg-orange-500 rounded-full"></div>
                 </div>
                 
                 <div class="prose prose-lg prose-slate dark:prose-invert max-w-none font-light leading-relaxed text-left md:text-justify">
                     <p class="whitespace-pre-line">{{ $culinary->full_description ?? $culinary->description }}</p>
                 </div>
             </div>

            <!-- Navigation Back -->
            <div class="mt-12 text-center">
                 <a href="{{ route('welcome') }}#culinary" class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold transition-all shadow-sm hover:shadow-md">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke Beranda
                 </a>
            </div>

        </div>
    </div>
</x-public-layout>
