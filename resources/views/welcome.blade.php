<!DOCTYPE html>
<html class="light scroll-smooth overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Portal Wisata - {{ config('app.name', 'Dinas Pariwisata dan Kebudayaan Jepara') }}</title>

    <!-- Leaflet & Icon -->
    <!-- Local assets handled by Vite -->

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Pinyon+Script&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://unpkg.com/maplibre-gl@2.4.0/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@2.4.0/dist/maplibre-gl.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Map Styles */
        #leaflet-map {
            height: 100%;
            width: 100%;
            z-index: 0;
        }

        /* Custom Marker Animations */
        .custom-marker {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .custom-marker:hover {
            transform: scale(1.25);
            z-index: 1000 !important;
        }

        .marker-pulse {
            animation: pulse-blue 2s infinite;
        }

        @keyframes pulse-blue {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-display antialiased transition-colors duration-200 overflow-x-hidden pt-20"
    x-data="mapComponent()">

    <!-- Top Navigation -->
    <div
        class="fixed top-0 left-0 right-0 z-[10000] w-full border-b border-surface-light dark:border-surface-dark bg-background-light/95 dark:bg-background-dark/95 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <header class="flex h-20 items-center justify-between gap-8" x-data="{ mobileMenuOpen: false }">
                <div class="flex items-center gap-8">
                    <a class="flex items-center gap-3 text-text-light dark:text-text-dark group" href="#">
                        <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" alt="Logo Kabupaten Jepara"
                            class="w-10 h-auto object-contain">
                        <h2 class="text-xl font-bold leading-tight tracking-tight">Blusukan Jepara</h2>
                    </a>
                    <nav class="hidden lg:flex items-center gap-8">
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('welcome') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('welcome') }}">Beranda</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('explore.map') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('explore.map') }}">Peta GIS</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('places.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('places.index') }}">Destinasi</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('events.public.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('events.public.index') }}">Agenda</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('posts.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('posts.index') }}">Berita</a>
                    </nav>
                </div>

                <div class="flex flex-1 items-center justify-end gap-4">
                    <!-- Search Bar -->
                    <label class="hidden md:flex flex-col w-full max-w-xs h-10 relative">
                        <div
                            class="flex w-full h-full items-center rounded-full bg-surface-light dark:bg-surface-dark px-4 transition-colors focus-within:ring-2 focus-within:ring-primary/50">
                            <span class="material-symbols-outlined text-gray-500 dark:text-gray-400">search</span>
                            <input
                                class="w-full bg-transparent border-none text-sm px-3 text-text-light dark:text-text-dark placeholder-gray-500 focus:ring-0"
                                placeholder="Cari lokasi, data..." type="text" x-model="searchQuery"
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

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden p-2 rounded-full hover:bg-surface-light dark:hover:bg-surface-dark">
                        <span class="material-symbols-outlined" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                    </button>
                </div>

                <!-- Mobile Menu Dropdown -->
                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="mobileMenuOpen = false" x-cloak
                    class="absolute top-20 left-0 w-full bg-background-light dark:bg-background-dark border-b border-surface-light dark:border-surface-dark shadow-xl lg:hidden z-50 p-4 flex flex-col gap-4">

                    <nav class="flex flex-col gap-4">
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2"
                            href="{{ route('welcome') }}">Beranda</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="#gis-map">Peta
                            GIS</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2"
                            href="#profile">Profil</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2"
                            href="{{ route('places.index') }}">Destinasi</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2"
                            href="{{ route('posts.index') }}">Berita</a>
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

    <div class="relative w-full">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div
                class="relative overflow-hidden rounded-xl h-[500px] lg:h-[600px] group bg-gray-900 border border-white/10 shadow-2xl">
                <!-- 3D Map Container -->
                <div id="hero-map" class="absolute inset-0 z-0 opacity-0 transition-opacity duration-[2000ms]"></div>

                <!-- Overlay Gradient for Readability -->
                <div
                    class="absolute inset-0 z-10 bg-gradient-to-t from-gray-900 via-gray-900/20 to-transparent pointer-events-none">
                </div>

                <!-- Content -->
                <div
                    class="absolute inset-0 z-20 flex flex-col items-center justify-center p-6 text-center pointer-events-none">
                    <div class="max-w-4xl space-y-6 pointer-events-auto">
                        <span
                            class="hidden md:inline-block px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold uppercase tracking-wider animate-fade-in-down">
                            Official Tourism Portal
                        </span>
                        <h1
                            class="text-white text-3xl sm:text-5xl lg:text-7xl font-black leading-tight tracking-tight drop-shadow-sm animate-fade-in-up">
                            Blusukan Jepara.<br /> Ukir Cerita Serumu Disini
                        </h1>
                        <p
                            class="text-gray-100 text-lg sm:text-xl font-medium max-w-2xl mx-auto leading-relaxed drop-shadow-sm animate-fade-in-up delay-100">
                            Temukan keindahan pantai tropis, kekayaan sejarah, dan mahakarya seni ukir kelas dunia.
                        </p>
                        <div
                            class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 animate-fade-in-up delay-200">
                            <a class="flex items-center justify-center h-12 px-8 rounded-full bg-primary hover:bg-primary-dark text-white text-base font-bold shadow-lg shadow-black/20 transition-all hover:-translate-y-0.5"
                                href="{{ route('explore.map') }}">
                                Jelajahi Destinasi
                            </a>
                            <a href="#profile"
                                class="hidden sm:flex items-center justify-center h-12 px-8 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white text-base font-bold transition-all">
                                Jepara Kalcer
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
                        paint: {
                            'raster-opacity': 1
                        }
                    }]
                },
                center: [110.678, -6.589], // Central Jepara Regency coordinates
                zoom: 9.5, // Zoomed out to view the regency
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
                        center: [110.68, -6.59],
                        zoom: isMobile ? 10 : 11,
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

                map.rotateTo(bearing % 360, {
                    duration: 0
                });
                window.requestAnimationFrame(rotateCamera);
            }
        });
    </script>

    <!-- Stats Section -->
    <div class="w-full bg-background-light dark:bg-background-dark py-8 transition-colors duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Responsive Grid: 1 col mobile, 2 cols tablet, 4 cols desktop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 lg:gap-8">
                <!-- Destinasi Wisata -->
                <div class="flex flex-col gap-3 rounded-xl p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-all duration-300 shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 group">
                    <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <span class="material-symbols-outlined text-2xl">photo_camera</span>
                    </div>
                    <div>
                        <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide mb-1">
                            Destinasi Wisata</p>
                        <!-- Using $countDestinasi passed from controller -->
                        <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">
                            {{ $countDestinasi }}+</p>
                        <p class="text-xs text-text-light/50 mt-1">Objek Wisata</p>
                    </div>
                </div>

                <!-- Kuliner Khas -->
                <div class="flex flex-col gap-3 rounded-xl p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-all duration-300 shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 group">
                    <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <span class="material-symbols-outlined text-2xl">restaurant_menu</span>
                    </div>
                    <div>
                        <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide mb-1">
                             Kuliner Khas</p>
                        <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">
                            {{ $countKuliner }}+</p>
                        <p class="text-xs text-text-light/50 mt-1">Menu Favorit</p>
                    </div>
                </div>

                <!-- Agenda Event -->
                <div class="flex flex-col gap-3 rounded-xl p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-all duration-300 shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 group">
                    <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <span class="material-symbols-outlined text-2xl">event_available</span>
                    </div>
                    <div>
                        <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide mb-1">
                            Agenda Event</p>
                        <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">
                            {{ $countEvent }}</p>
                        <p class="text-xs text-text-light/50 mt-1">Acara Tahunan</p>
                    </div>
                </div>

                <!-- Desa Wisata -->
                <div class="flex flex-col gap-3 rounded-xl p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-all duration-300 shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 group">
                    <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <span class="material-symbols-outlined text-2xl">holiday_village</span>
                    </div>
                    <div>
                        <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide mb-1">
                            Desa Wisata</p>
                        <p class="text-text-light dark:text-text-dark text-xl font-bold tracking-tight leading-tight">
                            {{ $countDesa }}</p>
                        <p class="text-xs text-text-light/50 mt-1">Desa Potensial</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

 

    <!-- Profile Section (Redesigned - Layered Editorial Style) -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-20 lg:py-24 scroll-mt-20 relative overflow-hidden" id="profile">
        <!-- Soft Gradient Spot -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                
                <!-- Left Column: Content -->
                <div class="space-y-10 order-2 lg:order-1" x-data="{ shown: false }" x-intersect.threshold.0.5="shown = true">
                    <div class="opacity-0 translate-y-8 transition-all duration-1000 ease-out" :class="shown ? 'opacity-100 translate-y-0' : ''">
                        <div class="flex items-center gap-4 mb-6">
                            <span class="h-px w-12 bg-primary"></span>
                            <span class="text-primary font-bold uppercase tracking-widest text-xs">Profil Wilayah</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white leading-tight mb-6">
                            Posisi Strategis <br>
                            <span class="font-serif italic font-normal text-gray-500 dark:text-gray-400">di Utara Pulau Jawa</span>
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed font-light">
                            Terletak di ujung utara Jawa Tengah, Jepara adalah perpaduan harmonis antara daratan subur dan lautan luas. 
                            Dikenal sebagai <strong>"Bumi Kartini"</strong>, wilayah ini memiliki garis pantai 83 km yang menyajikan panorama tropis eksotis.
                        </p>
                    </div>

                    <!-- Minimalist Boundaries List -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 border-t border-gray-200 dark:border-gray-700 pt-8 opacity-0 transition-all duration-1000 delay-300" :class="shown ? 'opacity-100' : ''">
                        <div class="group flex items-center justify-between py-2 border-b border-dashed border-gray-200 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-400 uppercase tracking-wider group-hover:text-primary transition-colors">Utara</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">Laut Jawa</span>
                        </div>
                        <div class="group flex items-center justify-between py-2 border-b border-dashed border-gray-200 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-400 uppercase tracking-wider group-hover:text-primary transition-colors">Timur</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">Kudus & Pati</span>
                        </div>
                        <div class="group flex items-center justify-between py-2 border-b border-dashed border-gray-200 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-400 uppercase tracking-wider group-hover:text-primary transition-colors">Selatan</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">Kab. Demak</span>
                        </div>
                        <div class="group flex items-center justify-between py-2 border-b border-dashed border-gray-200 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-400 uppercase tracking-wider group-hover:text-primary transition-colors">Barat</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">Laut Jawa</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Composition (Editorial) -->
                <div class="relative order-1 lg:order-2 group cursor-none" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    <!-- Main Image Layout -->
                    <div class="relative z-10 w-full aspect-[4/5] md:aspect-square lg:aspect-[4/5]">
                        <div class="absolute top-0 right-0 w-[85%] h-[90%] rounded-[3rem] overflow-hidden shadow-2xl transition-all duration-700 ease-out transform"
                             :class="hover ? 'scale-[1.02] -translate-y-2' : ''">
                            <img src="{{ asset('images/geografis.png') }}" 
                                 alt="Peta Geografis Jepara" 
                                 class="w-full h-full object-cover">
                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors duration-500"></div>
                        </div>

                        <!-- Secondary Image (Overlapping) -->
                        <div class="absolute bottom-0 left-0 w-[55%] h-[40%] rounded-[2rem] overflow-hidden shadow-xl border-4 border-white dark:border-gray-900 transition-all duration-700 delay-100 ease-out transform"
                             :class="hover ? 'scale-105 translate-x-2' : ''">
                             <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=2073&auto=format&fit=crop" 
                                  alt="Coastline" 
                                  class="w-full h-full object-cover filter brightness-110">
                        </div>

                        <!-- Floating Badge Circle -->
                        <div class="absolute top-[10%] left-[5%] z-20 transition-all duration-500 delay-200 transform"
                             :class="hover ? 'rotate-12 scale-110' : ''">
                            <div class="size-24 rounded-full bg-primary text-white flex flex-col items-center justify-center shadow-lg shadow-primary/30 border-4 border-white dark:border-gray-800">
                                <span class="text-2xl font-black">16</span>
                                <span class="text-[0.6rem] font-bold uppercase tracking-wider">Kecamatan</span>
                            </div>
                        </div>
                        
                        <!-- Decorative Pattern -->
                        <div class="absolute -bottom-6 -right-6 z-0 pointer-events-none">
                            <svg width="100" height="100" viewBox="0 0 100 100" fill="none" class="text-gray-200 dark:text-gray-800">
                                <pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="2" fill="currentColor"/>
                                </pattern>
                                <rect width="100" height="100" fill="url(#dots)"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Custom Cursor Follower (Optional - requires JS, but here just static CSS effect on hover) -->
                    <!-- <div class="hidden lg:block absolute pointer-events-none bg-white/90 backdrop-blur px-4 py-2 rounded-full text-xs font-bold shadow-lg transform -translate-x-1/2 -translate-y-1/2 transition-opacity duration-300 opacity-0 group-hover:opacity-100 z-50 mix-blend-hard-light"
                         x-bind:style="'left: ' + $event.offsetX + 'px; top: ' + $event.offsetY + 'px'">
                        Jelajahi
                    </div> -->
                </div>

            </div>
        </div>
    </div>

    <!-- History & Legend Section (Light & Clean Version) -->
    <div class="relative w-full bg-white dark:bg-gray-900 overflow-hidden py-24" id="history">
        <!-- Background Decor -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-700 to-transparent"></div>
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Section Header -->
            <div class="text-center mb-16" 
                 x-data="{ shown: false }" 
                 x-intersect.threshold.0.5="shown = true">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text-light dark:text-text-dark mb-6 opacity-0 translate-y-4 transition-all duration-700 delay-100"
                    :class="shown ? 'opacity-100 translate-y-0' : ''">
                    Sejarah & Legenda
                </h2>
                <p class="text-text-light/70 dark:text-text-dark/70 max-w-2xl mx-auto text-lg opacity-0 translate-y-4 transition-all duration-700 delay-200"
                   :class="shown ? 'opacity-100 translate-y-0' : ''">
                    Menelusuri jejak tokoh-tokoh besar yang membentuk karakter dan identitas Jepara sepanjang masa.
                </p>
            </div>

            <!-- Full Image Cards Grid -->
            <div class="flex md:grid md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 overflow-x-auto md:overflow-visible snap-x snap-mandatory pb-8 md:pb-0 px-4 md:px-0 -mx-4 md:mx-0 scrollbar-hide">
                
                <!-- Shima Card (Kalingga - Oldest) -->
                <div class="min-w-[85%] md:min-w-0 snap-center group relative h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true"
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12 transition-all duration-1000'">
                    
                    <!-- Full Background Image -->
                    <img src="{{ asset('images/shima.jpg') }}" 
                         alt="Ratu Shima" 
                         class="absolute inset-0 w-full h-full object-cover object-top filter grayscale-[0.2] group-hover:grayscale-0 group-hover:scale-105 transition-all duration-[1500ms] ease-out">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-700"></div>

                    <!-- Content Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">Ratu Shima</h3>
                        <p class="text-xl font-['Pinyon_Script'] text-white/90 mb-6">
                            "Keadilan Tanpa Pandang Bulu"
                        </p>
                        <div class="h-0 group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 group-hover:opacity-100">
                            <p class="text-white/80 text-sm leading-relaxed">
                                Penguasa Kerajaan Kalingga yang termasyhur akan ketegasan hukumnya. Simbol integritas dan keadilan sejati dari masa lampau.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kalinyamat Card (16th Century) -->
                <div class="min-w-[85%] md:min-w-0 snap-center group relative h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none transition-all duration-1000 delay-200"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true"
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
                    
                    <!-- Full Background Image -->
                    <img src="{{ asset('images/kalinyamat.jpg') }}" 
                         alt="Ratu Kalinyamat" 
                         class="absolute inset-0 w-full h-full object-cover object-center filter grayscale-[0.2] group-hover:grayscale-0 group-hover:scale-105 transition-all duration-[1500ms] ease-out">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-700"></div>

                    <!-- Content Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">Ratu Kalinyamat</h3>
                        <p class="text-xl font-['Pinyon_Script'] text-white/90 mb-6">
                            "Sang Ratu Laut yang Gagah Berani"
                        </p>
                        <div class="h-0 group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 group-hover:opacity-100">
                            <p class="text-white/80 text-sm leading-relaxed">
                                Penguasa maritim Nusantara yang disegani. Membangun Jepara menjadi pusat niaga dan kekuatan laut yang tak tertandingi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kartini Card (19th Century - Youngest) -->
                <div class="min-w-[85%] md:min-w-0 snap-center group relative h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none transition-all duration-1000 delay-400"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true"
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
                    
                    <!-- Full Background Image -->
                    <img src="{{ asset('images/kartini.jpg') }}" 
                         alt="R.A. Kartini" 
                         class="absolute inset-0 w-full h-full object-cover object-top filter grayscale-[0.2] group-hover:grayscale-0 group-hover:scale-105 transition-all duration-[1500ms] ease-out">
                    
                    <!-- Gradient Overlay (Subtle) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-700"></div>

                    <!-- Content Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-10 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">R.A. Kartini</h3>
                        <p class="text-xl font-['Pinyon_Script'] text-white/90 mb-6">
                            "Habis Gelap Terbitlah Terang"
                        </p>
                        <div class="h-0 group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 group-hover:opacity-100">
                            <p class="text-white/80 text-sm leading-relaxed">
                                Pahlawan emansipasi yang memperjuangkan hak pendidikan wanita. Sosoknya menginspirasi perubahan besar dari Jepara untuk Indonesia.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Culture & Traditions Section (Horizontal Accordion) -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-20 lg:py-28 overflow-hidden relative" id="culture" x-data="{ active: 0 }">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-15 dark:opacity-20 pointer-events-none mix-blend-multiply dark:mix-blend-soft-light saturate-0">
            <img src="{{ asset('images/tenun-troso.png') }}" alt="Motif Tenun Troso" class="w-full h-full object-cover">
        </div>
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="text-center mb-16" x-data="{ shown: false }" x-intersect.threshold.0.5="shown = true">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-text-light dark:text-text-dark mb-6 opacity-0 translate-y-4 transition-all duration-700 delay-100"
                    :class="shown ? 'opacity-100 translate-y-0' : ''">
                    Budaya Jepara
                </h2>
                <p class="text-text-light/70 dark:text-text-dark/70 max-w-2xl mx-auto text-lg opacity-0 translate-y-4 transition-all duration-700 delay-200"
                   :class="shown ? 'opacity-100 translate-y-0' : ''">
                    Ragam festival dan upacara adat yang lestari, menjadi identitas dan kebanggaan masyarakat Bumi Kartini.
                </p>
            </div>

            <!-- Horizontal Accordion -->
            <div class="flex flex-col md:flex-row h-[600px] w-full gap-4">
                @foreach($cultures as $index => $culture)
                <div class="relative rounded-3xl overflow-hidden cursor-pointer transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] group shadow-2xl border border-white/5"
                     :class="active === {{ $index }} ? 'flex-[10] md:flex-[5] opacity-100' : 'flex-[2] md:flex-[1] opacity-70 hover:opacity-100'"
                     @click="active = {{ $index }}">
                    
                    <!-- Background Image (Refactored to img tag for reliability) -->
                    <img src="{{ asset($culture->image) }}" 
                         alt="{{ $culture->name }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000"
                         :class="active === {{ $index }} ? 'scale-100' : 'scale-110 group-hover:scale-105'">
                    
                    <!-- Fallback Background -->
                    <div class="absolute inset-0 bg-gray-800 -z-10"></div>

                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 transition-opacity duration-500"
                         :class="active === {{ $index }} ? 'bg-gradient-to-t from-black/90 via-black/40 to-transparent' : 'bg-black/60 group-hover:bg-black/40'"></div>

                    <!-- Inactive State Content (Horizontal Text) -->
                    <div class="absolute inset-0 flex items-end justify-center pb-8 transition-opacity duration-500"
                         :class="active === {{ $index }} ? 'opacity-0 pointer-events-none' : 'opacity-100'">
                        <h3 class="text-white font-bold tracking-widest uppercase text-lg md:text-xl drop-shadow-lg text-center px-2">
                            {{ $culture->name }}
                        </h3>
                    </div>

                    <!-- Active State Content -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 transition-all duration-700 transform"
                         :class="active === {{ $index }} ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                        
                        <div class="flex flex-col items-start max-w-2xl">
                            <span class="inline-block px-3 py-1 rounded-full bg-primary/90 text-white text-xs font-bold mb-4 backdrop-blur-sm shadow-lg">
                                {{ $culture->location }}
                            </span>
                            <h3 class="text-3xl md:text-5xl font-bold text-white mb-4 leading-tight drop-shadow-md">
                                {{ $culture->name }}
                            </h3>
                            <p class="text-gray-200 text-lg md:text-xl line-clamp-3 mb-6 leading-relaxed">
                                {{ $culture->description }}
                            </p>
                            <div class="flex items-center gap-4 text-sm font-medium text-blue-300">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">calendar_month</span>
                                    {{ $culture->highlight }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Potency Section (Economy) -->
    <div class="w-full py-10 lg:py-16 scroll-mt-20" id="potency" x-data="{
        currentIndex: 0,
        totalItems: {{ $places->count() }},
        autoplay: null,
        scrollLeft() { 
            $refs.container.scrollBy({ left: -300, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        scrollRight() { 
            $refs.container.scrollBy({ left: 300, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        scrollToIndex(index) {
            const container = $refs.container;
            if (!container) return;
            
            // Proportional scrolling to match the index
            const maxScroll = container.scrollWidth - container.clientWidth;
            if (maxScroll <= 0) return;
            
            const targetProp = index / (this.totalItems - 1);
            const targetPos = targetProp * maxScroll;
            
            container.scrollTo({ left: targetPos, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        updateCurrentIndex() {
            const container = $refs.container;
            if (!container || !container.children.length) return;
            
            const scrollLeft = container.scrollLeft;
            const maxScroll = container.scrollWidth - container.clientWidth;
            
            if (maxScroll <= 0) {
                this.currentIndex = 0;
                return;
            }
            
            // Map scroll percentage to index range
            const scrollValues = scrollLeft / maxScroll;
            this.currentIndex = Math.round(scrollValues * (this.totalItems - 1));
        },
        startAutoplay() {
            this.stopAutoplay();
            this.autoplay = setInterval(() => {
                if (this.currentIndex >= this.totalItems - 1) {
                    this.scrollToIndex(0);
                } else {
                    this.scrollRight();
                }
            }, 3000);
        },
        stopAutoplay() {
            if (this.autoplay) {
                clearInterval(this.autoplay);
                this.autoplay = null;
            }
        },
        init() {
            this.$refs.container?.addEventListener('scroll', () => this.updateCurrentIndex());
            this.startAutoplay();
        }
    }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-text-light dark:text-text-dark mb-4 leading-tight">
                        Wisata Unggulan Jepara
                    </h2>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-lg leading-relaxed">
                        Jelajahi keindahan alam, kekayaan budaya, dan sejarah yang memukau di Bumi Kartini.
                    </p>
                </div>
                
                <!-- View All & Navigation Buttons -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- View All Button -->
                    <a href="{{ route('places.index') }}" 
                        class="text-primary font-bold hover:underline flex items-center gap-1">
                        Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    
                    <!-- Navigation Buttons -->
                    <button @click="scrollLeft()"
                        class="size-10 rounded-full border border-surface-light dark:border-white/10 flex items-center justify-center hover:bg-surface-light dark:hover:bg-white/5 text-text-light dark:text-text-dark transition-colors">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button @click="scrollRight()"
                        class="size-10 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-dark transition-colors shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>

            <!-- Carousel Container -->
            <div class="relative w-full">
                <div class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory scrollbar-hide" x-ref="container">
                    
                    @foreach($places as $place)
                    @if(!$place->slug) @continue @endif
                    <!-- Gallery Item -->
                    <a href="{{ route('places.show', $place) }}" class="block min-w-[85%] sm:min-w-[calc(50%-12px)] lg:min-w-[calc(33.333%-16px)] snap-center group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-lg cursor-pointer bg-surface-light dark:bg-surface-dark border border-surface-light dark:border-white/5">
                        <!-- Image -->
                        <div class="absolute inset-0 bg-gray-200">
                            @if($place->image_path)
                                <img src="{{ $place->image_path }}" alt="{{ $place->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-outlined text-4xl">image</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        </div>

                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-bold shadow-sm">
                                {{ $place->category->name ?? 'Wisata' }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-xl font-bold text-white mb-2 leading-tight drop-shadow-sm">{{ $place->name }}</h3>
                            <p class="text-white/80 text-sm line-clamp-2 mb-4 leading-relaxed">
                                {{ Str::limit($place->description, 80) }}
                            </p>
                            
                            <div class="flex items-center justify-between border-t border-white/20 pt-4">
                                <div class="flex items-center gap-2 text-white/90">
                                    <span class="material-symbols-outlined text-sm text-yellow-400">star</span>
                                    <span class="text-sm font-bold">{{ $place->rating }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach

                    @if($places->isEmpty())
                        <div class="w-full text-center py-10 text-gray-500">
                            Belum ada destinasi wisata yang ditampilkan.
                        </div>
                    @endif

                </div>
                
                <!-- Dots Indicator -->
                @if($places->count() > 0)
                <div class="flex justify-center gap-2 mt-6">
                    @foreach($places as $index => $place)
                    <button 
                        @click="scrollToIndex({{ $index }})"
                        :class="currentIndex === {{ $index }} ? 'w-8 bg-primary' : 'w-2 bg-gray-300 dark:bg-gray-600 hover:bg-primary/50'"
                        class="h-2 rounded-full transition-all duration-300"
                        aria-label="Go to slide {{ $index + 1 }}">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Makanan Khas Jepara -->
    <!-- Makanan Khas Jepara -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-10 lg:py-16 border-t border-surface-light dark:border-surface-dark" x-data="{
        currentIndex: 0,
        autoplay: null,
        scrollFrame: null,
        
        getGap() {
            const container = $refs.foodContainer;
            if(!container) return 32;
            const style = window.getComputedStyle(container);
            return parseInt(style.gap) || 0;
        },

        scrollLeft() { 
            const c = $refs.foodContainer;
            if(!c) return;
            const itemWidth = c.children[0].offsetWidth; 
            const gap = this.getGap();
            c.scrollBy({ left: -(itemWidth + gap), behavior: 'smooth' });
            this.stopAutoplay();
            this.startAutoplay();
        },
        scrollRight() { 
            const c = $refs.foodContainer;
            if(!c) return;
            const itemWidth = c.children[0].offsetWidth;
            const gap = this.getGap();
            c.scrollBy({ left: (itemWidth + gap), behavior: 'smooth' });
            this.stopAutoplay();
            this.startAutoplay();
        },
        scrollToIndex(index) {
            const container = $refs.foodContainer;
            if (!container || !container.children.length) return;
            
            const originalsCount = 9;
            const targetIndex = originalsCount + index;
            
            const targetElement = container.children[targetIndex];
            if(targetElement) {
                const scrollPos = targetElement.offsetLeft - (container.clientWidth - targetElement.offsetWidth) / 2;
                container.scrollTo({ left: scrollPos, behavior: 'smooth' });
            }
            this.stopAutoplay();
            this.startAutoplay();
        },
        updateActive() {
            const container = $refs.foodContainer;
            if (!container) return;
            
            const center = container.scrollLeft + (container.clientWidth / 2);
            
            Array.from(container.children).forEach(child => {
                const childCenter = child.offsetLeft + (child.offsetWidth / 2);
                const distance = Math.abs(center - childCenter);
                
                if (distance < child.offsetWidth / 2) {
                    child.setAttribute('data-snapped', 'true');
                } else {
                    child.setAttribute('data-snapped', 'false');
                }
            });
        },
        updateCurrentIndex() {
            const container = $refs.foodContainer;
            if (!container || !container.children.length) return;
            
            const center = container.scrollLeft + (container.clientWidth / 2);
            let closestIndex = -1;
            let minDistance = Infinity;
            
            Array.from(container.children).forEach((child, idx) => {
                 const childCenter = child.offsetLeft + (child.offsetWidth / 2);
                 const distance = Math.abs(center - childCenter);
                 if(distance < minDistance) {
                     minDistance = distance;
                     closestIndex = idx;
                 }
            });
            
            if(closestIndex !== -1) {
                const originalsCount = 9;
                const rawIndex = closestIndex - originalsCount;
                this.currentIndex = ((rawIndex % originalsCount) + originalsCount) % originalsCount;
            }
        },
        startAutoplay() {
            this.stopAutoplay();
            this.autoplay = setInterval(() => {
                const container = $refs.foodContainer;
                if(container) {
                    const itemWidth = container.children[0].offsetWidth;
                    const gap = this.getGap();
                    container.scrollBy({ left: (itemWidth + gap), behavior: 'smooth' });
                }
            }, 3000);
        },
        stopAutoplay() {
            if (this.autoplay) {
                clearInterval(this.autoplay);
                this.autoplay = null;
            }
        },
        init() {
            const container = $refs.foodContainer;
            if (!container) return;
            
            const originals = Array.from(container.children);
            const originalsCount = originals.length;
            
            originals.forEach(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute('data-clone', 'true');
                clone.setAttribute('aria-hidden', 'true');
                container.appendChild(clone);
            });
            
            [...originals].reverse().forEach(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute('data-clone', 'true');
                clone.setAttribute('aria-hidden', 'true');
                container.insertBefore(clone, container.firstChild);
            });
            
            this.$nextTick(() => {
                const startItem = container.children[originalsCount];
                if(startItem) {
                    container.scrollLeft = startItem.offsetLeft - (container.clientWidth - startItem.offsetWidth) / 2;
                }
                
                this.updateActive();
                this.updateCurrentIndex();
                this.startAutoplay();
                
                container.addEventListener('scroll', () => {
                    if (this.scrollFrame) cancelAnimationFrame(this.scrollFrame);
                    this.scrollFrame = requestAnimationFrame(() => {
                        this.updateActive();
                        this.updateCurrentIndex();
                        
                        if (container.children.length < originalsCount * 3) return;
                        
                        const setB_StartElement = container.children[originalsCount];
                        const setC_StartElement = container.children[originalsCount * 2];
                        
                        // Precise width calculation based on offsets
                        const setWidth = setC_StartElement.offsetLeft - setB_StartElement.offsetLeft;
                        const scrollLeft = container.scrollLeft;
                        
                        // Jump thresholds must be robust
                        // Jump BACK to Set B from Set C
                        if (scrollLeft >= setC_StartElement.offsetLeft) {
                            // Calculate overlap to maintain smooth momentum if needed (though tricky with scrollLeft)
                            // const overlap = scrollLeft - setC_StartElement.offsetLeft;
                            // container.scrollLeft = setB_StartElement.offsetLeft + overlap;
                            // Simple offset subtraction logic is usually safest
                             container.scrollLeft -= setWidth;
                        }
                        // Jump FORWARD to Set B from Set A
                        else if (scrollLeft < setB_StartElement.offsetLeft - setWidth) {
                             container.scrollLeft += setWidth;
                        }
                    });
                });
            });
        }
    }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-text-light dark:text-text-dark mb-4 leading-tight">
                        Kuliner Khas Jepara
                    </h2>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-lg leading-relaxed">
                        Nikmati cita rasa autentik kuliner tradisional Jepara yang kaya akan rempah dan warisan budaya.
                    </p>
                </div>
                
                <!-- Navigation Buttons -->
                <div class="flex gap-2 shrink-0">
                    <button @click="scrollLeft()"
                        class="size-10 rounded-full border border-surface-light dark:border-white/10 flex items-center justify-center hover:bg-surface-light dark:hover:bg-white/5 text-text-light dark:text-text-dark transition-colors">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <button @click="scrollRight()"
                        class="size-10 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-dark transition-colors shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>

            <!-- Food Carousel -->
            <div class="relative w-full group">
                <!-- Carousel Container -->
                <div class="flex gap-4 lg:gap-8 overflow-x-auto pb-12 pt-4 px-4 lg:px-0 snap-x snap-mandatory scrollbar-hide" 
                     x-ref="foodContainer">
                
                
                    <!-- 1. Pindang Serani -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/srani.png') }}" 
                             alt="Pindang Serani" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <!-- Inactive Background Fade -->
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <!-- Text Readability Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <!-- Content -->
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Pindang Serani</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Sup ikan laut dengan kuah bening segar berbumbu belimbing wuluh dan rempah khas Jepara.</p>
                        </div>
                    </div>

                    <!-- 2. Durian Jepara -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/duren.png') }}" 
                             alt="Durian Jepara" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Durian Jepara</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Raja buah lokal Petruk dari Jepara dengan daging tebal manis dan aroma menggoda.</p>
                        </div>
                    </div>

                    <!-- 3. Adon-adon Coro -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/adon-coro.png') }}" 
                             alt="Adon-adon Coro" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Adon-adon Coro</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Minuman jamu tradisional hangat berbahan santan, jahe, gula merah, dan rempah pilihan.</p>
                        </div>
                    </div>

                    <!-- 4. Horog-horog -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/horog.png') }}" 
                             alt="Horog-horog" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Horog-horog</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Pengganti nasi unik bertekstur butiran kenyal, terbuat dari tepung pohon aren.</p>
                        </div>
                    </div>

                    <!-- 5. Carang Madu -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/carang-madu.png') }}" 
                             alt="Carang Madu" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Carang Madu</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Kue oleh-oleh renyah berbentuk sarang madu dengan siraman gula merah manis.</p>
                        </div>
                    </div>

                    <!-- 6. Es Gempol Pleret -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/gempol.png') }}" 
                             alt="Es Gempol Pleret" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Es Gempol Pleret</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Minuman es segar berisi gempol beras dan pleret tepung, disiram kuah santan dan sirup.</p>
                        </div>
                    </div>

                    <!-- 7. Kopi Jeparanan -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/kopi.png') }}" 
                             alt="Kopi Jeparanan" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Kopi Jeparanan</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Kopi robusta khas pegunungan Muria Jepara dengan aroma kuat dan cita rasa otentik.</p>
                        </div>
                    </div>

                    <!-- 8. Kacang Listrik -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/kcang.png') }}" 
                             alt="Kacang Listrik" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Kacang Listrik</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Kacang tanah sangrai unik yang dimatangkan dengan bantuan oven, gurih dan renyah.</p>
                        </div>
                    </div>

                    <!-- 9. Krupuk Ikan Tengiri -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/krpktgr.png') }}" 
                             alt="Krupuk Ikan Tengiri" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">Krupuk Ikan Tengiri</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">Kerupuk gurih dengan rasa ikan tengiri asli yang kuat, oleh-oleh wajib khas pesisir.</p>
                        </div>
                    </div>

                </div>
                
                <!-- Dots Indicator -->
                <div class="flex justify-center gap-2 mt-8">
                    <template x-for="i in 9" :key="i">
                        <button 
                            @click="scrollToIndex(i - 1)"
                            :class="currentIndex === (i - 1) ? 'w-8 bg-primary' : 'w-2 bg-gray-300 dark:bg-gray-600 hover:bg-primary/50'"
                            class="h-2 rounded-full transition-all duration-300"
                            :aria-label="'Go to slide ' + i">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Berita & Pengumuman -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-10 lg:py-16 scroll-mt-20 border-t border-surface-light dark:border-surface-dark transition-colors duration-200"
        id="news">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-0 mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-text-light dark:text-text-dark">Berita & Pengumuman</h2>
                <a class="text-primary font-bold hover:underline flex items-center gap-1 self-start md:self-auto"
                    href="{{ route('posts.index') }}">
                    Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @foreach($posts as $post)
                <article class="bg-background-light dark:bg-surface-dark rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col h-full border border-surface-light dark:border-white/5">
                    <div class="h-48 overflow-hidden relative">
                        <div class="absolute top-3 left-3 {{ $post->type == 'event' ? 'bg-purple-600' : 'bg-blue-600' }} text-white text-xs font-bold px-3 py-1 rounded-full z-10 uppercase">
                            {{ $post->type == 'event' ? 'Agenda' : 'Berita' }}
                        </div>
                        <img alt="{{ $post->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            src="{{ $post->image_path }}" />
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 text-xs text-text-light/50 dark:text-text-dark/50 mb-2">
                            <span class="material-symbols-outlined text-sm">calendar_today</span>
                            <span>{{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-text-light dark:text-text-dark mb-2 leading-tight line-clamp-2">
                            {{ $post->title }}
                        </h3>
                        <p class="text-text-light/70 dark:text-text-dark/70 text-sm mb-4 line-clamp-2">
                            {{ Str::limit(strip_tags($post->content), 100) }}
                        </p>
                        <div class="mt-auto">
                            <a class="text-primary font-bold text-sm hover:underline" href="{{ route('posts.show', $post) }}">Baca Selengkapnya</a>
                        </div>
                    </div>
                </article>
                @endforeach
                
                @if($posts->isEmpty())
                    <div class="col-span-full text-center py-10 text-gray-500">
                        Belum ada berita atau agenda terbaru.
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Footer -->
    <!-- Footer Start -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700;900&display=swap');
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
                                Dinas Pariwisata & Kebudayaan
                            </span>
                            <span class="block text-white/40 text-[10px] md:text-sm font-medium tracking-wide mt-1">
                                Tourism & Culture Office of Jepara
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
                        Pusat informasi resmi pariwisata dan kebudayaan Kabupaten Jepara. Temukan keindahan alam dan kekayaan budaya Kartini.
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
                            Jelajahi
                            <span class="absolute -bottom-2 md:-bottom-2 left-0 w-8 h-1 bg-primary rounded-full hidden md:block"></span>
                        </h3>
                        <i id="icon-explore" class="fa-solid fa-chevron-down text-white/40 text-xs transition-transform duration-300 md:hidden"></i>
                    </button>
                    <ul id="content-explore" class="hidden md:block space-y-3 md:space-y-4 mt-4 md:mt-8 transition-all duration-300 origin-top">
                        <li><a href="{{ route('welcome') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Beranda</a></li>
                        <li><a href="{{ route('places.index') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Destinasi</a></li>
                        <li><a href="{{ route('explore.map') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Peta Wisata</a></li>
                        <li><a href="{{ route('events.public.index') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Agenda</a></li>
                        <li><a href="{{ route('posts.index') }}" class="text-white/60 hover:text-white md:hover:translate-x-2 transition-all duration-300 flex items-center gap-2 group font-semibold text-xs md:text-base selection:bg-primary/30"><span class="w-1.5 h-1.5 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span> Berita</a></li>
                    </ul>
                </div>

                <!-- Column 3: Categories (Collapsible on Mobile) -->
                <div class="border-b border-white/5 md:border-none pb-4 md:pb-0">
                    <button onclick="toggleFooterSection('categories')" class="w-full flex items-center justify-between md:cursor-default md:pointer-events-none group">
                        <h3 class="text-white text-sm md:text-lg font-extrabold relative inline-block tracking-tight">
                            Kategori
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
                            Hubungi Kami
                            <span class="absolute -bottom-2 md:-bottom-2 left-0 w-8 h-1 bg-primary rounded-full hidden md:block"></span>
                        </h3>
                        <i id="icon-contact" class="fa-solid fa-chevron-down text-white/40 text-xs transition-transform duration-300 md:hidden"></i>
                    </button>
                    <ul id="content-contact" class="hidden md:block space-y-4 md:space-y-5 mt-4 md:mt-8 transition-all duration-300 origin-top">
                        <li class="flex flex-col md:flex-row items-center md:items-start gap-2 md:gap-4 group leading-tight">
                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white/5 flex-shrink-0 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                <i class="fa-solid fa-location-dot text-primary text-xs md:text-base"></i>
                            </div>
                            <span class="text-white/60 text-[11px] md:text-sm leading-tight group-hover:text-white/80 transition-colors font-medium">Jl. Kartini No.1, Panggang I, Jepara, Jawa Tengah</span>
                        </li>
                        <li class="flex flex-col md:flex-row items-center md:items-start gap-2 md:gap-4 group leading-tight">
                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-white/5 flex-shrink-0 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                <i class="fa-solid fa-phone text-primary text-xs md:text-base"></i>
                            </div>
                            <span class="text-white/60 text-[11px] md:text-sm group-hover:text-white/80 transition-colors font-medium">(0291) 591148</span>
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
                    <p class="text-[9px] md:text-[10px] text-white/30 uppercase tracking-[0.4em] mb-2 md:mb-4 font-bold">Official Tourism Website of Jepara Regency</p>
                    <p class="text-[10px] md:text-xs text-white/50 font-semibold tracking-tight">
                        &copy; 2024 <span class="text-white">Dinas Pariwisata dan Kebudayaan</span>. <br class="md:hidden"> 
                        All Rights Reserved.
                    </p>
                </div>
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
                    this.map = L.map('leaflet-map', {
                        zoomControl: false,
                        attributionControl: false,
                        scrollWheelZoom: false
                    }).setView([-6.59, 110.68], 10);

                    // Define Base Layers
                    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });
                    const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
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
                    this.loading = true;
                    try {
                        // Fetch Places (Critical for Search)
                        const placesUrl = '{{ route('places.geojson') }}';
                        console.log('Fetching:', placesUrl);
                        
                        const placesResponse = await fetch(placesUrl);
                        console.log('Places Response Status:', placesResponse.status);

                        if (!placesResponse.ok) {
                            throw new Error(`HTTP Error ${placesResponse.status}`);
                        }

                        const placesData = await placesResponse.json();
                        
                        // Validation
                        if (!placesData.features) {
                            console.error('Invalid GeoJSON format:', placesData);
                            alert('Error: Data peta tidak valid (Format GeoJSON salah).');
                        }

                        this.geoFeatures = placesData.features || [];
                        this.allPlaces = placesData.features || [];
                        console.log('Loaded Places:', this.allPlaces.length);

                        if (this.allPlaces.length === 0) {
                             console.warn('Warning: No places received from server.');
                        }

                        this.updateMapMarkers();

                    } catch (e) {
                         console.error('PLACES FETCH ERROR:', e);
                         alert('Gagal memuat data destinasi: ' + e.message);
                    }

                    // Background load for other layers (non-critical)
                    try {
                        const [boundaries, infrastructures, landUses] = await Promise.all([
                            fetch('{{ route('boundaries.geojson') }}').then(r => r.json()),
                            fetch('{{ route('infrastructures.geojson') }}').then(r => r.json()),
                            fetch('{{ route('land_uses.geojson') }}').then(r => r.json())
                        ]);

                        this.loadBoundaries(boundaries.features || []);
                        this.loadInfrastructures(infrastructures.features || []);
                        this.loadLandUses(landUses.features || []);
                    } catch (e) {
                        console.error('Layer load error:', e);
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
                        style: {
                            color: '#059669',
                            weight: 2,
                            fillColor: '#10b981',
                            fillOpacity: 0.1,
                            dashArray: '5, 5'
                        },
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => {
                                L.DomEvent.stop(e);
                                this.selectFeature({
                                    ...f.properties,
                                    type: 'Batas Wilayah'
                                });
                            });
                        }
                    }).addTo(this.map);

                    // Removed fitBounds to keep regional view
                    // if (features.length > 0) this.map.fitBounds(this.boundariesLayer.getBounds(), { padding: [50, 50] });
                },

                loadInfrastructures(features) {
                    if (this.infrastructuresLayer) this.map.removeLayer(this.infrastructuresLayer);
                    if (!this.showInfrastructures) return;

                    this.infrastructuresLayer = L.geoJSON(features, {
                        style: f => {
                            const type = f.properties.type;
                            const color = type === 'river' ? '#3b82f6' : '#64748b';
                            return {
                                color: color,
                                weight: type === 'river' ? 4 : 3,
                                opacity: 0.8
                            };
                        },
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => {
                                L.DomEvent.stop(e);
                                this.selectFeature({
                                    ...f.properties,
                                    type: 'Infrastruktur'
                                });
                            });
                        }
                    }).addTo(this.map);
                },

                loadLandUses(features) {
                    if (this.landUsesLayer) this.map.removeLayer(this.landUsesLayer);
                    if (!this.showLandUses) return;

                    this.landUsesLayer = L.geoJSON(features, {
                        style: f => {
                            const colors = {
                                rice_field: '#fbbf24',
                                forest: '#15803d',
                                settlement: '#f97316',
                                plantation: '#84cc16'
                            };
                            return {
                                color: colors[f.properties.type] || '#94a3b8',
                                weight: 1,
                                fillOpacity: 0.3,
                                fillColor: colors[f.properties.type]
                            };
                        },
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => {
                                L.DomEvent.stop(e);
                                this.selectFeature({
                                    ...f.properties,
                                    type: 'Penggunaan Lahan'
                                });
                            });
                        }
                    }).addTo(this.map);
                },

                updateMapMarkers() {
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];

                    const filtered = this.geoFeatures.filter(f => this.selectedCategories.includes(f.properties.category
                        ?.id));

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
                            icon: L.divIcon({
                                html: iconHtml,
                                className: '',
                                iconSize: [36, 36],
                                iconAnchor: [18, 18]
                            })
                        });

                        marker.on('click', () => {
                            this.selectPlace({
                                ...p,
                                latitude: lat,
                                longitude: lng
                            });
                        });

                        marker.addTo(this.map);
                        this.markers.push(marker);
                    });
                },

                performSearch() {
                    console.log('Searching for:', this.searchQuery); // DEBUG
                    const q = this.searchQuery;
                    
                    if (!q) {
                        this.searchResults = [];
                        return;
                    }

                    // Server-side Search
                    fetch(`/search/places?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            console.log('Server Search Results:', data); // DEBUG
                            this.searchResults = data;
                        })
                        .catch(err => {
                            console.error('Search Error:', err);
                        });
                },

                selectFeature(result) {
                    console.log('Selected feature:', result); // DEBUG
                    // If result has a slug (is a Place), redirect to detail page
                    if (result.slug) {
                        console.log('Redirecting to:', `/destinasi/${result.slug}`); // DEBUG
                        window.location.href = `/destinasi/${result.slug}`;
                        return;
                    }

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
                        this.map.fitBounds(layer.getBounds(), {
                            padding: [50, 50]
                        });
                    }
                },

                scrollToMap() {
                    document.getElementById('gis-map').scrollIntoView({
                        behavior: 'smooth'
                    });
                },

                locateUser() {
                    if (!navigator.geolocation) {
                        alert('Browser tidak mendukung geolokasi');
                        return;
                    }
                    this.loading = true;
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const {
                                latitude,
                                longitude
                            } = pos.coords;
                            this.map.flyTo([latitude, longitude], 17);
                            if (this.userMarker) this.map.removeLayer(this.userMarker);
                            this.userMarker = L.marker([latitude, longitude], {
                                icon: L.divIcon({
                                    html: '<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-lg marker-pulse"></div>',
                                    iconSize: [16, 16]
                                })
                            }).addTo(this.map);
                            this.loading = false;
                        },
                        (err) => {
                            console.error(err);
                            this.loading = false;
                            alert('Gagal mengambil lokasi.');
                        }
                    );
                },

                getDirectionsUrl(feature) {
                    let lat, lng;
                    if (feature.latitude && feature.longitude) {
                        lat = feature.latitude;
                        lng = feature.longitude;
                    } else if (feature.coords) {
                        lat = feature.coords[0];
                        lng = feature.coords[1];
                    } else if (feature.geometry && feature.geometry.type === 'Point') {
                        lng = feature.geometry.coordinates[0];
                        lat = feature.geometry.coordinates[1];
                    }
                    
                    if (lat && lng) {
                        return `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
                    }
                    return null;
                }
            };
        }
    </script>
</body>

</html>
