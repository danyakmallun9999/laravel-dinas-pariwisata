<div class="fixed top-0 left-0 right-0 z-[10000] w-full transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] border-b border-slate-200/50 dark:border-slate-800 py-0 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md"
    x-data="{ 
        mobileMenuOpen: false, 
        searchOpen: false,
        isScrolled: false,
        init() {
            this.isScrolled = window.pageYOffset > 10;
            window.addEventListener('scroll', () => {
                this.isScrolled = window.pageYOffset > 10;
            });
        }
    }">
    
    <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
        <header class="flex items-center justify-between gap-8 transition-all duration-500 ease-in-out h-20">
            
            <!-- Logo Area -->
            <div class="flex items-center gap-8">
                <!-- Mobile Language Switcher (Visible on mobile when menu open) -->
                <div class="lg:hidden absolute left-0 top-0 bottom-0 flex items-center z-[60]" 
                     x-show="mobileMenuOpen" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-300 delay-100" 
                     x-transition:enter-start="opacity-0 -translate-x-2" 
                     x-transition:enter-end="opacity-100 translate-x-0">
                     <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-full p-1 border border-slate-200 dark:border-slate-700 shadow-sm ml-4">
                        <a href="{{ route('lang.switch', 'id') }}" class="px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-300 {{ app()->getLocale() == 'id' ? 'bg-white dark:bg-slate-600 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">ID</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="px-3 py-1.5 rounded-full text-xs font-bold transition-all duration-300 {{ app()->getLocale() == 'en' ? 'bg-white dark:bg-slate-600 text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">EN</a>
                     </div>
                </div>

                <!-- Logo (Hidden on mobile when menu open) -->
                <a class="flex items-center gap-3 group relative transition-opacity duration-300" 
                   :class="{ 'opacity-0 pointer-events-none absolute': mobileMenuOpen, 'opacity-100 relative': !mobileMenuOpen }"
                   href="{{ route('welcome') }}">
                    <div class="relative transition-all duration-300 group-hover:scale-110 w-20 h-20">
                         <!-- Logo Image -->
                         <img src="{{ asset('images/logo-kura.png') }}" alt="Logo Kabupaten Jepara" class="w-full h-full object-contain filter">
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold leading-none tracking-tight text-slate-600 dark:text-white group-hover:text-primary transition-colors font-['Caveat']">
                            {{ __('Nav.AppName') }}
                        </h2>
                    </div>
                </a>

                <!-- Desktop Navigation (Magnetic Pills) -->
                <nav class="hidden lg:flex items-center gap-1">
                    @foreach([
                        ['label' => __('Nav.Home'), 'route' => 'welcome', 'active' => 'welcome'],
                        ['label' => __('Nav.Map'), 'route' => 'explore.map', 'active' => 'explore.map'],
                        ['label' => __('Nav.Destinations'), 'route' => 'places.index', 'active' => 'places.*'],
                        ['label' => __('Nav.Tickets'), 'route' => 'tickets.index', 'active' => 'tickets.*'],
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
                            {{ __('Nav.SearchResults') }}
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
                                        <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400" x-text="result.type || '{{ __('Nav.DefaultType') }}'"></span>
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

                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-700">
                    @auth('web')
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full p-1 pr-3 transition-all">
                                <img src="{{ Auth::guard('web')->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::guard('web')->user()->name) }}" 
                                     alt="Profile" 
                                     class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-slate-700">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 max-w-[100px] truncate">
                                    {{ explode(' ', Auth::guard('web')->user()->name)[0] }}
                                </span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden z-50">
                                
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Halo,</p>
                                    <p class="font-bold text-slate-800 dark:text-white truncate">{{ Auth::guard('web')->user()->name }}</p>
                                </div>

                                <a href="{{ route('tickets.my') }}" class="block px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-ticket text-slate-400"></i> {{ __('Tickets.My.Title') ?? 'Tiket Saya' }}
                                </a>

                                <form method="POST" action="{{ route('auth.user.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-2">
                                        <i class="fa-solid fa-right-from-bracket opacity-70"></i> {{ __('Logout') ?? 'Keluar' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('auth.google') }}" class="px-5 py-2 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold hover:bg-primary hover:text-white dark:hover:bg-primary dark:hover:text-white transition-all shadow-lg shadow-slate-200/50 dark:shadow-none flex items-center gap-2">
                            <i class="fa-brands fa-google"></i>
                            <span>Login</span>
                        </a>
                    @endauth
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
                x-cloak
                class="fixed inset-0 min-h-screen w-full bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl lg:hidden z-40 flex flex-col pt-24 px-6 overflow-y-auto pb-8"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak>

                <!-- Language Switcher moved to Header -->
                
                <nav class="flex flex-col gap-1">
                    @foreach([
                        ['label' => __('Nav.Home'), 'route' => 'welcome', 'active' => 'welcome'],
                        ['label' => __('Nav.Map'), 'route' => 'explore.map', 'active' => 'explore.map'],
                        ['label' => __('Nav.Destinations'), 'route' => 'places.index', 'active' => ['places.index', 'places.show']],
                        ['label' => __('Nav.Tickets'), 'route' => 'tickets.index', 'active' => ['tickets.index', 'tickets.show', 'tickets.buy', 'tickets.my']],
                        ['label' => __('Nav.Events'), 'route' => 'events.public.index', 'active' => ['events.public.index', 'events.public.show']],
                        ['label' => __('Nav.News'), 'route' => 'posts.index', 'active' => ['posts.index', 'posts.show']]
                    ] as $index => $link)
                    <a href="{{ route($link['route']) }}" 
                       class="text-lg font-bold flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800 group"
                       x-transition:enter="transition ease-out duration-300 delay-{{ $index * 75 }}ms"
                       x-transition:enter-start="opacity-0 translate-x-10"
                       x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="{{ request()->routeIs($link['active']) ? 'text-primary' : 'text-slate-800 dark:text-white' }} group-hover:text-primary transition-colors">{{ $link['label'] }}</span>
                        <span class="material-symbols-outlined text-sm text-slate-300 group-hover:text-primary transition-transform group-hover:translate-x-2">arrow_forward</span>
                    </a>
                    @endforeach
                </nav>

                <!-- Mobile Auth -->
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    @auth('web')
                        <div class="nav-profile">
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ Auth::guard('web')->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::guard('web')->user()->name) }}" 
                                     alt="Profile" 
                                     class="w-9 h-9 rounded-full object-cover">
                                <div>
                                    <p class="font-bold text-sm text-slate-900 dark:text-white">{{ Auth::guard('web')->user()->name }}</p>
                                    <p class="text-xs text-slate-500">{{ Auth::guard('web')->user()->email }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('tickets.my') }}" class="flex items-center justify-center gap-2 py-2 px-3 bg-gradient-to-br from-primary/5 to-primary/10 text-primary rounded-xl text-center border border-primary/20 shadow-sm shadow-primary/10 active:scale-[0.97] transition-all duration-200">
                                    <span class="w-7 h-7 rounded-lg bg-primary/15 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-ticket text-xs"></i>
                                    </span>
                                    <span class="text-xs font-bold">Tiket Saya</span>
                                </a>
                                <form method="POST" action="{{ route('auth.user.logout') }}" class="contents">
                                    @csrf
                                    <button type="submit" class="flex items-center justify-center gap-2 py-2 px-3 bg-gradient-to-br from-red-50 to-red-100/80 text-red-600 rounded-xl text-center border border-red-200/60 shadow-sm shadow-red-100/50 active:scale-[0.97] transition-all duration-200">
                                        <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                                        </span>
                                        <span class="text-xs font-bold">Keluar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('auth.google') }}" class="block w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold rounded-xl text-center shadow-xl">
                            <i class="fa-brands fa-google mr-2"></i> Login dengan Google
                        </a>
                    @endauth
                </div>
            </div>

        </header>
    </div>
</div>
