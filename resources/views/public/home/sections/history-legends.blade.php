    <!-- SECTION: History -->
    <div class="relative w-full bg-white dark:bg-gray-900 overflow-hidden py-16 md:py-24" id="history">
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-12 md:mb-16">
                <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-4 md:mb-6">
                    {{ __('History.Title') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto text-base md:text-lg">
                    {{ __('History.Subtitle') }}
                </p>
            </div>

            <!-- EXPANDING CARDS CONTAINER -->
            <!-- Alpine Data: activeCard tracks which card is expanded. Default to 'kalinyamat' (center) on desktop. -->
            <div class="flex flex-col md:flex-row gap-4 h-auto md:h-[600px] w-full select-none"
                 x-data="{ activeCard: 'kalinyamat' }">

                <!-- CARD 1: SHIMA -->
                <div class="group relative overflow-hidden rounded-2xl transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] w-full md:w-auto min-h-[400px] md:min-h-0"
                     :class="activeCard === 'shima' ? 'md:flex-[3]' : 'md:flex-[1]'"
                     @mouseenter="activeCard = 'shima'"
                     @click="activeCard = 'shima'">
                    
                    <!-- Background -->
                    <img src="{{ asset('images/legenda/shima.jpg') }}" 
                         alt="Ratu Shima" 
                         class="absolute inset-0 w-full h-full object-cover object-top transition-transform duration-1000"
                         :class="activeCard === 'shima' ? 'scale-100 grayscale-0' : 'scale-110 grayscale md:grayscale'">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-90 transition-opacity duration-500"
                         :class="activeCard === 'shima' ? 'opacity-80' : 'opacity-60 md:opacity-80'"></div>

                    <!-- Content -->
                    <div class="absolute bottom-0 left-0 w-full p-6 md:p-10 transition-all duration-500">
                        <div class="flex items-end gap-4 mb-2">
                             <h3 class="text-2xl md:text-4xl font-serif font-black text-white whitespace-nowrap transition-all duration-500"
                                :class="activeCard === 'shima' ? 'translate-y-0' : 'translate-y-0'">
                                Ratu Shima
                            </h3>
                            <div class="h-px bg-white/50 flex-1 mb-2 md:mb-3 origin-left transition-all duration-500"
                                 :class="activeCard === 'shima' ? 'scale-x-100 opacity-100' : 'scale-x-0 opacity-0'"></div>
                        </div>

                        <div class="overflow-hidden transition-all duration-700 ease-out"
                             :class="activeCard === 'shima' ? 'max-h-[500px] opacity-100' : 'max-h-0 md:max-h-0 opacity-100 md:opacity-0'">
                             <p class="text-lg md:text-xl font-serif italic text-white/90 mb-4 md:mb-6 pl-4 border-l-2 border-primary">
                                {!! __('History.Shima.Quote') !!}
                            </p>
                            <p class="text-white/80 text-sm md:text-base leading-relaxed max-w-xl">
                                {{ __('History.Shima.Desc') }}
                            </p>
                        </div>
                        
                        <!-- Collapsed Hint (Desktop Only) -->
                        <div class="hidden md:block absolute bottom-10 right-10 transition-all duration-500 delay-100"
                             :class="activeCard === 'shima' ? 'opacity-0 translate-y-4' : 'opacity-100 translate-y-0'">
                             <span class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center text-white">
                                <i class="fa-solid fa-plus"></i>
                             </span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: KALINYAMAT -->
                <div class="group relative overflow-hidden rounded-2xl transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] w-full md:w-auto min-h-[400px] md:min-h-0"
                     :class="activeCard === 'kalinyamat' ? 'md:flex-[3]' : 'md:flex-[1]'"
                     @mouseenter="activeCard = 'kalinyamat'"
                     @click="activeCard = 'kalinyamat'">
                    
                    <img src="{{ asset('images/legenda/kalinyamat.jpg') }}" 
                         alt="Ratu Kalinyamat" 
                         class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-1000"
                         :class="activeCard === 'kalinyamat' ? 'scale-100 grayscale-0' : 'scale-110 grayscale md:grayscale'">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-90 transition-opacity duration-500"
                         :class="activeCard === 'kalinyamat' ? 'opacity-80' : 'opacity-60 md:opacity-80'"></div>

                    <div class="absolute bottom-0 left-0 w-full p-6 md:p-10 transition-all duration-500">
                        <div class="flex items-end gap-4 mb-2">
                             <h3 class="text-2xl md:text-4xl font-serif font-black text-white whitespace-nowrap transition-all duration-500">
                                Ratu Kalinyamat
                            </h3>
                            <div class="h-px bg-white/50 flex-1 mb-2 md:mb-3 origin-left transition-all duration-500"
                                 :class="activeCard === 'kalinyamat' ? 'scale-x-100 opacity-100' : 'scale-x-0 opacity-0'"></div>
                        </div>

                        <div class="overflow-hidden transition-all duration-700 ease-out"
                             :class="activeCard === 'kalinyamat' ? 'max-h-[500px] opacity-100' : 'max-h-0 md:max-h-0 opacity-100 md:opacity-0'">
                             <p class="text-lg md:text-xl font-serif italic text-white/90 mb-4 md:mb-6 pl-4 border-l-2 border-primary">
                                {!! __('History.Kalinyamat.Quote') !!}
                            </p>
                            <p class="text-white/80 text-sm md:text-base leading-relaxed max-w-xl">
                                {{ __('History.Kalinyamat.Desc') }}
                            </p>
                        </div>
                        
                         <div class="hidden md:block absolute bottom-10 right-10 transition-all duration-500 delay-100"
                             :class="activeCard === 'kalinyamat' ? 'opacity-0 translate-y-4' : 'opacity-100 translate-y-0'">
                             <span class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center text-white">
                                <i class="fa-solid fa-plus"></i>
                             </span>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: KARTINI -->
                <div class="group relative overflow-hidden rounded-2xl transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] w-full md:w-auto min-h-[400px] md:min-h-0"
                     :class="activeCard === 'kartini' ? 'md:flex-[3]' : 'md:flex-[1]'"
                     @mouseenter="activeCard = 'kartini'"
                     @click="activeCard = 'kartini'">
                    
                    <img src="{{ asset('images/legenda/kartini.jpg') }}" 
                         alt="R.A. Kartini" 
                         class="absolute inset-0 w-full h-full object-cover object-top transition-transform duration-1000"
                         :class="activeCard === 'kartini' ? 'scale-100 grayscale-0' : 'scale-110 grayscale md:grayscale'">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-90 transition-opacity duration-500"
                         :class="activeCard === 'kartini' ? 'opacity-80' : 'opacity-60 md:opacity-80'"></div>

                    <div class="absolute bottom-0 left-0 w-full p-6 md:p-10 transition-all duration-500">
                        <div class="flex items-end gap-4 mb-2">
                             <h3 class="text-2xl md:text-4xl font-serif font-black text-white whitespace-nowrap transition-all duration-500">
                                R.A. Kartini
                            </h3>
                            <div class="h-px bg-white/50 flex-1 mb-2 md:mb-3 origin-left transition-all duration-500"
                                 :class="activeCard === 'kartini' ? 'scale-x-100 opacity-100' : 'scale-x-0 opacity-0'"></div>
                        </div>

                        <div class="overflow-hidden transition-all duration-700 ease-out"
                             :class="activeCard === 'kartini' ? 'max-h-[500px] opacity-100' : 'max-h-0 md:max-h-0 opacity-100 md:opacity-0'">
                             <p class="text-lg md:text-xl font-serif italic text-white/90 mb-4 md:mb-6 pl-4 border-l-2 border-primary">
                                {!! __('History.Kartini.Quote') !!}
                            </p>
                            <p class="text-white/80 text-sm md:text-base leading-relaxed max-w-xl">
                                {{ __('History.Kartini.Desc') }}
                            </p>
                        </div>
                        
                         <div class="hidden md:block absolute bottom-10 right-10 transition-all duration-500 delay-100"
                             :class="activeCard === 'kartini' ? 'opacity-0 translate-y-4' : 'opacity-100 translate-y-0'">
                             <span class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center text-white">
                                <i class="fa-solid fa-plus"></i>
                             </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
