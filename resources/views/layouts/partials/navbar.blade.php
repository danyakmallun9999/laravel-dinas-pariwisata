<div class="fixed top-0 left-0 right-0 z-[10000] w-full transition-all duration-500 ease-in-out border-b border-transparent py-4 bg-transparent"
    x-data="{ 
        isScrolled: (window.pageYOffset > 10), 
        mobileMenuOpen: false, 
        searchOpen: false,
        menuUsed: false
    }" 
    @scroll.window="isScrolled = (window.pageYOffset > 10)"
    :class="{ 'bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-slate-200/50 dark:border-slate-800 !py-0': isScrolled, 'bg-transparent py-4': !isScrolled }">
    
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
        <header class="flex items-center justify-between gap-8 transition-all duration-500 ease-in-out h-20" :class="{ '!h-16': isScrolled, 'h-20': !isScrolled }">
            
            <!-- Logo Area -->
            <div class="flex items-center gap-8">
                <a class="flex items-center gap-3 group relative" href="{{ route('welcome') }}">
                    <div class="relative transition-all duration-300 group-hover:scale-110" :class="{ 'w-16 h-16': isScrolled, 'w-20 h-20': !isScrolled }">
                         <!-- Logo Image -->
                         <img src="{{ asset('images/logo-kura.png') }}" alt="Logo Kabupaten Jepara" class="w-full h-full object-contain filter">
                    </div>
                    <div>
                        <h2 class="text-xl font-bold leading-none tracking-tight text-slate-800 dark:text-white group-hover:text-primary transition-colors">
                            Jelajah Jepara
                        </h2>
                    </div>
                </a>

                <!-- Desktop Navigation (Magnetic Pills) -->
                <nav class="hidden lg:flex items-center gap-1">
                    @foreach([
                        ['label' => __('Nav.Home'), 'route' => 'welcome', 'active' => 'welcome'],
                        ['label' => __('Nav.Map'), 'route' => 'explore.map', 'active' => 'explore.map'],
                        ['label' => __('Nav.Destinations'), 'route' => 'places.index', 'active' => 'places.*'],
                        ['label' => 'E-Tiket', 'route' => 'tickets.index', 'active' => 'tickets.*'],
                        ['label' => __('Nav.Events'), 'route' => 'events.public.index', 'active' => 'events.public.*'],
                        ['label' => __('Nav.News'), 'route' => 'posts.index', 'active' => 'posts.*']
                    ] as $link)
                    <a href="{{ route($link['route']) }}" 
                       class="relative px-4 py-2 rounded-full text-sm font-medium transition-colors duration-300 group overflow-hidden {{ request()->routeIs($link['active']) ? 'text-primary dark:text-primary bg-primary/10' : 'text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-white' }}">
                        <span class="relative z-10">{{ $link['label'] }}</span>
                        <!-- Hover Pill Effect -->
                        @unless(request()->routeIs($link['active']))
                        <span class="absolute inset-0 bg-slate-100 dark:bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-0"></span>
                        @endunless
                    </a>
                    @endforeach
                </nav>
            </div>

            <!-- Right Actions Area -->
            <div class="flex flex-1 items-center justify-end gap-3">
                
                <!-- Expanded Search Bar Island -->
                @if(request()->routeIs('welcome'))
                <div class="hidden md:flex relative transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]"
                     :class="searchOpen ? 'w-80' : 'w-10'"
                     @click.away="if(searchQuery === '') searchOpen = false">
                    
                    <div class="absolute inset-0 bg-slate-100 dark:bg-slate-800/50 rounded-full border border-slate-200 dark:border-slate-700/50 transition-all duration-300"
                         :class="searchOpen ? 'opacity-100' : 'opacity-0 scale-90'"></div>

                    <div class="relative w-full h-10 flex items-center">
                        <input type="text" x-model="searchQuery" 
                               class="w-full h-full bg-transparent border-none focus:ring-0 text-sm pl-10 pr-4 text-slate-800 dark:text-white placeholder-slate-400 transition-opacity duration-200"
                               :class="searchOpen ? 'opacity-100 pointer-events-auto delay-100' : 'opacity-0 pointer-events-none'"
                               placeholder="{{ __('Nav.SearchPlaceholder') }}"
                               x-ref="searchInput"
                               @keydown.escape="searchOpen = false"
                               @input.debounce.300ms="performSearch()" 
                               @keydown.enter="scrollToMap()">
                        
                        <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" 
                                class="absolute left-0 top-0 w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary transition-colors z-20">
                            <span class="material-symbols-outlined text-xl">search</span>
                        </button>
                        
                        <button x-show="searchOpen && searchQuery" 
                                @click="searchQuery = ''; performSearch()" 
                                class="absolute right-0 top-0 w-10 h-10 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors z-20">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>

                    <!-- Search Results Dropdown -->
                     <div x-show="searchResults.length > 0" 
                        class="absolute top-14 left-0 right-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-blue-100 dark:border-blue-900 overflow-hidden z-[100] max-h-96 overflow-y-auto w-[120%] -ml-[10%]"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                        @click.away="searchResults = []"
                        x-cloak>
                        
                        <div class="px-4 py-3 text-xs font-bold text-blue-500 dark:text-blue-400 uppercase tracking-wider bg-blue-50/50 dark:bg-blue-900/20 border-b border-blue-50 dark:border-blue-900/30">
                            Hasil Pencarian
                        </div>
                        
                        <template x-for="(result, index) in searchResults" :key="index">
                            <button @click="selectFeature(result); scrollToMap(); searchResults = []"
                                class="w-full text-left px-4 py-3 hover:bg-blue-50 dark:hover:bg-blue-900/40 border-b border-slate-50 dark:border-slate-800/50 last:border-0 transition-colors flex items-center gap-4 group">
                                <div class="shrink-0 w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-xl" x-text="result.type === 'event' ? 'event' : (result.type === 'news' ? 'article' : 'location_on')"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-slate-800 dark:text-slate-100 text-sm truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" x-text="result.name"></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400" x-text="result.type || 'Destinasi'"></span>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate" x-text="result.address || result.description || ''"></p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-slate-300 group-hover:text-blue-500 -translate-x-2 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all">arrow_forward</span>
                            </button>
                        </template>
                    </div>
                </div>
                @endif
                
                <!-- Divider -->
                 <div class="hidden lg:block w-px h-6 bg-slate-200 dark:bg-slate-700 mx-2"></div>

                <!-- Language Switcher (Minimalist) -->
                <div class="hidden lg:flex items-center bg-slate-100 dark:bg-slate-800 rounded-full p-1 border border-slate-200 dark:border-slate-700">
                    <a href="{{ route('lang.switch', 'id') }}" 
                       class="px-2 py-1 rounded-full text-xs font-bold transition-all duration-300 {{ app()->getLocale() == 'id' ? 'bg-white dark:bg-slate-600 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}" 
                       class="px-2 py-1 rounded-full text-xs font-bold transition-all duration-300 {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-slate-600 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
                </div>

                <!-- Auth Buttons (Hidden as per request, but structure kept if needed) -->
                <div class="hidden">
                    <!-- ... -->
                </div>

                <!-- Mobile Menu Button (Hamburger to Close) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen; menuUsed = true"
                    class="lg:hidden p-2 relative z-50 w-10 h-10 flex items-center justify-center text-slate-800 dark:text-white rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <div class="w-6 flex flex-col items-end gap-[5px] transition-all duration-300" :class="{ 'gap-0': mobileMenuOpen }">
                        <span class="w-full h-0.5 bg-current rounded-full transition-all duration-300 origin-right" :class="{ '-rotate-45 -translate-x-[2px] w-6': mobileMenuOpen }"></span>
                        <span class="w-4 h-0.5 bg-current rounded-full transition-all duration-300" :class="{ 'opacity-0 scale-0': mobileMenuOpen }"></span>
                        <span class="w-full h-0.5 bg-current rounded-full transition-all duration-300 origin-right" :class="{ 'rotate-45 -translate-x-[2px] w-6': mobileMenuOpen }"></span>
                    </div>
                </button>
            </div>

            <!-- Mobile Menu Fullscreen Overlay -->
            <div x-show="mobileMenuOpen" 
                class="fixed inset-0 min-h-screen w-full bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl lg:hidden z-40 flex flex-col pt-24 px-8"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak>
                
                <nav class="flex flex-col gap-2">
                    @foreach([
                        ['label' => __('Nav.Home'), 'route' => 'welcome', 'active' => 'welcome'],
                        ['label' => __('Nav.Map'), 'route' => 'explore.map', 'active' => 'explore.map'],
                        ['label' => __('Nav.Destinations'), 'route' => 'places.index', 'active' => 'places.*'],
                        ['label' => 'E-Tiket', 'route' => 'tickets.index', 'active' => 'tickets.*'],
                        ['label' => __('Nav.Events'), 'route' => 'events.public.index', 'active' => 'events.public.*'],
                        ['label' => __('Nav.News'), 'route' => 'posts.index', 'active' => 'posts.*']
                    ] as $index => $link)
                    <a href="{{ route($link['route']) }}" 
                       class="text-2xl font-bold flex items-center justify-between py-4 border-b border-slate-100 dark:border-slate-800 group"
                       x-transition:enter="transition ease-out duration-300 delay-{{ $index * 100 }}ms"
                       x-transition:enter-start="opacity-0 translate-x-10"
                       x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="{{ request()->routeIs($link['active']) ? 'text-primary' : 'text-slate-800 dark:text-white' }} group-hover:text-primary transition-colors">{{ $link['label'] }}</span>
                        <span class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-transform group-hover:translate-x-2">arrow_forward</span>
                    </a>
                    @endforeach
                </nav>

                <div class="mt-8">
                     <p class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Pengaturan Bahasa</p>
                     <div class="flex gap-4">
                        <a href="{{ route('lang.switch', 'id') }}" class="flex-1 py-3 rounded-xl border text-center font-bold transition-colors {{ app()->getLocale() == 'id' ? 'border-primary text-primary bg-primary/5' : 'border-slate-200 dark:border-slate-700 text-slate-500' }}">Bahasa Indonesia</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="flex-1 py-3 rounded-xl border text-center font-bold transition-colors {{ app()->getLocale() == 'en' ? 'border-primary text-primary bg-primary/5' : 'border-slate-200 dark:border-slate-700 text-slate-500' }}">English</a>
                     </div>
                </div>
            </div>

        </header>
    </div>
</div>
