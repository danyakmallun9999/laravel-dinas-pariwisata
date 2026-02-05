<x-public-layout>
    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans -mt-20 pt-20">
        
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row">
            
            <!-- Left Side: Sticky Visuals (50%) -->
            <div class="lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative bg-white dark:bg-slate-950 z-10 p-4 lg:pl-16 lg:pr-8 lg:pt-24 flex flex-col justify-start">
                 
                 <!-- Back Button (Separated) -->
                 <div class="mb-6">
                    <a href="{{ route('welcome') }}#culinary" class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/10 dark:bg-slate-800 text-primary dark:text-primary hover:bg-primary hover:text-white transition-all duration-300 shadow-sm border border-primary/20">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                    </a>
                 </div>

                 <!-- Image Card Wrapper -->
                 <div class="relative w-full max-w-2xl mx-auto aspect-[4/3] rounded-[2.5rem] overflow-hidden group">
                    <!-- Main Image with "Ken Burns" Effect -->
                    <img src="{{ asset($culinary->image) }}" alt="{{ $culinary->name }}" class="w-full h-full object-cover transform scale-100 group-hover:scale-110 transition-transform duration-[20s] ease-in-out will-change-transform">
                    
                    <!-- Cinematic Overlays -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/10 opacity-60 transition-opacity duration-700"></div>
                 </div>
            </div>

            <!-- Right Side: Scrollable Content (50%) -->
            <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                <main class="max-w-2xl mx-auto px-8 py-12 md:py-20 lg:px-16 lg:py-24">
                    
                    <!-- Header Section -->
                    <div class="mb-10 animate-fade-in-up">
                        <!-- Breadcrumbs / Badges -->
                        <div class="flex items-center gap-3 mb-4 text-sm">
                            <span class="px-3 py-1 rounded-full bg-primary/10 dark:bg-blue-900/30 text-primary dark:text-blue-400 font-bold uppercase tracking-wider text-xs border border-primary/20">
                                Kuliner Khas
                            </span>
                        </div>

                        <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-tight mb-4">
                            {{ $culinary->name }}
                        </h1>
                        
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                            <span class="material-symbols-outlined text-xl text-primary">restaurant_menu</span>
                            <span>Authentic Taste of Jepara</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="border-slate-100 dark:border-slate-800 mb-10">

                    <!-- Content Body -->
                    <div class="space-y-12">
                        
                        <!-- Description -->
                        <section>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="w-1 h-6 bg-primary rounded-full"></span>
                                Tentang Hidangan
                            </h3>
                            <div x-data="{ expanded: false }">
                                <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed text-justify transition-all duration-300"
                                     :class="expanded ? '' : 'line-clamp-4 mask-image-b'">
                                    <p class="whitespace-pre-line">{{ $culinary->full_description ?? $culinary->description }}</p>
                                </div>
                                @if(strlen($culinary->full_description ?? $culinary->description) > 300)
                                    <button @click="expanded = !expanded" 
                                            class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-primary dark:text-blue-400 hover:text-primary-dark dark:hover:text-blue-300 transition-colors">
                                        <span x-text="expanded ? 'Sembunyikan' : 'Baca Selengkapnya'"></span>
                                        <span class="material-symbols-outlined text-lg transition-transform duration-300" 
                                              :class="expanded ? 'rotate-180' : ''">expand_more</span>
                                    </button>
                                @endif
                            </div>
                        </section>

                        <!-- Highlights / Quick Info Grid -->
                        <div class="grid grid-cols-1 gap-4">
                            <div class="p-6 rounded-2xl bg-primary/5 dark:bg-blue-900/10 border border-primary/20 dark:border-blue-800/30">
                                <div class="text-primary text-xs font-bold uppercase tracking-wider mb-2">Rekomendasi Kami</div>
                                <p class="text-slate-800 dark:text-blue-100 font-medium text-sm italic">
                                    "{{ $culinary->description }}"
                                </p>
                            </div>
                        </div>

                        <!-- Location / Map Section -->
                        <section>
                             <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-8 border border-slate-100 dark:border-slate-800">
                                 <div class="flex items-center gap-3 mb-4">
                                     <div class="w-10 h-10 rounded-full bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400">
                                         <span class="material-symbols-outlined text-xl">storefront</span>
                                     </div>
                                     <div>
                                         <h3 class="font-bold text-slate-900 dark:text-white">Ingin Mencoba?</h3>
                                         <p class="text-slate-500 text-xs mt-0.5">Temukan {{ $culinary->name }} di sekitar Jepara</p>
                                     </div>
                                 </div>
                                 
                                 <!-- Embedded Map -->
                                 <div class="relative w-full h-[400px] md:h-auto md:aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm group">
                                     <iframe 
                                        width="100%" 
                                        height="100%" 
                                        frameborder="0" 
                                        scrolling="no" 
                                        marginheight="0" 
                                        marginwidth="0" 
                                        src="https://maps.google.com/maps?q=Kuliner+{{ urlencode($culinary->name) }}+Jepara&t=&z=13&ie=UTF8&iwloc=&output=embed">
                                     </iframe>
                                     
                                     <!-- Click Overlay to Open External Map -->
                                     <a href="https://www.google.com/maps/search/Kuliner+{{ urlencode($culinary->name) }}+Jepara" 
                                        target="_blank"
                                        class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center z-10"
                                        title="Buka di Google Maps">
                                         <div class="bg-white/90 backdrop-blur text-slate-900 px-4 py-2 rounded-full font-bold text-sm shadow-lg opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all flex items-center gap-2">
                                             <span class="material-symbols-outlined text-red-500">map</span>
                                             Buka di Google Maps
                                         </div>
                                     </a>
                                 </div>
                             </div>
                        </section>
                    </div>

                    <!-- Footer Area -->
                    <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
                        <span class="text-slate-400 text-sm font-serif italic">Bagikan kelezatan ini:</span>
                        <div class="flex gap-3">
                             <x-share-modal :url="request()->url()" :title="$culinary->name" :text="Str::limit(strip_tags($culinary->description), 100)">
                                <button class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold hover:bg-primary hover:text-white transition-all group" title="Bagikan Kuliner Ini">
                                    <i class="fa-solid fa-share-nodes text-lg group-hover:rotate-12 transition-transform"></i>
                                    <span>Bagikan</span>
                                </button>
                             </x-share-modal>
                        </div>
                    </div>

                </main>
            </div>

            </div>
        </div>
    </div>
</x-public-layout>
