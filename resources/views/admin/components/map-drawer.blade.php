@props([
    'drawType' => 'point', // point, line, polygon
    'initialGeometry' => null,
    'center' => [-6.7289, 110.7485],
    'zoom' => 14,
    'height' => 'h-96'
])



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
    
            <button 
                type="button"
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
        <div x-ref="mapContainer" class="absolute inset-0 w-full h-full bg-gray-100 z-10"></div>
    </div>

    <!-- Bottom Section: Nav, Inputs, Preview -->
    <div class="flex-none mt-3 space-y-3">
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
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mapDrawer', (config) => ({
            map: null,
            drawControl: null,
            drawnItems: null,
            drawingMode: null,
            geometryJson: (function() {
                if (!config.initialGeometry) return '';
                // Handle case where initialGeometry is already a JSON string (e.g. from old input)
                if (typeof config.initialGeometry === 'string') {
                    try {
                        // Validate if it is parseable, but keep it as string for the input value
                        JSON.parse(config.initialGeometry);
                        return config.initialGeometry;
                    } catch (e) {
                         // If not parseable JSON, ignore
                         return '';
                    }
                }
                return JSON.stringify(config.initialGeometry);
            })(),
            coordinates: { lat: config.center[0], lng: config.center[1] },
            hasGeometry: false,
            hasGeometry: false,
            currentDrawHandler: null, // Track active draw handler

            init() {
                this.$nextTick(() => {
                    this.initMap();
                    if (config.initialGeometry) {
                        // Ensure we pass an object to loadExistingGeometry
                        let geom = config.initialGeometry;
                        if (typeof geom === 'string') {
                            try {
                                geom = JSON.parse(geom);
                            } catch (e) {
                                console.error('Failed to parse initialGeometry string:', e);
                                geom = null;
                            }
                        }
                        if (geom) {
                            this.loadExistingGeometry(geom);
                        }
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
                            allowIntersection: true,
                            showArea: true,
                            drawError: {
                                color: '#e1e100', // Color the shape will turn when intersects
                                message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                            },
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
            }
        }));
    });
</script>

