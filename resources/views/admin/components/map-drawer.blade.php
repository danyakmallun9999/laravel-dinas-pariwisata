@props([
    'drawType' => 'point', // point, line, polygon
    'initialGeometry' => null,
    'center' => [-6.7289, 110.7485],
    'zoom' => 14,
    'height' => 'h-96'
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
    class="flex flex-col {{ $height }}"
>
    <!-- Top Section: Drawing Controls & Info -->
    <div class="flex-none space-y-3 mb-3">
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
    
            <div class="border-l border-gray-300 mx-2 h-8"></div>
    
            <input type="file" x-ref="geojsonInput" accept=".geojson,.json" class="hidden" @change="handleFileUpload($event)">
            <button 
                type="button"
                @click="$refs.geojsonInput.click()"
                class="px-4 py-2 rounded-lg text-sm font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition"
            >
                <i class="fa-solid fa-file-import mr-2"></i> Upload GeoJSON
            </button>
        </div>

        <!-- Info Text -->
        <div class="flex items-center justify-between">
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
    </div>

    <!-- Map Container (Flex-1 to fill space) -->
    <div class="flex-1 relative min-h-[300px] rounded-2xl overflow-hidden border border-gray-200">
        <div x-ref="mapContainer" class="absolute inset-0 w-full h-full bg-gray-100"></div>
    </div>

    <!-- Bottom Section: Nav, Inputs, Preview -->
    <div class="flex-none mt-3 space-y-3">
        <!-- Geometry JSON Output (Hidden) -->
        <input type="hidden" name="geometry" x-model="geometryJson" :required="drawType !== 'point'">
        
        <!-- Multiple Feature Navigation -->
        <div x-show="uploadedFeatures.length > 1" x-cloak class="bg-blue-50 p-3 rounded-lg flex items-center justify-between border border-blue-100">
            <div class="text-sm text-blue-800 font-medium">
                Fitur <span x-text="currentIndex + 1"></span> dari <span x-text="uploadedFeatures.length"></span>
            </div>
            <div class="flex gap-2">
                <button type="button" @click="prevFeature()" class="px-3 py-1 bg-white border border-blue-200 rounded text-blue-700 hover:bg-blue-50 text-xs font-semibold disabled:opacity-50" :disabled="currentIndex === 0">
                    <i class="fa-solid fa-chevron-left mr-1"></i> Prev
                </button>
                <button type="button" @click="nextFeature()" class="px-3 py-1 bg-white border border-blue-200 rounded text-blue-700 hover:bg-blue-50 text-xs font-semibold disabled:opacity-50" :disabled="currentIndex === uploadedFeatures.length - 1">
                    Next <i class="fa-solid fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>
        
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
            uploadedItems: null,
            uploadedFeatures: [],
            uploadedFeatures: [],
            currentIndex: 0,
            currentDrawHandler: null, // Track active draw handler

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

                const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps'
                }).addTo(this.map);

                const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps'
                });

                const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps'
                });

                const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '&copy; Google Maps'
                });

                // Base Layers Control
                const baseLayers = {
                    "Google Streets": googleStreets,
                    "Google Hybrid": googleHybrid,
                    "Google Satellite": googleSatellite,
                    "Google Terrain": googleTerrain
                };

                // Initialize feature group for drawn items
                this.drawnItems = new L.FeatureGroup();
                this.map.addLayer(this.drawnItems);

                // Initialize feature group for uploaded items (candidates)
                this.uploadedItems = new L.FeatureGroup();
                this.map.addLayer(this.uploadedItems);

                const overlays = {
                    "Gambar": this.drawnItems
                };

                L.control.layers(baseLayers, overlays).addTo(this.map);

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
                // Remove previous click handler if it was 'point' mode
                this.map.off('click', this.handleMapClick);
                
                // Disable valid existing draw handler if exists
                if (this.currentDrawHandler) {
                    this.currentDrawHandler.disable();
                    this.currentDrawHandler = null;
                }

                this.drawingMode = type;

                // Enable drawing for the selected type
                if (type === 'point') {
                    this.map.on('click', this.handleMapClick.bind(this));
                } else {
                    // For line and polygon, use Leaflet Draw
                    if (type === 'line') {
                        this.currentDrawHandler = new L.Draw.Polyline(this.map, this.drawControl.options.draw.polyline);
                    } else if (type === 'polygon') {
                        this.currentDrawHandler = new L.Draw.Polygon(this.map, this.drawControl.options.draw.polygon);
                    }
                    
                    if (this.currentDrawHandler) {
                        this.currentDrawHandler.enable();
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
                const geojson = layer.toGeoJSON();
                const geometry = geojson.geometry;

                if (geometry) {
                    this.geometryJson = JSON.stringify(geometry);
                    this.hasGeometry = true;
                    this.coordinates = {
                        lat: Number(geojson.properties?.lat || config.center[0]).toFixed(6),
                        lng: Number(geojson.properties?.lng || config.center[1]).toFixed(6)
                    };

                    // Update coordinates for Point
                    if (geometry.type === 'Point') {
                        this.coordinates = {
                            lat: Number(geometry.coordinates[1]).toFixed(6),
                            lng: Number(geometry.coordinates[0]).toFixed(6)
                        };
                    }

                    // Calculate area if it's a polygon
                    if (geometry.type === 'Polygon' || geometry.type === 'MultiPolygon') {
                        this.calculateArea(layer);
                    }
                }
            },

            calculateArea(layer) {
                // Use L.GeometryUtil if available, otherwise approximation
                // For MultiPolygon, we might need to sum areas of parts
                // But simpler is to rely on the layer's getLatLngs
                
                let areaSqMeters = 0;
                
                if (layer instanceof L.Polygon) {
                    const latlngs = layer.getLatLngs();
                    // Check if it's a MultiPolygon or Polygon with holes
                    // Leaflet structure:
                    // Simple Polygon: [latlngs]
                    // Polygon with holes: [outer, hole1, hole2]
                    // MultiPolygon: [[outer, hole], [outer]]
                    
                    // Helper to calculate area of a ring (array of latlngs)
                    const ringArea = (ring) => L.GeometryUtil.geodesicArea(ring);
                    
                    // Recursive helper to handle nested arrays
                    const processLatLngs = (coords) => {
                        if (coords.length === 0) return;
                        
                        // Check depth
                        if (coords[0] instanceof L.LatLng) {
                            // It's a ring
                            areaSqMeters += ringArea(coords);
                        } else if (Array.isArray(coords[0])) {
                            // It's an array of rings or polygons
                            coords.forEach(child => processLatLngs(child));
                        }
                    };
                    
                    processLatLngs(latlngs);
                }

                const areaHectares = (areaSqMeters / 10000).toFixed(4);
                
                // Dispatch event for parent components
                this.$dispatch('area-calculated', areaHectares);
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
                // Use L.geoJSON to create the layer correctly for any geometry type
                const tempLayer = L.geoJSON(geometry, {
                    style: {
                        color: '#3388ff',
                        fillOpacity: 0.2,
                        weight: 3
                    },
                    pointToLayer: (feature, latlng) => {
                        return L.marker(latlng);
                    }
                });

                const layers = tempLayer.getLayers();
                if (layers.length > 0) {
                    const layer = layers[0];
                    
                    // If it's a MultiPolygon, L.geoJSON might return a FeatureGroup or a single L.Polygon depending on implementation
                    // But usually for single geometry it returns one layer.
                    // If it's a FeatureGroup (e.g. GeometryCollection), we might need to handle differently.
                    // But for standard Point/Line/Polygon/Multi types, it's usually one layer.
                    
                    // However, for MultiPolygon, Leaflet might create a layer that doesn't fully behave like a simple Polygon in Draw
                    // But let's try adding it.
                    
                    // If the layer is a group (e.g. MultiPoint), extract children?
                    // For now, assume simple geometry or handled by Leaflet.
                    
                    // Important: For editing to work, we need to add the layer to drawnItems
                    // If it's a FeatureGroup, we might need to add its children.
                    
                    if (layer instanceof L.LayerGroup) {
                        layer.eachLayer(l => {
                            l.addTo(this.drawnItems);
                        });
                    } else {
                        layer.addTo(this.drawnItems);
                    }

                    this.map.fitBounds(tempLayer.getBounds());
                    this.hasGeometry = true;
                    this.geometryJson = JSON.stringify(geometry);
                    
                    // Update coordinates if Point
                    if (geometry.type === 'Point') {
                         this.coordinates = {
                            lat: Number(geometry.coordinates[1]).toFixed(6),
                            lng: Number(geometry.coordinates[0]).toFixed(6)
                        };
                    }
                    
                    // Calculate area
                    if (geometry.type === 'Polygon' || geometry.type === 'MultiPolygon') {
                        // For area calculation, we need the layer(s)
                        // If we added multiple layers (MultiPolygon as Group), we need to sum
                        if (layer instanceof L.LayerGroup) {
                            // TODO: Handle area for group
                        } else {
                            this.calculateArea(layer);
                        }
                    }
                }
            },

            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const geojson = JSON.parse(e.target.result);
                        
                        // Clear existing
                        this.clearDrawing();
                        if (this.uploadedItems) {
                            this.uploadedItems.clearLayers();
                        }
                        this.uploadedFeatures = [];
                        this.currentIndex = 0;

                        const layer = L.geoJSON(geojson);
                        const layers = [];

                        layer.eachLayer((l) => {
                            // Validate geometry type
                            const geometry = l.feature ? l.feature.geometry : l.toGeoJSON().geometry;
                            let isValid = false;
                            if (config.drawType === 'point' && geometry.type === 'Point') isValid = true;
                            else if (config.drawType === 'line' && (geometry.type === 'LineString' || geometry.type === 'MultiLineString')) isValid = true;
                            else if (config.drawType === 'polygon' && (geometry.type === 'Polygon' || geometry.type === 'MultiPolygon')) isValid = true;

                            if (isValid) {
                                // Add click handler to select this feature
                                l.on('click', () => {
                                    // Find index
                                    const index = this.uploadedFeatures.indexOf(l);
                                    if (index !== -1) {
                                        this.currentIndex = index;
                                        this.selectFeature(l);
                                    }
                                });
                                
                                // Style for candidates
                                if (l instanceof L.Path) {
                                    l.setStyle({ color: '#9ca3af', weight: 2, dashArray: '5, 5' });
                                } else if (l instanceof L.Marker) {
                                    l.setOpacity(0.6);
                                }

                                this.uploadedItems.addLayer(l);
                                layers.push(l);
                            }
                        });

                        this.uploadedFeatures = layers;

                        if (layers.length === 0) {
                            alert(`Tidak ada fitur valid dalam file. Harapkan ${config.drawType}.`);
                            return;
                        }

                        // Auto-select first feature
                        this.selectFeature(layers[0]);
                        
                        if (layers.length > 1) {
                            this.map.fitBounds(this.uploadedItems.getBounds());
                            // alert(`File berisi ${layers.length} fitur. Gunakan tombol Next/Prev atau klik di peta untuk memilih.`);
                        }

                        event.target.value = ''; // Reset input
                    } catch (error) {
                        console.error('Error parsing GeoJSON:', error);
                        alert('Gagal membaca file GeoJSON: ' + error.message);
                    }
                };
                reader.readAsText(file);
            },

            nextFeature() {
                if (this.currentIndex < this.uploadedFeatures.length - 1) {
                    this.currentIndex++;
                    this.selectFeature(this.uploadedFeatures[this.currentIndex]);
                }
            },

            prevFeature() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    this.selectFeature(this.uploadedFeatures[this.currentIndex]);
                }
            },

            selectFeature(layer) {
                // Clear drawn items
                this.drawnItems.clearLayers();
                
                // Reset style of all uploaded items
                this.uploadedItems.eachLayer(l => {
                    if (l instanceof L.Path) {
                        l.setStyle({ color: '#9ca3af', weight: 2, dashArray: '5, 5' });
                    } else if (l instanceof L.Marker) {
                        l.setOpacity(0.6);
                    }
                });

                // Highlight selected in uploaded items
                if (layer instanceof L.Path) {
                    layer.setStyle({ color: '#2563eb', weight: 4, dashArray: null });
                } else if (layer instanceof L.Marker) {
                    layer.setOpacity(1);
                }
                
                // Also add to drawnItems so it's the "active" geometry
                const feature = layer.feature;
                const geometry = feature ? feature.geometry : layer.toGeoJSON().geometry;
                
                this.loadExistingGeometry(geometry);
                
                // Populate properties
                if (feature && feature.properties) {
                    this.populateFormFields(feature.properties);
                }
            },

            populateFormFields(properties) {
                const fieldMapping = {
                    'name': ['name', 'nama', 'title', 'label'],
                    'description': ['description', 'desc', 'deskripsi', 'keterangan'],
                    'category_id': ['category_id', 'category', 'kategori']
                };

                Object.entries(fieldMapping).forEach(([inputName, propertyKeys]) => {
                    const input = document.querySelector(`[name="${inputName}"]`);
                    if (input) {
                        const foundKey = propertyKeys.find(key => properties[key] !== undefined && properties[key] !== null);
                        if (foundKey) {
                            const value = properties[foundKey];
                            if (input.tagName === 'SELECT') {
                                let matched = false;
                                for (let i = 0; i < input.options.length; i++) {
                                    if (input.options[i].value == value) {
                                        input.value = value;
                                        matched = true;
                                        break;
                                    }
                                }
                                if (!matched) {
                                    const lowerValue = String(value).toLowerCase();
                                    for (let i = 0; i < input.options.length; i++) {
                                        if (input.options[i].text.toLowerCase() === lowerValue) {
                                            input.value = input.options[i].value;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                input.value = value;
                            }
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                });
            }
        }));
    });
</script>

