<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jelajahi Destinasi</title>
    <link rel="icon" href="{{ asset('images/logo-kabupaten-jepara.png') }}" type="image/png">
    
    
    <!-- Local assets handled by Vite -->
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #d1d5db; border-radius: 20px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4b5563; }
        
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .filled-icon { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        
        #leaflet-map { height: 100%; width: 100%; z-index: 0; }
        [x-cloak] { display: none !important; }
        
        /* Marker Animations */
        .custom-marker { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .custom-marker:hover { transform: scale(1.25); z-index: 1000 !important; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main-light dark:text-text-main-dark font-display h-screen flex flex-col overflow-hidden" x-data="mapComponent()">

    <!-- Main Layout Container -->
    <div class="flex flex-1 h-full w-full overflow-hidden flex-col lg:flex-row relative">
        
        <!-- Mobile Header / Toggle -->
        <div class="lg:hidden p-4 bg-white dark:bg-surface-dark border-b border-surface-light flex items-center justify-between z-30">
            <a href="{{ route('welcome') }}" class="text-xl font-bold font-display hover:text-primary transition-colors">Pesona Jepara</a>
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg bg-surface-light text-text-light">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>

        <!-- Sidebar -->
        <aside class="w-full lg:w-[400px] xl:w-[450px] flex-shrink-0 flex flex-col bg-white dark:bg-[#24211b] border-b lg:border-b-0 lg:border-r border-[#e5e7eb] dark:border-[#3a3630] z-20 shadow-xl transition-transform duration-300 absolute lg:relative h-full"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <!-- Header Section -->
            <div class="p-6 pb-4 border-b border-surface-light dark:border-stone-800 bg-white/50 dark:bg-[#24211b]/50 backdrop-blur-sm sticky top-0 z-10">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                         <a href="{{ route('welcome') }}" class="flex items-center justify-center w-8 h-8 rounded-full bg-surface-light hover:bg-primary hover:text-white transition group" title="Kembali ke Beranda">
                            <span class="material-symbols-outlined text-sm group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                         </a>
                        <h1 class="text-3xl font-bold leading-tight tracking-tight font-display bg-gradient-to-r from-text-light to-text-light/70 dark:from-text-dark dark:to-text-dark/70 bg-clip-text text-transparent">Kabupaten Jepara</h1>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="px-6 py-4">
                <div class="relative flex items-center w-full h-12 rounded-lg bg-[#f0eeea] dark:bg-[#322f29] focus-within:ring-2 focus-within:ring-primary/50 transition-all">
                    <div class="grid place-items-center h-full w-12 text-text-light/50 dark:text-text-dark/50">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input class="peer h-full w-full bg-transparent border-none text-text-light dark:text-text-dark placeholder:text-text-light/50 dark:placeholder:text-text-dark/50 focus:ring-0 text-base font-normal leading-normal font-sans" 
                           id="search" placeholder="Cari nama lokasi, jalan..." type="text"
                           x-model="searchQuery" @input.debounce.300ms="performSearch()">
                           
                     <!-- Search Dropdown -->
                    <div x-show="searchResults.length > 0" @click.outside="searchResults = []"
                         class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-surface-light max-h-60 overflow-y-auto z-50 p-2" 
                         x-cloak x-transition>
                        <template x-for="result in searchResults" :key="result.id">
                            <button @click="selectFeature(result)" class="w-full text-left px-3 py-2 hover:bg-surface-light rounded-lg transition flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-sm">location_on</span>
                                <div>
                                    <p class="font-bold text-sm text-text-light dark:text-text-dark font-display" x-text="result.name"></p>
                                    <p class="text-xs text-text-light/50" x-text="result.type"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Filter Dropdowns (Accordions) -->
            <div class="px-6 py-4 border-b border-dashed border-stone-200 dark:border-stone-800">
                <div class="space-y-3">
                    

                    <!-- Categories Dropdown -->
                    <div x-data="{ expanded: true }" class="border border-stone-200 dark:border-stone-700 rounded-xl bg-white dark:bg-[#2c2923] overflow-hidden">
                        <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-3 bg-stone-50 dark:bg-white/5 hover:bg-stone-100 dark:hover:bg-white/10 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-stone-500 text-lg">category</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-text-light dark:text-text-dark font-display">Kategori Lokasi</span>
                                <span class="ml-2 flex h-5 w-5 items-center justify-center rounded-full bg-stone-200 dark:bg-stone-700 text-[10px] text-text-light dark:text-text-dark font-bold" x-text="selectedCategories.length"></span>
                            </div>
                            <span class="material-symbols-outlined text-stone-400 transition-transform duration-200" :class="expanded ? 'rotate-180' : ''">keyboard_arrow_down</span>
                        </button>
                        <div x-show="expanded" x-collapse class="p-3 border-t border-stone-100 dark:border-stone-700 bg-white dark:bg-[#2c2923]">
                            <div class="flex gap-2 flex-wrap">
                                @foreach($categories as $category)
                                <button @click="toggleCategory({{ $category->id }})" 
                                        :class="selectedCategories.includes({{ $category->id }}) ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white dark:bg-[#322f29] text-text-light dark:text-text-dark hover:bg-stone-50 border-stone-200 dark:border-stone-700'"
                                        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all border text-xs font-medium active:scale-95 flex-grow justify-center">
                                    <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }} text-sm"></i>
                                    <span>{{ $category->name }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar px-6 relative">
                
                <!-- Sticky Toolbar -->
                <div class="sticky top-0 bg-white/95 dark:bg-[#24211b]/95 backdrop-blur-md z-10 pb-4 pt-4 -mx-6 px-6 border-b border-stone-100 dark:border-stone-800 mb-4 transition-all sidebar-header">
                    <div class="flex items-center justify-between">
                         <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-text-light dark:text-text-dark font-display" x-text="visiblePlaces.length"></span>
                            <span class="text-xs font-bold uppercase tracking-widest text-text-light/40 dark:text-text-dark/40 font-display">Lokasi</span>
                         </div>
                         
                         <button @click="toggleSortNearby()" 
                                 :class="sortByDistance ? 'bg-primary text-white shadow-primary/30 shadow-lg ring-2 ring-primary/20' : 'bg-stone-100 dark:bg-white/5 text-text-light/60 dark:text-text-dark/60 hover:bg-stone-200 dark:hover:bg-white/10'"
                                 class="text-[10px] font-bold px-3 py-1.5 rounded-full transition-all flex items-center gap-1.5 active:scale-95 group">
                             <span class="material-symbols-outlined text-base transition-transform group-hover:scale-110" :class="sortByDistance ? 'text-white' : ''">near_me</span>
                             <span x-text="sortByDistance ? 'Terdekat' : 'Cari Terdekat'"></span>
                         </button>
                    </div>
                </div>
                
                 <!-- List of Places -->
                 <div class="pb-6 space-y-3">
                     <template x-if="visiblePlaces.length === 0">
                         <div class="text-center py-12 px-4 flex flex-col items-center justify-center">
                             <div class="w-16 h-16 bg-stone-100 dark:bg-white/5 rounded-full flex items-center justify-center mb-4 text-stone-300 dark:text-stone-600">
                                 <span class="material-symbols-outlined text-3xl">location_off</span>
                             </div>
                             <p class="text-text-light/50 dark:text-text-dark/50 font-medium font-display">Tidak ada lokasi ditemukan</p>
                             <p class="text-xs text-text-light/30 dark:text-text-dark/30 mt-1 max-w-[200px]">Coba ubah kata kunci pencarian atau filter kategori.</p>
                         </div>
                     </template>

                     <template x-for="place in visiblePlaces" :key="place.id">
                        <div @click="selectPlace(place)" 
                             :class="selectedFeature && selectedFeature.id === place.id ? 'border-primary ring-1 ring-primary bg-primary/5 dark:bg-primary/10' : 'border-transparent bg-white dark:bg-[#2c2923] hover:border-stone-200 dark:hover:border-stone-700'"
                             class="flex gap-4 p-4 rounded-2xl border shadow-[0_2px_8px_rgba(0,0,0,0.04)] dark:shadow-none hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] dark:hover:shadow-none cursor-pointer transition-all duration-300 group relative overflow-hidden">
                            
                            <!-- Active Indicator Strip -->
                            <div x-show="selectedFeature && selectedFeature.id === place.id" class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>

                            <!-- Image -->
                            <div class="w-20 h-20 rounded-xl bg-gray-200 shrink-0 relative overflow-hidden ring-1 ring-black/5 dark:ring-white/10 group-hover:ring-primary/30 transition-all">
                                <template x-if="place.image_path">
                                    <img :src="'{{ url('/') }}/' + place.image_path" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                </template>
                                <template x-if="!place.image_path">
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-600 bg-gray-50 dark:bg-white/5">
                                        <i class="fa-solid fa-map-marker-alt text-2xl opacity-50"></i>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex flex-col min-w-0 flex-1 py-0.5">
                                <div class="flex justify-between items-start gap-2">
                                    <h4 class="font-bold text-base text-text-light dark:text-text-dark group-hover:text-primary transition-colors leading-tight line-clamp-2 font-display" x-text="place.name"></h4>
                                </div>
                                
                                <div class="mt-auto flex items-center justify-between">
                                    <!-- Category Badge -->
                                    <div class="flex items-center gap-1.5 px-2 py-1 rounded-md bg-stone-100 dark:bg-white/5 w-fit">
                                         <span class="w-1.5 h-1.5 rounded-full" :style="`background-color: ${place.category?.color || '#ccc'}`"></span>
                                         <p class="text-[10px] font-bold uppercase tracking-wider text-text-light/60 dark:text-text-dark/60 truncate max-w-[80px]" x-text="place.category?.name"></p>
                                    </div>

                                    <!-- Distance Badge -->
                                    <template x-if="place.distance">
                                        <div class="flex items-center gap-1 text-primary">
                                            <span class="material-symbols-outlined text-[14px]">directions_walk</span>
                                            <span class="text-xs font-bold" x-text="place.distance + ' km'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Hover Arrow -->
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                                <span class="material-symbols-outlined text-stone-300 dark:text-stone-600">chevron_right</span>
                            </div>
                        </div>
                     </template>
                </div>
                
                <!-- Footer -->
                <div class="mt-4 pt-6 border-t border-[#e5e7eb] dark:border-[#3a3630]">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-text-light/40 dark:text-text-dark/40 font-sans">© 2025 SIG Dinas Pariwisata</span>
                    </div>
                </div>
            </div>
            
        </aside>

        <!-- Map Canvas -->
        <main class="flex-1 relative bg-[#e5e3df] dark:bg-[#1a1814] overflow-hidden group/map z-10">
            <!-- Leaflet Map -->
            <div id="leaflet-map" class="w-full h-full z-0"></div>

            <!-- Detail Slide-Over (Right) -->
            <div x-show="selectedFeature" 
                 @click.outside="selectedFeature = null"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 class="absolute top-4 right-4 bottom-24 w-80 max-h-[calc(100vh-8rem)] bg-white/95 dark:bg-[#2c2923]/95 backdrop-blur-md rounded-2xl shadow-2xl z-[500] border border-white/50 dark:border-white/10 flex flex-col overflow-hidden"
                 x-cloak>
                
                <!-- Header Image -->
                <div class="h-40 bg-black/10 relative shrink-0">
                    <template x-if="selectedFeature?.image_url">
                        <img :src="selectedFeature.image_url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedFeature?.image_url">
                        <div class="w-full h-full flex items-center justify-center text-text-light/20 bg-black/5 dark:bg-white/5">
                            <span class="material-symbols-outlined text-4xl">image</span>
                        </div>
                    </template>
                    <button @click="selectedFeature = null" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur flex items-center justify-center transition">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/80 via-black/50 to-transparent">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white/20 text-white backdrop-blur uppercase tracking-wider" x-text="selectedFeature?.type || 'LOKASI'"></span>
                        </div>
                        <h3 class="text-white font-bold text-xl leading-tight text-shadow font-display" x-text="selectedFeature?.name"></h3>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto custom-scrollbar p-5 space-y-4">
                    <!-- Description -->
                    <div>
                        <h4 class="text-[10px] font-bold text-text-light/40 dark:text-text-dark/40 uppercase tracking-widest mb-1 font-display">Deskripsi</h4>
                        <p class="text-xs text-text-light/80 dark:text-text-dark/80 leading-relaxed font-sans" x-text="selectedFeature?.description || 'Tidak ada deskripsi tersedia.'"></p>
                    </div>
                    
                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <template x-if="selectedFeature?.area">
                            <div class="bg-surface-light dark:bg-white/5 p-3 rounded-xl">
                                <p class="text-[10px] uppercase text-text-light/40 dark:text-text-dark/40 font-bold mb-1 font-display">Luas Area</p>
                                <p class="font-bold text-text-light dark:text-text-dark text-base"><span x-text="selectedFeature.area"></span> <span class="text-xs font-normal">ha</span></p>
                            </div>
                        </template>
                        <template x-if="selectedFeature?.owner">
                            <div class="bg-surface-light dark:bg-white/5 p-3 rounded-xl">
                                <p class="text-[10px] uppercase text-text-light/40 dark:text-text-dark/40 font-bold mb-1 font-display">Pemilik</p>
                                <p class="font-bold text-text-light dark:text-text-dark text-base" x-text="selectedFeature.owner"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-2 mt-auto">
                         <div class="grid grid-cols-2 gap-2">
                             <button @click="startRouting(selectedFeature)" class="py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/20 transition flex items-center justify-center gap-2 transform active:scale-95 font-display">
                                <span class="material-symbols-outlined text-sm">directions</span> Rute
                            </button>
                             <button @click="openGoogleMaps(selectedFeature)" class="py-2.5 bg-white dark:bg-white/10 hover:bg-gray-50 dark:hover:bg-white/20 text-text-light dark:text-text-dark border border-stone-200 dark:border-stone-700 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 transform active:scale-95 font-display">
                                <i class="fa-brands fa-google text-red-500"></i> Maps
                            </button>
                         </div>
                         
                         <!-- Toggle Instructions Button (Only visible if route exists) -->
                         <template x-if="routingControl">
                             <button @click="toggleNavigationInstructions()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-2 transform active:scale-95 font-display">
                                <span class="material-symbols-outlined text-sm">list_alt</span> Lihat Petunjuk Arah
                            </button>
                         </template>
                         
                        <button @click="zoomToFeature(selectedFeature)" class="w-full py-2.5 bg-surface-light hover:bg-stone-200 text-text-light rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 transform active:scale-95 font-display">
                            <span class="material-symbols-outlined text-sm">my_location</span> Zoom ke Lokasi
                        </button>
                    </div>
                </div>
            </div>

            <!-- Floating Controls (Top Right) -->
            <div class="absolute top-4 right-4 flex flex-col gap-2 z-[400]">
                <!-- Layer Toggle with Checkboxes -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center justify-center h-10 w-10 bg-white dark:bg-[#2c2923] rounded-lg shadow-md text-text-light dark:text-text-dark hover:bg-stone-50 dark:hover:bg-stone-700 transition-colors">
                        <span class="material-symbols-outlined">layers</span>
                    </button>
                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false" class="absolute top-0 right-12 w-48 bg-white dark:bg-[#2c2923] p-3 rounded-lg shadow-xl border border-stone-100 dark:border-stone-800" x-cloak x-transition>
                         <p class="text-[10px] font-bold uppercase text-text-light/50 mb-2 font-display">Peta Dasar</p>
                         <div class="flex gap-1">
                             <button @click="setBaseLayer('streets')" :class="currentBaseLayer === 'streets' ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300'" class="flex-1 py-1.5 text-[10px] rounded-lg font-bold font-display transition-colors">Jalan</button>
                             <button @click="setBaseLayer('satellite')" :class="currentBaseLayer === 'satellite' ? 'bg-primary text-white' : 'bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300'" class="flex-1 py-1.5 text-[10px] rounded-lg font-bold font-display transition-colors">Satelit</button>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Floating Controls (Bottom Right) -->
            <div class="absolute bottom-6 right-6 flex flex-col gap-4 z-[400]">
                <!-- Zoom Controls -->
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white dark:bg-[#2c2923] divide-y divide-stone-100 dark:divide-stone-700">
                    <button @click="map.zoomIn()" class="flex items-center justify-center h-10 w-10 text-text-light dark:text-text-dark hover:bg-stone-50 dark:hover:bg-stone-700 active:bg-stone-100">
                        <span class="material-symbols-outlined">add</span>
                    </button>
                    <button @click="map.zoomOut()" class="flex items-center justify-center h-10 w-10 text-text-light dark:text-text-dark hover:bg-stone-50 dark:hover:bg-stone-700 active:bg-stone-100">
                        <span class="material-symbols-outlined">remove</span>
                    </button>
                </div>
                <!-- Navigation Toggle -->
                <button @click="toggleLiveNavigation()" 
                        :class="isNavigating ? 'bg-red-500 text-white animate-pulse' : 'bg-white dark:bg-[#2c2923] text-text-light dark:text-text-dark'"
                        class="flex items-center justify-center h-12 w-12 rounded-full shadow-lg hover:scale-105 transition-all">
                    <span class="material-symbols-outlined" x-text="isNavigating ? 'navigation' : 'near_me'"></span>
                </button>
                
                <!-- My Location -->
                <button @click="locateUser(null, true)" class="flex items-center justify-center h-12 w-12 bg-primary text-[#171511] rounded-full shadow-lg hover:bg-primary/90 hover:scale-105 transition-all">
                    <span class="material-symbols-outlined">my_location</span>
                </button>
            </div>

            <!-- Legend Overlay (Bottom Left) -->
            <div class="absolute bottom-6 left-6 z-[400] hidden sm:block">
                <div class="bg-white/95 dark:bg-[#2c2923]/95 backdrop-blur-sm p-3 rounded-lg shadow-lg border border-stone-200 dark:border-stone-800">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-text-light/40 dark:text-text-dark/40 mb-2 font-display">Legenda</h5>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-emerald-500/30"></span>
                            <span class="text-xs font-medium text-text-light dark:text-text-dark font-sans">Batas Wilayah</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Loading Overlay -->
            <div x-show="loading" class="absolute inset-0 bg-white/50 backdrop-blur-[2px] z-[1000] flex items-center justify-center" x-transition.opacity>
                <div class="bg-white p-4 rounded-xl shadow-xl flex items-center gap-3">
                    <div class="w-6 h-6 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-sm font-bold text-text-light font-display">Memuat Peta...</span>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    
    <script>
        function mapComponent() {
            return {
                map: null,
                loading: true,
                sidebarOpen: true,
                activeTab: 'places', // Default logic
                currentBaseLayer: 'satellite',
                baseLayers: {},
                categories: @json($categories),
                selectedCategories: [],
                showBoundaries: true,
                sortByDistance: false, // New State

                allPlaces: [],
                geoFeatures: [],
                searchQuery: '',
                searchResults: [],
                selectedFeature: null,
                markers: [],
                boundariesLayer: null,
                routingControl: null, // Routing instance
                
                // Proximity Alert State
                nearbyAlert: null,
                notifiedPlaces: new Set(),
                watchId: null,
                
                // Navigation Mode State
                isNavigating: false,
                heading: 0,
                wakeLock: null,

                defaultCenter: [-6.59, 110.68],
                defaultZoom: 10,
                userMarker: null,
                userLocation: null, // Store user coords

                get visiblePlaces() {
                    // Logic to show all places if no category selected, OR matching categories
                    const ids = this.selectedCategories.length > 0 ? this.selectedCategories : this.categories.map(c => c.id);
                    let places = this.allPlaces.filter(p => ids.includes(p.properties.category?.id))
                         .map(p => ({
                             ...p.properties,
                             image_path: p.properties.image_path,
                             category: p.properties.category,
                             latitude: p.geometry.coordinates[1],
                             longitude: p.geometry.coordinates[0],
                             distance: this.calculateDistance(p.geometry.coordinates[1], p.geometry.coordinates[0]) // Add distance
                         }));
                    
                    if (this.sortByDistance) {
                        places.sort((a, b) => (a.distance || Infinity) - (b.distance || Infinity));
                    }
                    
                    return places;
                },

                init() {
                    // Start with all categories selected
                    this.selectedCategories = this.categories.map(c => c.id);
                    this.initMap();
                    this.fetchAllData();
                    
                    this.$watch('selectedCategories', () => this.updateMapMarkers());
                    this.$watch('showBoundaries', () => this.loadBoundaries());
                    
                    // Device Orientation for Compass
                    if (window.DeviceOrientationEvent) {
                        window.addEventListener('deviceorientation', (e) => this.handleOrientation(e));
                    }

                    
                    if (window.innerWidth < 1024) { this.sidebarOpen = false; }
                },
                
                handleOrientation(event) {
                    if (!this.isNavigating) return;
                    
                    // Calculate heading
                    let heading = event.alpha; 
                    if (event.webkitCompassHeading) {
                        // iOS
                        heading = event.webkitCompassHeading;
                    }
                    
                    this.heading = heading;
                    
                    // Rotate marker if it exists
                    if (this.userMarker) {
                         const icon = this.userMarker.getElement();
                         if (icon) {
                             const arrow = icon.querySelector('.user-arrow');
                             if (arrow) {
                                 arrow.style.transform = `rotate(${heading}deg)`;
                             }
                         }
                    }
                },
                
                async toggleLiveNavigation() {
                    this.isNavigating = !this.isNavigating;
                    
                    if (this.isNavigating) {
                        // Enter Navigation Mode
                        this.sidebarOpen = false;
                        this.selectedFeature = null; // Clear selection to see full map
                        this.map.closePopup();
                        
                        // Request Wake Lock
                        try {
                            if ('wakeLock' in navigator) {
                                this.wakeLock = await navigator.wakeLock.request('screen');
                            }
                        } catch (err) { console.log('Wake Lock error:', err); }
                        
                        // Force locate and zoom
                        this.locateUser(() => {
                            this.map.setZoom(19);
                        }, true); // true = force follow
                        
                    } else {
                        // Exit Navigation Mode
                        if (this.wakeLock) {
                            this.wakeLock.release();
                            this.wakeLock = null;
                        }
                        this.map.setZoom(15);
                    }
                },

                toggleCategory(id) {
                    if (this.selectedCategories.includes(id)) {
                        this.selectedCategories = this.selectedCategories.filter(c => c !== id);
                    } else {
                        this.selectedCategories.push(id);
                    }
                    // Loop is reactive via watch
                },
                
                toggleSortNearby() {
                    if (!this.userLocation) {
                        this.locateUser(() => {
                            this.sortByDistance = !this.sortByDistance;
                        });
                    } else {
                        this.sortByDistance = !this.sortByDistance;
                    }
                },

                calculateDistance(lat2, lon2) {
                    if (!this.userLocation) return null;
                    const lat1 = this.userLocation.lat;
                    const lon1 = this.userLocation.lng;
                    const R = 6371; // km
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                            Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return (R * c).toFixed(1);
                },

                initMap() {
                    this.map = L.map('leaflet-map', { zoomControl: false, attributionControl: false }).setView(this.defaultCenter, this.defaultZoom);
                    
                    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'] });
                    const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'] });

                    this.baseLayers = { 'streets': googleStreets, 'satellite': googleSatellite };
                    this.baseLayers['satellite'].addTo(this.map);
                },

                setBaseLayer(type) {
                    if (this.currentBaseLayer === type) return;
                    this.map.removeLayer(this.baseLayers[this.currentBaseLayer]);
                    this.currentBaseLayer = type;
                    this.baseLayers[type].addTo(this.map);
                },

                async fetchAllData() {
                    try {
                        this.loading = true;
                        const [places, boundaries] = await Promise.all([
                            fetch('{{ route('places.geojson') }}').then(r => r.json()),
                            fetch('{{ route('boundaries.geojson') }}').then(r => r.json())
                        ]);

                        this.geoFeatures = places.features || [];
                        this.allPlaces = places.features || [];
                        
                        // Keep raw features for toggle loading
                        this.boundariesFeatures = boundaries.features || [];
                        this.loadBoundaries();
                        
                        this.updateMapMarkers();

                    } catch (e) {
                        console.error('Error loading data:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                updateLayers() {
                     // Handled by watchers now
                },

                loadBoundaries() {
                    if (this.boundariesLayer) this.map.removeLayer(this.boundariesLayer);
                    if (!this.showBoundaries) return;
                    this.boundariesLayer = L.geoJSON(this.boundariesFeatures, {
                        style: { color: '#10b981', weight: 2, fillColor: '#10b981', fillOpacity: 0.1, dashArray: '5, 5' },
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => { L.DomEvent.stop(e); this.selectFeature({...f.properties, type: 'Batas Wilayah'}); });
                        }
                    }).addTo(this.map);
                },



                updateMapMarkers() {
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];
                    
                    const visible = this.visiblePlaces; // Use localized computed getter
                    
                    visible.forEach(p => {
                         const color = p.category?.color || '#3b82f6';
                         const iconHtml = `
                            <div class="w-9 h-9 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-sm custom-marker" style="background-color: ${color}">
                                <i class="${p.category?.icon_class ?? 'fa-solid fa-map-marker-alt'}"></i>
                            </div>
                        `;
                        const marker = L.marker([p.latitude, p.longitude], {
                             icon: L.divIcon({ html: iconHtml, className: '', iconSize: [36, 36], iconAnchor: [18, 18] })
                        });
                        marker.on('click', () => { this.selectPlace(p); });
                        marker.addTo(this.map);
                        this.markers.push(marker);
                    });
                },

                performSearch() {
                    if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                    const q = this.searchQuery.toLowerCase();
                    this.searchResults = this.allPlaces.filter(p => p.properties.name.toLowerCase().includes(q))
                        .map(p => ({ ...p.properties, type: 'Lokasi', latitude: p.geometry.coordinates[1], longitude: p.geometry.coordinates[0] }))
                        .slice(0, 5);
                },

                selectFeature(feat) {
                    this.selectedFeature = feat;
                    this.zoomToFeature(feat);
                    // On mobile, keep sidebar open to show detail
                },

                selectPlace(place) {
                     this.selectedFeature = {
                        ...place,
                        type: 'Lokasi',
                        image_url: place.image_url || (place.image_path ? '{{ url('/') }}/' + place.image_path : null)
                    };
                    this.zoomToFeature(place);
                    // if (window.innerWidth < 1024 && !this.sidebarOpen) { this.sidebarOpen = true; } 
                },

                zoomToFeature(feature) {
                    if (feature.latitude && feature.longitude) {
                        this.map.flyTo([feature.latitude, feature.longitude], 18);
                    } else if (feature.geometry) { // GeoJSON feature directly
                         const layer = L.geoJSON(feature);
                         this.map.fitBounds(layer.getBounds(), { padding: [100, 100] });
                    }
                },
                
                startRouting(destination) {
                    if (!this.userLocation) {
                        this.locateUser(() => this.calculateRoute(destination));
                    } else {
                        this.calculateRoute(destination);
                    }
                },
                
                calculateRoute(destination) {
                    if (this.routingControl) {
                        this.map.removeControl(this.routingControl);
                    }
                    
                    this.routingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(this.userLocation.lat, this.userLocation.lng),
                            L.latLng(destination.latitude, destination.longitude)
                        ],
                        routeWhileDragging: true,
                        lineOptions: {
                            styles: [{color: '#6FA1EC', opacity: 0.8, weight: 6}]
                        },
                        show: true, // MUST be true to generate the itinerary HTML
                        addWaypoints: false,
                        draggableWaypoints: false,
                        fitSelectedRoutes: true,
                        createMarker: function() { return null; }, // Don't create default markers, use ours
                        containerClassName: 'routing-container custom-scrollbar' // Add class for styling
                    }).addTo(this.map);
                    
                    // Force hide initially (we toggle it with the button)
                    setTimeout(() => {
                        const container = this.routingControl.getContainer();
                        if (container) container.style.display = 'none';
                    }, 100);
                    
                    // Close sidebar to show map/route
                    if (window.innerWidth < 1024) { this.sidebarOpen = false; }
                },

                openGoogleMaps(destination) {
                    if (!this.userLocation) {
                        alert('Lokasi anda belum terdeteksi.');
                        return;
                    }
                    const url = `https://www.google.com/maps/dir/?api=1&destination=${destination.latitude},${destination.longitude}&travelmode=driving`;
                    window.open(url, '_blank');
                },
                
                toggleNavigationInstructions() {
                     if (!this.routingControl) return;
                     // Toggle the routing control container visibility
                     const container = this.routingControl.getContainer();
                     if (container) {
                         if (container.style.display === 'none') {
                             container.style.display = 'block';
                         } else {
                             container.style.display = 'none';
                         }
                     }
                },

                locateUser(callback = null, forceFollow = false) {
                    if (!navigator.geolocation) { alert('Browser tidak mendukung geolokasi'); return; }
                    this.loading = true;
                    
                    // Use watchPosition for continuous tracking
                    if (this.watchId) navigator.geolocation.clearWatch(this.watchId);
                    
                    this.watchId = navigator.geolocation.watchPosition(
                        (pos) => {
                            const { latitude, longitude } = pos.coords;
                            this.userLocation = { lat: latitude, lng: longitude };
                            
                            // Compass Marker HTML
                            const compassHtml = `
                                <div class="relative w-12 h-12 flex items-center justify-center">
                                    <div class="user-arrow w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-b-[16px] border-b-blue-600 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transition-transform duration-300 origin-center" style="transform: rotate(${this.heading}deg)"></div>
                                    <div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-md relative z-10 pulse"></div>
                                    <div class="absolute inset-0 bg-blue-500/10 rounded-full animate-ping"></div>
                                </div>
                            `;

                            // Update marker
                            if (this.userMarker) {
                                this.userMarker.setLatLng([latitude, longitude]);
                                // Update icon if needed (mostly just for the arrow rotation which is handled in handleOrientation)
                                // But if we just switched modes, we might want to ensure the arrow is there.
                                // For simplicity, we just set the icon again if it doesn't have the arrow class? 
                                // Actually, let's just use the compass icon always for the user.
                            } else {
                                this.userMarker = L.marker([latitude, longitude], {
                                    icon: L.divIcon({ html: compassHtml, className: '', iconSize: [48, 48], iconAnchor: [24, 24] })
                                }).addTo(this.map);
                            }
                            
                            // Navigation Mode: Auto-Center
                            if (this.isNavigating || forceFollow || this.loading) {
                                this.map.flyTo([latitude, longitude], this.isNavigating ? 18 : 17, { animate: true, duration: 1 });
                            }
                            
                            this.loading = false;
                            if (callback) callback();
                            
                            // Check Proximity
                            this.checkProximity(latitude, longitude);
                        },
                        (err) => { 
                            this.loading = false; 
                            console.error(err);
                        },
                        { enableHighAccuracy: true, maximumAge: 1000, timeout: 5000 }
                    );
                },
                
                checkProximity(lat, lng) {
                    const threshold = 0.5; // 500 meters
                    
                    this.allPlaces.forEach(place => {
                        // Skip if already notified
                        if (this.notifiedPlaces.has(place.properties.name)) return;
                        
                        const dist = this.calculateDistance(place.geometry.coordinates[1], place.geometry.coordinates[0]);
                        if (dist && parseFloat(dist) <= threshold) {
                            this.nearbyAlert = {
                                ...place.properties,
                                image_url: place.properties.image_path ? '{{ url('/') }}/' + place.properties.image_path : null
                            };
                            this.notifiedPlaces.add(place.properties.name);
                            
                            // Optional: Play sound or vibrate
                            if (navigator.vibrate) navigator.vibrate(200);
                        }
                    });
                }
            };
        }
    </script>
    
    <style>
        /* Custom Styling for Leaflet Routing Machine */
        .leaflet-routing-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 1rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-height: 40vh;
            overflow-y: auto;
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: 1px solid rgba(0,0,0,0.05);
            width: 320px !important; /* Force width */
            
            /* Positioning override */
            position: absolute !important;
            top: 20px !important;
            right: 20px !important;
            z-index: 9999 !important; /* Ensure on top of everything */
            display: none; /* Hidden by default */
        }
        .dark .leaflet-routing-container {
            background-color: rgba(44, 41, 35, 0.95);
            color: #eceae4;
            border-color: rgba(255,255,255,0.1);
        }
        .leaflet-routing-alt {
            max-height: 100%;
        }
        .leaflet-routing-alt tr:hover {
            background-color: rgba(0,0,0,0.03);
        }
        .dark .leaflet-routing-alt tr:hover {
            background-color: rgba(255,255,255,0.05);
        }
    </style>
    
    <!-- Proximity Alert Modal -->
    <div x-show="nearbyAlert" 
         class="fixed inset-0 z-[1000] flex items-end sm:items-center justify-center p-4 sm:p-0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-cloak>
        
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="nearbyAlert = null"></div>

        <div class="relative bg-white dark:bg-[#2c2923] rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border border-white/20">
            <!-- Image Header -->
            <div class="h-32 bg-gray-200 relative">
                <template x-if="nearbyAlert?.image_url">
                    <img :src="nearbyAlert.image_url" class="w-full h-full object-cover">
                </template>
                 <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-4">
                     <div>
                         <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1 block">Di Sekitar Anda</span>
                         <h3 class="text-white font-bold text-xl leading-tight" x-text="nearbyAlert?.name"></h3>
                     </div>
                 </div>
            </div>
            
            <div class="p-5">
                <p class="text-text-light/80 dark:text-text-dark/80 text-sm mb-6">
                    Anda berada dalam jarak 500m dari lokasi ini. Ingin melihat detailnya?
                </p>
                
                <div class="flex gap-3">
                    <button @click="nearbyAlert = null" class="flex-1 px-4 py-2 rounded-xl border border-stone-200 dark:border-stone-700 text-text-light dark:text-text-dark text-sm font-bold hover:bg-stone-50 dark:hover:bg-white/5 transition">
                        Tutup
                    </button>
                    <button @click="selectPlace({ properties: nearbyAlert, geometry: { coordinates: [0,0] } }); nearbyAlert = null;" class="flex-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-dark shadow-lg shadow-primary/20 transition">
                        Lihat Detail
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
