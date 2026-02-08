<div x-data="{ open: false }" class="flex flex-col h-full">
    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between bg-white border-b border-gray-200 px-4 h-16">
        <div class="flex items-center gap-3">
            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <span class="font-bold text-gray-800">Admin Panel</span>
        </div>
        <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>
    </div>

    <!-- Sidebar Backdrop -->
    <div x-show="open" 
         @click="open = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 z-40 md:hidden"
         style="display: none;"></div>

    <!-- Sidebar Content -->
    <div :class="{'translate-x-0': open, '-translate-x-full': !open}" 
         class="fixed md:static inset-y-0 left-0 z-50 bg-white border-r border-gray-200 transform transition-all duration-300 ease-in-out md:translate-x-0 flex flex-col h-full w-64 md:w-full">
        
        <!-- Logo -->
        <div class="flex items-center h-20 border-b border-gray-200 px-4 transition-all duration-300" :class="sidebarMinimized ? 'justify-center' : 'justify-between'">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                <div x-show="sidebarMinimized" class="w-10 h-10 rounded-xl flex items-center justify-center text-blue-600 border-blue-600 transition flex-shrink-0">
                    <i class="fa-solid fa-map-location-dot text-lg"></i>
                </div>
                <span x-show="!sidebarMinimized" class="font-bold text-xl text-gray-800 tracking-tight whitespace-nowrap transition-opacity duration-300">Dinas<span class="text-blue-600">Pariwisata</span></span>
            </a>
            
            <!-- Toggle Button (Desktop) -->
            <button @click="sidebarMinimized = !sidebarMinimized" class="hidden md:flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition focus:outline-none toggle-minimize">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
        </div>
        
        <!-- Toggle Button (When Minimized) -->
        <div class="hidden md:flex justify-center py-2 border-b border-gray-100 toggle-maximize">
             <button @click="sidebarMinimized = !sidebarMinimized" class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition focus:outline-none">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-3 space-y-1 custom-scroll">
            <p x-show="!sidebarMinimized" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 transition-opacity duration-300">Menu Utama</p>
            <div x-show="sidebarMinimized" class="h-4"></div>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="sidebarMinimized ? 'justify-center' : ''">
                <i class="fa-solid fa-gauge w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">Dashboard</span>
                
                <!-- Tooltip -->
                <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="sidebarMinimized" 
                     class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Dashboard
                </div>
            </a>



            <p x-show="!sidebarMinimized" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 transition-opacity duration-300">Manajemen Pariwisata</p>
            <div x-show="sidebarMinimized" class="border-t border-gray-100 my-2"></div>

            <a href="{{ route('admin.places.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.places.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="sidebarMinimized ? 'justify-center' : ''">
                <i class="fa-solid fa-map-location-dot w-5 text-center {{ request()->routeIs('admin.places.*') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">Destinasi Wisata</span>
                 <!-- Tooltip -->
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="sidebarMinimized" 
                     class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Destinasi Wisata
                </div>
            </a>



            <a href="{{ route('admin.posts.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.posts.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="sidebarMinimized ? 'justify-center' : ''">
                <i class="fa-solid fa-newspaper w-5 text-center {{ request()->routeIs('admin.posts.*') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">Berita & Agenda</span>
                 <!-- Tooltip -->
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="sidebarMinimized" 
                     class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Berita & Agenda
                </div>
            </a>

            <a href="{{ route('admin.events.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.events.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="sidebarMinimized ? 'justify-center' : ''">
                <i class="fa-solid fa-calendar-days w-5 text-center {{ request()->routeIs('admin.events.*') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">Calendar of Events</span>
                 <!-- Tooltip -->
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="sidebarMinimized" 
                     class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Calendar of Events
                </div>
            </a>

            <!-- E-Tiket Dropdown -->
            <div x-data="{ ticketOpen: {{ request()->routeIs('admin.tickets.*') ? 'true' : 'false' }} }">
                <button @click="ticketOpen = !ticketOpen" @click.away="!sidebarMinimized && (ticketOpen = false)"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.tickets.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                    :class="sidebarMinimized ? 'justify-center' : 'justify-between'">
                    
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-ticket w-5 text-center {{ request()->routeIs('admin.tickets.*') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                        <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">E-Tiket</span>
                    </div>

                    <i x-show="!sidebarMinimized" class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{'rotate-180': ticketOpen}"></i>

                    <!-- Tooltip for Minimized -->
                    <div x-show="sidebarMinimized" 
                         @mouseenter="$el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px'"
                         class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                         style="display: none;">
                        E-Tiket
                    </div>
                </button>

                <!-- Dropdown Content -->
                <div x-show="ticketOpen && !sidebarMinimized" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="pl-11 pr-3 py-2 space-y-1">
                    
                    <a href="{{ route('admin.tickets.dashboard') }}" 
                       class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.tickets.dashboard') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                       Dashboard
                    </a>
                    
                    <a href="{{ route('admin.tickets.index') }}" 
                       class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.tickets.index') || request()->routeIs('admin.tickets.create') || request()->routeIs('admin.tickets.edit') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                       Kelola Tiket
                    </a>

                    <a href="{{ route('admin.tickets.orders') }}" 
                       class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.tickets.orders') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                       Pesanan Masuk
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.categories.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="sidebarMinimized ? 'justify-center' : ''">
                <i class="fa-solid fa-tags w-5 text-center {{ request()->routeIs('admin.categories.*') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">Kategori</span>
                 <!-- Tooltip -->
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="sidebarMinimized" 
                     class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Kategori
                </div>
            </a>

            <p x-show="!sidebarMinimized" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 transition-opacity duration-300">Data Wilayah</p>
            <div x-show="sidebarMinimized" class="border-t border-gray-100 my-2"></div>

            <a href="{{ route('admin.boundaries.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.boundaries.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="sidebarMinimized ? 'justify-center' : ''">
                <i class="fa-solid fa-draw-polygon w-5 text-center {{ request()->routeIs('admin.boundaries.*') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">Batas Wilayah</span>
                 <!-- Tooltip -->
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="sidebarMinimized" 
                     class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Batas Wilayah
                </div>
            </a>




            
            <p x-show="!sidebarMinimized" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6 mb-2 transition-opacity duration-300">Laporan</p>
            <div x-show="sidebarMinimized" class="border-t border-gray-100 my-2"></div>

             <a href="{{ route('admin.reports.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition group relative {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               :class="sidebarMinimized ? 'justify-center' : ''">
                <i class="fa-solid fa-file-alt w-5 text-center {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-gray-400' }} text-lg"></i>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap transition-opacity duration-300">Laporan</span>
                 <!-- Tooltip -->
                 <!-- Tooltip -->
                <div x-init="$el.parentElement.addEventListener('mouseenter', () => { $el.style.top = ($el.parentElement.getBoundingClientRect().top + 10) + 'px' })"
                     x-show="sidebarMinimized" 
                     class="fixed left-[5.5rem] px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-[9999] whitespace-nowrap"
                     style="display: none;">
                    Laporan
                </div>
            </a>
        </div>

        <!-- User Profile -->
        <div class="border-t border-gray-200 p-4">
            <div x-data="{ userOpen: false }" class="relative">
                <button @click="userOpen = !userOpen" class="flex items-center gap-3 w-full hover:bg-gray-50 p-2 rounded-lg transition" :class="sidebarMinimized ? 'justify-center' : ''">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div x-show="!sidebarMinimized" class="flex-1 text-left overflow-hidden">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <i x-show="!sidebarMinimized" class="fa-solid fa-chevron-up text-xs text-gray-400 transition-transform" :class="{'rotate-180': userOpen}"></i>
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
                     :class="sidebarMinimized ? 'w-48 left-full ml-2 bottom-0' : 'w-full'"
                     style="display: none;">
                    
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">
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
