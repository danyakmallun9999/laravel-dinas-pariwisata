<div class="flex flex-col h-full sidebar-container">
    <style>
        /* Anti-Flicker: Force minimized state immediately if class exists on HTML */
        html.sidebar-minimized .sidebar-logo-container {
            justify-content: center !important;
        }
        html.sidebar-minimized .sidebar-logo-text,
        html.sidebar-minimized .sidebar-toggle-desktop {
            display: none !important;
        }
        html.sidebar-minimized .sidebar-logo-img {
            display: block !important;
        }
        html.sidebar-minimized .sidebar-nav-item {
            justify-content: center !important;
        }
        html.sidebar-minimized .sidebar-nav-text,
        html.sidebar-minimized .sidebar-nav-chevron {
            display: none !important;
        }
        html.sidebar-minimized .sidebar-divider-mini {
            display: block !important;
        }
        html.sidebar-minimized .sidebar-toggle-maximize {
            display: flex !important;
        }
        
        /* Default States (Expanded) */
        .sidebar-logo-img { display: none; }
        .sidebar-divider-mini { display: none; }
        .sidebar-toggle-maximize { display: none; }
    </style>

    @php
        $user = auth('admin')->user();
        $brandLabel = 'JelajahJepara';
        $brandSub = '';
        
        if ($user->hasRole('super_admin')) {
            $brandLabel = 'SUPER ADMIN';
        } elseif ($user->hasRole('admin_wisata')) {
            $brandLabel = 'ADMIN WISATA';
        } elseif ($user->hasRole('admin_berita')) {
            $brandLabel = 'ADMIN BERITA';
        } elseif ($user->hasRole('pengelola_wisata')) {
             $place = $user->ownedPlaces()->first();
             $placeName = $place ? $place->name : '';
             $brandLabel = 'PENGELOLA WISATA';
             $brandSub = $placeName;
        }
    @endphp

    <!-- Sidebar Backdrop -->
    <div x-show="mobileSidebarOpen" 
         @click="mobileSidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 z-[5000] md:hidden"
         style="display: none;"></div>

    <!-- Sidebar Content -->
    <div :class="{'translate-x-0': mobileSidebarOpen, '-translate-x-full': !mobileSidebarOpen}" 
         class="fixed inset-y-0 left-0 z-[10000] bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0 md:static md:inset-auto flex flex-col h-full w-64 md:w-full shadow-2xl md:shadow-none">
        
        <!-- Logo -->
        <div class="flex items-center h-16 md:h-20 border-b border-gray-200 px-4 transition-all duration-300 sidebar-logo-container" :class="isSidebarMini ? 'justify-center' : 'justify-between'">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group" wire:navigate>
                <img x-show="isSidebarMini" src="{{ asset('images/logo-kura.png') }}" class="w-10 h-10 object-contain transition flex-shrink-0 sidebar-logo-img" alt="Logo">
                <div x-show="!isSidebarMini" class="flex flex-col justify-center transition-opacity duration-300 sidebar-logo-text">
                    @if($brandLabel === 'JelajahJepara')
                        <span class="font-bold text-xl text-gray-800 tracking-tight whitespace-nowrap">Jelajah<span class="text-blue-600">Jepara</span></span>
                    @else
                        <span class="font-bold text-sm text-gray-800 tracking-tight whitespace-normal uppercase leading-tight">{{ $brandLabel }}</span>
                        @if($brandSub)
                            <span class="text-[10px] font-semibold text-blue-600 uppercase leading-tight mt-0.5 truncate max-w-[170px]" title="{{ $brandSub }}">{{ $brandSub }}</span>
                        @endif
                    @endif
                </div>
            </a>
            
            <!-- Toggle Button (Desktop) -->
            <button @click="sidebarMinimized = !sidebarMinimized" class="hidden md:flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition focus:outline-none toggle-minimize sidebar-toggle-desktop">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>

            <!-- Close Button (Mobile) -->
            <button @click="mobileSidebarOpen = false" class="md:hidden flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition focus:outline-none">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        
        <!-- Toggle Button (When Minimized) -->
        <div class="hidden md:flex justify-center py-2 border-b border-gray-100 toggle-maximize sidebar-toggle-maximize">
             <button @click="sidebarMinimized = !sidebarMinimized" class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition focus:outline-none">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 no-scrollbar">
            
            <!-- Dashboard -->
            <div x-show="isSidebarMini" class="h-4"></div>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-gauge text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Dashboard</span>
                
                <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Dashboard
                </div>
            </a>


            @role('super_admin', 'admin')
            <p x-show="!isSidebarMini" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 transition-opacity duration-300 sidebar-nav-text">Manajemen Web</p>
            <div x-show="isSidebarMini" class="border-t border-gray-100 my-2 sidebar-divider-mini"></div>
            
            <a href="{{ route('admin.hero-settings.edit') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.hero-settings.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-image text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Pengaturan Hero</span>
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Pengaturan Hero
                </div>
            </a>

            <a href="{{ route('admin.footer-settings.edit') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.footer-settings.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Pengaturan Footer</span>
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Pengaturan Footer
                </div>
            </a>

            <!-- Pengumuman & Legenda (Super Admin only) -->
            @role('super_admin')
            <a href="{{ route('admin.announcements.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.announcements.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-bullhorn text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Pengumuman</span>
                <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Pengumuman
                </div>
            </a>

            <a href="{{ route('admin.legends.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.legends.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-landmark text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Tokoh & Sejarah</span>
                <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Tokoh & Sejarah
                </div>
            </a>
            @endrole

            <p x-show="!isSidebarMini" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 transition-opacity duration-300 sidebar-nav-text">Manajemen User</p>
            <div x-show="isSidebarMini" class="border-t border-gray-100 my-2 sidebar-divider-mini"></div>

            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-users-gear text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Admin</span>
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Kelola Admin
                </div>
            </a>
            @endrole
            <p x-show="!isSidebarMini" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 transition-opacity duration-300 sidebar-nav-text">Manajemen Pariwisata</p>
            <div x-show="isSidebarMini" class="border-t border-gray-100 my-2 sidebar-divider-mini"></div>

            @if(auth('admin')->user()->hasAnyPermission(['view all destinations', 'view own destinations', 'manage categories']))
            <div x-data="{ placesOpen: {{ request()->routeIs('admin.places.*') || request()->routeIs('admin.categories.*') ? 'true' : 'false' }} }" class="relative group">
                <button @click="if(!isSidebarMini) placesOpen = !placesOpen" 
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition relative {{ request()->routeIs('admin.places.*') || request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
                        :class="isSidebarMini ? 'justify-center' : ''">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-map-location-dot text-lg"></i>
                        </div>
                        <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Destinasi Wisata</span>
                    </div>
                    <i x-show="!isSidebarMini" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 sidebar-nav-chevron" :class="placesOpen ? 'rotate-180' : ''"></i>
                </button>

                <!-- Floating Submenu (Minimized) -->
                <div x-show="isSidebarMini"
                     x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top) + 'px' })"
                     class="fixed left-[4.7rem] w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-[9999] opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-200"
                     style="display: none;">
                    <div class="px-3 py-2 border-b border-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 rounded-t-lg">
                        Destinasi
                    </div>
                    @if(auth('admin')->user()->hasAnyPermission(['view all destinations', 'view own destinations']))
                    <a href="{{ route('admin.places.index') }}" class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors" wire:navigate>
                        Daftar Destinasi
                    </a>
                    @endif
                    @can('manage categories')
                    <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors" wire:navigate>
                        Kategori
                    </a>
                    @endcan
                </div>

                <!-- Accordion Submenu (Expanded) -->
                <div x-show="placesOpen && !isSidebarMini" 
                     x-collapse
                     class="pl-4 pr-3 py-1 space-y-1 relative ml-2.5 border-l-2 border-gray-100 sidebar-nav-text">
                    @if(auth('admin')->user()->hasAnyPermission(['view all destinations', 'view own destinations']))
                    <a href="{{ route('admin.places.index') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.places.index') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.places.index') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Daftar Destinasi</span>
                    </a>
                    @endif
                    @can('manage categories')
                    <a href="{{ route('admin.categories.index') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.categories.*') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.categories.*') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Kategori</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endif

            @can('manage culture')
            <a href="{{ route('admin.cultures.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.cultures.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-masks-theater text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Budaya</span>
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Budaya
                </div>
            </a>
            @endcan



            @if(auth('admin')->user()->hasAnyPermission(['view all posts', 'view own posts']))
            <a href="{{ route('admin.posts.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.posts.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-newspaper text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Berita</span>
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Berita
                </div>
            </a>
            @endif

            @if(auth('admin')->user()->hasAnyPermission(['view all events', 'view own events']))
            <div x-data="{ eventsOpen: {{ request()->routeIs('admin.events.*') ? 'true' : 'false' }} }" class="relative group">
                <button @click="if(!isSidebarMini) eventsOpen = !eventsOpen" 
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition relative {{ request()->routeIs('admin.events.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
                        :class="isSidebarMini ? 'justify-center' : ''">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-calendar-days text-lg"></i>
                        </div>
                        <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Events</span>
                    </div>
                    <i x-show="!isSidebarMini" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 sidebar-nav-chevron" :class="eventsOpen ? 'rotate-180' : ''"></i>
                </button>

                <!-- Floating Submenu (Minimized) -->
                <div x-show="isSidebarMini"
                     x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top) + 'px' })"
                     class="fixed left-[4.7rem] w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-[9999] opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-200"
                     style="display: none;">
                    <div class="px-3 py-2 border-b border-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 rounded-t-lg">
                        Events
                    </div>
                    <a href="{{ route('admin.events.index') }}" class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors" wire:navigate>
                        Daftar Event
                    </a>
                    <a href="{{ route('admin.events.calendar') }}" class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition-colors" wire:navigate>
                        Kalender Tahunan
                    </a>
                </div>

                <!-- Accordion Submenu (Expanded) -->
                <div x-show="eventsOpen && !isSidebarMini" 
                     x-collapse
                     class="pl-4 pr-3 py-1 space-y-1 relative ml-2.5 border-l-2 border-gray-100 sidebar-nav-text">
                    <a href="{{ route('admin.events.index') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.events.index') || request()->routeIs('admin.events.create') || request()->routeIs('admin.events.edit') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.events.index') || request()->routeIs('admin.events.create') || request()->routeIs('admin.events.edit') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Daftar Event</span>
                    </a>
                    <a href="{{ route('admin.events.calendar') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.events.calendar') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.events.calendar') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Kalender Tahunan</span>
                    </a>
                </div>
            </div>
            @endif

            @if(config('features.e_ticket_enabled'))
            @can('scan tickets')
            <a href="{{ route('admin.scan.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.scan.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
               :class="isSidebarMini ? 'justify-center' : ''"
               wire:navigate>
                <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-qrcode text-lg"></i>
                </div>
                <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">Scan Barcode</span>
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="isSidebarMini" 
                     class="fixed left-[4.7rem] px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Scan Barcode
                </div>
            </a>
            @endcan
            @endif



            @if(config('features.e_ticket_enabled'))
            <!-- E-Tiket Parent (Standardized Dropdown) -->
            @if(auth('admin')->user()->can('view all tickets') || auth('admin')->user()->hasAnyPermission(['view all financial reports', 'view own financial reports', 'view own tickets']))
            <div x-data="{ ticketsOpen: {{ request()->routeIs('admin.tickets.*') || request()->routeIs('admin.reports.financial.*') ? 'true' : 'false' }} }" class="relative group">
                <button @click="if(!isSidebarMini) ticketsOpen = !ticketsOpen" 
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition relative {{ request()->routeIs('admin.tickets.*') || request()->routeIs('admin.reports.financial.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} sidebar-nav-item"
                        :class="isSidebarMini ? 'justify-center' : ''">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-ticket text-lg"></i>
                        </div>
                        <span x-show="!isSidebarMini" class="whitespace-nowrap transition-opacity duration-300 sidebar-nav-text">E-Tiket</span>
                    </div>
                    <i x-show="!isSidebarMini" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 sidebar-nav-chevron" :class="ticketsOpen ? 'rotate-180' : ''"></i>
                </button>

                <!-- Floating Submenu (Minimized) -->
                <div x-show="isSidebarMini"
                     x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top) + 'px' })"
                     class="fixed left-[4.7rem] w-56 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-[9999] opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all duration-200"
                     style="display: none;">
                    <div class="px-3 py-2 border-b border-gray-50 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 rounded-t-lg">
                        E-Tiket
                    </div>
                    @can('view all tickets')
                    <a href="{{ route('admin.tickets.dashboard') }}" class="block px-4 py-2.5 text-sm {{ request()->routeIs('admin.tickets.dashboard') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors" wire:navigate>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.tickets.index') }}" class="block px-4 py-2.5 text-sm {{ request()->routeIs('admin.tickets.index') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors" wire:navigate>
                        Kelola Tiket
                    </a>
                    <a href="{{ route('admin.tickets.orders') }}" class="block px-4 py-2.5 text-sm {{ request()->routeIs('admin.tickets.orders') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors" wire:navigate>
                        Pesanan Masuk
                    </a>
                    @endcan

                    @if(auth('admin')->user()->hasAnyPermission(['view all tickets', 'view own destinations']))
                    <a href="{{ route('admin.tickets.history') }}" class="block px-4 py-2.5 text-sm {{ request()->routeIs('admin.tickets.history') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors" wire:navigate>
                        Riwayat Penjualan
                    </a>
                    @endif

                    @if(auth('admin')->user()->hasAnyPermission(['view all financial reports', 'view own financial reports']))
                    <a href="{{ route('admin.reports.financial.index') }}" class="block px-4 py-2.5 text-sm {{ request()->routeIs('admin.reports.financial.*') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }} transition-colors" wire:navigate>
                        Laporan Keuangan
                    </a>
                    @endif
                </div>

                <!-- Accordion Submenu (Expanded) -->
                <div x-show="ticketsOpen && !isSidebarMini" 
                     x-collapse
                     class="pl-4 pr-3 py-1 space-y-1 relative ml-2.5 border-l-2 border-gray-100 sidebar-nav-text">
                    @can('view all tickets')
                    <a href="{{ route('admin.tickets.dashboard') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.tickets.dashboard') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.tickets.dashboard') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.tickets.index') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.tickets.index') || request()->routeIs('admin.tickets.create') || request()->routeIs('admin.tickets.edit') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.tickets.index') || request()->routeIs('admin.tickets.create') || request()->routeIs('admin.tickets.edit') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Kelola Tiket</span>
                    </a>
                    <a href="{{ route('admin.tickets.orders') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.tickets.orders') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.tickets.orders') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Pesanan Masuk</span>
                    </a>
                    @endcan

                    @if(auth('admin')->user()->hasAnyPermission(['view all tickets', 'view own destinations']))
                    <a href="{{ route('admin.tickets.history') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.tickets.history') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.tickets.history') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Riwayat Penjualan</span>
                    </a>
                    @endif

                    @if(auth('admin')->user()->hasAnyPermission(['view all financial reports', 'view own financial reports']))
                    <a href="{{ route('admin.reports.financial.index') }}" 
                       class="block px-3 py-2 rounded-lg text-sm transition-all relative {{ request()->routeIs('admin.reports.financial.*') ? 'text-blue-600 font-bold bg-blue-50/50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
                       wire:navigate>
                       <span class="{{ request()->routeIs('admin.reports.financial.*') ? 'translate-x-1' : '' }} inline-block transition-transform duration-200">Laporan Keuangan</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif
            @endif



        </div>

        <!-- User Profile -->
        <div class="border-t border-gray-200 p-4">
            <div x-data="{ userOpen: false }" class="relative">
                <button @click="userOpen = !userOpen" class="flex items-center gap-3 w-full hover:bg-gray-50 p-2 rounded-lg transition sidebar-nav-item" :class="isSidebarMini ? 'justify-center' : ''">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold flex-shrink-0">
                        {{ substr(auth('admin')->user()->name, 0, 1) }}
                    </div>
                    <div x-show="!isSidebarMini" class="flex-1 text-left overflow-hidden sidebar-nav-text">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ auth('admin')->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth('admin')->user()->email }}</p>
                    </div>
                    <i x-show="!isSidebarMini" class="fa-solid fa-chevron-up text-xs text-gray-400 transition-transform sidebar-nav-chevron" :class="{'rotate-180': userOpen}"></i>
                </button>

                <!-- Dropdown -->
                <div x-show="userOpen" 
                     @click.away="userOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute bottom-full left-0 w-full mb-2 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50"
                     :class="isSidebarMini ? 'w-48 left-[4.7rem] ml-0 bottom-0' : 'w-full'"
                     style="display: none;">
                    
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition" wire:navigate>
                        <i class="fa-solid fa-user text-gray-400 w-5"></i>
                        Profile
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-3 text-sm text-red-600 hover:bg-red-50 w-full text-left transition">
                            <i class="fa-solid fa-right-from-bracket w-5"></i>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
