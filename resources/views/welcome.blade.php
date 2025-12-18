<!DOCTYPE html>
<html class="light scroll-smooth overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sistem Informasi Geografis - {{ config('app.name', 'Mayong Lor') }}</title>
    
    <!-- Leaflet & Icon -->
    <!-- Local assets handled by Vite -->

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
        
        /* Map Styles */
        #leaflet-map { height: 100%; width: 100%; z-index: 0; }
        
        /* Custom Marker Animations */
        .custom-marker { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .custom-marker:hover { transform: scale(1.25); z-index: 1000 !important; }
        
        .marker-pulse { animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
        
        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-display antialiased transition-colors duration-200 overflow-x-hidden" x-data="mapComponent()">

<!-- Top Navigation -->
<div class="sticky top-0 z-[10000] w-full border-b border-surface-light dark:border-surface-dark bg-background-light/95 dark:bg-background-dark/95 backdrop-blur-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <header class="flex h-20 items-center justify-between gap-8" x-data="{ mobileMenuOpen: false }">
            <div class="flex items-center gap-8">
                <a class="flex items-center gap-3 text-text-light dark:text-text-dark group" href="#">
                    <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" alt="Logo Kabupaten Jepara" class="w-10 h-auto object-contain">
                    <h2 class="text-xl font-bold leading-tight tracking-tight">Mayong Lor</h2>
                </a>
                <nav class="hidden lg:flex items-center gap-8">
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#">Beranda</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#gis-map">Peta GIS</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#profile">Profil</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#potency">Potensi</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors" href="#news">Berita</a>
                </nav>
            </div>
            
            <div class="flex flex-1 items-center justify-end gap-4">
                <!-- Search Bar -->
                <label class="hidden md:flex flex-col w-full max-w-xs h-10 relative">
                    <div class="flex w-full h-full items-center rounded-full bg-surface-light dark:bg-surface-dark px-4 transition-colors focus-within:ring-2 focus-within:ring-primary/50">
                        <span class="material-symbols-outlined text-gray-500 dark:text-gray-400">search</span>
                        <input class="w-full bg-transparent border-none text-sm px-3 text-text-light dark:text-text-dark placeholder-gray-500 focus:ring-0" 
                               placeholder="Cari lokasi, data..." type="text"
                               x-model="searchQuery" 
                               @input.debounce.300ms="performSearch()"
                               @keydown.enter="scrollToMap()"/>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="searchResults.length > 0" 
                         @click.outside="searchResults = []"
                         class="absolute top-12 left-0 right-0 bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-surface-light dark:border-surface-dark overflow-hidden z-50 max-h-80 overflow-y-auto" 
                         x-cloak
                         x-transition>
                        <template x-for="result in searchResults" :key="result.id || result.name">
                            <button @click="selectFeature(result); scrollToMap()" class="w-full text-left px-4 py-3 hover:bg-surface-light dark:hover:bg-black/20 border-b border-surface-light dark:border-surface-dark last:border-0 transition flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary flex-shrink-0">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-text-light dark:text-text-dark text-sm truncate" x-text="result.name"></p>
                                    <p class="text-xs text-text-light/60 dark:text-text-dark/60 truncate" x-text="result.type || 'Location'"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </label>

                <!-- Auth Buttons (Desktop) -->
                <div class="hidden lg:flex">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                <span class="truncate">Login</span>
                            </a>
                        @endauth
                    @endif
                </div>
                
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-full hover:bg-surface-light dark:hover:bg-surface-dark">
                    <span class="material-symbols-outlined" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                </button>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 @click.outside="mobileMenuOpen = false"
                 x-cloak
                 class="absolute top-20 left-0 w-full bg-background-light dark:bg-background-dark border-b border-surface-light dark:border-surface-dark shadow-xl lg:hidden z-50 p-4 flex flex-col gap-4">
                
                <nav class="flex flex-col gap-4">
                    <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="#">Beranda</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="#gis-map">Peta GIS</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="#profile">Profil</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="#potency">Potensi</a>
                    <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="#news">Berita</a>
                </nav>

                <div class="border-t border-surface-light dark:border-surface-dark pt-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="flex items-center justify-center rounded-xl h-12 w-full bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center justify-center rounded-xl h-12 w-full bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                Login
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>
    </div>
</div>

<div class="relative w-full">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="relative overflow-hidden rounded-xl h-[500px] lg:h-[600px] group bg-gray-900 border border-white/10 shadow-2xl">
            <!-- 3D Map Container -->
            <div id="hero-map" class="absolute inset-0 z-0 opacity-0 transition-opacity duration-[2000ms]"></div>
            
            <!-- Overlay Gradient for Readability -->
            <div class="absolute inset-0 z-10 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent pointer-events-none"></div>

            <!-- Content -->
            <div class="absolute inset-0 z-20 flex flex-col items-center justify-center p-6 text-center pointer-events-none">
                <div class="max-w-4xl space-y-6 pointer-events-auto">
                    <span class="hidden md:inline-block px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold uppercase tracking-wider animate-fade-in-down">
                        Selamat Datang di Sistem Informasi Desa
                    </span>
                    <h1 class="text-white text-3xl sm:text-5xl lg:text-7xl font-black leading-tight tracking-tight drop-shadow-sm animate-fade-in-up">
                        Desa Mayong Lor<br/>Pusat Gerabah & Sejarah
                    </h1>
                    <p class="text-gray-100 text-lg sm:text-xl font-medium max-w-2xl mx-auto leading-relaxed drop-shadow-sm animate-fade-in-up delay-100">
                        Menjelajahi potensi warisan budaya, ekonomi kreatif, dan transparansi data spasial untuk kemajuan bersama.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 animate-fade-in-up delay-200">
                        <a class="flex items-center justify-center h-12 px-8 rounded-full bg-primary hover:bg-primary-dark text-white text-base font-bold shadow-lg shadow-black/20 transition-all hover:-translate-y-0.5" href="{{ route('explore.map') }}">
                            Jelajahi Peta GIS
                        </a>
                        <a href="#profile" class="hidden sm:flex items-center justify-center h-12 px-8 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white text-base font-bold transition-all">
                            Profil Desa
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MapLibre Logic -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Ensure maplibregl is loaded
        if (typeof maplibregl === 'undefined') {
            console.error('MapLibre GL JS not loaded');
            return;
        }

        const map = new maplibregl.Map({
            container: 'hero-map',
            style: {
                version: 8,
                sources: {
                    'satellite': {
                        type: 'raster',
                        tiles: [
                            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
                        ],
                        tileSize: 256,
                        attribution: '&copy; Esri'
                    }
                },
                layers: [{
                    id: 'satellite-layer',
                    type: 'raster',
                    source: 'satellite',
                    paint: { 'raster-opacity': 1 }
                }]
            },
            center: [110.75611, -6.75611], // Mayong Lor (Corrected)
            zoom: 1.5, // Start from space
            pitch: 0,
            attributionControl: false,
            interactive: false // Disable interaction for background effect
        });

        map.on('load', () => {
            // Reveal map smoothly
            document.getElementById('hero-map').classList.remove('opacity-0');
            document.getElementById('hero-map').classList.add('opacity-80');

            // Add Boundary Source
            map.addSource('boundaries', {
                type: 'geojson',
                data: '/boundaries.geojson'
            });

            // 3D Extrusion Layer (Volume)
            map.addLayer({
                'id': 'boundary-extrusion',
                'type': 'fill-extrusion',
                'source': 'boundaries',
                'paint': {
                    'fill-extrusion-color': '#fbbf24', // Amber/Gold
                    'fill-extrusion-height': 20, // Reduced from 500m to 50m
                    'fill-extrusion-base': 0,
                    'fill-extrusion-opacity': 0.3
                }
            });

            // Boundary Outline (Line)
            map.addLayer({
                'id': 'boundary-line',
                'type': 'line',
                'source': 'boundaries',
                'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                'paint': {
                    'line-color': '#ffffff',
                    'line-width': 3,
                    'line-opacity': 0.8
                }
            });

            // "Fly To" Animation when map loads
            setTimeout(() => {
                const isMobile = window.innerWidth < 768;
                map.flyTo({
                    center: [110.75611, -6.75611],
                    zoom: isMobile ? 13.5 : 14.8, 
                    pitch: isMobile ? 45 : 60,
                    bearing: 0,
                    speed: 0.5,
                    curve: 1.2,
                    essential: true
                });
            }, 2000); // Increased delay to ensure tiles are ready

            // Wait for flyTo to likely finish
            map.once('moveend', () => {
                window.requestAnimationFrame(rotateCamera);
            });
        });

        let startTime;
        const rotationsPerMinute = 0.5; // Slow rotation

        function rotateCamera(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = timestamp - startTime;
            
            // Calculate bearing based on relative time to ensure smooth start from 0
            // 360 degrees / (60s / RPM * 1000ms) 
            const bearing = (progress / (60000 / rotationsPerMinute)) * 360;
            
            map.rotateTo(bearing % 360, { duration: 0 });
            window.requestAnimationFrame(rotateCamera);
        }
    });
</script>

<!-- Stats Section -->
<div class="w-full bg-background-light dark:bg-background-dark py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-8">
            <!-- Population -->
            <!-- Population -->
            <div class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                <div class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                    <span class="material-symbols-outlined">groups</span>
                </div>
                <div>
                    <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">Penduduk</p>
                    <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">{{ number_format($population?->total_population ?? 0) }}</p>
                    <p class="text-xs text-text-light/50">Jiwa ({{ $population?->updated_at?->format('Y') ?? date('Y') }})</p>
                </div>
            </div>
            <!-- Area -->
            <!-- Area -->
            <div class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                <div class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                    <span class="material-symbols-outlined">square_foot</span>
                </div>
                <div>
                    <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">Luas Wilayah</p>
                    <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">{{ number_format($totalArea ?? 0, 1) }}</p>
                    <p class="text-xs text-text-light/50">Hektar</p>
                </div>
            </div>
            <!-- Dukuh -->
            <!-- Dukuh -->
            <div class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                <div class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                    <span class="material-symbols-outlined">holiday_village</span>
                </div>
                <div>
                    <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">Dukuh</p>
                    <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">{{ $totalBoundaries ?? 0 }}</p>
                    <p class="text-xs text-text-light/50">Wilayah</p>
                </div>
            </div>
            <!-- Industry -->
            <!-- Industry -->
            <div class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                <div class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                    <span class="material-symbols-outlined">palette</span>
                </div>
                <div>
                    <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">Potensi</p>
                    <p class="text-text-light dark:text-text-dark text-xl font-bold tracking-tight">Sentra Gerabah</p>
                    <p class="text-xs text-text-light/50">Nasional</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GIS Map Section -->
<div class="w-full py-12 lg:py-20 scroll-mt-20" id="gis-map">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div class="max-w-2xl">
                <h2 class="text-3xl md:text-4xl font-bold text-text-light dark:text-text-dark tracking-tight">Interactive GIS Map</h2>
                <p class="mt-2 text-text-light/70 dark:text-text-dark/70 text-base md:text-lg">Navigate our village geography, check land use, and find public facilities.</p>
            </div>
            <div class="relative z-[500] flex flex-wrap gap-3" x-data="{ showLayers: false, showFilters: false }">
                <!-- Layers Toggle -->
                <div class="relative">
                    <button @click="showLayers = !showLayers" 
                            class="flex items-center gap-2 px-4 py-2 rounded-full bg-surface-light dark:bg-surface-dark text-text-light dark:text-text-dark font-medium hover:bg-primary/20 transition-colors text-sm md:text-base border border-surface-light dark:border-white/10">
                        <span class="material-symbols-outlined text-lg">layers</span>
                        Layers
                    </button>
                    <!-- Layers Dropdown -->
                    <div x-show="showLayers" @click.outside="showLayers = false" 
                         class="absolute top-full right-auto left-0 md:left-auto md:right-0 mt-2 w-72 bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-surface-light dark:border-surface-dark p-4 z-[1000]" x-cloak x-transition>
                         
                         <!-- Base Maps Section -->
                         <h4 class="text-xs font-bold uppercase tracking-wider text-text-light mb-3">Tipe Peta</h4>
                         <div class="grid grid-cols-2 gap-2 mb-4">
                            <button @click="setBaseLayer('satellite')" 
                                    :class="currentBaseLayer === 'satellite' ? 'ring-2 ring-primary border-transparent' : 'border-surface-light hover:border-primary/50'"
                                    class="relative h-16 rounded-lg border overflow-hidden group transition-all">
                                <img src="https://mt1.google.com/vt/lyrs=s&x=1325&y=3145&z=13" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <span class="text-white text-xs font-bold text-shadow">Satelit</span>
                                </div>
                            </button>
                            <button @click="setBaseLayer('streets')" 
                                    :class="currentBaseLayer === 'streets' ? 'ring-2 ring-primary border-transparent' : 'border-surface-light hover:border-primary/50'"
                                    class="relative h-16 rounded-lg border overflow-hidden group transition-all">
                                <img src="https://mt1.google.com/vt/lyrs=m&x=1325&y=3145&z=13" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <span class="text-white text-xs font-bold text-shadow">Jalan</span>
                                </div>
                            </button>
                         </div>

                         <div class="h-px bg-surface-light dark:bg-gray-700 my-3"></div>

                         <h4 class="text-xs font-bold uppercase tracking-wider text-text-light mb-3">Layer Data</h4>
                         <div class="space-y-3">
                            <label class="flex items-center justify-between cursor-pointer hover:bg-surface-light dark:hover:bg-white/5 p-1 rounded transition">
                                <span class="text-sm">Batas Wilayah</span>
                                <input type="checkbox" x-model="showBoundaries" @change="updateLayers()" class="rounded text-primary focus:ring-primary bg-transparent">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer hover:bg-surface-light dark:hover:bg-white/5 p-1 rounded transition">
                                <span class="text-sm">Infrastruktur</span>
                                <input type="checkbox" x-model="showInfrastructures" @change="updateLayers()" class="rounded text-primary focus:ring-primary bg-transparent">
                            </label>
                            <label class="flex items-center justify-between cursor-pointer hover:bg-surface-light dark:hover:bg-white/5 p-1 rounded transition">
                                <span class="text-sm">Penggunaan Lahan</span>
                                <input type="checkbox" x-model="showLandUses" @change="updateLayers()" class="rounded text-primary focus:ring-primary bg-transparent">
                            </label>
                         </div>
                    </div>
                </div>

                <!-- Filters Toggle -->
                <div>
                    <button @click="showFilters = !showFilters" 
                            class="flex items-center gap-2 px-4 py-2 rounded-full bg-surface-light dark:bg-surface-dark text-text-light dark:text-text-dark font-medium hover:bg-primary/20 transition-colors text-sm md:text-base border border-surface-light dark:border-white/10">
                        <span class="material-symbols-outlined text-lg">filter_alt</span>
                        Filters
                    </button>
                    <!-- Filters Dropdown -->
                    <div x-show="showFilters" @click.outside="showFilters = false" 
                         class="absolute top-full right-auto left-0 md:left-auto md:right-0 mt-2 w-64 bg-white dark:bg-surface-dark rounded-xl shadow-xl border border-surface-light dark:border-surface-dark p-4 z-[1000]" x-cloak x-transition>
                         <h4 class="text-xs font-bold uppercase tracking-wider text-text-light mb-3">Filter Places</h4>
                         <div class="space-y-2 max-h-60 overflow-y-auto custom-scroll">
                            @foreach($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-surface-light dark:hover:bg-white/5 p-1 rounded transition">
                                <input type="checkbox" value="{{ $category->id }}" x-model="selectedCategories" @change="updateMapMarkers()" class="rounded text-primary focus:ring-primary bg-transparent">
                                <span class="text-sm" style="color: {{ $category->color }}">{{ $category->name }}</span>
                            </label>
                            @endforeach
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAP CONTAINER -->
        <div class="relative w-full aspect-[4/3] md:aspect-[16/9] lg:aspect-[21/9] bg-surface-light dark:bg-surface-dark rounded-xl overflow-hidden shadow-lg border border-surface-light dark:border-surface-dark group">
            
            <!-- Real Leaflet Map -->
            <div id="leaflet-map" class="w-full h-full z-0"></div>

            <!-- Floating Map Controls -->
            <div class="absolute top-4 right-4 md:top-6 md:right-6 flex flex-col gap-2 z-[400]">
                <button @click="map.zoomIn()" class="size-10 flex items-center justify-center bg-white dark:bg-surface-dark rounded-full shadow-md hover:bg-gray-50 dark:hover:bg-black/40 text-text-light dark:text-text-dark transition-colors" title="Zoom In">
                    <span class="material-symbols-outlined">add</span>
                </button>
                <button @click="map.zoomOut()" class="size-10 flex items-center justify-center bg-white dark:bg-surface-dark rounded-full shadow-md hover:bg-gray-50 dark:hover:bg-black/40 text-text-light dark:text-text-dark transition-colors" title="Zoom Out">
                    <span class="material-symbols-outlined">remove</span>
                </button>
                <button @click="locateUser()" class="size-10 flex items-center justify-center bg-white dark:bg-surface-dark rounded-full shadow-md hover:bg-gray-50 dark:hover:bg-black/40 text-text-light dark:text-text-dark transition-colors mt-2" title="My Location">
                    <span class="material-symbols-outlined">my_location</span>
                </button>
            </div>

            <!-- Floating Legend (Static for demo visuals, effectively shows what IS possible) -->
            <div class="hidden md:block absolute bottom-6 left-6 p-4 bg-white/90 dark:bg-black/80 backdrop-blur-sm rounded-lg shadow-md max-w-[200px] z-[400]">
                <h4 class="text-xs font-bold uppercase tracking-wider text-text-light dark:text-text-dark mb-3">Map Legend (Samples)</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="size-3 rounded-full bg-green-500"></span>
                        <span class="text-text-light dark:text-text-dark">Agriculture</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="size-3 rounded-full bg-blue-500"></span>
                        <span class="text-text-light dark:text-text-dark">Water Source</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="size-3 rounded-full bg-orange-400"></span>
                        <span class="text-text-light dark:text-text-dark">Residential</span>
                    </div>
                </div>
            </div>

            <!-- Selected Feature Modal (Slide-over) -->
            <div x-show="selectedFeature" 
                 @click.outside="selectedFeature = null"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="absolute top-4 right-4 bottom-4 w-80 bg-white/95 dark:bg-surface-dark/95 backdrop-blur rounded-2xl shadow-2xl z-[500] border border-surface-light dark:border-black/20 flex flex-col overflow-hidden"
                 x-cloak>
                 
                 <!-- Header Image -->
                <div class="h-40 bg-slate-200 relative shrink-0">
                    <template x-if="selectedFeature?.image_url">
                        <img :src="selectedFeature.image_url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedFeature?.image_url">
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                             <span class="material-symbols-outlined text-4xl">image</span>
                        </div>
                    </template>
                    <button @click="selectedFeature = null" class="absolute top-2 right-2 size-8 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur flex items-center justify-center transition">
                         <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                        <h3 class="text-white font-bold text-lg leading-tight text-shadow" x-text="selectedFeature?.name"></h3>
                        <p class="text-white/80 text-xs" x-text="selectedFeature?.type"></p>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto custom-scroll p-4 space-y-4">
                    <p class="text-sm text-text-light/80 dark:text-text-dark/80 leading-relaxed" x-text="selectedFeature?.description || 'No description available.'"></p>
                    
                    <button @click="zoomToFeature(selectedFeature)" class="w-full py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold text-sm shadow-lg transition flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">location_on</span> View on Map
                    </button>
                </div>
            </div>
            
            <!-- Loading Overlay -->
            <div x-show="loading" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-[1000] flex items-center justify-center">
                 <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"></div>
            </div>

        </div>
    </div>
</div>

<!-- Profile Section -->
<div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-10 lg:py-16 scroll-mt-20" id="profile">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-sm font-bold">
                    <span class="material-symbols-outlined text-lg">map</span>
                    <span>Profil Wilayah</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-text-light dark:text-text-dark leading-tight">Geografis & Administrasi</h2>
                <div class="prose prose-lg text-text-light/80 dark:text-text-dark/80">
                    <p>
                        Desa Mayong Lor terletak di Kecamatan Mayong, Kabupaten Jepara, Jawa Tengah. Dengan luas wilayah sekitar <strong>290,195 hektar</strong>, desa ini didominasi oleh dataran rendah yang subur.
                    </p>
                    <p>
                        Secara administratif, wilayah desa terbagi menjadi 4 dukuh utama:
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 not-prose mt-4">
                        <li class="flex items-center gap-2 p-3 rounded-lg bg-white dark:bg-surface-dark shadow-sm border border-surface-light dark:border-white/5">
                            <span class="material-symbols-outlined text-primary">location_on</span> Bendowangin
                        </li>
                        <li class="flex items-center gap-2 p-3 rounded-lg bg-white dark:bg-surface-dark shadow-sm border border-surface-light dark:border-white/5">
                            <span class="material-symbols-outlined text-primary">location_on</span> Krajan
                        </li>
                        <li class="flex items-center gap-2 p-3 rounded-lg bg-white dark:bg-surface-dark shadow-sm border border-surface-light dark:border-white/5">
                            <span class="material-symbols-outlined text-primary">location_on</span> Karangpanggung
                        </li>
                        <li class="flex items-center gap-2 p-3 rounded-lg bg-white dark:bg-surface-dark shadow-sm border border-surface-light dark:border-white/5">
                            <span class="material-symbols-outlined text-primary">location_on</span> Karang
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Right Column: Map & Boundaries -->
            <div class="relative overflow-hidden p-0 md:p-4 mt-8 md:mt-0 md:-m-4">
                <!-- Decorative Elements -->
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary/10 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white dark:border-surface-dark bg-surface-light dark:bg-surface-dark group flex flex-col items-center">
                    <!-- Main Map Image -->
                    <div class="aspect-[4/3] w-full overflow-hidden bg-gray-200 dark:bg-gray-800 relative">
                        <img src="https://images.unsplash.com/photo-1572099606223-6e29045d7de3?q=80&w=2070&auto=format&fit=crop" 
                             alt="Peta Wilayah Desa" 
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    </div>

                    <!-- Floating Info Card (Responsive Position) -->
                    <div class="relative w-full p-4 md:absolute md:bottom-6 md:left-6 md:right-6 md:w-auto md:p-0 z-10">
                        <div class="bg-white/95 dark:bg-surface-dark/95 backdrop-blur-md rounded-xl p-4 md:p-5 shadow-lg border border-white/20">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-primary/10 rounded-lg text-primary shrink-0">
                                    <span class="material-symbols-outlined">share_location</span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-text-light dark:text-text-dark text-lg leading-none truncate">Batas Wilayah</h4>
                                    <span class="text-xs text-text-light/60 dark:text-text-dark/60 block truncate">Tapal Batas Administratif</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-3">
                                <div class="flex items-center justify-between text-sm py-2 border-b border-dashed border-gray-200 dark:border-gray-700">
                                    <span class="text-text-light/60 dark:text-text-dark/60">Utara</span>
                                    <span class="font-bold text-text-light dark:text-text-dark text-right truncate ml-2">Desa Pelemkerep</span>
                                </div>
                                <div class="flex items-center justify-between text-sm py-2 border-b border-dashed border-gray-200 dark:border-gray-700">
                                    <span class="text-text-light/60 dark:text-text-dark/60">Selatan</span>
                                    <span class="font-bold text-text-light dark:text-text-dark text-right truncate ml-2">Desa Mayong Kidul</span>
                                </div>
                                <div class="flex items-center justify-between text-sm py-2">
                                    <span class="text-text-light/60 dark:text-text-dark/60">Barat</span>
                                    <span class="font-bold text-text-light dark:text-text-dark text-right truncate ml-2">Desa Tigajuru</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Badge -->
                <div class="absolute top-3 right-3 md:top-6 md:right-6 bg-white/90 dark:bg-surface-dark/90 backdrop-blur-sm px-3 py-1 md:px-4 md:py-2 rounded-full shadow-lg border border-surface-light dark:border-white/10 flex items-center gap-2 transform translate-x-0 translate-y-0 hover:scale-105 transition-all z-20 max-w-[calc(100%-2rem)]">
                    <span class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-green-500 animate-pulse shrink-0"></span>
                    <span class="text-[10px] md:text-xs font-bold text-text-light dark:text-text-dark truncate">Zona Dataran Rendah</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Section -->
<div class="relative w-full py-20 bg-surface-dark overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Jepara_Regency_Coat_of_Arms.svg/1200px-Jepara_Regency_Coat_of_Arms.svg.png')] bg-center bg-no-repeat opacity-5 mix-blend-overlay grayscale bg-contain"></div>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <span class="material-symbols-outlined text-6xl text-white/20 mb-4">history_edu</span>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Sejarah & Legenda</h2>
        <div class="space-y-6 text-lg text-gray-300 leading-relaxed">
            <p>
                Nama <strong>"Mayong"</strong> erat kaitannya dengan kisah perjalanan <strong>Ratu Kalinyamat</strong>. 
                Konon, saat membawa jenazah suaminya, Pangeran Kalinyamat, beliau berjalan dalam kondisi sangat lelah dan sedih.
            </p>
            <p>
                Cara berjalannya yang sempoyongan, atau dalam Bahasa Jawa disebut <em>"moyang-mayong"</em>, 
                kemudian diabadikan menjadi nama daerah ini. Warisan sejarah ini menjadikan Mayong Lor tidak hanya sekadar desa, 
                tetapi juga bagian dari tapak tilas sejarah besar Jepara.
            </p>
        </div>
    </div>
</div>

<!-- Potency Section (Economy) -->
<div class="w-full py-10 lg:py-16 scroll-mt-20" id="potency" x-data="{
    scrollLeft() { $refs.container.scrollBy({ left: -300, behavior: 'smooth' }) },
    scrollRight() { $refs.container.scrollBy({ left: 300, behavior: 'smooth' }) },
    checkScroll() {
        // Optional: logic to hide/show buttons based on scroll position could go here
    }
}">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
            <div class="max-w-2xl">
                <h2 class="text-3xl md:text-4xl font-bold text-text-light dark:text-text-dark mb-4">Galeri Produk Unggulan</h2>
                <p class="text-text-light/70 dark:text-text-dark/70">
                    Koleksi mahakarya pengrajin Desa Mayong Lor yang merefleksikan keindahan dan kearifan lokal.
                </p>
            </div>
            <!-- Navigation Buttons -->
            <div class="flex gap-2 shrink-0">
                <button @click="scrollLeft()" class="size-10 rounded-full border border-surface-light dark:border-white/10 flex items-center justify-center hover:bg-surface-light dark:hover:bg-white/5 text-text-light dark:text-text-dark transition-colors">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button @click="scrollRight()" class="size-10 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-dark transition-colors shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>

        <!-- Carousel Container -->
        <div class="relative w-full">
            <div class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory scrollbar-hide" x-ref="container">
                
                <!-- Gallery Item 1: Kendi Maling -->
                <div class="min-w-[85%] sm:min-w-[calc(50%-12px)] lg:min-w-[calc(33.333%-16px)] snap-center group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-lg cursor-pointer">
                    <img src="https://static.promediateknologi.id/crop/0x0:0x0/0x0/webp/photo/p2/13/2023/10/25/IMG-20231024-WA0032-1596700063.jpg" alt="Kendi Maling" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform">
                        <h3 class="text-white text-xl font-bold mb-1">Kendi Maling</h3>
                        <p class="text-white/80 text-sm font-light">Ikon Kerajinan Mayong Lor</p>
                    </div>
                </div>

                <!-- Gallery Item 2: Vas Terakota -->
                <div class="min-w-[85%] sm:min-w-[calc(50%-12px)] lg:min-w-[calc(33.333%-16px)] snap-center group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-lg cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1624823183488-297ebceb53cc?q=80&w=2070&auto=format&fit=crop" alt="Vas Terakota" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform">
                        <h3 class="text-white text-xl font-bold mb-1">Vas Terakota</h3>
                        <p class="text-white/80 text-sm font-light">Dekorasi Interior Estetik</p>
                    </div>
                </div>

                <!-- Gallery Item 3: Set Poci -->
                <div class="min-w-[85%] sm:min-w-[calc(50%-12px)] lg:min-w-[calc(33.333%-16px)] snap-center group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-lg cursor-pointer">
                    <img src="https://cdn.antaranews.com/cache/1200x800/2020/10/05/gerabah-mayong-jepara.jpg" alt="Set Poci Teh" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform">
                        <h3 class="text-white text-xl font-bold mb-1">Set Poci Teh</h3>
                        <p class="text-white/80 text-sm font-light">Tradisi Minum Teh</p>
                    </div>
                </div>

                 <!-- Gallery Item 4: Celengan -->
                <div class="min-w-[85%] sm:min-w-[calc(50%-12px)] lg:min-w-[calc(33.333%-16px)] snap-center group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-lg cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=2070&auto=format&fit=crop" alt="Aneka Souvenir" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-80 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform">
                        <h3 class="text-white text-xl font-bold mb-1">Aneka Souvenir</h3>
                        <p class="text-white/80 text-sm font-light">Oleh-oleh Khas Desa</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Social & Facilities -->
<div class="w-full bg-background-light dark:bg-background-dark py-10 lg:py-16 border-t border-surface-light dark:border-surface-dark">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16">
            <!-- Social -->
            <div class="flex gap-6 items-start">
                <div class="size-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-3xl">diversity_3</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Kampung KB Mandiri</h3>
                    <p class="text-text-light/70 dark:text-text-dark/70 leading-relaxed mb-4">
                        Desa Mayong Lor telah ditetapkan sebagai <strong>Kampung KB Mandiri</strong> oleh BKKBN, menunjukkan komitmen kuat dalam pembangunan keluarga sejahtera dan pengendalian penduduk.
                    </p>
                    <div class="h-1.5 w-full bg-surface-light dark:bg-surface-dark rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 w-3/4 rounded-full"></div>
                    </div>
                    <span class="text-xs text-text-light/50 mt-1 block">Partisipasi Aktif Masyarakat Tinggi</span>
                </div>
            </div>

            <!-- Education -->
            <div class="flex gap-6 items-start">
                <div class="size-14 rounded-2xl bg-green-100 dark:bg-green-900/30 text-green-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-3xl">school</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-2">Fasilitas Pendidikan</h3>
                    <p class="text-text-light/70 dark:text-text-dark/70 leading-relaxed mb-4">
                        Tersedia fasilitas pendidikan lengkap untuk menunjang kualitas SDM, mulai dari tingkat dasar (SD Negeri) hingga menengah kejuruan seperti <strong>SMK Al-Anwar</strong>.
                    </p>
                    <ul class="flex gap-4">
                         <li class="flex items-center gap-1 text-sm font-bold text-text-light dark:text-text-dark">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> SD Negeri
                         </li>
                         <li class="flex items-center gap-1 text-sm font-bold text-text-light dark:text-text-dark">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Madrasah
                         </li>
                         <li class="flex items-center gap-1 text-sm font-bold text-text-light dark:text-text-dark">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> SMK
                         </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Berita & Pengumuman -->
<div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-10 lg:py-16 scroll-mt-20 border-t border-surface-light dark:border-surface-dark transition-colors duration-200" id="news">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-0 mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-text-light dark:text-text-dark">Berita & Pengumuman</h2>
            <a class="text-primary font-bold hover:underline flex items-center gap-1 self-start md:self-auto" href="#">
                Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- News Card 1 -->
            <article class="bg-background-light dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col h-full border border-surface-light dark:border-white/5">
                <div class="h-48 overflow-hidden relative">
                    <div class="absolute top-3 left-3 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full z-10">Agenda</div>
                    <img alt="Kegiatan Desa" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBwBBQX_RtcqkH1dvzPt8sfLSmNN3INyBtY96t4JKXIl8wN0okQpf7aSfD1NHqyeJT6hLcR_J1yJcYfMqAnCoL3LNmVFjdQzfCAE4ZPLWL0BXxNmZc5jtf7WZG5RG4bqpYnhG7kh05BWLd7gRvGBQr5P46PmxqnYh518XqJ--i2YE7f1O43mYsbMu0yg88QfFGOKCtf95irXxDl46peV-IicnHkUwh1FE8nUS729tCfifqZq5NFNgeKXZvB6keGE8l_lEfG37LCVAiI"/>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 text-xs text-text-light/50 dark:text-text-dark/50 mb-2">
                        <span class="material-symbols-outlined text-sm">calendar_today</span>
                        <span>24 Okt 2025</span>
                    </div>
                    <h3 class="text-xl font-bold text-text-light dark:text-text-dark mb-2 leading-tight">Musyawarah Desa</h3>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-sm mb-4 line-clamp-2">Pembahasan rencana pembangunan infrastruktur tahun anggaran 2026.</p>
                    <div class="mt-auto">
                        <a class="text-primary font-bold text-sm hover:underline" href="#">Baca Selengkapnya</a>
                    </div>
                </div>
            </article>
            <!-- More cards... -->
             <article class="bg-background-light dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col h-full border border-surface-light dark:border-white/5">
                <div class="h-48 overflow-hidden relative">
                    <div class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10">Penting</div>
                    <img alt="Perbaikan Jalan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAWLj65QAHHISSdPjIhHxHPJoGYQjZWPbZI-2aSzOjUi3z5Y4UCKUmU12j-hZDouGaPuWiJb9icYzESmeqRulsGb2T1Q7CO67F9Pf-tx9Kzrxp6SnhbrI9tiggzkKt1POy6smWhDuUZBNkVALHRH2Mns42WcpA-a16jckchGyGI5eBVJHSqccDAF_BavOoUpLtfQZcC5Q17PsUs9U4dmh6SMtdF4K8w7qClVnPBsK0ijzoEd-eaZqOEvP2I60J6FAxpuPuvlnOE9YZu"/>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 text-xs text-text-light/50 dark:text-text-dark/50 mb-2">
                        <span class="material-symbols-outlined text-sm">campaign</span>
                        <span>20 Okt 2025</span>
                    </div>
                    <h3 class="text-xl font-bold text-text-light dark:text-text-dark mb-2 leading-tight">Perbaikan Jalan Poros</h3>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-sm mb-4 line-clamp-2">Akan dilakukan pengaspalan ulang di jalan utama dukuh Krajan mulai minggu depan.</p>
                    <div class="mt-auto">
                        <a class="text-primary font-bold text-sm hover:underline" href="#">Baca Selengkapnya</a>
                    </div>
                </div>
            </article>
            <!-- Card 3 -->
             <article class="bg-background-light dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col h-full border border-surface-light dark:border-white/5">
                <div class="h-48 overflow-hidden relative">
                    <div class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10">Prestasi</div>
                    <img alt="Lomba Desa" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"/>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 text-xs text-text-light/50 dark:text-text-dark/50 mb-2">
                        <span class="material-symbols-outlined text-sm">emoji_events</span>
                        <span>15 Okt 2025</span>
                    </div>
                    <h3 class="text-xl font-bold text-text-light dark:text-text-dark mb-2 leading-tight">Juara 1 Lomba Kebersihan</h3>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-sm mb-4 line-clamp-2">Desa Mayong Lor meraih penghargaan desa terbersih se-Kecamatan Mayong.</p>
                    <div class="mt-auto">
                        <a class="text-primary font-bold text-sm hover:underline" href="#">Baca Selengkapnya</a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-surface-light dark:bg-surface-dark pt-10 md:pt-16 pb-8 border-t border-surface-light dark:border-surface-dark">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <div class="space-y-4">
                <h2 class="text-lg font-bold flex items-center gap-2"><span class="material-symbols-outlined text-primary">terrain</span> Desa Mayong Lor</h2>
                <p class="text-text-light/60 text-sm">Memberdayakan desa melalui transparansi data dan gotong royong masyarakat.</p>
            </div>
            <!-- Quick Links -->
            <div>
                 <h3 class="font-bold mb-4">Tautan Cepat</h3>
                 <ul class="space-y-2 text-sm text-text-light/70">
                    <li><a href="#" class="hover:text-primary">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-primary">Layanan Publik</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-primary">Login Admin</a></li>
                 </ul>
            </div>
        </div>
        <div class="border-t border-text-light/10 pt-8 text-center text-xs text-text-light/50">
            &copy; 2025 Pemerintah Desa Mayong Lor. All rights reserved.
        </div>
    </div>
</footer>

<!-- JS Logic from Old File -->
<!-- JS Logic from Old File -->
<script>
    function mapComponent() {
        return {
            map: null,
            loading: true,
            
            // Toggles
            showBoundaries: true,
            showInfrastructures: true,
            showLandUses: true,
            
            // Data
            categories: @json($categories),
            selectedCategories: [],
            
            // Computed Data
            allPlaces: [],
            geoFeatures: [],
            
            // Search
            searchQuery: '',
            searchResults: [],
            selectedFeature: null,
            userMarker: null,
            
            // Layers
            markers: [],
            boundariesLayer: null,
            infrastructuresLayer: null,
            landUsesLayer: null,
            baseLayers: {},
            currentBaseLayer: 'streets',

            init() {
                this.selectedCategories = this.categories.map(c => c.id);
                this.$nextTick(() => {
                   this.initMap();
                   this.fetchAllData();
                });
                
                this.$watch('selectedCategories', () => this.updateMapMarkers());
            },
            
            initMap() {
                 this.map = L.map('leaflet-map', { zoomControl: false, attributionControl: false }).setView([-6.7289, 110.7485], 14);
                                    
                // Define Base Layers
                const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                });
                const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                    maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                });

                this.baseLayers = {
                    'streets': googleStreets,
                    'satellite': googleSatellite
                };

                // Default to Satellite
                this.currentBaseLayer = 'satellite';
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
                    
                    const [places, boundaries, infrastructures, landUses] = await Promise.all([
                        fetch('{{ route('places.geojson') }}').then(r => r.json()),
                        fetch('{{ route('boundaries.geojson') }}').then(r => r.json()),
                        fetch('{{ route('infrastructures.geojson') }}').then(r => r.json()),
                        fetch('{{ route('land_uses.geojson') }}').then(r => r.json())
                    ]);

                    // Store raw features
                    this.geoFeatures = places.features || []; // Places with geometry
                    this.allPlaces = places.features || [];
                    
                    // Load Layers
                    this.loadBoundaries(boundaries.features || []);
                    this.loadInfrastructures(infrastructures.features || []);
                    this.loadLandUses(landUses.features || []);
                    
                    // Initial Markers Render
                    this.updateMapMarkers();

                } catch (e) {
                    console.error('Error loading data:', e);
                } finally {
                    this.loading = false;
                }
            },
            
            updateLayers() {
                this.fetchAllData(); // Simple refresh for now
            },

            loadBoundaries(features) {
                if (this.boundariesLayer) this.map.removeLayer(this.boundariesLayer);
                if (!this.showBoundaries) return;
                
                this.boundariesLayer = L.geoJSON(features, {
                    style: { color: '#059669', weight: 2, fillColor: '#10b981', fillOpacity: 0.1, dashArray: '5, 5' },
                    onEachFeature: (f, l) => {
                        l.on('click', (e) => {
                            L.DomEvent.stop(e);
                            this.selectFeature({...f.properties, type: 'Batas Wilayah'});
                        });
                    }
                }).addTo(this.map);

                if (features.length > 0) this.map.fitBounds(this.boundariesLayer.getBounds(), { padding: [50, 50] });
            },

            loadInfrastructures(features) {
                if (this.infrastructuresLayer) this.map.removeLayer(this.infrastructuresLayer);
                if (!this.showInfrastructures) return;

                this.infrastructuresLayer = L.geoJSON(features, {
                    style: f => {
                        const type = f.properties.type;
                        const color = type === 'river' ? '#3b82f6' : '#64748b'; 
                        return { color: color, weight: type === 'river' ? 4 : 3, opacity: 0.8 };
                    },
                    onEachFeature: (f, l) => {
                        l.on('click', (e) => {
                            L.DomEvent.stop(e);
                            this.selectFeature({...f.properties, type: 'Infrastruktur'});
                        });
                    }
                }).addTo(this.map);
            },

            loadLandUses(features) {
                if (this.landUsesLayer) this.map.removeLayer(this.landUsesLayer);
                if (!this.showLandUses) return;

                this.landUsesLayer = L.geoJSON(features, {
                    style: f => {
                        const colors = { rice_field: '#fbbf24', forest: '#15803d', settlement: '#f97316', plantation: '#84cc16' };
                        return { color: colors[f.properties.type] || '#94a3b8', weight: 1, fillOpacity: 0.3, fillColor: colors[f.properties.type] };
                    },
                    onEachFeature: (f, l) => {
                        l.on('click', (e) => {
                            L.DomEvent.stop(e);
                            this.selectFeature({...f.properties, type: 'Penggunaan Lahan'});
                        });
                    }
                }).addTo(this.map);
            },

            updateMapMarkers() {
                this.markers.forEach(m => this.map.removeLayer(m));
                this.markers = [];
                
                const filtered = this.geoFeatures.filter(f => this.selectedCategories.includes(f.properties.category?.id));
                
                filtered.forEach(feature => {
                    const [lng, lat] = feature.geometry.coordinates;
                    const p = feature.properties;
                    const color = p.category ? p.category.color : '#3b82f6';
                    
                    const iconHtml = `
                        <div class="w-9 h-9 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-sm custom-marker" style="background: linear-gradient(to bottom right, ${color}, #475569);">
                            <i class="${p.category?.icon_class ?? 'fa-solid fa-map-marker-alt'}"></i>
                        </div>
                    `;
                    
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({ html: iconHtml, className: '', iconSize: [36, 36], iconAnchor: [18, 18] })
                    });
                    
                    marker.on('click', () => {
                        this.selectPlace({...p, latitude: lat, longitude: lng});
                    });
                    
                    marker.addTo(this.map);
                    this.markers.push(marker);
                });
            },
            
            performSearch() {
                if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                const q = this.searchQuery.toLowerCase();
                const matches = this.allPlaces.filter(p => p.properties.name.toLowerCase().includes(q))
                    .map(p => ({ 
                        ...p.properties, 
                        coords: [...p.geometry.coordinates].reverse(),
                        type: 'Lokasi',
                        feature: p
                    }));
                this.searchResults = matches.slice(0, 5);
            },

            selectFeature(result) {
                this.selectedFeature = result;
                this.zoomToFeature(result);
                this.searchResults = [];
            },
            
            selectPlace(place) {
                    this.selectedFeature = {
                    ...place,
                    type: 'Lokasi',
                    image_url: place.image_url || null
                };
                this.zoomToFeature(place);
            },

            zoomToFeature(feature) {
                if (feature.coords) {
                    this.map.flyTo(feature.coords, 18);
                } else if (feature.latitude && feature.longitude) {
                    this.map.flyTo([feature.latitude, feature.longitude], 18);
                } else if (feature.geometry) {
                        const layer = L.geoJSON(feature);
                        this.map.fitBounds(layer.getBounds(), { padding: [50, 50] });
                }
            },
            
            scrollToMap() {
                document.getElementById('gis-map').scrollIntoView({ behavior: 'smooth' });
            },

            locateUser() {
                if (!navigator.geolocation) { alert('Browser tidak mendukung geolokasi'); return; }
                this.loading = true;
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const { latitude, longitude } = pos.coords;
                        this.map.flyTo([latitude, longitude], 17);
                        if (this.userMarker) this.map.removeLayer(this.userMarker);
                        this.userMarker = L.marker([latitude, longitude], {
                            icon: L.divIcon({ html: '<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-lg marker-pulse"></div>', iconSize: [16, 16] })
                        }).addTo(this.map);
                        this.loading = false;
                    },
                    (err) => {
                        console.error(err);
                        this.loading = false;
                        alert('Gagal mengambil lokasi.');
                    }
                );
            }
        };
    }
</script>
</body>
</html>
