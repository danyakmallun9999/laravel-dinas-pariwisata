@props([
    'drawType' => 'point', // point, line, polygon
    'initialGeometry' => null,
    'center' => [-6.7289, 110.7485],
    'zoom' => 14
])

@pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
@endPushOnce

@pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
@endPushOnce

<div 
    x-data="mapDrawer({
        drawType: '{{ $drawType }}',
        initialGeometry: @js($initialGeometry),
        center: @js($center),
        zoom: {{ $zoom }}
    })"
    x-init="init()"
    class="space-y-4"
>
    <!-- Drawing Controls -->
    <div class="flex items-center gap-2 flex-wrap">
        <button 
            type="button"
            @click="startDrawing('point')"
            :class="drawingMode === 'point' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
            class="px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-500 hover:text-white transition"
        >
            <i class="fa-solid fa-map-marker-alt mr-2"></i> Titik
        </button>
        
        @if($drawType === 'line' || $drawType === 'polygon')
            <button 
                type="button"
                @click="startDrawing('line')"
                :class="drawingMode === 'line' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-500 hover:text-white transition"
            >
                <i class="fa-solid fa-route mr-2"></i> Garis
            </button>
        @endif
        
        @if($drawType === 'polygon')
            <button 
                type="button"
                @click="startDrawing('polygon')"
                :class="drawingMode === 'polygon' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-500 hover:text-white transition"
            >
                <i class="fa-solid fa-draw-polygon mr-2"></i> Polygon
            </button>
        @endif
        
        <button 
            type="button"
            @click="clearDrawing()"
            class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition"
        >
            <i class="fa-solid fa-trash mr-2"></i> Hapus
        </button>
        
        <button 
            type="button"
            @click="editDrawing()"
            :disabled="!hasGeometry"
            :class="hasGeometry ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition"
        >
            <i class="fa-solid fa-edit mr-2"></i> Edit
        </button>
    </div>

    <!-- Map Container -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <div>
                <h4 class="text-sm font-semibold text-gray-900">Gambar di Peta</h4>
                <p class="text-xs text-gray-500">
                    @if($drawType === 'point')
                        Klik tombol "Titik" lalu klik di peta untuk menambahkan lokasi
                    @elseif($drawType === 'line')
                        Klik tombol "Garis" lalu gambar garis di peta untuk infrastruktur
                    @else
                        Klik tombol "Polygon" lalu gambar area di peta untuk batas wilayah
                    @endif
                </p>
            </div>
        </div>
        <div x-ref="mapContainer" class="w-full h-96 rounded-2xl overflow-hidden border border-gray-200"></div>
    </div>

    <!-- Geometry JSON Output (Hidden) -->
    <input type="hidden" name="geometry" x-model="geometryJson" :required="drawType !== 'point'">
    
    @if($drawType === 'point')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                <input type="text" name="latitude" x-model="coordinates.lat" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50" required readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                <input type="text" name="longitude" x-model="coordinates.lng" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50" required readonly>
            </div>
        </div>
    @endif

    <!-- Geometry Preview -->
    <div x-show="hasGeometry" x-cloak class="bg-gray-50 p-4 rounded-lg">
        <p class="text-xs font-semibold text-gray-700 mb-2">Geometry JSON:</p>
        <pre class="text-xs bg-white p-2 rounded border overflow-auto max-h-32" x-text="geometryJson || 'Belum ada geometry'"></pre>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mapDrawer', (config) => ({
            map: null,
            drawControl: null,
            drawnItems: null,
            drawingMode: null,
            geometryJson: config.initialGeometry ? JSON.stringify(config.initialGeometry) : '',
            coordinates: { lat: config.center[0], lng: config.center[1] },
            hasGeometry: false,

            init() {
                this.$nextTick(() => {
                    this.initMap();
                    if (config.initialGeometry) {
                        this.loadExistingGeometry(config.initialGeometry);
                    }
                });
            },

            initMap() {
                if (!this.$refs.mapContainer) return;

                this.map = L.map(this.$refs.mapContainer).setView(config.center, config.zoom);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(this.map);

                // Initialize feature group for drawn items
                this.drawnItems = new L.FeatureGroup();
                this.map.addLayer(this.drawnItems);

                // Initialize draw control
                this.initDrawControl();

                // Set default drawing mode
                if (config.drawType === 'point') {
                    this.startDrawing('point');
                } else if (config.drawType === 'line') {
                    this.startDrawing('line');
                } else if (config.drawType === 'polygon') {
                    this.startDrawing('polygon');
                }
            },

            initDrawControl() {
                const drawOptions = {
                    position: 'topright',
                    draw: {
                        polygon: {
                            allowIntersection: false,
                            showArea: true
                        },
                        polyline: {
                            metric: true
                        },
                        circle: false,
                        rectangle: false,
                        marker: true,
                        circlemarker: false
                    },
                    edit: {
                        featureGroup: this.drawnItems,
                        remove: true
                    }
                };

                this.drawControl = new L.Control.Draw(drawOptions);
                this.map.addControl(this.drawControl);

                // Handle draw events
                this.map.on(L.Draw.Event.CREATED, (e) => {
                    this.handleDrawCreated(e);
                });

                this.map.on(L.Draw.Event.EDITED, (e) => {
                    this.handleDrawEdited(e);
                });

                this.map.on(L.Draw.Event.DELETED, (e) => {
                    this.handleDrawDeleted(e);
                });
            },

            startDrawing(type) {
                // Remove previous click handler
                this.map.off('click', this.handleMapClick);
                
                this.drawingMode = type;

                // Enable drawing for the selected type
                if (type === 'point') {
                    this.map.on('click', this.handleMapClick.bind(this));
                } else {
                    // For line and polygon, use Leaflet Draw
                    let drawHandler;
                    if (type === 'line') {
                        drawHandler = new L.Draw.Polyline(this.map, this.drawControl.options.draw.polyline);
                    } else if (type === 'polygon') {
                        drawHandler = new L.Draw.Polygon(this.map, this.drawControl.options.draw.polygon);
                    }
                    if (drawHandler) {
                        drawHandler.enable();
                    }
                }
            },

            handleMapClick(e) {
                const { lat, lng } = e.latlng;
                this.coordinates = {
                    lat: Number(lat).toFixed(6),
                    lng: Number(lng).toFixed(6)
                };
                
                // Clear existing marker
                this.drawnItems.clearLayers();
                
                // Add new marker
                const marker = L.marker([lat, lng]);
                marker.addTo(this.drawnItems);
                
                // Update geometry
                this.geometryJson = JSON.stringify({
                    type: 'Point',
                    coordinates: [parseFloat(lng), parseFloat(lat)]
                });
                this.hasGeometry = true;
            },

            handleDrawCreated(e) {
                const layer = e.layer;
                this.drawnItems.addLayer(layer);
                this.updateGeometryFromLayer(layer);
            },

            handleDrawEdited(e) {
                const layers = e.layers;
                layers.eachLayer((layer) => {
                    this.updateGeometryFromLayer(layer);
                });
            },

            handleDrawDeleted(e) {
                this.geometryJson = '';
                this.hasGeometry = false;
                if (config.drawType === 'point') {
                    this.coordinates = { lat: config.center[0], lng: config.center[1] };
                }
            },

            updateGeometryFromLayer(layer) {
                let geometry;
                
                if (layer instanceof L.Marker) {
                    const latlng = layer.getLatLng();
                    geometry = {
                        type: 'Point',
                        coordinates: [latlng.lng, latlng.lat]
                    };
                    this.coordinates = {
                        lat: Number(latlng.lat).toFixed(6),
                        lng: Number(latlng.lng).toFixed(6)
                    };
                } else if (layer instanceof L.Polyline) {
                    const latlngs = layer.getLatLngs();
                    const coordinates = latlngs.map(ll => [ll.lng, ll.lat]);
                    geometry = {
                        type: 'LineString',
                        coordinates: coordinates
                    };
                } else if (layer instanceof L.Polygon) {
                    const latlngs = layer.getLatLngs()[0]; // First ring
                    const coordinates = latlngs.map(ll => [ll.lng, ll.lat]);
                    // Close the polygon
                    coordinates.push(coordinates[0]);
                    geometry = {
                        type: 'Polygon',
                        coordinates: [coordinates]
                    };
                }

                if (geometry) {
                    this.geometryJson = JSON.stringify(geometry);
                    this.hasGeometry = true;
                }
            },

            clearDrawing() {
                this.drawnItems.clearLayers();
                this.geometryJson = '';
                this.hasGeometry = false;
                if (config.drawType === 'point') {
                    this.coordinates = { lat: config.center[0], lng: config.center[1] };
                }
            },

            editDrawing() {
                if (!this.hasGeometry) return;
                
                // Enable edit mode
                const editControl = new L.EditToolbar.Edit(this.map, {
                    featureGroup: this.drawnItems
                });
                editControl.enable();
            },

            loadExistingGeometry(geometry) {
                let layer;
                
                if (geometry.type === 'Point') {
                    const [lng, lat] = geometry.coordinates;
                    layer = L.marker([lat, lng]);
                    this.coordinates = {
                        lat: Number(lat).toFixed(6),
                        lng: Number(lng).toFixed(6)
                    };
                } else if (geometry.type === 'LineString') {
                    const latlngs = geometry.coordinates.map(coord => [coord[1], coord[0]]);
                    layer = L.polyline(latlngs, { color: '#3388ff' });
                } else if (geometry.type === 'Polygon') {
                    const latlngs = geometry.coordinates[0].map(coord => [coord[1], coord[0]]);
                    layer = L.polygon(latlngs, { color: '#3388ff', fillOpacity: 0.2 });
                }

                if (layer) {
                    layer.addTo(this.drawnItems);
                    this.map.fitBounds(layer.getBounds());
                    this.hasGeometry = true;
                }
            }
        }));
    });
</script>

