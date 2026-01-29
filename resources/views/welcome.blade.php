<!DOCTYPE html>
<html class="light scroll-smooth overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Portal Wisata - {{ config('app.name', 'Jepara') }}</title>

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
                        <h2 class="text-xl font-bold leading-tight tracking-tight">Pesona Jepara</h2>
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
                                @input.debounce.300ms="performSearch()" @keydown.enter="scrollToMap()" />
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
                    <div class="hidden lg:flex">
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

                    <div class="border-t border-surface-light dark:border-surface-dark pt-4">
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
                            Jepara<br /> The World Carving Center
                        </h1>
                        <p
                            class="text-gray-100 text-lg sm:text-xl font-medium max-w-2xl mx-auto leading-relaxed drop-shadow-sm animate-fade-in-up delay-100">
                            Temukan keindahan pantai tropis, kekayaan sejarah, dan mahakarya seni ukir kelas dunia.
                        </p>
                        <div
                            class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 animate-fade-in-up delay-200">
                            <a class="flex items-center justify-center h-12 px-8 rounded-full bg-primary hover:bg-primary-dark text-white text-base font-bold shadow-lg shadow-black/20 transition-all hover:-translate-y-0.5"
                                href="{{ route('explore.map') }}">
                                Jelajahi Peta GIS
                            </a>
                            <a href="#profile"
                                class="hidden sm:flex items-center justify-center h-12 px-8 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white text-base font-bold transition-all">
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
    <div class="w-full bg-background-light dark:bg-background-dark py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-8">
                <div class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                    <div class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                        <span class="material-symbols-outlined">groups</span>
                    </div>
                    <div>
                        <p class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">
                            Jumlah Penduduk</p>
                        <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">
                            1.250.000++</p>
                        <p class="text-xs text-text-light/50">Jiwa</p>
                    </div>
                </div>

                <!-- Area -->
                <!-- Area -->
                <div
                    class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                    <div
                        class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                        <span class="material-symbols-outlined">square_foot</span>
                    </div>
                    <div>
                        <p
                            class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">
                             Wilayah Administratif</p>
                        <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">
                            {{ number_format($totalArea ?? 0, 1) }}</p>
                        <p class="text-xs text-text-light/50">Hektar</p>
                    </div>
                </div>
                <!-- Dukuh -->
                <!-- Dukuh -->
                <div
                    class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                    <div
                        class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                        <span class="material-symbols-outlined">holiday_village</span>
                    </div>
                    <div>
                        <p
                            class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">
                            Kecamatan</p>
                        <p class="text-text-light dark:text-text-dark text-3xl font-bold tracking-tight">
                            16</p>
                        <p class="text-xs text-text-light/50">Wilayah</p>
                    </div>
                </div>
                <!-- Industry -->
                <!-- Industry -->
                <div
                    class="flex flex-col gap-3 rounded-xl p-4 md:p-6 bg-surface-light dark:bg-surface-dark hover:bg-white dark:hover:bg-white/5 transition-colors shadow-sm border border-transparent hover:border-primary/20">
                    <div
                        class="size-10 rounded-full bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                        <span class="material-symbols-outlined">palette</span>
                    </div>
                    <div>
                        <p
                            class="text-text-light/60 dark:text-text-dark/60 text-sm font-medium uppercase tracking-wide">
                            Potensi</p>
                        <p class="text-text-light dark:text-text-dark text-xl font-bold tracking-tight">The World Carving Center
                        </p>
                        <p class="text-xs text-text-light/50">Nasional</p>
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
                            <span class="font-serif italic font-normal text-gray-500 dark:text-gray-400">di Utara Jawa</span>
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
            <div class="text-center mb-20" 
                 x-data="{ shown: false }" 
                 x-intersect.threshold.0.5="shown = true">
                <span class="block text-primary font-bold tracking-[0.3em] uppercase text-xs mb-4 opacity-0 translate-y-4 transition-all duration-700"
                      :class="shown ? 'opacity-100 translate-y-0' : ''">
                    Warisan Leluhur
                </span>
                <h2 class="text-5xl md:text-6xl lg:text-7xl font-['Pinyon_Script'] text-gray-900 dark:text-gray-100 mb-6 opacity-0 translate-y-4 transition-all duration-700 delay-100 drop-shadow-sm"
                    :class="shown ? 'opacity-100 translate-y-0' : ''">
                    Sejarah & Legenda
                </h2>
                <div class="w-16 h-1 bg-gray-200 dark:bg-gray-700 mx-auto rounded-full overflow-hidden opacity-0 scale-x-0 transition-all duration-700 delay-200"
                     :class="shown ? 'opacity-100 scale-x-100' : ''">
                     <div class="w-1/2 h-full bg-primary animate-slide-x"></div>
                </div>
            </div>

            <!-- Full Image Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16">
                
                <!-- Kartini Card -->
                <div class="group relative h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none"
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.2="shown = true"
                     :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12 transition-all duration-1000'">
                    
                    <!-- Full Background Image -->
                    <img src="{{ asset('images/kartini.jpg') }}" 
                         alt="R.A. Kartini" 
                         class="absolute inset-0 w-full h-full object-cover object-top filter grayscale-[0.2] group-hover:grayscale-0 group-hover:scale-105 transition-all duration-[1500ms] ease-out">
                    
                    <!-- Gradient Overlay (Subtle) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-700"></div>

                    <!-- Content Overlay -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl md:text-5xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">R.A. Kartini</h3>
                        <p class="text-xl md:text-2xl font-['Pinyon_Script'] text-white/90 mb-6">
                            "Habis Gelap Terbitlah Terang"
                        </p>
                        <div class="h-0 group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 group-hover:opacity-100">
                            <p class="text-white/80 text-sm md:text-base leading-relaxed max-w-md">
                                Pahlawan emansipasi yang memperjuangkan hak pendidikan wanita. Sosoknya menginspirasi perubahan besar dari Jepara untuk Indonesia.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kalinyamat Card -->
                <div class="group relative h-[600px] w-full rounded-[2rem] overflow-hidden shadow-2xl shadow-gray-200/50 dark:shadow-none transition-all duration-1000 delay-200"
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
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700">
                        <div class="w-12 h-1 bg-white/30 backdrop-blur-sm mb-6 rounded-full group-hover:w-20 transition-all duration-500"></div>
                        <h3 class="text-3xl md:text-5xl font-['Playfair_Display'] font-black mb-3 leading-tight tracking-tight">Ratu Kalinyamat</h3>
                        <p class="text-xl md:text-2xl font-['Pinyon_Script'] text-white/90 mb-6">
                            "Sang Ratu Laut yang Gagah Berani"
                        </p>
                        <div class="h-0 group-hover:h-auto overflow-hidden transition-all duration-500 opacity-0 group-hover:opacity-100">
                            <p class="text-white/80 text-sm md:text-base leading-relaxed max-w-md">
                                Penguasa maritim Nusantara yang disegani. Membangun Jepara menjadi pusat niaga dan kekuatan laut yang tak tertandingi.
                            </p>
                        </div>
                    </div>
                </div>

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
            const itemWidth = container.children[0]?.offsetWidth || 0;
            const gap = 24; // gap-6 = 24px
            container.scrollTo({ left: index * (itemWidth + gap), behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        updateCurrentIndex() {
            const container = $refs.container;
            if (!container || !container.children.length) return;
            const scrollLeft = container.scrollLeft;
            const itemWidth = container.children[0].offsetWidth;
            const gap = 24;
            this.currentIndex = Math.round(scrollLeft / (itemWidth + gap));
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
                                <span class="text-white/90 text-sm font-medium">
                                    {{ $place->ticket_price == 'Gratis' ? 'Gratis' : $place->ticket_price }}
                                </span>
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
        totalItems: 9,
        autoplay: null,
        scrollLeft() { 
            const c = $refs.foodContainer;
            const w = c.children[0].clientWidth; // lebar 1 item
            c.scrollBy({ left: -w, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        scrollRight() { 
            const c = $refs.foodContainer;
            const w = c.children[0].clientWidth;
            c.scrollBy({ left: w, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        scrollToIndex(index) {
            const container = $refs.foodContainer;
            if (!container || !container.children.length) return;
            const originals = 9; // total original items
            const targetIndex = originals + index; // Skip clones di awal
            const itemWidth = container.children[0].offsetWidth;
            const gap = 32; // gap-8 lg
            container.scrollTo({ left: targetIndex * (itemWidth + gap) - (container.clientWidth - itemWidth) / 2, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        updateActive() {
            const container = $refs.foodContainer;
            if (!container) return;
            
            const center = container.scrollLeft + (container.clientWidth / 2);
            
            Array.from(container.children).forEach(child => {
                const childCenter = child.offsetLeft + (child.clientWidth / 2);
                const distance = Math.abs(center - childCenter);
                
                // Jika jarak ke tengah kurang dari setengah lebar card, anggap aktif
                if (distance < child.clientWidth / 2) {
                    child.setAttribute('data-snapped', 'true');
                } else {
                    child.setAttribute('data-snapped', 'false');
                }
            });
        },
        updateCurrentIndex() {
            const container = $refs.foodContainer;
            if (!container || !container.children.length) return;
            const originals = 9;
            const scrollLeft = container.scrollLeft;
            const itemWidth = container.children[0].offsetWidth;
            const gap = 32;
            const rawIndex = Math.round(scrollLeft / (itemWidth + gap));
            // Map back to original indices (0-8)
            this.currentIndex = ((rawIndex - originals) % originals + originals) % originals;
        },
        startAutoplay() {
            this.stopAutoplay();
            this.autoplay = setInterval(() => {
                this.scrollRight();
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
            
            // Simpan original items
            const originals = Array.from(container.children);
            
            // Clone sets untuk infinite loop (Total 3 set: A-B-A)
            // Append clone set (Set C)
            originals.forEach(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute('data-clone', 'true');
                container.appendChild(clone);
            });
            
            // Prepend clone set (Set A) - Agar bisa scroll kiri dari awal
            // Note: Prepend mengubah scroll position, harus kita fix nanti
            originals.reverse().forEach(item => {
                const clone = item.cloneNode(true);
                clone.setAttribute('data-clone', 'true');
                container.insertBefore(clone, container.firstChild);
            });
            
            // Set scroll position ke Set B (Originals)
            // Tunggu render selesai
            this.$nextTick(() => {
                const itemWidth = container.children[0].clientWidth; // asumsikan sama
                const gap = 32; // gap-8 = 2rem = 32px (lg)
                // Total width 1 set (8 items) approx
                // Kita cari elemen original pertama (index ke-8, karena ada 8 clone di depan)
                const startItem = container.children[originals.length];
                
                // Scroll ke posisi startItem
                container.scrollLeft = startItem.offsetLeft - (container.clientWidth - startItem.clientWidth) / 2;
                
                this.updateActive();
                
                // Infinite Loop Listener
                container.addEventListener('scroll', () => {
                    this.updateActive();
                    this.updateCurrentIndex();
                    
                    const oneSetWidth = (originals.length * (container.children[0].clientWidth + 32)); // Estimasi rough width
                    // Atau lebih akurat: startItem.offsetLeft
                    
                    const scroll = container.scrollLeft;
                    const max = container.scrollWidth;
                    
                    // Logic Jump
                    // Ambil posisi Set B start dan Set B end
                    const setB_Start = container.children[originals.length].offsetLeft;
                    const setB_End = container.children[originals.length * 2].offsetLeft;
                    
                    // Jika scroll terlalu ke kiri (masuk Set A), lompat ke Set B
                    // if (scroll < 100) ... logic ini butuh presisi tinggi agar seamless.
                    
                    // Metode simple: Jika sampai ujung, reset.
                    // Jika scroll mencapai hampir ujung kanan (Set C end), lompat ke Set B end.
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 50) {
                        container.scrollLeft = setB_End - container.clientWidth;
                    }
                    // Jika scroll mencapai ujung kiri (Set A start), lompat ke Set B start
                    else if (container.scrollLeft <= 50) {
                        container.scrollLeft = setB_Start;
                    }
                });
                
                this.startAutoplay();
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
                <!-- Carousel Container with Mask Image for Perfect Fade -->
                <div class="flex gap-4 lg:gap-8 overflow-x-auto pb-12 pt-4 px-4 lg:px-0 snap-x snap-mandatory scrollbar-hide" 
                     x-ref="foodContainer">
                
                
                    <!-- 1. Pindang Serani -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/srani.png') }}" 
                             alt="Pindang Serani" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <!-- Inactive Background Fade -->
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <!-- Text Readability Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <!-- Content -->
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Pindang Serani</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Sup ikan laut dengan kuah bening segar berbumbu belimbing wuluh dan rempah khas Jepara.</p>
                        </div>
                    </div>

                    <!-- 2. Durian Jepara -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/duren.png') }}" 
                             alt="Durian Jepara" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Durian Jepara</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Raja buah lokal Petruk dari Jepara dengan daging tebal manis dan aroma menggoda.</p>
                        </div>
                    </div>

                    <!-- 3. Adon-adon Coro -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/adon-coro.png') }}" 
                             alt="Adon-adon Coro" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Adon-adon Coro</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Minuman jamu tradisional hangat berbahan santan, jahe, gula merah, dan rempah pilihan.</p>
                        </div>
                    </div>

                    <!-- 4. Horog-horog -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/horog.png') }}" 
                             alt="Horog-horog" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Horog-horog</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Pengganti nasi unik bertekstur butiran kenyal, terbuat dari tepung pohon aren.</p>
                        </div>
                    </div>

                    <!-- 5. Carang Madu -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/carang-madu.png') }}" 
                             alt="Carang Madu" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Carang Madu</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Kue oleh-oleh renyah berbentuk sarang madu dengan siraman gula merah manis.</p>
                        </div>
                    </div>

                    <!-- 6. Es Gempol Pleret -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/gempol.png') }}" 
                             alt="Es Gempol Pleret" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Es Gempol Pleret</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Minuman es segar berisi gempol beras dan pleret tepung, disiram kuah santan dan sirup.</p>
                        </div>
                    </div>

                    <!-- 7. Kopi Jeparanan -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/kopi.png') }}" 
                             alt="Kopi Jeparanan" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Kopi Jeparanan</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Kopi robusta khas pegunungan Muria Jepara dengan aroma kuat dan cita rasa otentik.</p>
                        </div>
                    </div>

                    <!-- 8. Kacang Listrik -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/kcang.png') }}" 
                             alt="Kacang Listrik" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Kacang Listrik</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Kacang tanah sangrai unik yang dimatangkan dengan bantuan oven, gurih dan renyah.</p>
                        </div>
                    </div>

                    <!-- 9. Krupuk Ikan Tengiri -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset('images/kuliner-jppr/krpktgr.png') }}" 
                             alt="Krupuk Ikan Tengiri" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <div class="absolute inset-0 bg-surface-light/95 dark:bg-surface-dark/95 transition-opacity duration-500 group-data-[snapped=true]:opacity-0 backdrop-blur-[2px]"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute inset-0 flex flex-col justify-end p-8 lg:p-12">
                            <h3 class="text-white font-bold text-4xl lg:text-5xl mb-3 drop-shadow-2xl">Krupuk Ikan Tengiri</h3>
                            <p class="text-white/95 text-lg lg:text-xl max-w-xl mb-6 leading-relaxed drop-shadow-lg">Kerupuk gurih dengan rasa ikan tengiri asli yang kuat, oleh-oleh wajib khas pesisir.</p>
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
    <footer class="bg-gray-900/50 bg-cover bg-center bg-no-repeat bg-blend-multiply border-t border-white/10 pt-32 pb-8 px-4 md:px-10" style="font-family: 'Poppins', sans-serif; background-image: url('{{ asset('images/footer/image.png') }}');">
        <div class="w-full mx-auto max-w-7xl">
            
            <!-- Baris 1: DINAS PARIWISATA -->
            <div class="flex justify-between w-full text-center text-[8vw] font-[900] text-white drop-shadow-lg uppercase tracking-tighter mb-6 leading-[0.8] select-none">
                <span>D</span><span>I</span><span>N</span><span>A</span><span>S</span>
                <span class="invisible text-[1vw]">_</span>
                <span>P</span><span>A</span><span>R</span><span>I</span><span>W</span><span>I</span><span>S</span><span>A</span><span>T</span><span>A</span>
            </div>

            <!-- Baris 2: KABUPATEN JEPARA -->
            <div class="flex justify-between w-full text-center text-[7.8vw] font-[900] text-white drop-shadow-lg uppercase tracking-tighter mb-10 leading-[0.8] select-none">
                <span>K</span><span>A</span><span>B</span><span>U</span><span>P</span><span>A</span><span>T</span><span>E</span><span>N</span>
                <span class="invisible text-[1vw]">_</span>
                <span class="text-white">J</span><span class="text-white">E</span><span class="text-white">P</span><span class="text-white">A</span><span class="text-white">R</span><span class="text-white">A</span>
            </div>

            <!-- Divider -->
            <div class="w-full h-[1px] bg-white/20 mb-8"></div>

            <!-- Info Icons & Copyright -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                
                <div class="flex items-center gap-10 opacity-80 hover:opacity-100 transition-opacity duration-300 -translate-y-4">
                    <div class="flex items-center drop-shadow-md">
                        <img src="{{ asset('images/footer/wndrfl-indonesia.png') }}" alt="Wonderful Indonesia" class="h-20 w-auto object-contain contrast-150 drop-shadow-[0_0_1px_rgba(255,255,255,0.8)]">
                    </div>
                    
                    <div class="flex items-center drop-shadow-md">
                        <img src="{{ asset('images/footer/logo-jpr-psn.png') }}" alt="Jepara Mempesona" class="h-20 w-auto object-contain">
                    </div>
                </div>

                <div class="text-center md:text-right">
                    <!-- Social Media Icons -->
                    <div class="flex justify-center md:justify-end gap-6 mb-6">
                        <a href="#" aria-label="Facebook" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" aria-label="Instagram" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                        <a href="#" aria-label="YouTube" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-youtube text-xl"></i>
                        </a>
                        <a href="#" aria-label="Twitter" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-twitter text-xl"></i>
                        </a>
                    </div>

                    <p class="text-xs text-white/70 font-bold uppercase tracking-widest mb-2 drop-shadow-md">Pemerintah Kabupaten Jepara</p>
                    <p class="text-sm text-white font-medium drop-shadow-md">© 2024 Dinas Pariwisata. Seluruh hak cipta dilindungi undang-undang.</p>
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
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
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
