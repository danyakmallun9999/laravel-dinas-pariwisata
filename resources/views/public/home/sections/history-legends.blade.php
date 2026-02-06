    <!-- SECTION: History -->
    <div class="relative w-full bg-white dark:bg-gray-900 overflow-hidden py-24" id="history">
        <!-- Background Decor -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-700 to-transparent"></div>
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16" 
                 x-data="{ shown: false }" 
                 x-intersect.threshold.0.5="shown = true">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text-light dark:text-text-dark mb-6 opacity-0 translate-y-4 transition-all duration-700 delay-100"
                    :class="shown ? 'opacity-100 translate-y-0' : ''">
                    {{ __('History.Title') }}
                </h2>
                <p class="text-text-light/70 dark:text-text-dark/70 max-w-2xl mx-auto text-lg opacity-0 translate-y-4 transition-all duration-700 delay-200"
                    :class="shown ? 'opacity-100 translate-y-0' : ''">
                    {{ __('History.Subtitle') }}
                </p>
            </div>

            <!-- Full Image Cards Grid -->
            <div class="flex md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 overflow-x-auto md:overflow-visible snap-x snap-mandatory pb-8 md:pb-0 px-4 md:px-0 -mx-4 md:mx-0 scrollbar-hide touch-pan-x">
                
                <!-- Shima Card (Kalingga - Oldest) -->
                <div class="min-w-[85%] md:min-w-0 snap-center group relative h-[450px] md:h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true"
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12 transition-all duration-1000'">
                    
                    <!-- Full Background Image -->
                    <img src="{{ asset('images/legenda/shima.jpg') }}" 
                         alt="Ratu Shima" 
                         class="absolute top-0 left-0 w-full h-full object-cover object-top origin-top filter grayscale-[0.2] md:group-hover:grayscale-0 md:group-hover:scale-105 transition-all duration-[1500ms] ease-out will-change-transform pointer-events-none select-none">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 md:group-hover:opacity-60 transition-opacity duration-700"></div>

                    <!-- Content Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 text-white transform translate-y-4 md:group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full md:group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">Ratu Shima</h3>
                        <p class="text-xl font-['Pinyon_Script'] text-white/90 mb-6">
                            {!! __('History.Shima.Quote') !!}
                        </p>
                        <div class="h-0 md:group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 md:group-hover:opacity-100">
                            <p class="text-white/80 text-sm leading-relaxed">
                                {{ __('History.Shima.Desc') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kalinyamat Card (16th Century) -->
                <div class="min-w-[85%] md:min-w-0 snap-center group relative h-[450px] md:h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none transition-all duration-1000 delay-200"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true"
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
                    
                    <!-- Full Background Image -->
                    <img src="{{ asset('images/legenda/kalinyamat.jpg') }}" 
                         alt="Ratu Kalinyamat" 
                         class="absolute top-0 left-0 w-full h-full object-cover object-center origin-top filter grayscale-[0.2] md:group-hover:grayscale-0 md:group-hover:scale-105 transition-all duration-[1500ms] ease-out will-change-transform pointer-events-none select-none">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 md:group-hover:opacity-60 transition-opacity duration-700"></div>

                    <!-- Content Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 text-white transform translate-y-4 md:group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full md:group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">Ratu Kalinyamat</h3>
                        <p class="text-xl font-['Pinyon_Script'] text-white/90 mb-6">
                            {!! __('History.Kalinyamat.Quote') !!}
                        </p>
                        <div class="h-0 md:group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 md:group-hover:opacity-100">
                            <p class="text-white/80 text-sm leading-relaxed">
                                {{ __('History.Kalinyamat.Desc') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kartini Card (19th Century - Youngest) -->
                <div class="min-w-[85%] md:min-w-0 snap-center group relative h-[450px] md:h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none transition-all duration-1000 delay-400"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true"
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
                    
                    <!-- Full Background Image -->
                    <img src="{{ asset('images/legenda/kartini.jpg') }}" 
                         alt="R.A. Kartini" 
                         class="absolute top-0 left-0 w-full h-full object-cover object-top origin-top filter grayscale-[0.2] md:group-hover:grayscale-0 md:group-hover:scale-105 transition-all duration-[1500ms] ease-out will-change-transform pointer-events-none select-none">
                    
                    <!-- Gradient Overlay (Subtle) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 md:group-hover:opacity-60 transition-opacity duration-700"></div>

                    <!-- Content Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 text-white transform translate-y-4 md:group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full md:group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">R.A. Kartini</h3>
                        <p class="text-xl font-['Pinyon_Script'] text-white/90 mb-6">
                            {!! __('History.Kartini.Quote') !!}
                        </p>
                        <div class="h-0 md:group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 md:group-hover:opacity-100">
                            <p class="text-white/80 text-sm leading-relaxed">
                                {{ __('History.Kartini.Desc') }}
                            </p>
                        </div>
                    </div>
                </div>

                </div>
        </div>
    </div>
    <!-- END SECTION: History -->
