<footer class="bg-[#1a1c23] text-white/80 pt-16 pb-8 border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6 md:px-10">
        <!-- Top Section: Brand & Navigation -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-8 mb-16">
            
            <!-- Brand Column -->
            <div class="md:col-span-4 space-y-6">
                <a href="{{ route('welcome') }}" class="block">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Jepara" class="h-24 md:h-28 w-auto object-contain transition-transform hover:scale-105 duration-300">
                </a>
                <div class="space-y-4">
                    <p class="text-sm leading-relaxed text-white/50 max-w-sm">
                        {{ __('Footer.About') }}
                    </p>
                    
                    <!-- Simpler Social Icons -->
                    <div class="flex items-center gap-4">
                        <a href="https://www.facebook.com/disparbudjepara" target="_blank" class="text-white/40 hover:text-white transition-colors duration-300">
                            <i class="fa-brands fa-facebook-f text-lg"></i>
                        </a>
                        <a href="https://www.instagram.com/disparbudjepara" target="_blank" class="text-white/40 hover:text-white transition-colors duration-300">
                            <i class="fa-brands fa-instagram text-lg"></i>
                        </a>
                        <a href="https://www.youtube.com/@disparbud.jepara" target="_blank" class="text-white/40 hover:text-white transition-colors duration-300">
                            <i class="fa-brands fa-youtube text-lg"></i>
                        </a>
                        <a href="https://twitter.com/disparbudjepara" target="_blank" class="text-white/40 hover:text-white transition-colors duration-300">
                            <i class="fa-brands fa-twitter text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Links Column 1: Explore -->
            <div class="md:col-start-7 md:col-span-2">
                <h4 class="text-white font-medium text-sm mb-6">{{ __('Footer.Section.Explore') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('welcome') }}" class="text-white/50 hover:text-white transition-colors">{{ __('Nav.Home') }}</a></li>
                    <li><a href="{{ route('places.index') }}" class="text-white/50 hover:text-white transition-colors">{{ __('Nav.Destinations') }}</a></li>
                    <li><a href="{{ route('explore.map') }}" class="text-white/50 hover:text-white transition-colors">{{ __('Nav.Map') }}</a></li>
                    <li><a href="{{ route('events.public.index') }}" class="text-white/50 hover:text-white transition-colors">{{ __('Nav.Events') }}</a></li>
                    <li><a href="{{ route('posts.index') }}" class="text-white/50 hover:text-white transition-colors">{{ __('Nav.News') }}</a></li>
                </ul>
            </div>

            <!-- Links Column 2: Categories -->
            <div class="md:col-span-2">
                <h4 class="text-white font-medium text-sm mb-6">{{ __('Footer.Section.Categories') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="text-white/50 hover:text-white transition-colors">Wisata Alam</a></li>
                    <li><a href="#" class="text-white/50 hover:text-white transition-colors">Wisata Budaya</a></li>
                    <li><a href="#" class="text-white/50 hover:text-white transition-colors">Wisata Kuliner</a></li>
                    <li><a href="#" class="text-white/50 hover:text-white transition-colors">Wisata Religi</a></li>
                </ul>
            </div>

            <!-- Links Column 3: Contact -->
            <div class="md:col-span-2">
                <h4 class="text-white font-medium text-sm mb-6">{{ __('Footer.Section.Contact') }}</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex gap-3 text-white/50">
                        <i class="fa-solid fa-location-dot mt-1 text-white/30"></i>
                        <span>Jl. Abdul Rahman Hakim. No. 51, Jepara</span>
                    </li>
                    <li class="flex gap-3 text-white/50">
                        <i class="fa-solid fa-phone mt-1 text-white/30"></i>
                        <span>(0291) 591219</span>
                    </li>
                    <li class="flex gap-3 text-white/50">
                        <i class="fa-solid fa-envelope mt-1 text-white/30"></i>
                        <span>disparbud@jepara.go.id</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
            <!-- Copyright -->
            <p class="text-xs text-white/30">
                &copy; <span id="current-year"></span> {{ __('Footer.Department') }}. {{ __('Footer.Rights') }}
            </p>

            <!-- Partners (Original Colors) -->
            <div class="flex items-center gap-6">
                <img src="{{ asset('images/footer/wndrfl-indonesia2.png') }}" alt="Wonderful Indonesia" class="h-6 w-auto mobile-balance hover:scale-110 transition-transform duration-300">
                <img src="{{ asset('images/footer/logo-jpr-psn.png') }}" alt="Jepara Mempesona" class="h-8 w-auto mobile-balance hover:scale-110 transition-transform duration-300">
            </div>
        </div>
    </div>

    <script>
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
</footer>
