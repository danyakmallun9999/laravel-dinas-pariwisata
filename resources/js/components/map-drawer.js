
import L from 'leaflet';
import 'leaflet-draw';

// Fix Leaflet default icon paths for Vite/SPA
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

export default (config) => ({
    map: null,
    marker: null,
    geometryJson: (function () {
        if (!config.initialGeometry) return '';
        if (typeof config.initialGeometry === 'string') {
            try {
                const geom = JSON.parse(config.initialGeometry);
                return JSON.stringify(geom); // normalized
            } catch (e) {
                return '';
            }
        }
        return JSON.stringify(config.initialGeometry);
    })(),
    coordinates: { lat: config.center[0], lng: config.center[1] },
    hasGeometry: false,
    isLoading: true,

    init() {
        this.$nextTick(() => {
            // Show loading state first
            this.isLoading = true;
            setTimeout(() => {
                this.initMap();
                if (this.map) {
                    this.map.invalidateSize();
                }
                this.isLoading = false;
            }, 500); // Slight delay to simulate "fresh" reload
        });

        // Handle Livewire navigation specifically
        document.addEventListener('livewire:navigated', () => {
            if (this.map) {
                setTimeout(() => {
                    this.map.invalidateSize();
                }, 300);
            }
        }, { once: true }); // Use once so it doesn't pile up per component instance

        // Robust cleanup for SPA
        this.$cleanup(() => {
            if (this.map) {
                console.log('Cleaning up map instance...');
                this.map.remove();
                this.map = null;
            }
        });
    },

    initMap() {
        if (!this.$refs.mapContainer) return;

        // Ensure clean slate
        if (this.map) {
            this.map.remove();
            this.map = null;
        }

        // Initialize map with performance optimizations
        this.map = L.map(this.$refs.mapContainer, {
            preferCanvas: true,
            wheelPxPerZoomLevel: 120
        }).setView(config.center, config.zoom);

        // Switch to Stadia Maps Streets as requested
        const googleStreets = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth/{z}/{x}/{y}{r}.png', {
            maxZoom: 20
        }).addTo(this.map);

        // Define icons using CDN to avoid build issues
        const iconDefault = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Load existing geometry if available
        if (this.geometryJson) {
            try {
                const geo = JSON.parse(this.geometryJson);
                if (geo.type === 'Point') {
                    const lat = geo.coordinates[1];
                    const lng = geo.coordinates[0];
                    this.setMarker([lat, lng], iconDefault);
                    this.map.setView([lat, lng], 16);
                }
            } catch (e) {
                console.error('Error parsing initial geometry', e);
            }
        } else {
            // Default to point mode even without geometry
        }

        // Always activate "Point" mode by default
        this.startDrawing('point');
    },

    startDrawing(type) {
        // We only support 'point' now
        if (type !== 'point') return;

        // Change cursor to indicate placement mode
        this.$refs.mapContainer.style.cursor = 'crosshair';

        // Remove any existing click listeners to prevent duplicates
        this.map.off('click');

        // Persistent click handler to place/move marker
        this.map.on('click', (e) => {
            if (this.$refs.mapContainer) {
                this.$refs.mapContainer.style.cursor = 'crosshair';
            }
            this.setMarker(e.latlng);
        });
    },

    setMarker(latlng, icon = null) {
        if (!icon) {
            icon = L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }

        // Strict cleanup: Remove ALL markers to prevent duplicates
        this.map.eachLayer((layer) => {
            if (layer instanceof L.Marker) {
                this.map.removeLayer(layer);
            }
        });

        this.marker = L.marker(latlng, {
            icon: icon,
            draggable: true
        }).addTo(this.map);

        this.updateCoordinates(latlng);

        // Update on drag
        this.marker.on('dragend', (event) => {
            const position = event.target.getLatLng();
            this.updateCoordinates(position);
        });
    },

    updateCoordinates(latlng) {
        const lat = parseFloat(latlng.lat).toFixed(6);
        const lng = parseFloat(latlng.lng).toFixed(6);

        this.coordinates = { lat, lng };
        this.hasGeometry = true;
        this.geometryJson = JSON.stringify({
            type: 'Point',
            coordinates: [parseFloat(lng), parseFloat(lat)]
        });
    },

    clearDrawing() {
        // Strict cleanup
        this.map.eachLayer((layer) => {
            if (layer instanceof L.Marker) {
                this.map.removeLayer(layer);
            }
        });
        this.marker = null;
        this.geometryJson = '';
        this.hasGeometry = false;
        this.coordinates = { lat: '', lng: '' };
    },

    // Kept for compatibility if view calls it, but disabled logic
    editDrawing() {
        // no-op
    },

    refreshMap() {
        this.isLoading = true;

        // Beri jeda sedikit agar indikator loading muncul
        setTimeout(() => {
            // initMap sudah memiliki logika map.remove() untuk menghancurkan peta yang macet
            this.initMap();

            // Beri waktu ekstra bagi kanvas baru untuk merender sebelum memastikan ukurannya
            setTimeout(() => {
                if (this.map) {
                    this.map.invalidateSize();
                }
                this.isLoading = false;
            }, 400);
        }, 100);
    }
});
