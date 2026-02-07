    <div class="relative w-full h-[calc(100dvh-6rem)] pt-8 pb-4 md:pb-6 flex flex-col items-center justify-center bg-white dark:bg-slate-950 overflow-hidden">
        <div class="relative w-full h-full mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="relative w-full h-full rounded-[2.5rem] overflow-hidden ring-1 ring-slate-900/5 dark:ring-white/10 group bg-slate-900">
                <!-- 3D Map Container -->
                <div id="hero-map" class="absolute inset-0 z-0 opacity-0 transition-opacity duration-[2000ms]"></div>

                <!-- Overlay Gradient for Readability -->
                <div class="absolute inset-0 z-10 bg-gradient-to-t from-slate-900 via-slate-900/40 to-slate-900/20 pointer-events-none"></div>

                <!-- Content -->
                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center px-4 text-center pointer-events-none">
                    <div class="w-full max-w-4xl mx-auto space-y-8 pointer-events-auto flex flex-col items-center">
                        <span class="inline-block px-5 py-2 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-white text-xs font-bold uppercase tracking-widest animate-fade-in-down shadow-lg hover:bg-white/20 transition-colors cursor-default">
                            {{ __('Hero.Badge') }}
                        </span>
                        
                        <h1 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight tracking-tight drop-shadow-2xl animate-fade-in-up selection:bg-primary/30">
                            {!! __('Hero.Title') !!}
                        </h1>
                        
                        <p class="text-slate-200 text-base sm:text-lg md:text-xl font-medium max-w-2xl mx-auto leading-relaxed drop-shadow-lg shadow-black/50 animate-fade-in-up delay-100">
                            {{ __('Hero.Subtitle') }}
                        </p>
                        
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-6 animate-fade-in-up delay-200">
                            <a class="group relative flex items-center justify-center h-14 px-8 rounded-full bg-primary text-white text-base font-bold overflow-hidden transition-all hover:-translate-y-1 shadow-xl shadow-primary/20 hover:shadow-2xl hover:shadow-primary/40"
                                href="{{ route('places.index') }}">
                                <span class="relative z-10">{{ __('Hero.Button') }}</span>
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof maplibregl === 'undefined') {
                console.error('MapLibre GL JS not loaded');
                return;
            }

            const mapContainer = document.getElementById('hero-map');
            const map = new maplibregl.Map({
                container: 'hero-map',
                style: {
                    version: 8,
                    sources: {
                        'satellite': {
                            type: 'raster',
                            tiles: ['https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'],
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
                center: [110.678, -6.589],
                zoom: 9.5, 
                minZoom: 9,
                maxZoom: 12,
                maxBounds: [[110.0, -7.0], [111.3, -5.9]],
                renderWorldCopies: false,
                pitch: 0,
                attributionControl: false,
                interactive: false
            });

            map.on('load', () => {
                mapContainer.classList.remove('opacity-0');
                mapContainer.classList.add('opacity-80');

                map.addSource('boundaries', { type: 'geojson', data: '/boundaries.geojson' });

                map.addLayer({
                    'id': 'boundary-extrusion',
                    'type': 'fill-extrusion',
                    'source': 'boundaries',
                    'paint': {
                        'fill-extrusion-color': '#fbbf24',
                        'fill-extrusion-height': 20,
                        'fill-extrusion-base': 0,
                        'fill-extrusion-opacity': 0.3
                    }
                });

                map.addLayer({
                    'id': 'boundary-line',
                    'type': 'line',
                    'source': 'boundaries',
                    'layout': { 'line-join': 'round', 'line-cap': 'round' },
                    'paint': {
                        'line-color': '#ffffff',
                        'line-width': 3,
                        'line-opacity': 0.8
                    }
                });

                // Fly animation
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
                }, 2000);

                // Rotation Loop with Intersection Observer
                let startTime;
                let requestID;
                const rotationsPerMinute = 0.5;

                function rotateCamera(timestamp) {
                    if (!startTime) startTime = timestamp;
                    const progress = timestamp - startTime;
                    const bearing = (progress / (60000 / rotationsPerMinute)) * 360;
                    
                    map.rotateTo(bearing % 360, { duration: 0 });
                    requestID = requestAnimationFrame(rotateCamera);
                }

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            if (!requestID) requestID = requestAnimationFrame(rotateCamera);
                        } else {
                            if (requestID) {
                                cancelAnimationFrame(requestID);
                                requestID = null;
                            }
                        }
                    });
                });

                observer.observe(mapContainer);
                
                // Start rotation initially after move ends
                map.once('moveend', () => {
                    // Check visibility before starting
                    if (mapContainer.offsetParent !== null) {
                         requestID = requestAnimationFrame(rotateCamera);
                    }
                });
            });
        });
    </script>
    <!-- END SECTION: Hero -->
