    <!-- SECTION: History -->
    <div class="relative w-full bg-white dark:bg-gray-900 overflow-hidden py-24" id="history">
        <!-- Background Decor -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-700 to-transparent"></div>
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16" 
                 x-data="{ shown: false }" 
                 x-intersect.threshold.0.5="shown = true">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text-light dark:text-text-dark mb-6"
                    x-show="shown"
                    x-transition:enter="transition ease-out duration-700 delay-100"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    {{ __('History.Title') }}
                </h2>
                <p class="text-text-light/70 dark:text-text-dark/70 max-w-2xl mx-auto text-lg"
                    x-show="shown"
                    x-transition:enter="transition ease-out duration-700 delay-200"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    {{ __('History.Subtitle') }}
                </p>
            </div>

            <!-- Full Image Cards Grid -->
            <div class="flex md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 overflow-x-auto md:overflow-visible snap-x snap-mandatory pb-8 md:pb-0 px-4 md:px-0 -mx-4 md:mx-0 scrollbar-hide"
                 x-data="{
                     touchStartX: 0,
                     touchStartY: 0,
                     scrolling: false,
                     direction: null
                 }"
                 @touchstart.passive="
                     touchStartX = $event.touches[0].clientX;
                     touchStartY = $event.touches[0].clientY;
                     scrolling = false;
                     direction = null;
                 "
                 @touchmove="
                     if (!scrolling) {
                         const deltaX = Math.abs($event.touches[0].clientX - touchStartX);
                         const deltaY = Math.abs($event.touches[0].clientY - touchStartY);
                         
                         if (deltaX > 10 || deltaY > 10) {
                             scrolling = true;
                             direction = deltaX > deltaY ? 'horizontal' : 'vertical';
                         }
                     }
                     
                     if (direction === 'horizontal') {
                         $event.stopPropagation();
                     }
                 ">
                
                @php
                    $legends = [
                        [
                            'name' => 'Ratu Shima',
                            'image' => 'images/legenda/shima.jpg',
                            'quote' => 'History.Shima.Quote',
                            'desc' => 'History.Shima.Desc',
                            'delay' => 0
                        ],
                        [
                            'name' => 'Ratu Kalinyamat',
                            'image' => 'images/legenda/kalinyamat.jpg',
                            'quote' => 'History.Kalinyamat.Quote',
                            'desc' => 'History.Kalinyamat.Desc',
                            'delay' => 200
                        ],
                        [
                            'name' => 'R.A. Kartini',
                            'image' => 'images/legenda/kartini.jpg',
                            'quote' => 'History.Kartini.Quote',
                            'desc' => 'History.Kartini.Desc',
                            'delay' => 400
                        ]
                    ];
                @endphp

                @foreach($legends as $legend)
                <div class="min-w-[85%] md:min-w-0 snap-center group relative h-[450px] md:h-[600px] w-full rounded-[2.5rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true">
                    
                    <div x-show="shown"
                         x-transition:enter="transition ease-out duration-1000 delay-[{{ $legend['delay'] }}ms]"
                         x-transition:enter-start="opacity-0 translate-y-12"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="w-full h-full">
                        
                        <!-- Full Background Image -->
                        <img src="{{ asset($legend['image']) }}" 
                             alt="{{ $legend['name'] }}" 
                             class="absolute top-0 left-0 w-full h-full object-cover object-top origin-top filter grayscale-0 lg:grayscale-[0.2] lg:group-hover:grayscale-0 lg:group-hover:scale-105 transition-all duration-[1500ms] ease-out will-change-transform pointer-events-none select-none">
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 lg:group-hover:opacity-60 transition-opacity duration-700"></div>

                        <!-- Content Overlay -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 text-white transform translate-y-0 lg:translate-y-4 lg:group-hover:translate-y-0 transition-transform duration-700">
                            <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full lg:group-hover:w-20 transition-all duration-500"></div>
                            <h3 class="text-3xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">{{ $legend['name'] }}</h3>
                            <p class="text-xl font-['Pinyon_Script'] text-white/90 mb-6">
                                {!! __($legend['quote']) !!}
                            </p>
                            <div class="h-auto opacity-100 lg:h-0 lg:opacity-0 lg:group-hover:h-auto overflow-hidden transition-all duration-500 lg:group-hover:opacity-100">
                                <p class="text-white/80 text-sm leading-relaxed">
                                    {{ __($legend['desc']) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
    <!-- END SECTION: History -->
