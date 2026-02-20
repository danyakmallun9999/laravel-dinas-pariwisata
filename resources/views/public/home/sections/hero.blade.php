    @php
        $locale = app()->getLocale();
        $badge = $locale === 'en' ? $heroSetting->badge_en : $heroSetting->badge_id;
        $title = $locale === 'en' ? $heroSetting->title_en : $heroSetting->title_id;
        $subtitle = $locale === 'en' ? $heroSetting->subtitle_en : $heroSetting->subtitle_id;
        $btnText = $locale === 'en' ? $heroSetting->button_text_en : $heroSetting->button_text_id;
        $btnLink = $heroSetting->button_link;
    @endphp

    <div class="relative w-full h-[calc(100dvh-6rem)] pt-8 pb-4 md:pb-6 flex flex-col items-center justify-center bg-white dark:bg-slate-950 overflow-hidden">
        <div class="relative w-full h-full mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="relative w-full h-full rounded-[2.5rem] overflow-hidden ring-1 ring-slate-900/5 dark:ring-white/10 group bg-slate-900">
                
                @if($heroSetting->type === 'map' || empty($heroSetting->type))
                    <!-- 3D Map Container -->
                    <div id="hero-map" class="absolute inset-0 z-0 opacity-0 transition-opacity duration-700"></div>
                @elseif($heroSetting->type === 'video' && !empty($heroSetting->media_paths))
                    <!-- Video Background -->
                    <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover z-0">
                        <source src="{{ Storage::url($heroSetting->media_paths[0]) }}" type="video/mp4">
                    </video>
                @elseif($heroSetting->type === 'image' && !empty($heroSetting->media_paths))
                    @if(count($heroSetting->media_paths) === 1)
                        <!-- Single Image Background -->
                        <img src="{{ Storage::url(is_array($heroSetting->media_paths) ? array_values($heroSetting->media_paths)[0] : $heroSetting->media_paths[0]) }}" class="absolute inset-0 w-full h-full object-cover z-0" alt="Hero Background">
                    @else
                        <!-- Image Carousel Background -->
                        <div x-data="{ 
                                slides: {{ json_encode(array_values(array_map(fn($path) => Storage::url($path), $heroSetting->media_paths))) }},
                                currentSlide: 0,
                                init() {
                                    if (this.slides.length > 1) {
                                        setInterval(() => {
                                            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                                        }, 5000); 
                                    }
                                }
                            }" 
                            class="absolute inset-0 z-0 bg-slate-900">
                            
                            <!-- Static Base Image to prevent FOUC on load -->
                            <img src="{{ Storage::url(array_values($heroSetting->media_paths)[0]) }}" class="absolute inset-0 w-full h-full object-cover" alt="Hero Background Base">

                            <template x-for="(slide, index) in slides" :key="index">
                                <div x-show="currentSlide === index" 
                                     x-transition:enter="transition-opacity ease-in-out duration-1000"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition-opacity ease-in-out duration-1000"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0 w-full h-full bg-slate-900"
                                     x-cloak>
                                     <img :src="slide" class="w-full h-full object-cover" alt="Hero Background Slide">
                                </div>
                            </template>
                        </div>
                    @endif
                @endif

                <!-- Overlay Gradient for Readability (Only if texts exist or map helps to darken it slightly) -->
                @if($badge || $title || $subtitle || $heroSetting->type === 'map')
                    <div class="absolute inset-0 z-10 bg-gradient-to-t from-slate-900 via-slate-900/40 to-slate-900/20 pointer-events-none"></div>
                @endif

                @if($badge || $title || $subtitle || $btnText)
                <!-- Content -->
                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center px-4 text-center pointer-events-none">
                    <div class="w-full max-w-4xl mx-auto space-y-8 pointer-events-auto flex flex-col items-center">
                        @if($badge)
                        <span class="hero-badge inline-block px-5 py-2 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-white text-xs font-bold uppercase tracking-widest opacity-0 transform translate-y-4 shadow-lg hover:bg-white/20 transition-colors cursor-default">
                            {{ $badge }}
                        </span>
                        @endif
                        
                        @if($title)
                        <h1 class="hero-title text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black leading-tight tracking-tight drop-shadow-2xl opacity-0 transform translate-y-8 selection:bg-primary/30">
                            {!! $title !!}
                        </h1>
                        @endif
                        
                        @if($subtitle)
                        <p class="hero-subtitle text-slate-200 text-base sm:text-lg md:text-xl font-medium max-w-2xl mx-auto leading-relaxed drop-shadow-lg shadow-black/50 opacity-0 transform translate-y-8">
                            {{ $subtitle }}
                        </p>
                        @endif
                        
                        @if($btnText && $btnLink)
                        <div class="hero-buttons flex flex-col sm:flex-row items-center justify-center gap-4 pt-6 opacity-0 transform translate-y-8">
                            <a class="group relative flex items-center justify-center h-14 px-8 rounded-full bg-primary text-white text-base font-bold overflow-hidden transition-all hover:-translate-y-1 shadow-xl shadow-primary/20 hover:shadow-2xl hover:shadow-primary/40"
                                href="{{ url($btnLink) }}"
                                wire:navigate>
                                <span class="relative z-10">{{ $btnText }}</span>
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <script>
        (function() {
            const initHero = () => {
                // GSAP Hero Animation (Conditional based on Elements)
                const tl = gsap.timeline({ defaults: { ease: "power3.out" } });
                if(document.querySelector(".hero-badge")) tl.to(".hero-badge", { y: 0, opacity: 1, duration: 1, delay: 0.5 });
                if(document.querySelector(".hero-title")) tl.to(".hero-title", { y: 0, opacity: 1, duration: 1 }, "-=0.6");
                if(document.querySelector(".hero-subtitle")) tl.to(".hero-subtitle", { y: 0, opacity: 1, duration: 1 }, "-=0.8");
                if(document.querySelector(".hero-buttons")) tl.to(".hero-buttons", { y: 0, opacity: 1, duration: 1 }, "-=0.8");

                // Map setup (Only proceed if Map is chosen)
                const mapContainer = document.getElementById('hero-map');
                if (!mapContainer) return;

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
                    center: [110.68, -6.45], // Shifted center north for better balance
                    zoom: 10, 
                    minZoom: 8,
                    maxZoom: 12,
                    maxBounds: [[109.8, -7.2], [111.5, -5.4]],
                    renderWorldCopies: false,
                    pitch: 45,
                    attributionControl: false,
                    interactive: false
                });

                map.on('load', () => {
                    // Map fade in using GSAP for consistency
                    gsap.to(mapContainer, { opacity: 1, duration: 0.7, ease: "power2.out" });

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
                            center: [110.68, -6.45], // Keeping the northern focal point
                            zoom: isMobile ? 10.2 : 10.0, // Zoomed in more
                            pitch: isMobile ? 65 : 75,
                            bearing: 0,
                            speed: 0.5,
                            curve: 1.2,
                            essential: true
                        });
                    }, 500);

                    // Rotation Loop
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

                    ScrollTrigger.create({
                        trigger: mapContainer,
                        start: "top bottom",
                        end: "bottom top",
                        onEnter: () => {
                            if (!requestID) requestID = requestAnimationFrame(rotateCamera);
                        },
                        onLeave: () => {
                            if (requestID) { cancelAnimationFrame(requestID); requestID = null; }
                        },
                        onEnterBack: () => {
                            if (!requestID) requestID = requestAnimationFrame(rotateCamera);
                        },
                        onLeaveBack: () => {
                            if (requestID) { cancelAnimationFrame(requestID); requestID = null; }
                        }
                    });
                });
            };

            document.addEventListener('DOMContentLoaded', initHero);
            document.addEventListener('livewire:navigated', initHero);
        })();
    </script>
    <!-- END SECTION: Hero -->
