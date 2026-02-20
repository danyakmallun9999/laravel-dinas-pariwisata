<x-public-layout>
    @push('seo')
        <x-seo 
            :title="$culture->name . ' - Budaya Jepara'"
            :description="Str::limit(strip_tags($culture->full_description ?? $culture->description), 150)"
            :image="$culture->image_url ? $culture->image_url : asset('images/logo-kura.png')"
            type="article"
        />
    @endpush
    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans -mt-20 pt-20">
        
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12 stagger-entry">
            
            <!-- Breadcrumbs -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('welcome') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-lg mr-1">home</span>
                                Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                <a href="{{ route('culture.index') }}" wire:navigate class="text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                    Budaya
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                <span class="text-sm font-medium text-slate-900 dark:text-white line-clamp-1 max-w-[150px] md:max-w-xs">
                                    {{ $culture->name }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Header Section -->
            <div class="text-center mb-10">
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
            <div class="w-full aspect-video rounded-[2.5rem] overflow-hidden shadow-2xl mb-12 relative group">
                <img src="{{ $culture->image_url }}" alt="{{ $culture->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>
            </div>

            <!-- Content Body -->
            <div>
                
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
                <div class="border-t border-slate-100 dark:border-slate-800 pt-8 flex items-center justify-between">
                    <span class="text-slate-500 text-sm font-medium">Bagikan cerita ini</span>
                    
                    <x-share-modal :url="request()->url()" :title="$culture->name" :text="Str::limit(strip_tags($culture->description), 100)">
                        <button class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all duration-300" title="Bagikan">
                            <i class="fa-solid fa-share-nodes text-sm"></i>
                        </button>
                    </x-share-modal>
                </div>

            </div>

        </div>
    </div>
</x-public-layout>
