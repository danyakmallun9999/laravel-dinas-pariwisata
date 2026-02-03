<style>

</style>
<!-- Redesigned Footer with Photo Collage -->
<style>
    .text-outline {
        text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
    }
    .text-outline-sm {
        text-shadow: -0.5px -0.5px 0 #000, 0.5px -0.5px 0 #000, -0.5px 0.5px 0 #000, 0.5px 0.5px 0 #000;
    }
</style>
<footer class="relative bg-[#1a1c23] text-white pt-16 md:pt-24 pb-8 md:pb-12 overflow-hidden">
    <!-- Dynamic Photo Collage Background -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 h-full w-full gap-0.5 md:gap-2 p-0.5 md:p-2 transform scale-105 motion-safe:animate-[slow-pan_20s_ease-in-out_infinite] opacity-30 md:opacity-40">
            <!-- Item 1 (Large Square) -->
            <div class="relative overflow-hidden rounded-lg col-span-2 row-span-2 bg-gray-800">
                <img src="{{ asset('images/culture/barikan-kubro.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <!-- Item 2 (Small) -->
            <div class="relative overflow-hidden rounded-lg bg-gray-800">
                <img src="{{ asset('images/culture/festival-kupat-lepet.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <!-- Item 3 (Tall) -->
            <div class="relative overflow-hidden rounded-lg row-span-2 bg-gray-800">
                <img src="{{ asset('images/culture/jondang-kawak.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <!-- Item 4 (Wide - Original Footer Image) -->
            <div class="relative overflow-hidden rounded-lg col-span-2 bg-gray-800">
                <img src="{{ asset('images/footer/image.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <!-- Item 5 (Small) -->
            <div class="relative overflow-hidden rounded-lg bg-gray-800">
                <img src="{{ asset('images/culture/kirab-buka-luwur.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <!-- Item 6 (Wide) -->
            <div class="relative overflow-hidden rounded-lg col-span-2 bg-gray-800">
                <img src="{{ asset('images/culture/lomban.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <!-- Item 7 (Small) -->
            <div class="relative overflow-hidden rounded-lg bg-gray-800">
                <img src="{{ asset('images/culture/obor.png') }}" alt="" class="w-full h-full object-cover">
            </div>
        </div>
        
        <!-- Deep Dark Overlays -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-[#1a1c23]/70 to-[#1a1c23] z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#1a1c23] via-transparent to-[#1a1c23] z-10"></div>
    </div>

    <style>
        @keyframes slow-pan {
            0%, 100% { transform: scale(1.05) translate(0, 0); }
            50% { transform: scale(1.1) translate(-0.5%, -0.5%); }
        }
    </style>

    <!-- Background Decorative Elements -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-px bg-white/5 z-10"></div>
    
    <!-- Animated Background Orbs -->
    <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[120px] pointer-events-none z-10"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[30%] bg-blue-500/5 rounded-full blur-[100px] pointer-events-none z-10"></div>

    <div class="relative w-full mx-auto max-w-7xl px-6 md:px-10 z-20">
        <!-- Branding Section -->
        <div class="mb-12 md:mb-16 text-center">
            <div class="inline-flex flex-col mb-4 md:mb-6 w-full items-center">
                
                <!-- Main Branding -->
                <h2 class="text-3xl md:text-7xl font-bold tracking-tight leading-[0.9] md:leading-[0.8] uppercase mb-6 text-outline-sm md:text-outline drop-shadow-2xl">
                    Pemerintah <br class="hidden md:block">
                    Kabupaten <span class="text-primary">Jepara</span>
                </h2>

                <!-- Department Subtitle (Centered) -->
                <div class="flex flex-col items-center justify-center relative">
                    <div class="text-center">
                        <span class="block text-white/90 text-sm md:text-xl font-bold tracking-tight leading-tight uppercase font-heading">
                            {{ __('Footer.Department') }}
                        </span>
                        <span class="block text-white/40 text-[10px] md:text-sm font-medium tracking-wide mt-1">
                            {{ __('Footer.Subtitle') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 md:gap-12 mb-12 md:mb-20">
            <!-- Column 1: About (Always visible) -->
            <div class="space-y-6 text-center md:text-left">
                <p class="text-white/60 text-sm leading-relaxed max-w-xs mx-auto md:ml-0 font-medium">
                    {{ __('Footer.About') }}
                </p>
                <div class="flex items-center gap-4 justify-center md:justify-start">
                    <a href="#" aria-label="Facebook" class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary hover:scale-110 transition-all duration-300 group backdrop-blur-sm">
                        <i class="fa-brands fa-facebook-f text-white/70 group-hover:text-white text-sm"></i>
                    </a>
                    <a href="#" aria-label="Instagram" class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary hover:scale-110 transition-all duration-300 group backdrop-blur-sm">
                        <i class="fa-brands fa-instagram text-white/70 group-hover:text-white text-sm"></i>
                    </a>
                    <a href="#" aria-label="YouTube" class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary hover:scale-110 transition-all duration-300 group backdrop-blur-sm">
                        <i class="fa-brands fa-youtube text-white/70 group-hover:text-white text-sm"></i>
                    </a>
                    <a href="#" aria-label="Twitter" class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary hover:scale-110 transition-all duration-300 group backdrop-blur-sm">
                        <i class="fa-brands fa-twitter text-white/70 group-hover:text-white text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Column 2: Explore (Collapsible on Mobile) -->
            <div class="border-b border-white/5 md:border-none pb-4 md:pb-0">
                <button onclick="toggleFooterSection('explore')" class="w-full flex items-center justify-between md:cursor-default md:pointer-events-none group">
                    <h3 class="text-white text-sm md:text-lg font-extrabold relative inline-block tracking-tight">
                        {{ __('Footer.Section.Explore') }}
                        <span class="absolute -bottom-2 md:-bottom-2 left-0 w-8 h-1 bg-primary rounded-full hidden md:block"></span>
                    </h3>
                    <i id="icon-explore" class="fa-solid fa-chevron-down text-white/40 text-xs transition-transform duration-300 md:hidden"></i>
                </button>
                <ul id="content-explore" class="hidden md:block space-y-3 md:space-y-4 mt-4 md:mt-8 transition-all duration-300 origin-top">
                    <li><a href="{{ route('welcome') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> {{ __('Nav.Home') }}</a></li>
                    <li><a href="{{ route('places.index') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> {{ __('Nav.Destinations') }}</a></li>
                    <li><a href="{{ route('explore.map') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> {{ __('Nav.Map') }}</a></li>
                    <li><a href="{{ route('events.public.index') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> {{ __('Nav.Events') }}</a></li>
                    <li><a href="{{ route('posts.index') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> {{ __('Nav.News') }}</a></li>
                </ul>
            </div>

            <!-- Column 3: Categories (Collapsible on Mobile) -->
            <div class="border-b border-white/5 md:border-none pb-4 md:pb-0">
                <button onclick="toggleFooterSection('categories')" class="w-full flex items-center justify-between md:cursor-default md:pointer-events-none group">
                    <h3 class="text-white text-sm md:text-lg font-extrabold relative inline-block tracking-tight">
                        {{ __('Footer.Section.Categories') }}
                        <span class="absolute -bottom-2 md:-bottom-2 left-0 w-8 h-1 bg-primary rounded-full hidden md:block"></span>
                    </h3>
                    <i id="icon-categories" class="fa-solid fa-chevron-down text-white/40 text-xs transition-transform duration-300 md:hidden"></i>
                </button>
                <ul id="content-categories" class="hidden md:block space-y-3 md:space-y-4 mt-4 md:mt-8 transition-all duration-300 origin-top">
                    <li><a href="#" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Wisata Alam</a></li>
                    <li><a href="#" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Wisata Budaya</a></li>
                    <li><a href="#" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Wisata Kuliner</a></li>
                    <li><a href="#" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Wisata Religi</a></li>
                </ul>
            </div>

            <!-- Column 4: Contact (Collapsible on Mobile) -->
            <div class="border-b border-white/5 md:border-none pb-4 md:pb-0">
                <button onclick="toggleFooterSection('contact')" class="w-full flex items-center justify-between md:cursor-default md:pointer-events-none group">
                    <h3 class="text-white text-sm md:text-lg font-extrabold relative inline-block tracking-tight">
                        {{ __('Footer.Section.Contact') }}
                        <span class="absolute -bottom-2 md:-bottom-2 left-0 w-8 h-1 bg-primary rounded-full hidden md:block"></span>
                    </h3>
                    <i id="icon-contact" class="fa-solid fa-chevron-down text-white/40 text-xs transition-transform duration-300 md:hidden"></i>
                </button>
                <ul id="content-contact" class="hidden md:block space-y-4 md:space-y-5 mt-4 md:mt-8 transition-all duration-300 origin-top">
                    <li class="flex flex-col md:flex-row items-center md:items-start gap-2 md:gap-4 group leading-tight">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white/5 flex-shrink-0 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <i class="fa-solid fa-location-dot text-primary text-xs md:text-base"></i>
                        </div>
                        <span class="text-white/60 text-[11px] md:text-sm leading-tight group-hover:text-white/80 transition-colors font-medium">Jl. Abdul Rahman Hakim. No. 51, Kauman, Kec. Jepara, Kabupaten Jepara, Jawa Tengah 59417</span>
                    </li>
                    <li class="flex flex-col md:flex-row items-center md:items-start gap-2 md:gap-4 group leading-tight">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white/5 flex-shrink-0 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <i class="fa-solid fa-phone text-primary text-xs md:text-base"></i>
                        </div>
                        <span class="text-white/60 text-[11px] md:text-sm group-hover:text-white/80 transition-colors font-medium">(0291) 591219</span>
                    </li>
                    <li class="flex flex-col md:flex-row items-center md:items-start gap-2 md:gap-4 group leading-tight">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white/5 flex-shrink-0 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                            <i class="fa-solid fa-envelope text-primary text-xs md:text-base"></i>
                        </div>
                        <span class="text-white/60 text-[11px] md:text-sm group-hover:text-white/80 transition-colors font-medium">disparbud@jepara.go.id</span>
                    </li>
                </ul>
            </div>
        </div>

        <script>
            function toggleFooterSection(id) {
                const content = document.getElementById('content-' + id);
                const icon = document.getElementById('icon-' + id);
                
                if (window.innerWidth < 768) { // Only enable toggle on mobile
                    if (content.classList.contains('hidden')) {
                        content.classList.remove('hidden');
                        icon.classList.add('rotate-180');
                    } else {
                        content.classList.add('hidden');
                        icon.classList.remove('rotate-180');
                    }
                }
            }
        </script>

        <!-- Stamps & Partners -->
        <div class="pt-8 md:pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-8 md:gap-12">
            <div class="flex items-center gap-8 md:gap-12 grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                <img src="{{ asset('images/footer/wndrfl-indonesia2.png') }}" alt="Wonderful Indonesia" class="h-8 md:h-10 w-auto object-contain">
                <img src="{{ asset('images/footer/logo-jpr-psn.png') }}" alt="Jepara Mempesona" class="h-12 md:h-16 w-auto object-contain">
            </div>
            
            <div class="text-center md:text-right">
                <p class="text-[9px] md:text-[10px] text-white/30 uppercase tracking-[0.4em] mb-2 md:mb-4 font-bold">{{ __('Footer.Section.Official') }}</p>
                <p class="text-[10px] md:text-xs text-white/50 font-semibold tracking-tight">
                    &copy; 2024 <span class="text-white">{{ __('Footer.Department') }}</span>. <br class="md:hidden"> 
                    {{ __('Footer.Rights') }}
                </p>
            </div>
        </div>
    </div>
</footer>
