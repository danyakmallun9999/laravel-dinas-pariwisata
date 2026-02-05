<x-public-layout>
    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans">
        
        <div class="flex flex-col lg:flex-row">
            
            <!-- Left Side: Sticky Visuals (50%) -->
            <div class="lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative h-[50vh] bg-slate-50 dark:bg-slate-950 z-10 p-4 lg:p-8 flex flex-col justify-center">
                 <!-- Image Card Wrapper -->
                 <div class="relative w-full h-full rounded-[2.5rem] overflow-hidden shadow-2xl shadow-slate-200/50 dark:shadow-black/50 group">
                    <!-- Main Image with "Ken Burns" Effect -->
                    <img src="{{ asset($culinary->image) }}" alt="{{ $culinary->name }}" class="w-full h-full object-cover transform scale-100 group-hover:scale-110 transition-transform duration-[20s] ease-in-out will-change-transform">
                    
                    <!-- Cinematic Overlays -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-80 lg:opacity-60 transition-opacity duration-700"></div>
                    <div class="absolute inset-0 bg-black/10 mix-blend-overlay"></div>

                    <!-- Floating Title (Visible only on desktop when stickied) -->
                    <div class="hidden lg:block absolute bottom-12 left-12 right-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-700 delay-100 translate-y-4 group-hover:translate-y-0 text-shadow-lg">
                         <p class="font-script text-3xl text-orange-300 mb-2">Taste of Jepara</p>
                         <h2 class="font-playfair text-5xl font-bold leading-tight">{{ $culinary->name }}</h2>
                    </div>

                    <!-- Back Button (Floating) -->
                    <a href="{{ route('welcome') }}#culinary" class="absolute top-8 left-8 z-20 w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-slate-900 transition-all duration-300 group-hover:scale-110">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                    </a>
                 </div>
            </div>

            <!-- Right Side: Scrollable Content (50%) -->
            <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                <main class="max-w-2xl mx-auto px-8 py-12 md:py-20 lg:px-16 lg:py-24">
                    
                    <!-- Header Section -->
                    <div class="mb-10 animate-fade-in-up">
                        <!-- Breadcrumbs / Badges -->
                        <div class="flex items-center gap-3 mb-4 text-sm">
                            <span class="px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider text-xs">
                                Kuliner Khas
                            </span>
                        </div>

                        <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-tight mb-4">
                            {{ $culinary->name }}
                        </h1>
                        
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                            <span class="material-symbols-outlined text-xl text-orange-500">restaurant_menu</span>
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
                                <span class="w-1 h-6 bg-orange-500 rounded-full"></span>
                                Tentang Hidangan
                            </h3>
                            <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed text-justify">
                                <p class="whitespace-pre-line">{{ $culinary->full_description ?? $culinary->description }}</p>
                            </div>
                        </section>

                        <!-- Highlights / Quick Info Grid -->
                        <div class="grid grid-cols-1 gap-4">
                            <div class="p-6 rounded-2xl bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800/30">
                                <div class="text-orange-400 text-xs font-bold uppercase tracking-wider mb-2">Rekomendasi Kami</div>
                                <p class="text-orange-900 dark:text-orange-100 font-medium text-sm italic">
                                    "{{ $culinary->description }}"
                                </p>
                            </div>
                        </div>

                        <!-- Location / Map Section -->
                        <section>
                             <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-8 border border-slate-100 dark:border-slate-800">
                                 <div class="flex items-center gap-3 mb-4">
                                     <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                                         <span class="material-symbols-outlined text-xl">storefront</span>
                                     </div>
                                     <div>
                                         <h3 class="font-bold text-slate-900 dark:text-white">Ingin Mencoba?</h3>
                                         <p class="text-slate-500 text-xs mt-0.5">Temukan {{ $culinary->name }} di sekitar Jepara</p>
                                     </div>
                                 </div>
                                 
                                 <!-- Embedded Map -->
                                 <div class="relative w-full aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm group">
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
                             <!-- WhatsApp -->
                             <a href="https://wa.me/?text={{ urlencode('Ayo coba ' . $culinary->name . ' khas Jepara! Cek detailnya: ' . request()->url()) }}" target="_blank" class="w-10 h-10 rounded-full bg-[#25D366]/10 text-[#25D366] border border-[#25D366]/20 flex items-center justify-center hover:bg-[#25D366] hover:text-white transition-all" title="Share via WhatsApp">
                                 <i class="fa-brands fa-whatsapp text-lg"></i>
                             </a>
                             <!-- Facebook -->
                             <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="w-10 h-10 rounded-full bg-[#1877F2]/10 text-[#1877F2] border border-[#1877F2]/20 flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition-all" title="Share via Facebook">
                                 <i class="fa-brands fa-facebook-f text-lg"></i>
                             </a>
                             <!-- Copy Link -->
                             <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link disalin!');" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 transition-all" title="Copy Link">
                                 <span class="material-symbols-outlined text-lg">link</span>
                             </button>
                        </div>
                    </div>

                </main>
            </div>

        </div>
    </div>
</x-public-layout>
