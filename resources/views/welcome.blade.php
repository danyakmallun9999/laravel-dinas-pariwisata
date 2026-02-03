<!DOCTYPE html>
<html class="light scroll-smooth overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Portal Wisata - {{ config('app.name', 'Dinas Pariwisata dan Kebudayaan Jepara') }}</title>

    <!-- Leaflet & Icon -->
    <!-- Local assets handled by Vite -->

    <!-- Fonts & Icons -->


    <!-- Scripts & Styles -->
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/css/pages/welcome.css', 'resources/js/pages/welcome.js', 'resources/js/app.js'])
</head>

<body
    class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-display antialiased transition-colors duration-200 overflow-x-hidden pt-20"
    x-data="mapComponent({
        routes: {
            places: '{{ route('places.geojson') }}',
            boundaries: '{{ route('boundaries.geojson') }}',
            infrastructures: '{{ route('infrastructures.geojson') }}',
            landUses: '{{ route('land_uses.geojson') }}',
            search: '/search/places'
        },
        categories: {{ Js::from($categories) }}
    })">

    <!-- Top Navigation -->
    <!-- Top Navigation -->
    @include('layouts.partials.navbar')

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
                        zoom: isMobile ? 10 : 10.0,
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
    <div class="w-full bg-background-light dark:bg-background-dark py-6 border-b border-surface-light dark:border-surface-dark transition-colors duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Responsive Grid: 2 cols on mobile/tablet, 4 cols on desktop -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
                <!-- Destinasi Wisata -->
                <div class="flex items-center gap-3 md:gap-4 rounded-xl p-4 bg-surface-light/50 dark:bg-white/5 hover:bg-surface-light dark:hover:bg-white/10 transition-colors duration-200 border border-transparent hover:border-primary/20 group">
                    <div class="size-10 md:size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <span class="material-symbols-outlined text-xl md:text-2xl">photo_camera</span>
                    </div>
                    <div>
                        <!-- Using $countDestinasi passed from controller -->
                        <p class="text-text-light dark:text-text-dark text-xl md:text-2xl font-bold tracking-tight leading-none mb-1">
                            {{ $countDestinasi }}+</p>
                        <p class="text-[10px] md:text-xs text-text-light/60 dark:text-text-dark/60 font-medium uppercase tracking-wider">
                            Destinasi</p>
                    </div>
                </div>

                <!-- Kuliner Khas -->
                <div class="flex items-center gap-3 md:gap-4 rounded-xl p-4 bg-surface-light/50 dark:bg-white/5 hover:bg-surface-light dark:hover:bg-white/10 transition-colors duration-200 border border-transparent hover:border-primary/20 group">
                    <div class="size-10 md:size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <span class="material-symbols-outlined text-xl md:text-2xl">restaurant_menu</span>
                    </div>
                    <div>
                        <p class="text-text-light dark:text-text-dark text-xl md:text-2xl font-bold tracking-tight leading-none mb-1">
                            {{ $countKuliner }}+</p>
                        <p class="text-[10px] md:text-xs text-text-light/60 dark:text-text-dark/60 font-medium uppercase tracking-wider">
                             Kuliner</p>
                    </div>
                </div>

                <!-- Agenda Event -->
                <div class="flex items-center gap-3 md:gap-4 rounded-xl p-4 bg-surface-light/50 dark:bg-white/5 hover:bg-surface-light dark:hover:bg-white/10 transition-colors duration-200 border border-transparent hover:border-primary/20 group">
                    <div class="size-10 md:size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <span class="material-symbols-outlined text-xl md:text-2xl">event_available</span>
                    </div>
                    <div>
                        <p class="text-text-light dark:text-text-dark text-xl md:text-2xl font-bold tracking-tight leading-none mb-1">
                            {{ $countEvent }}</p>
                        <p class="text-[10px] md:text-xs text-text-light/60 dark:text-text-dark/60 font-medium uppercase tracking-wider">
                            Event</p>
                    </div>
                </div>

                <!-- Desa Wisata -->
                <div class="flex items-center gap-3 md:gap-4 rounded-xl p-4 bg-surface-light/50 dark:bg-white/5 hover:bg-surface-light dark:hover:bg-white/10 transition-colors duration-200 border border-transparent hover:border-primary/20 group">
                    <div class="size-10 md:size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-300 shrink-0">
                        <span class="material-symbols-outlined text-xl md:text-2xl">holiday_village</span>
                    </div>
                    <div>
                        <p class="text-text-light dark:text-text-dark text-xl md:text-2xl font-bold tracking-tight leading-none mb-1">
                            {{ $countDesa }}</p>
                        <p class="text-[10px] md:text-xs text-text-light/60 dark:text-text-dark/60 font-medium uppercase tracking-wider">
                            Desa Wisata</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

 

    <!-- Profile Section (Redesigned - Hyper Minimalist) -->
    <div class="w-full bg-white dark:bg-gray-950 py-24 lg:py-32 relative" id="profile">
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-32 items-start">
                
                <!-- Left Column: Pure Content -->
                <div class="order-2 lg:order-1 pt-8" 
                     x-data="{ shown: false }" 
                     x-intersect.threshold.0.3="shown = true">
                    
                    <div class="space-y-10 opacity-0 translate-y-8 transition-all duration-1000 ease-out"
                         :class="shown ? 'opacity-100 translate-y-0' : ''">
                        
                        <!-- Minimal Label -->
                        <span class="block text-xs font-bold uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500">Profil Wilayah</span>

                        <!-- Typography -->
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-serif text-gray-900 dark:text-white leading-[1.1]">
                            Mutiara <br>
                            <span class="italic text-gray-500 font-light">Semenanjung.</span>
                        </h2>

                        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed font-light max-w-md">
                            Kabupaten Jepara, permata di ujung utara Jawa Tengah. Garis pantai membentang 83 km, menyatukan budaya ukir kelas dunia dengan keindahan alam tropis.
                        </p>

                        <!-- Key Highlights (Pillars) -->
                        <div class="pt-8 mt-8 border-t border-gray-100 dark:border-gray-800">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Alam</span>
                                    <p class="font-serif text-lg text-gray-900 dark:text-white leading-tight">Surga <br> Tropis</p>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Sejarah</span>
                                    <p class="font-serif text-lg text-gray-900 dark:text-white leading-tight">Bumi <br> Kartini</p>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Seni</span>
                                    <p class="font-serif text-lg text-gray-900 dark:text-white leading-tight">Ukir <br> Dunia</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Column: Clean Visuals -->
                <div class="relative order-1 lg:order-2" 
                     x-data="{ hover: false }" 
                     @mouseenter="hover = true" 
                     @mouseleave="hover = false">
                    
                    <!-- Main Image (Clean Crop) -->
                    <div class="relative w-full aspect-[3/4] overflow-hidden bg-gray-100 dark:bg-gray-900">
                        <img src="{{ asset('images/section-2.jpg') }}" 
                             alt="Landscape Jepara" 
                             class="w-full h-full object-cover grayscale transition-all duration-1000 ease-out"
                             :class="hover ? 'grayscale-0 scale-105' : 'grayscale'">
                    </div>

                    <!-- Secondary Image (Smaller, Clean Overlay) -->
                    <div class="absolute bottom-8 -left-12 w-48 lg:w-64 aspect-square overflow-hidden shadow-2xl border-8 border-white dark:border-gray-950 transition-transform duration-700 ease-out hidden md:block"
                         :class="hover ? 'translate-x-4 -translate-y-4' : ''">
                        <img src="{{ asset('images/diving-karimunjawa.jpg') }}" 
                             alt="Diving" 
                             class="w-full h-full object-cover">
                    </div>

                    <!-- Minimal Stats (Absolute, no glassmorphism, just solid) -->
                    <div class="absolute top-8 -right-4 bg-white dark:bg-gray-800 p-6 shadow-xl hidden lg:block">
                        <span class="block text-4xl font-serif text-gray-900 dark:text-white">{{ $countDestinasi }}</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Destinasi</span>
                    </div>

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
                    SEJARAH & LEGENDA
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
                    BUDAYA JEPARA
                </h2>
                <p class="text-text-light/70 dark:text-text-dark/70 max-w-2xl mx-auto text-lg opacity-0 translate-y-4 transition-all duration-700 delay-200"
                   :class="shown ? 'opacity-100 translate-y-0' : ''">
                    Ragam festival dan upacara adat yang lestari, menjadi identitas dan kebanggaan masyarakat Bumi Kartini.
                </p>
            </div>

            <!-- Horizontal Accordion -->
            <div class="flex flex-col md:flex-row h-[700px] md:h-[600px] w-full gap-4">
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
                    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12 transition-all duration-700 transform"
                         :class="active === {{ $index }} ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                        
                        <div class="flex flex-col items-start max-w-2xl">
                            <span class="inline-block px-3 py-1 rounded-full bg-primary/90 text-white text-[10px] md:text-xs font-bold mb-3 md:mb-4 backdrop-blur-sm shadow-lg">
                                {{ $culture->location }}
                            </span>
                            <h3 class="text-2xl md:text-5xl font-bold text-white mb-2 md:mb-4 leading-tight drop-shadow-md">
                                {{ $culture->name }}
                            </h3>
                            <p class="text-gray-200 text-sm md:text-xl line-clamp-3 mb-4 md:mb-6 leading-relaxed">
                                {{ $culture->description }}
                            </p>
                            <div class="flex items-center gap-4 text-xs md:text-sm font-medium text-blue-300">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base md:text-lg">calendar_month</span>
                                    {{ $culture->highlight }}
                                </span>
                            </div>
                            
                            <!-- Link to Detail Page -->
                            <a href="{{ route('culture.show', $culture->slug) }}" class="mt-4 inline-flex items-center gap-2 px-6 py-2 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-white text-sm font-bold transition-all shadow-lg hover:translate-x-1 pointer-events-auto z-10">
                                <span>Selengkapnya</span>
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
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
                        WISATA UNGGULAN JEPARA
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
                        class="size-10 rounded-full border border-surface-light dark:border-white/10 flex items-center justify-center hover:bg-surface-light dark:hover:bg-white/5 text-text-light dark:text-text-dark transition-colors">
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
                        KULINER KHAS JEPARA
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
                        class="size-10 rounded-full border border-surface-light dark:border-white/10 flex items-center justify-center hover:bg-surface-light dark:hover:bg-white/5 text-text-light dark:text-text-dark transition-colors">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>
            </div>

            <!-- Food Carousel -->
            <div class="relative w-full group">
                <!-- Carousel Container -->
                <div class="flex gap-4 lg:gap-8 overflow-x-auto pb-12 pt-4 px-4 lg:px-0 snap-x snap-mandatory scrollbar-hide" 
                     x-ref="foodContainer">
                
                    @foreach($culinaries as $index => $culinary)
                    <!-- Culinary Item -->
                    <div class="min-w-[95%] sm:min-w-[85%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        <img src="{{ asset($culinary->image) }}" 
                             alt="{{ $culinary->name }}" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        <!-- Inactive Background Fade -->
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        <!-- Text Readability Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        <!-- Content -->
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">{{ $culinary->name }}</h3>
                            <p class="text-white/95 text-sm lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg">{{ $culinary->description }}</p>
                            
                            <!-- Read More Button (Visible only when snapped/active) -->
                            <div class="opacity-0 group-data-[snapped=true]:opacity-100 transition-opacity duration-500 delay-100">
                                <a href="{{ route('culinary.show', $culinary->slug) }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-white text-sm font-bold transition-all shadow-lg hover:translate-x-1">
                                    <span>Selengkapnya</span>
                                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
                
                <!-- Dots Indicator -->
                <div class="flex justify-center gap-2 mt-8">
                    @foreach($culinaries as $index => $culinary)
                    <button 
                        @click="scrollToIndex({{ $index }})"
                        :class="currentIndex === {{ $index }} ? 'w-8 bg-primary' : 'w-2 bg-gray-300 dark:bg-gray-600 hover:bg-primary/50'"
                        class="h-2 rounded-full transition-all duration-300"
                        aria-label="Go to slide {{ $index + 1 }}">
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Berita & Pengumuman -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-10 lg:py-16 scroll-mt-20 border-t border-surface-light dark:border-surface-dark transition-colors duration-200"
        id="news">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-0 mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-text-light dark:text-text-dark">BERITA & PENGUMUMAN</h2>
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
    <!-- Footer -->
    @include('layouts.partials.footer')

    <!-- JS Logic from Old File -->
    <!-- JS Logic from Old File -->
    <!-- JS Logic from Old File -->
</body>

</html>
