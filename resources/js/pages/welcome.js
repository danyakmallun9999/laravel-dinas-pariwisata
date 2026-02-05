import L from 'leaflet'; // Ensure Leaflet is imported if using module system, or rely on global L

window.mapComponent = function (config = {}) {
    return {
        map: null,
        loading: true,

        // Toggles
        showBoundaries: true,
        showInfrastructures: true,
        showLandUses: true,

        // Data
        categories: config.categories || [],
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

        // API Endpoints
        endpoints: {
            places: config.routes?.places || '/geojson/places',
            boundaries: config.routes?.boundaries || '/geojson/boundaries',
            infrastructures: config.routes?.infrastructures || '/geojson/infrastructures',
            landUses: config.routes?.landUses || '/geojson/land-uses',
            search: config.routes?.search || '/search/places'
        },

        init() {
            this.selectedCategories = this.categories.map(c => c.id);
            this.$nextTick(() => {
                this.initMap();
                this.fetchAllData();
            });

            this.$watch('selectedCategories', () => this.updateMapMarkers());
        },

        initMap() {
            // Check if L (Leaflet) exists
            if (typeof L === 'undefined') {
                console.error('Leaflet not loaded');
                return;
            }

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
                const placesUrl = this.endpoints.places;
                const placesResponse = await fetch(placesUrl);

                if (!placesResponse.ok) {
                    throw new Error(`HTTP Error ${placesResponse.status}`);
                }

                const placesData = await placesResponse.json();

                // Validation
                if (!placesData.features) {
                    console.error('Invalid GeoJSON format:', placesData);
                }

                this.geoFeatures = placesData.features || [];
                this.allPlaces = placesData.features || [];

                if (this.allPlaces.length === 0) {
                    console.warn('Warning: No places received from server.');
                }

                this.updateMapMarkers();

            } catch (e) {
                console.error('PLACES FETCH ERROR:', e);
                // alert('Gagal memuat data destinasi: ' + e.message); // Silent fail is better for UX sometimes
            }

            // Background load for other layers (non-critical)
            try {
                const [boundaries, infrastructures, landUses] = await Promise.all([
                    fetch(this.endpoints.boundaries).then(r => r.json()),
                    fetch(this.endpoints.infrastructures).then(r => r.json()),
                    fetch(this.endpoints.landUses).then(r => r.json())
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
            const q = this.searchQuery;

            if (!q) {
                this.searchResults = [];
                return;
            }

            // Server-side Search
            fetch(`${this.endpoints.search}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    this.searchResults = data;
                })
                .catch(err => {
                    console.error('Search Error:', err);
                });
        },

        selectFeature(result) {
            // Priority: URL redirection (for Places, Posts, Events)
            if (result.url) {
                window.location.href = result.url;
                return;
            }

            // Fallback for map features (Boundaries, Infrastructure, etc.)
            this.selectedFeature = result;
            this.zoomToFeature(result);
            this.searchResults = [];
        },

        getIconClass(type) {
            switch (type) {
                case 'Destinasi':
                    return 'fa-solid fa-location-dot';
                case 'Berita':
                    return 'fa-solid fa-newspaper';
                case 'Agenda':
                    return 'fa-solid fa-calendar-alt';
                default:
                    return 'fa-solid fa-search';
            }
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
