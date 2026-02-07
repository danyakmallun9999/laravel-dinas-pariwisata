    <!-- SECTION: Profile -->
    <div class="w-full bg-white dark:bg-gray-950 py-16 md:py-24 lg:py-32 relative" id="profile">
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16 lg:gap-32 items-start">
                
                <!-- Left Column: Pure Content -->
                <div class="profile-content order-2 lg:order-1 pt-0 md:pt-8 opacity-0 -translate-x-12">
                    
                    <div class="space-y-8 md:space-y-10">
                        
                        <!-- Minimal Label -->
                        <span class="block text-[10px] md:text-xs font-bold uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500">{{ __('Profile.Label') }}</span>
                        <!-- Typography -->
                        <h2 class="text-3xl md:text-5xl lg:text-6xl font-poppins font-bold text-gray-900 dark:text-white leading-[1.2] md:leading-[1.1]">
                            {!! __('Profile.Title') !!}
                        </h2>
                        <p class="text-base md:text-lg text-gray-600 dark:text-gray-400 leading-relaxed font-light max-w-md">
                            {{ __('Profile.Description') }}
                        </p>
                        <!-- Key Highlights (Pillars) -->
                        <div class="pt-6 md:pt-8 mt-6 md:mt-8 border-t border-gray-100 dark:border-gray-800">
                            <div class="grid grid-cols-3 gap-4 md:gap-6">
                                <div>
                                    <span class="block text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 md:mb-2">{{ __('Profile.Pillars.Nature.Title') }}</span>
                                    <p class="font-serif text-sm md:text-lg text-gray-900 dark:text-white leading-tight">{!! __('Profile.Pillars.Nature.Desc') !!}</p>
                                </div>
                                <div>
                                    <span class="block text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 md:mb-2">{{ __('Profile.Pillars.Heritage.Title') }}</span>
                                    <p class="font-serif text-sm md:text-lg text-gray-900 dark:text-white leading-tight">{!! __('Profile.Pillars.Heritage.Desc') !!}</p>
                                </div>
                                <div>
                                    <span class="block text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 md:mb-2">{{ __('Profile.Pillars.Arts.Title') }}</span>
                                    <p class="font-serif text-sm md:text-lg text-gray-900 dark:text-white leading-tight">{!! __('Profile.Pillars.Arts.Desc') !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column: Clean Visuals -->
                <div class="relative order-1 lg:order-2">
                    
                    <!-- Main Image (Clean Crop) -->
                    <div class="profile-image relative w-full aspect-[4/3] md:aspect-[3/4] overflow-hidden bg-gray-100 dark:bg-gray-900 rounded-[2.5rem] opacity-0 translate-x-12"
                         x-data="{ hover: false }" 
                         @mouseenter="hover = true" 
                         @mouseleave="hover = false">
                        <img src="{{ asset('images/profile/section-2.jpg') }}" 
                             alt="Landscape Jepara" 
                             class="w-full h-full object-cover transition-all duration-1000 ease-out"
                             :class="hover ? 'scale-105' : ''">
                    </div>
                    <!-- Secondary Image (Smaller, Clean Overlay) -->
                    <div class="absolute bottom-6 -left-6 md:bottom-8 md:-left-12 w-32 md:w-48 lg:w-64 aspect-square overflow-hidden rounded-[1.5rem] shadow-2xl border-4 md:border-8 border-white dark:border-gray-950 transition-transform duration-700 ease-out hidden sm:block"
                         :class="hover ? 'translate-x-4 -translate-y-4' : ''">
                        <img src="{{ asset('images/profile/diving-karimunjawa.jpg') }}" 
                             alt="Diving" 
                             class="w-full h-full object-cover">
                    </div>

                    <!-- Minimal Stats (Absolute, no glassmorphism, just solid) -->
                    <div class="absolute top-8 -right-4 bg-white dark:bg-gray-800 p-6 rounded-[2rem] shadow-xl hidden lg:block">
                        <span class="block text-4xl font-serif text-gray-900 dark:text-white">{{ $countDestinasi }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ __('Nav.Destinations') }}</span>
                    </div>

                </div>

            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.registerPlugin(ScrollTrigger);

            // Left Column (Content) Slide In
            gsap.to(".profile-content", {
                scrollTrigger: {
                    trigger: ".profile-content",
                    start: "top 80%",
                    toggleActions: "play none none reverse"
                },
                x: 0,
                opacity: 1,
                duration: 1,
                ease: "power2.out"
            });

            // Right Column (Image) Slide In
            gsap.to(".profile-image", {
                scrollTrigger: {
                    trigger: ".profile-image",
                    start: "top 80%",
                    toggleActions: "play none none reverse"
                },
                x: 0,
                opacity: 1,
                duration: 1,
                delay: 0.2, // Slight delay for staggering
                ease: "power2.out"
            });
        });
    </script>
    <!-- END SECTION: Profile -->
