<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Jelajahi Destinasi - Kabupaten Jepara</title>
    <link rel="icon" href="{{ asset('images/logo-kabupaten-jepara.png') }}" type="image/png">
    
    {{-- GSAP Animation Library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    
    {{-- Leaflet MarkerCluster CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('public.explore-map._styles')
</head>
<body class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-white font-display h-screen overflow-hidden" x-data="mapComponent()">

    {{-- Map Canvas (Full Screen) --}}
    <div id="leaflet-map" class="fixed inset-0 z-0"></div>

    {{-- Loading Overlay --}}
    <div x-show="loading" x-transition.opacity class="fixed inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm z-[1000] flex items-center justify-center">
        <div class="text-center">
            <div class="w-12 h-12 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Memuat Peta...</p>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- MOBILE LAYOUT (< 1024px) --}}
    {{-- ============================================= --}}
    
    {{-- Mobile: Floating Search Bar --}}
    <div class="lg:hidden fixed top-0 left-0 right-0 z-[500] p-4 pointer-events-none"
         x-show="!isNavigating" x-transition.opacity.duration.500ms>
        <div class="pointer-events-auto flex items-center gap-3">
            {{-- Back Button --}}
            <a href="{{ route('welcome') }}" class="flex-shrink-0 w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-600 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700 active:scale-95 transition-transform">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            
            {{-- Search Input --}}
            <div class="flex-1 relative">
                <div class="flex items-center h-12 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/50 dark:border-slate-700 px-4 gap-3">
                    <span class="material-symbols-outlined text-slate-400">search</span>
                    <input type="text" 
                           x-model="searchQuery" 
                           @input.debounce.300ms="performSearch()"
                           placeholder="Cari destinasi wisata..." 
                           class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-slate-800 dark:text-white placeholder:text-slate-400">
                    <button x-show="searchQuery" @click="searchQuery = ''; searchResults = []" class="text-slate-400">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
                
                {{-- Search Results Dropdown --}}
                <div x-show="searchResults.length > 0" 
                     @click.outside="searchResults = []"
                     class="absolute top-14 left-0 right-0 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 max-h-60 overflow-y-auto z-[600]"
                     x-transition x-cloak>
                    <template x-for="result in searchResults" :key="result.id">
                        <button @click="selectFeature(result); searchResults = []; bottomSheetState = 'collapsed'" 
                                class="w-full text-left px-4 py-3 hover:bg-sky-50 dark:hover:bg-sky-900/30 flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 last:border-0 active:bg-sky-100">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-sky-500">location_on</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-sm text-slate-800 dark:text-white truncate" x-text="result.name"></p>
                                <p class="text-xs text-slate-400 truncate" x-text="result.category?.name || 'Destinasi'"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile: Map Controls (Right Side) --}}
    <div class="lg:hidden fixed right-4 z-[400] flex flex-col gap-3" 
         :style="'bottom: ' + (bottomSheetState === 'collapsed' ? '220px' : '60px')"
         x-show="!isNavigating" x-transition.opacity.duration.500ms>
        {{-- Layer Toggle --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-600 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700 active:scale-95 transition-transform">
                <span class="material-symbols-outlined">layers</span>
            </button>
            <div x-show="open" @click.outside="open = false" 
                 class="absolute right-14 bottom-0 w-32 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-2"
                 x-transition x-cloak>
                <button @click="setBaseLayer('streets'); open = false" 
                        :class="currentBaseLayer === 'streets' ? 'bg-sky-500 text-white' : 'text-slate-600 dark:text-slate-300'"
                        class="w-full py-2.5 text-xs font-bold rounded-lg mb-1 active:scale-95 transition-transform">Jalan</button>
                <button @click="setBaseLayer('satellite'); open = false"
                        :class="currentBaseLayer === 'satellite' ? 'bg-sky-500 text-white' : 'text-slate-600 dark:text-slate-300'"
                        class="w-full py-2.5 text-xs font-bold rounded-lg active:scale-95 transition-transform">Satelit</button>
            </div>
        </div>

        {{-- Zoom Controls --}}
        <div class="flex flex-col bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200/50 dark:border-slate-700">
            <button @click="map.zoomIn()" class="w-12 h-12 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 active:bg-slate-100">
                <span class="material-symbols-outlined">add</span>
            </button>
            <div class="h-px bg-slate-200 dark:bg-slate-700"></div>
            <button @click="map.zoomOut()" class="w-12 h-12 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 active:bg-slate-100">
                <span class="material-symbols-outlined">remove</span>
            </button>
        </div>

        {{-- My Location --}}
        <button @click="locateUser(null, true)" class="w-12 h-12 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-2xl flex items-center justify-center text-white active:scale-95 transition-transform">
            <span class="material-symbols-outlined">my_location</span>
        </button>
    </div>

    {{-- Mobile: Bottom Sheet --}}
    {{-- Mobile: Bottom Sheet --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-[450] transition-all duration-300 ease-out"
         :class="{
             'translate-y-[calc(100%-80px)]': bottomSheetState === 'collapsed',
             'translate-y-[45%]': bottomSheetState === 'half',
             'translate-y-0': bottomSheetState === 'full'
         }"
         x-show="!selectedFeature && !isNavigating"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full">
        
        <div class="bg-white dark:bg-slate-800 rounded-t-3xl h-[calc(100vh-180px)] border-t border-slate-200 dark:border-slate-700 flex flex-col">
            
            {{-- Drag Handle --}}
            <div class="flex justify-center py-3 cursor-grab active:cursor-grabbing w-full touch-none" 
                 @click="cycleBottomSheet()"
                 @touchstart.passive="handleTouchStart($event)"
                 @touchmove.passive="handleTouchMove($event)"
                 @touchend="handleTouchEnd($event)">
                <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
            </div>

            {{-- Category Pills (Horizontal Scroll) --}}
            <div class="px-4 pb-3 flex-shrink-0">
                <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                    <button @click="selectedCategories = categories.map(c => c.id)" 
                            :class="selectedCategories.length === categories.length ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                            class="flex-shrink-0 px-4 py-2.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors active:scale-95">
                        Semua
                    </button>
                    @foreach($categories as $category)
                    <button @click="toggleCategorySingle({{ $category->id }})" 
                            :class="selectedCategories.length === 1 && selectedCategories.includes({{ $category->id }}) ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                            class="flex-shrink-0 px-4 py-2.5 rounded-full text-sm font-medium whitespace-nowrap transition-colors flex items-center gap-2 active:scale-95">
                        <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }} text-xs"></i>
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
            </div>

             {{-- Horizontal Cards (Collapsed State) - REMOVED as per request --}}
            {{-- <div x-show="bottomSheetState === 'collapsed'" class="px-4 pb-4 flex-shrink-0">
                <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide snap-x snap-mandatory">
                    ...
                </div>
            </div> --}}

            {{-- Full List (Half/Full State) --}}
            <div x-show="bottomSheetState !== 'collapsed'" class="flex-1 overflow-y-auto px-4 pb-8">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-4 sticky top-0 bg-white dark:bg-slate-800 py-2 z-10">
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-bold text-slate-800 dark:text-white" x-text="visiblePlaces.length"></span>
                        <span class="text-sm text-slate-400">Destinasi</span>
                    </div>
                    <button @click="toggleSortNearby()" 
                            :class="sortByDistance ? 'bg-sky-500 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                            class="px-4 py-2 rounded-full text-xs font-bold flex items-center gap-2 transition-colors active:scale-95">
                        <span class="material-symbols-outlined text-base">near_me</span>
                        <span x-text="sortByDistance ? 'Terdekat' : 'Urutkan'"></span>
                    </button>
                </div>

                {{-- Empty State --}}
                <template x-if="visiblePlaces.length === 0">
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-3xl text-slate-400">location_off</span>
                        </div>
                        <p class="text-slate-500 font-medium">Tidak ada destinasi</p>
                        <p class="text-xs text-slate-400 mt-1">Coba ubah filter kategori</p>
                    </div>
                </template>

                {{-- List Items --}}
                <div class="space-y-3">
                    <template x-for="place in visiblePlaces" :key="place.id">
                        <div @click="selectPlace(place)" 
                             class="flex gap-4 p-4 rounded-2xl cursor-pointer active:scale-[0.98] transition-all bg-slate-50 dark:bg-slate-700/50">
                            <div class="w-20 h-20 rounded-xl bg-slate-200 dark:bg-slate-600 overflow-hidden flex-shrink-0">
                                <template x-if="place.image_path">
                                    <img :src="'{{ url('/') }}/' + place.image_path" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!place.image_path">
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-image text-2xl"></i>
                                    </div>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                <h4 class="font-bold text-base text-slate-800 dark:text-white line-clamp-2" x-text="place.name"></h4>
                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300" x-text="place.category?.name"></span>
                                    <template x-if="place.distance">
                                        <span class="text-xs text-sky-500 font-medium flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">directions_walk</span>
                                            <span x-text="place.distance + ' km'"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-slate-300">chevron_right</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================= --}}
    {{-- DESKTOP LAYOUT (>= 1024px) --}}
    {{-- ============================================= --}}
    <div class="hidden lg:block">
        @include('public.explore-map._sidebar')
        @include('public.explore-map._map-controls')
    </div>

    {{-- Detail Panel (Both Mobile & Desktop) --}}
    @include('public.explore-map._detail-panel')

    {{-- Proximity Modal --}}
    @include('public.explore-map._proximity-modal')

    {{-- Start Journey Button --}}
    <div x-show="hasActiveRoute && !isNavigating" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-20 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-20 opacity-0"
         class="fixed left-1/2 -translate-x-1/2 z-[600] transition-all duration-300 ease-out"
         :style="{
             bottom: bottomSheetState === 'collapsed' ? '220px' : 
                    (bottomSheetState === 'half' ? 'calc(55% + 20px)' : '30px')
         }"
         x-cloak>
        <div class="flex flex-col items-center gap-2">
            {{-- Route Info Pill --}}
            <div x-show="routeDistance && routeTime" 
                 class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-md px-4 py-2 rounded-full shadow-lg border border-slate-200/50 dark:border-slate-700 flex items-center gap-3 text-sm">
                <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-200">
                    <span class="material-symbols-outlined text-sky-500 text-base">straighten</span>
                    <span class="font-bold" x-text="routeDistance + ' km'"></span>
                </div>
                <div class="w-px h-4 bg-slate-300 dark:bg-slate-600"></div>
                <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-200">
                    <span class="material-symbols-outlined text-emerald-500 text-base">schedule</span>
                    <span class="font-medium" x-text="'~' + (routeTime >= 60 ? Math.floor(routeTime/60) + ' jam ' + (routeTime % 60 > 0 ? (routeTime % 60) + ' mnt' : '') : routeTime + ' menit')"></span>
                </div>
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex items-center gap-2">
                {{-- Start Navigation Button --}}
                <button @click="toggleLiveNavigation()" class="h-12 pl-4 pr-6 bg-sky-600 hover:bg-sky-700 text-white rounded-full shadow-lg shadow-sky-600/30 flex items-center gap-2 active:scale-95 transition-all">
                     <span class="material-symbols-outlined text-[20px]">near_me</span>
                     <span class="font-bold text-sm tracking-wide">Mulai</span>
                </button>
                
                {{-- Cancel Route Button --}}
                <button @click="cancelRoute()" 
                        class="w-12 h-12 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-full shadow-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center active:scale-95 transition-all"
                        title="Batalkan Rute">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Navigation Mode Overlay --}}
    <div x-show="isNavigating" x-transition.opacity.duration.500ms class="fixed inset-0 z-[2000] pointer-events-none flex flex-col justify-between p-6" x-cloak>
        {{-- Top Bar --}}
        <div class="pointer-events-auto bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4 animate-slide-down">
             <div class="w-12 h-12 rounded-xl bg-sky-500 flex items-center justify-center text-white shrink-0">
                 <span class="material-symbols-outlined text-2xl animate-pulse">navigation</span>
             </div>
             <div class="flex-1 min-w-0">
                 <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menuju Lokasi</p>
                 <h2 class="text-lg font-bold text-slate-800 dark:text-white truncate" x-text="navigationDestination?.name || 'Destinasi'"></h2>
                 <p class="text-xs text-sky-600 dark:text-sky-400 font-medium mt-0.5">Mode Navigasi Aktif</p>
             </div>
        </div>

        {{-- Bottom Controls --}}
        <div class="pointer-events-auto flex items-center justify-center pb-8 animate-slide-up">
            <button @click="toggleLiveNavigation()" class="group relative px-8 py-4 bg-red-500 hover:bg-red-600 text-white rounded-full font-bold shadow-lg shadow-red-500/30 transition-all active:scale-95 flex items-center gap-3">
                <span class="material-symbols-outlined text-2xl">stop_circle</span>
                <span>Akhiri Perjalanan</span>
            </button>
        </div>
    </div>

    {{-- Scripts --}}
    @include('public.explore-map._scripts')
</body>
</html>
