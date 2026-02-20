    <!-- SECTION: Culture -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-20 lg:py-28 overflow-hidden relative" id="culture" x-data="cultureSection">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5 dark:opacity-10 pointer-events-none mix-blend-multiply dark:mix-blend-soft-light saturate-0">
            <img src="{{ asset('images/culture/tenun-troso.png') }}" alt="Motif Tenun Troso" class="w-full h-full object-cover">
        </div>
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="text-center mb-16 culture-header opacity-0 translate-y-8">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-poppins font-bold text-text-light dark:text-text-dark mb-6">
                    {{ __('Culture.Title') }}
                </h2>
                <p class="text-text-light/70 dark:text-text-dark/70 max-w-2xl mx-auto text-lg">
                    {{ __('Culture.Subtitle') }}
                </p>
            </div>

            <!-- Horizontal Accordion -->
            <div class="flex flex-col md:flex-row h-[700px] md:h-[600px] w-full gap-4">
                @foreach($cultures as $index => $culture)
                <div class="relative rounded-[2.5rem] overflow-hidden cursor-pointer transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] group shadow-2xl border border-white/5"
                     :class="active === {{ $index }} ? 'flex-[10] md:flex-[5] opacity-100' : 'flex-[2] md:flex-[1] opacity-70 hover:opacity-100'"
                     @click="setActive({{ $index }})">
                    
                    <!-- Background Image (Refactored to img tag for reliability) -->
                    <img src="{{ asset($culture->image) }}" 
                         alt="{{ $culture->name }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000"
                         :class="active === {{ $index }} ? 'scale-100' : 'scale-110 group-hover:scale-105'">
                    
                    <!-- Fallback Background -->
                    <div class="absolute inset-0 bg-gray-800 -z-10"></div>

                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 transition-opacity duration-500"
                         :class="active === {{ $index }} ? 'bg-gradient-to-t from-black/90 via-black/40 to-transparent' : 'bg-black/60 group-hover:bg-black/40'"></div>

                    <!-- Inactive State Content (Horizontal Text) -->
                    <div class="absolute inset-0 flex items-end justify-center pb-8 transition-opacity duration-500"
                         :class="active === {{ $index }} ? 'opacity-0 pointer-events-none' : 'opacity-100'">
                        <h3 class="text-white font-bold tracking-widest uppercase text-lg md:text-xl drop-shadow-lg text-center px-2">
                            {{ $culture->name }}
                        </h3>
                    </div>

                    <!-- Active State Content -->
                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12 transition-all duration-700 transform"
                         :class="active === {{ $index }} ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                        
                        <div class="flex flex-col items-start max-w-2xl">
                            <span class="inline-block px-3 py-1 rounded-full bg-primary/90 text-white text-[10px] md:text-xs font-bold mb-3 md:mb-4 backdrop-blur-sm shadow-lg">
                                {{ $culture->location }}
                            </span>
                            <h3 class="text-2xl md:text-5xl font-bold text-white mb-2 md:mb-4 leading-tight drop-shadow-md">
                                {{ $culture->name }}
                            </h3>
                            <p class="text-gray-200 text-sm md:text-xl line-clamp-3 mb-4 md:mb-6 leading-relaxed">
                                {{ $culture->description }}
                            </p>
                            <div class="flex items-center gap-4 text-xs md:text-sm font-medium text-blue-300">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base md:text-lg">calendar_month</span>
                                    {{ $culture->highlight }}
                                </span>
                            </div>
                            
                            <!-- Link to Detail Page -->
                            <a href="{{ route('culture.show', $culture->slug) }}" class="mt-4 inline-flex items-center gap-2 px-6 py-2 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-white text-sm font-bold transition-all shadow-lg hover:translate-x-1 pointer-events-auto z-10">
                                <span>{{ __('Culture.Button.More') }}</span>
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- View All Button -->
            <div class="mt-12 text-center">
                <a href="{{ route('culture.index') }}" class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-primary hover:bg-primary/90 text-white font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                    <span>{{ __('Culture.Button.ViewAll') ?? 'Lihat Semua Budaya' }}</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.registerPlugin(ScrollTrigger);

            gsap.to(".culture-header", {
                scrollTrigger: {
                    trigger: ".culture-header",
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                },
                y: 0,
                opacity: 1,
                duration: 1,
                ease: "power2.out"
            });
        });
    </script>
    <!-- END SECTION: Culture -->
