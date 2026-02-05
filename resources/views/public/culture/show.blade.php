<x-public-layout>
    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans -mt-20 pt-20">
        
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12">
            
            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ route('welcome') }}#culture" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary transition-colors font-medium">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:bg-primary hover:text-white transition-all">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                    </div>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            <!-- Header Section -->
            <div class="text-center mb-10 animate-fade-in-up">
                <div class="inline-block px-4 py-1.5 rounded-full bg-primary/10 dark:bg-primary/20 text-primary dark:text-primary font-bold uppercase tracking-wider text-xs border border-primary/20 mb-6">
                    Mengenal Budaya
                </div>
                
                <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-tight mb-6">
                    {{ $culture->name }}
                </h1>
                
                <div class="flex items-center justify-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                    <span class="material-symbols-outlined text-xl text-primary">location_on</span>
                    <span>{{ $culture->location }}</span>
                </div>
            </div>

            <!-- Main Image -->
            <div class="w-full aspect-video rounded-[2.5rem] overflow-hidden shadow-2xl mb-12 animate-fade-in-up delay-100 relative group">
                <img src="{{ asset($culture->image) }}" alt="{{ $culture->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
            </div>

            <!-- Content Body -->
            <div class="animate-fade-in-up delay-200">
                
                <!-- Description -->
                <div class="prose prose-lg prose-slate dark:prose-invert max-w-none font-light text-slate-600 dark:text-slate-300 leading-relaxed text-left text-justify mb-12">
                    <p class="whitespace-pre-line">{{ $culture->full_description ?? $culture->description }}</p>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                    <!-- Time Card -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary dark:text-primary">
                            <span class="material-symbols-outlined text-2xl">event</span>
                        </div>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Waktu Pelaksanaan</div>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $culture->highlight }}</div>
                        </div>
                    </div>

                    <!-- Location Card -->
                    <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary dark:text-primary">
                            <span class="material-symbols-outlined text-2xl">map</span>
                        </div>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Lokasi Tradisi</div>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $culture->location }}</div>
                        </div>
                    </div>
                </div>

                <!-- Footer / Share -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-10 flex flex-col items-center justify-center gap-4 text-center">
                    <h3 class="font-playfair text-2xl font-bold text-slate-900 dark:text-white">Bagikan Cerita Ini</h3>
                    <p class="text-slate-500 max-w-md mx-auto mb-4">Bantu kami memperkenalkan kekayaan budaya {{ $culture->name }} kepada dunia.</p>
                    
                    <x-share-modal :url="request()->url()" :title="$culture->name" :text="Str::limit(strip_tags($culture->description), 100)">
                        <button class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-primary text-white font-bold hover:bg-primary-dark transition-all shadow-lg shadow-primary/25 hover:-translate-y-1 hover:shadow-primary/40 group">
                            <i class="fa-solid fa-share-nodes text-xl group-hover:rotate-12 transition-transform"></i>
                            <span>Bagikan Budaya</span>
                        </button>
                    </x-share-modal>
                </div>

            </div>

        </div>
    </div>
</x-public-layout>
