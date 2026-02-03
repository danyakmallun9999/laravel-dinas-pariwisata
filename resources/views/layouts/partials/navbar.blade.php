<div class="fixed top-0 left-0 right-0 z-[10000] w-full border-b border-surface-light dark:border-surface-dark bg-background-light/95 dark:bg-background-dark/95 backdrop-blur-md">
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
        <header class="flex h-20 items-center justify-between gap-8" x-data="{ mobileMenuOpen: false }">
            <div class="flex items-center gap-8">
                <a class="flex items-center gap-3 text-text-light dark:text-text-dark group" href="{{ route('welcome') }}">
                    <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" alt="Logo Kabupaten Jepara"
                        class="w-10 h-auto object-contain">
                    <h2 class="text-xl font-bold leading-tight tracking-tight">Blusukan Jepara</h2>
                </a>
                <nav class="hidden lg:flex items-center gap-8">
                    <a class="text-sm font-medium transition-colors hover:underline underline-offset-4 {{ request()->routeIs('welcome') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                       href="{{ route('welcome') }}">{{ __('Nav.Home') }}</a>
                       
                    <a class="text-sm font-medium transition-colors hover:underline underline-offset-4 {{ request()->routeIs('explore.map') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                       href="{{ route('explore.map') }}">{{ __('Nav.Map') }}</a>
                       
                    <a class="text-sm font-medium transition-colors hover:underline underline-offset-4 {{ request()->routeIs('places.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                       href="{{ route('places.index') }}">{{ __('Nav.Destinations') }}</a>
                       
                    <a class="text-sm font-medium transition-colors hover:underline underline-offset-4 {{ request()->routeIs('events.public.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                       href="{{ route('events.public.index') }}">{{ __('Nav.Events') }}</a>
                       
                    <a class="text-sm font-medium transition-colors hover:underline underline-offset-4 {{ request()->routeIs('posts.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                       href="{{ route('posts.index') }}">{{ __('Nav.News') }}</a>
                </nav>
            </div>

            <div class="flex flex-1 items-center justify-end gap-4">
                <!-- Search Bar (Only on Welcome Page where JS exists) -->
                @if(request()->routeIs('welcome'))
                <label class="hidden md:flex flex-col w-full max-w-xs h-10 relative">
                    <div
                        class="flex w-full h-full items-center rounded-full bg-surface-light dark:bg-surface-dark px-4 transition-colors focus-within:ring-2 focus-within:ring-primary/50">
                        <span class="material-symbols-outlined text-gray-500 dark:text-gray-400">search</span>
                        <input
                            class="w-full bg-transparent border-none text-sm px-3 text-text-light dark:text-text-dark placeholder-gray-500 focus:ring-0"
                            placeholder="{{ __('Nav.SearchPlaceholder') }}" type="text" x-model="searchQuery"
                            @input.debounce.50ms="performSearch()" @keydown.enter="scrollToMap()" />
                    </div>

                    <!-- Search Results Dropdown -->
                    <div x-show="searchResults.length > 0" @click.outside="searchResults = []"
                        class="absolute top-12 left-0 right-0 bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-surface-light dark:border-surface-dark overflow-hidden z-50 max-h-80 overflow-y-auto"
                        x-cloak x-transition>
                        <template x-for="result in searchResults" :key="result.id || result.name">
                            <button @click="selectFeature(result); scrollToMap()"
                                class="w-full text-left px-4 py-3 hover:bg-surface-light dark:hover:bg-black/20 border-b border-surface-light dark:border-surface-dark last:border-0 transition flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary flex-shrink-0">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-text-light dark:text-text-dark text-sm truncate"
                                        x-text="result.name"></p>
                                    <p class="text-xs text-text-light/60 dark:text-text-dark/60 truncate"
                                        x-text="result.type || 'Location'"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </label>
                @endif

                <!-- Language Switcher (Desktop) -->
                <div class="hidden lg:flex items-center gap-2 mr-4">
                    <a href="{{ route('lang.switch', 'id') }}" class="text-xs font-bold transition-colors {{ app()->getLocale() == 'id' ? 'text-primary' : 'text-gray-400 hover:text-gray-600' }}">ID</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('lang.switch', 'en') }}" class="text-xs font-bold transition-colors {{ app()->getLocale() == 'en' ? 'text-primary' : 'text-gray-400 hover:text-gray-600' }}">EN</a>
                </div>

                <!-- Auth Buttons (Desktop) -->
                <div class="hidden">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                <span class="truncate">Login</span>
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Mobile Menu Button (Imploding Star Animation - Monochrome) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden menu-button group relative z-50 text-text-light dark:text-text-dark">
                    
                    <!-- SVG Icon -->
                    <svg viewBox="0 0 24 24" fill="none" class="w-8 h-8 relative z-10 transition-transform duration-300" 
                         :class="{ 'rotate-90': mobileMenuOpen }">
                        
                        <!-- Menu Group (Implodes) -->
                        <g class="menu-icon origin-center" :class="mobileMenuOpen ? 'menu-icon-hidden' : 'menu-icon-visible'">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </g>

                        <!-- Star/X Group (Explodes) -->
                        <g class="menu-icon origin-center" :class="mobileMenuOpen ? 'menu-icon-visible' : 'menu-icon-hidden'">
                             <!-- A 4-pointed star shape for the X -->
                             <path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" fill="currentColor" />
                        </g>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="mobileMenuOpen = false" x-cloak
                class="absolute top-20 left-0 w-full bg-background-light dark:bg-background-dark border-b border-surface-light dark:border-surface-dark shadow-xl lg:hidden z-50 p-4 flex flex-col gap-4">

                <nav class="flex flex-col gap-4">
                    <div class="flex items-center gap-4 p-2 border-b border-surface-light dark:border-surface-dark pb-4 mb-2">
                        <a href="{{ route('lang.switch', 'id') }}" class="text-sm font-bold {{ app()->getLocale() == 'id' ? 'text-primary' : 'text-gray-400' }}">ID</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('lang.switch', 'en') }}" class="text-sm font-bold {{ app()->getLocale() == 'en' ? 'text-primary' : 'text-gray-400' }}">EN</a>
                    </div>

                    <a class="text-sm font-medium hover:text-primary hover:underline underline-offset-4 transition-colors p-2"
                        href="{{ route('welcome') }}">{{ __('Nav.Home') }}</a>
                    @if(request()->routeIs('welcome'))
                        <a class="text-sm font-medium hover:text-primary hover:underline underline-offset-4 transition-colors p-2" href="#gis-map">{{ __('Nav.Map') }}</a>
                        <a class="text-sm font-medium hover:text-primary hover:underline underline-offset-4 transition-colors p-2" href="#profile">{{ __('Profile.Label') }}</a>
                    @else
                        <a class="text-sm font-medium hover:text-primary hover:underline underline-offset-4 transition-colors p-2" href="{{ route('explore.map') }}">{{ __('Nav.Map') }}</a>
                    @endif
                    <a class="text-sm font-medium hover:text-primary hover:underline underline-offset-4 transition-colors p-2"
                        href="{{ route('places.index') }}">{{ __('Nav.Destinations') }}</a>
                    <a class="text-sm font-medium hover:text-primary hover:underline underline-offset-4 transition-colors p-2"
                        href="{{ route('posts.index') }}">{{ __('Nav.News') }}</a>
                </nav>

                <div class="hidden border-t border-surface-light dark:border-surface-dark pt-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="flex items-center justify-center rounded-xl h-12 w-full bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center justify-center rounded-xl h-12 w-full bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                Login
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>
    </div>
</div>
