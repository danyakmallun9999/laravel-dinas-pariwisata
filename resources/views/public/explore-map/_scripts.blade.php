{{-- Alpine.js Map Component Script --}}
<script>
    function mapComponent() {
        return {
            // Core State
            map: null,
            loading: true,
            sidebarOpen: true,
            currentBaseLayer: 'satellite',
            baseLayers: {},
            
            // Categories & Filters
            categories: @json($categories),
            selectedCategories: [],
            showBoundaries: true,
            sortByDistance: false,

            // Places Data
            allPlaces: [],
            geoFeatures: [],
            searchQuery: '',
            searchResults: [],
            selectedFeature: null,
            markers: [],
            markerClusterGroup: null,
            boundariesLayer: null,
            boundariesFeatures: [],
            routingControl: null,
            
            // Proximity Alert State
            nearbyAlert: null,
            notifiedPlaces: new Set(),
            watchId: null,
            
            // Navigation Mode State
            isNavigating: false,
            hasActiveRoute: false,
            navigationDestination: null,
            heading: 0,
            wakeLock: null,
            
            // Route Info
            routeDistance: null,
            routeTime: null,

            // Map Settings
            defaultCenter: [-6.59, 110.68],
            defaultZoom: 10,
            userMarker: null,
            userLocation: null,
            
            // Mobile Bottom Sheet
            bottomSheetState: 'collapsed',
            touchStartY: 0,
            touchCurrentY: 0,

            // Computed: Visible Places
            get visiblePlaces() {
                const ids = this.selectedCategories.length > 0 ? this.selectedCategories : this.categories.map(c => c.id);
                let places = this.allPlaces.filter(p => ids.includes(p.properties.category?.id))
                     .map(p => ({
                         ...p.properties,
                         image_url: p.properties.image_url || (p.properties.image_path ? '{{ url('/') }}/' + p.properties.image_path : null),
                         category: p.properties.category,
                         latitude: p.geometry.coordinates[1],
                         longitude: p.geometry.coordinates[0],
                         distance: this.calculateDistance(p.geometry.coordinates[1], p.geometry.coordinates[0])
                     }));
                
                if (this.sortByDistance) {
                    places.sort((a, b) => (parseFloat(a.distance) || Infinity) - (parseFloat(b.distance) || Infinity));
                }
                
                return places;
            },

            // Initialize
            init() {
                this.selectedCategories = this.categories.map(c => c.id);
                this.initMap();
                this.fetchAllData();
                
                this.$watch('selectedCategories', () => this.updateMapMarkers());
                this.$watch('showBoundaries', () => this.loadBoundaries());
                
                // Device Orientation for Compass
                if (window.DeviceOrientationEvent) {
                    window.addEventListener('deviceorientation', (e) => this.handleOrientation(e));
                }

                // Mobile sidebar default closed
                if (window.innerWidth < 1024) { 
                    this.sidebarOpen = false; 
                }
            },
            
            // ============================================
            // MOBILE BOTTOM SHEET METHODS
            // ============================================
            
            cycleBottomSheet() {
                const states = ['collapsed', 'half', 'full'];
                const current = states.indexOf(this.bottomSheetState);
                this.bottomSheetState = states[(current + 1) % 3];
            },
            
            handleTouchStart(e) {
                this.touchStartY = e.touches[0].clientY;
                this.touchCurrentY = e.touches[0].clientY; // Reset to avoid stale data
            },
            
            handleTouchMove(e) {
                this.touchCurrentY = e.touches[0].clientY;
            },
            
            handleTouchEnd(e) {
                const diff = this.touchStartY - this.touchCurrentY;
                const threshold = 50;
                
                if (diff > threshold) {
                    // Swipe up
                    if (this.bottomSheetState === 'collapsed') this.bottomSheetState = 'half';
                    else if (this.bottomSheetState === 'half') this.bottomSheetState = 'full';
                } else if (diff < -threshold) {
                    // Swipe down
                    if (this.bottomSheetState === 'full') this.bottomSheetState = 'half';
                    else if (this.bottomSheetState === 'half') this.bottomSheetState = 'collapsed';
                }
            },

            // ============================================
            // DETAIL PANEL DRAG
            // ============================================
            
            detailTouchStartY: 0,
            
            handleDetailTouchStart(e) {
                this.detailTouchStartY = e.touches[0].clientY;
            },
            
            handleDetailTouchEnd(e) {
                const currentY = e.changedTouches[0].clientY;
                const diff = currentY - this.detailTouchStartY;
                
                // If dragged down significantly (> 50px), close panel
                if (diff > 50) {
                    this.selectedFeature = null;
                }
            },
            
            toggleCategorySingle(id) {
                if (this.selectedCategories.length === 1 && this.selectedCategories.includes(id)) {
                    this.selectedCategories = this.categories.map(c => c.id);
                } else {
                    this.selectedCategories = [id];
                }
            },
            
            // ============================================
            // ORIENTATION & NAVIGATION
            // ============================================
            
            handleOrientation(event) {
                if (!this.isNavigating) return;
                
                let heading = event.alpha; 
                if (event.webkitCompassHeading) {
                    heading = event.webkitCompassHeading;
                }
                
                this.heading = heading;
                
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
                    this.sidebarOpen = false;
                    this.selectedFeature = null;
                    this.bottomSheetState = 'collapsed';
                    if (this.map) this.map.closePopup();
                    
                    try {
                        if ('wakeLock' in navigator) {
                            this.wakeLock = await navigator.wakeLock.request('screen');
                        }
                    } catch (err) { console.log('Wake Lock error:', err); }
                    
                    this.locateUser(null, true);
                    
                } else {
                    if (this.wakeLock) {
                        this.wakeLock.release();
                        this.wakeLock = null;
                    }
                    this.map.setZoom(15);
                    this.hasActiveRoute = false;
                    if (this.routingControl) {
                        this.map.removeControl(this.routingControl);
                        this.routingControl = null;
                    }
                    this.sidebarOpen = true; // Re-open sidebar
                }
            },

            // ============================================
            // CATEGORY & FILTER METHODS
            // ============================================

            toggleCategory(id) {
                if (this.selectedCategories.includes(id)) {
                    this.selectedCategories = this.selectedCategories.filter(c => c !== id);
                } else {
                    this.selectedCategories.push(id);
                }
            },
            
            toggleSortNearby() {
                if (!this.userLocation) {
                    this.locateUser(() => {
                        this.sortByDistance = true;
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

            // ============================================
            // MAP INITIALIZATION
            // ============================================

            initMap() {
                this.map = L.map('leaflet-map', { zoomControl: false, attributionControl: false }).setView(this.defaultCenter, this.defaultZoom);
                
                const googleStreets = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20
                });
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

            // ============================================
            // DATA FETCHING
            // ============================================

            async fetchAllData() {
                try {
                    this.loading = true;
                    const [places, boundaries] = await Promise.all([
                        fetch('{{ route('places.geojson') }}').then(r => r.json()),
                        fetch('{{ route('boundaries.geojson') }}').then(r => r.json())
                    ]);

                    this.geoFeatures = places.features || [];
                    this.allPlaces = places.features || [];
                    
                    this.boundariesFeatures = boundaries.features || [];
                    this.loadBoundaries();
                    
                    this.updateMapMarkers();

                } catch (e) {
                    console.error('Error loading data:', e);
                } finally {
                    this.loading = false;
                }
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

            // ============================================
            // MARKERS
            // ============================================

            updateMapMarkers() {
                // Remove existing markers and cluster group
                if (this.markerClusterGroup) {
                    this.map.removeLayer(this.markerClusterGroup);
                    this.markerClusterGroup = null;
                }
                this.markers.forEach(m => this.map.removeLayer(m));
                this.markers = [];
                
                const visible = this.visiblePlaces;
                const useCluster = typeof L.markerClusterGroup === 'function';
                
                // Create cluster group if available
                if (useCluster) {
                    this.markerClusterGroup = L.markerClusterGroup({
                        maxClusterRadius: 50,
                        spiderfyOnMaxZoom: true,
                        showCoverageOnHover: false,
                        zoomToBoundsOnClick: true,
                        iconCreateFunction: (cluster) => {
                            const count = cluster.getChildCount();
                            let size = 'small';
                            let color = '#0ea5e9'; // sky-500
                            
                            if (count >= 50) {
                                size = 'large';
                                color = '#10b981'; // emerald-500
                            } else if (count >= 10) {
                                size = 'medium';
                                color = '#06b6d4'; // cyan-500
                            }
                            
                            return L.divIcon({
                                html: `<div class="cluster-icon cluster-${size}" style="background-color: ${color};">
                                           <span>${count}</span>
                                       </div>`,
                                className: 'custom-cluster-icon',
                                iconSize: L.point(40, 40)
                            });
                        }
                    });
                }
                
                visible.forEach(p => {
                     const color = p.category?.color || '#0ea5e9';
                     const iconHtml = `
                        <div class="marker-container">
                            <div class="marker-pulse" style="background-color: ${color};"></div>
                            <div class="marker-icon" style="background-color: ${color};">
                                <i class="${p.category?.icon_class ?? 'fa-solid fa-map-marker-alt'}"></i>
                            </div>
                            <div class="marker-pointer" style="border-top-color: ${color};"></div>
                        </div>
                    `;
                    const marker = L.marker([p.latitude, p.longitude], {
                         icon: L.divIcon({ html: iconHtml, className: '', iconSize: [44, 52], iconAnchor: [22, 52] })
                    });
                    marker.on('click', () => { this.selectPlace(p); });
                    
                    if (useCluster) {
                        this.markerClusterGroup.addLayer(marker);
                    } else {
                        marker.addTo(this.map);
                    }
                    this.markers.push(marker);
                });
                
                if (useCluster) {
                    this.map.addLayer(this.markerClusterGroup);
                }
            },

            // ============================================
            // SEARCH
            // ============================================

            performSearch() {
                if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                const q = this.searchQuery.toLowerCase();
                this.searchResults = this.allPlaces.filter(p => p.properties.name.toLowerCase().includes(q))
                    .map(p => ({ 
                        ...p.properties, 
                        image_url: p.properties.image_url,
                        type: 'Lokasi', 
                        category: p.properties.category,
                        latitude: p.geometry.coordinates[1], 
                        longitude: p.geometry.coordinates[0] 
                    }))
                    .slice(0, 5);
            },

            // ============================================
            // SELECTION
            // ============================================

            selectFeature(feat) {
                this.selectedFeature = {
                    ...feat,
                    image_url: feat.image_url || (feat.image_path ? '{{ url('/') }}/' + feat.image_path : null)
                };
                this.zoomToFeature(feat);
                this.bottomSheetState = 'collapsed';
            },

            selectPlace(place) {
                 this.selectedFeature = {
                    ...place,
                    type: place.category?.name || 'Lokasi',
                    image_url: place.image_url || (place.image_path ? '{{ url('/') }}/' + place.image_path : null)
                };
                this.zoomToFeature(place);
                this.bottomSheetState = 'collapsed';
            },

            zoomToFeature(feature) {
                if (feature.latitude && feature.longitude) {
                    this.map.flyTo([feature.latitude, feature.longitude], 16, { duration: 0.5 });
                } else if (feature.geometry) {
                     const layer = L.geoJSON(feature);
                     this.map.fitBounds(layer.getBounds(), { padding: [100, 100] });
                }
            },
            
            // ============================================
            // ROUTING
            // ============================================
            
            startRouting(destination) {
                this.navigationDestination = destination;
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
                    // NOTE: This uses the default OSRM demo server which may show usage warnings.
                    // For production, set up your own OSRM server or use a paid service (e.g. Mapbox)
                    // and configure the 'serviceUrl' option here.
                    // serviceUrl: 'https://YOUR_OSRM_SERVER/route/v1',
                    routeWhileDragging: false,
                    lineOptions: {
                        styles: [{color: '#0ea5e9', opacity: 0.8, weight: 6}]
                    },
                    show: true,
                    addWaypoints: false,
                    draggableWaypoints: false,
                    fitSelectedRoutes: false,
                    createMarker: function() { return null; },
                    containerClassName: 'routing-container custom-scrollbar'
                }).addTo(this.map);
                
                this.routingControl.on('routesfound', (e) => {
                    const routes = e.routes;
                    const route = routes[0];
                    
                    // Extract distance (in km) and time (in seconds)
                    this.routeDistance = (route.summary.totalDistance / 1000).toFixed(1);
                    this.routeTime = Math.round(route.summary.totalTime / 60); // Convert to minutes
                    
                    const bounds = L.latLngBounds(route.coordinates);
                    this.map.fitBounds(bounds, { 
                        paddingTopLeft: [20, 100], 
                        paddingBottomRight: [20, 300],
                        animate: true
                    });
                });
                
                setTimeout(() => {
                    const container = this.routingControl.getContainer();
                    if (container) container.style.display = 'none';
                    this.hasActiveRoute = true;
                }, 100);
            },

            openGoogleMaps(destination) {
                const url = `https://www.google.com/maps/dir/?api=1&destination=${destination.latitude},${destination.longitude}&travelmode=driving`;
                window.open(url, '_blank');
            },
            
            openStreetView(place) {
                const url = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${place.latitude},${place.longitude}`;
                window.open(url, '_blank');
            },
            
            shareToWhatsApp(place) {
                const mapsUrl = `https://www.google.com/maps?q=${place.latitude},${place.longitude}`;
                const description = place.description ? place.description.substring(0, 100) + '...' : '';
                const message = `🗺️ *${place.name}*\n📍 ${place.category?.name || 'Destinasi Wisata'}\n\n${description}\n\nLihat di peta: ${mapsUrl}`;
                const waUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
                window.open(waUrl, '_blank');
            },
            
            async copyShareLink(place) {
                const mapsUrl = `https://www.google.com/maps?q=${place.latitude},${place.longitude}`;
                const text = `${place.name} - ${mapsUrl}`;
                
                try {
                    await navigator.clipboard.writeText(text);
                    // Show toast notification
                    this.showToast('Link berhasil disalin!');
                } catch (err) {
                    // Fallback for older browsers
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    this.showToast('Link berhasil disalin!');
                }
            },
            
            showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-24 left-1/2 -translate-x-1/2 bg-slate-800 text-white px-4 py-2 rounded-full text-sm font-medium shadow-lg z-[1000] animate-fade-in';
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                    setTimeout(() => toast.remove(), 300);
                }, 2000);
            },
            
            toggleNavigationInstructions() {
                 if (!this.routingControl) return;
                 const container = this.routingControl.getContainer();
                 if (container) {
                     container.style.display = container.style.display === 'none' ? 'block' : 'none';
                 }
            },
            
            cancelRoute() {
                // Remove routing control from map
                if (this.routingControl) {
                    this.map.removeControl(this.routingControl);
                    this.routingControl = null;
                }
                
                // Reset route state
                this.hasActiveRoute = false;
                this.navigationDestination = null;
                this.routeDistance = null;
                this.routeTime = null;
                
                // Re-open sidebar on desktop
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                }
            },

            // ============================================
            // GEOLOCATION
            // ============================================

            locateUser(callback = null, forceFollow = false) {
                if (!navigator.geolocation) { alert('Browser tidak mendukung geolokasi'); return; }
                this.loading = true;
                
                if (this.watchId) navigator.geolocation.clearWatch(this.watchId);
                
                this.watchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        const { latitude, longitude } = pos.coords;
                        this.userLocation = { lat: latitude, lng: longitude };
                        
                        const compassHtml = `
                            <div class="relative w-12 h-12 flex items-center justify-center">
                                <div class="user-arrow w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-b-[16px] border-b-sky-600 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 transition-transform duration-300 origin-center" style="transform: rotate(${this.heading}deg)"></div>
                                <div class="w-5 h-5 bg-sky-500 rounded-full border-3 border-white relative z-10" style="box-shadow: 0 0 0 8px rgba(14,165,233,0.2);"></div>
                            </div>
                        `;

                        if (this.userMarker) {
                            this.userMarker.setLatLng([latitude, longitude]);
                        } else {
                            this.userMarker = L.marker([latitude, longitude], {
                                icon: L.divIcon({ html: compassHtml, className: '', iconSize: [48, 48], iconAnchor: [24, 24] })
                            }).addTo(this.map);
                        }
                        
                        if (this.isNavigating || forceFollow) {
                            this.map.flyTo([latitude, longitude], this.isNavigating ? 19 : 16, { animate: true, duration: 0.5 });
                        }
                        
                        this.loading = false;
                        if (callback) callback();
                        
                        this.checkProximity(latitude, longitude);
                    },
                    (err) => { 
                        this.loading = false; 
                        console.error('Geolocation error:', err);
                        alert('Tidak dapat mendeteksi lokasi. Pastikan GPS aktif.');
                    },
                    { enableHighAccuracy: true, maximumAge: 1000, timeout: 10000 }
                );
            },
            
            // ============================================
            // PROXIMITY ALERT
            // ============================================
            
            checkProximity(lat, lng) {
                const threshold = 0.5; // 500 meters
                
                this.allPlaces.forEach(place => {
                    if (this.notifiedPlaces.has(place.properties.name)) return;
                    
                    const dist = this.calculateDistance(place.geometry.coordinates[1], place.geometry.coordinates[0]);
                    if (dist && parseFloat(dist) <= threshold) {
                        this.nearbyAlert = {
                            ...place.properties,
                            latitude: place.geometry.coordinates[1],
                            longitude: place.geometry.coordinates[0],
                            image_url: place.properties.image_path ? '{{ url('/') }}/' + place.properties.image_path : null
                        };
                        this.notifiedPlaces.add(place.properties.name);
                        
                        if (navigator.vibrate) navigator.vibrate(200);
                    }
                });
            },
            
            // ============================================
            // UTILITY HELPERS
            // ============================================
            
            darkenColor(hex, percent) {
                // Convert hex to RGB, darken, convert back
                const num = parseInt(hex.replace('#', ''), 16);
                const r = Math.max(0, (num >> 16) - Math.round(2.55 * percent));
                const g = Math.max(0, ((num >> 8) & 0x00FF) - Math.round(2.55 * percent));
                const b = Math.max(0, (num & 0x0000FF) - Math.round(2.55 * percent));
                return '#' + (0x1000000 + r * 0x10000 + g * 0x100 + b).toString(16).slice(1);
            }
        };
    }
</script>
