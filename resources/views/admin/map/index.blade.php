<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Admin Panel · Peta Interaktif</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Pemetaan Digital Desa Mayonglor
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="interactiveMap()" x-init="init()">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Tab Navigation -->
            <div class="bg-white rounded-t-lg border-b border-gray-200">
                <nav class="flex space-x-1 px-4 overflow-x-auto" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'boundary'"
                        :class="activeTab === 'boundary' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fa-solid fa-map mr-2"></i> Batas Wilayah
                    </button>
                    <button 
                        @click="activeTab = 'infrastructure'"
                        :class="activeTab === 'infrastructure' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fa-solid fa-route mr-2"></i> Infrastruktur
                    </button>
                    <button 
                        @click="activeTab = 'landuse'"
                        :class="activeTab === 'landuse' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fa-solid fa-seedling mr-2"></i> Penggunaan Lahan
                    </button>
                    <button 
                        @click="activeTab = 'road'"
                        :class="activeTab === 'road' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fa-solid fa-road mr-2"></i> Jalan
                    </button>
                    <button 
                        @click="activeTab = 'place'"
                        :class="activeTab === 'place' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition"
                    >
                        <i class="fa-solid fa-map-marker-alt mr-2"></i> Titik Lokasi
                    </button>
                </nav>
            </div>

            <!-- Map Container with Sidebar -->
            <div class="bg-white rounded-b-lg border border-gray-200 border-t-0">
                <div class="flex h-[calc(100vh-200px)]">
                    <!-- Sidebar -->
                    <div class="w-80 border-r border-gray-200 bg-gray-50 flex flex-col">
                        <!-- Tab Content -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <!-- Batas Wilayah Tab -->
                            <div x-show="activeTab === 'boundary'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Batas Wilayah</h3>
                                    <a href="{{ route('admin.boundaries.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </a>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="boundaries.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data batas wilayah</p>
                                            <a href="{{ route('admin.boundaries.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                Tambah sekarang
                                            </a>
                                        </div>
                                    </template>
                                    <template x-for="item in boundaries" :key="item.id">
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition"
                                             @click="focusOnFeature(item, 'boundary')">
                                            <p class="font-semibold text-sm text-gray-900" x-text="item.name"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="item.type"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Infrastruktur Tab -->
                            <div x-show="activeTab === 'infrastructure'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Infrastruktur</h3>
                                    <a href="{{ route('admin.infrastructures.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </a>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="infrastructures.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data infrastruktur</p>
                                            <a href="{{ route('admin.infrastructures.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                Tambah sekarang
                                            </a>
                                        </div>
                                    </template>
                                    <template x-for="item in infrastructures" :key="item.id">
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition"
                                             @click="focusOnFeature(item, 'infrastructure')">
                                            <p class="font-semibold text-sm text-gray-900" x-text="item.name"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="item.type"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Penggunaan Lahan Tab -->
                            <div x-show="activeTab === 'landuse'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Penggunaan Lahan</h3>
                                    <a href="{{ route('admin.land-uses.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </a>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="landUses.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data penggunaan lahan</p>
                                            <a href="{{ route('admin.land-uses.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                Tambah sekarang
                                            </a>
                                        </div>
                                    </template>
                                    <template x-for="item in landUses" :key="item.id">
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition"
                                             @click="focusOnFeature(item, 'landuse')">
                                            <p class="font-semibold text-sm text-gray-900" x-text="item.name"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="item.type"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Jalan Tab -->
                            <div x-show="activeTab === 'road'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Jalan</h3>
                                    <a href="{{ route('admin.infrastructures.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </a>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="roads.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data jalan</p>
                                            <a href="{{ route('admin.infrastructures.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                Tambah sekarang
                                            </a>
                                        </div>
                                    </template>
                                    <template x-for="item in roads" :key="item.id">
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition"
                                             @click="focusOnFeature(item, 'road')">
                                            <p class="font-semibold text-sm text-gray-900" x-text="item.name"></p>
                                            <p class="text-xs text-gray-500 mt-1">Panjang: <span x-text="item.length_meters ? item.length_meters + ' m' : '-'"></span></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Titik Lokasi Tab -->
                            <div x-show="activeTab === 'place'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Titik Lokasi</h3>
                                    <a href="{{ route('admin.places.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </a>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="places.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data titik lokasi</p>
                                            <a href="{{ route('admin.places.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                                Tambah sekarang
                                            </a>
                                        </div>
                                    </template>
                                    <template x-for="item in places" :key="item.id">
                                        <div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-blue-300 cursor-pointer transition"
                                             @click="focusOnFeature(item, 'place')">
                                            <p class="font-semibold text-sm text-gray-900" x-text="item.name"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="item.category?.name || ''"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Drawing Tools -->
                        <div class="border-t border-gray-200 p-4 bg-white">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Alat Gambar</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    @click="startDrawing('point')"
                                    :class="drawingMode === 'point' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-3 py-2 rounded-lg text-xs font-semibold hover:bg-blue-500 hover:text-white transition"
                                >
                                    <i class="fa-solid fa-map-marker-alt mr-1"></i> Titik
                                </button>
                                <button 
                                    @click="startDrawing('line')"
                                    :class="drawingMode === 'line' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-3 py-2 rounded-lg text-xs font-semibold hover:bg-blue-500 hover:text-white transition"
                                >
                                    <i class="fa-solid fa-route mr-1"></i> Garis
                                </button>
                                <button 
                                    @click="startDrawing('polygon')"
                                    :class="drawingMode === 'polygon' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-3 py-2 rounded-lg text-xs font-semibold hover:bg-blue-500 hover:text-white transition"
                                >
                                    <i class="fa-solid fa-draw-polygon mr-1"></i> Polygon
                                </button>
                                <button 
                                    @click="clearDrawing()"
                                    class="px-3 py-2 rounded-lg text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200 transition"
                                >
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                </button>
                            </div>
                            <div class="mt-3">
                                <button 
                                    @click="toggleMeasure()"
                                    :class="measureMode ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700'"
                                    class="w-full px-3 py-2 rounded-lg text-xs font-semibold hover:bg-green-500 hover:text-white transition"
                                >
                                    <i class="fa-solid fa-ruler mr-1"></i> <span x-text="measureMode ? 'Nonaktifkan' : 'Aktifkan'"></span> Pengukuran
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Map Area -->
                    <div class="flex-1 relative">
                        <div id="interactiveMap" class="w-full h-full"></div>
                        
                        <!-- Map Controls Overlay -->
                        <div class="absolute top-4 right-4 z-[1000] space-y-2">
                            <!-- Layer Control -->
                            <div class="bg-white/90 backdrop-blur-sm border border-gray-200 rounded-lg shadow-sm p-3 min-w-[200px]">
                                <h5 class="text-xs font-semibold text-gray-700 mb-2">Layer</h5>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" x-model="showBoundaries" @change="toggleLayer('boundaries')" class="rounded">
                                        <span class="text-xs text-gray-700">Batas Wilayah</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" x-model="showInfrastructures" @change="toggleLayer('infrastructures')" class="rounded">
                                        <span class="text-xs text-gray-700">Infrastruktur</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" x-model="showLandUses" @change="toggleLayer('landuses')" class="rounded">
                                        <span class="text-xs text-gray-700">Penggunaan Lahan</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" x-model="showPlaces" @change="toggleLayer('places')" class="rounded">
                                        <span class="text-xs text-gray-700">Titik Lokasi</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Info Panel -->
                        <div x-show="selectedFeature" x-cloak 
                             class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm border border-gray-200 rounded-lg shadow-sm p-4 min-w-[300px] z-[1000]">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-semibold text-gray-900" x-text="selectedFeature?.name"></h4>
                                <button @click="selectedFeature = null" class="text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p x-show="selectedFeature?.type"><strong>Tipe:</strong> <span x-text="selectedFeature?.type"></span></p>
                                <p x-show="selectedFeature?.area_hectares"><strong>Luas:</strong> <span x-text="selectedFeature?.area_hectares + ' ha'"></span></p>
                                <p x-show="selectedFeature?.length_meters"><strong>Panjang:</strong> <span x-text="selectedFeature?.length_meters + ' m'"></span></p>
                                <p x-show="selectedFeature?.description"><strong>Deskripsi:</strong> <span x-text="selectedFeature?.description"></span></p>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <a :href="selectedFeature?.editUrl" class="text-xs px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Edit
                                </a>
                                <button @click="deleteFeature(selectedFeature)" class="text-xs px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @pushOnce('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endPushOnce

    @pushOnce('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            function interactiveMap() {
                return {
                    activeTab: 'boundary',
                    map: null,
                    drawnItems: null,
                    drawControl: null,
                    drawingMode: null,
                    currentDrawHandler: null,
                    measureMode: false,
                    selectedFeature: null,
                    
                    // Data
                    boundaries: @json($boundaries ?? []),
                    infrastructures: @json($infrastructures ?? []),
                    landUses: @json($landUses ?? []),
                    roads: @json($roads ?? []),
                    places: @json($places ?? []),
                    
                    // Layers
                    boundariesLayer: null,
                    infrastructuresLayer: null,
                    landUsesLayer: null,
                    placesLayer: null,
                    
                    // Layer visibility
                    showBoundaries: true,
                    showInfrastructures: true,
                    showLandUses: true,
                    showPlaces: true,

                    init() {
                        this.$nextTick(() => {
                            this.initMap();
                            this.loadAllLayers();
                        });
                    },

                    initMap() {
                        this.map = L.map('interactiveMap').setView([-6.7289, 110.7485], 14);

                        // Google Maps Layers
                        const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                            maxZoom: 20,
                            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                            attribution: '&copy; Google Maps'
                        }).addTo(this.map);

                        const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                            maxZoom: 20,
                            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                            attribution: '&copy; Google Maps'
                        });

                        const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                            maxZoom: 20,
                            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                            attribution: '&copy; Google Maps'
                        });

                        const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
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

                        L.control.layers(baseLayers).addTo(this.map);

                        this.drawnItems = new L.FeatureGroup();
                        this.map.addLayer(this.drawnItems);

                        this.initDrawControl();
                        this.initMapEvents();
                    },

                    initDrawControl() {
                        this.drawControl = new L.Control.Draw({
                            position: 'topright',
                            draw: {
                                polygon: { allowIntersection: false, showArea: true },
                                polyline: { metric: true },
                                circle: false,
                                rectangle: false,
                                marker: true,
                                circlemarker: false
                            },
                            edit: {
                                featureGroup: this.drawnItems,
                                remove: true
                            }
                        });
                        this.map.addControl(this.drawControl);

                        this.map.on(L.Draw.Event.CREATED, (e) => {
                            const layer = e.layer;
                            this.drawnItems.addLayer(layer);
                            
                            // Disable drawing after creation
                            if (this.currentDrawHandler) {
                                this.currentDrawHandler.disable();
                                this.currentDrawHandler = null;
                            }
                            this.drawingMode = null;
                            this.map.getContainer().style.cursor = '';
                            
                            // Show confirmation dialog
                            if (confirm('Geometry berhasil dibuat. Simpan sebagai fitur baru?')) {
                                this.handleNewFeature(layer);
                            }
                        });
                        
                        this.map.on(L.Draw.Event.DRAWSTART, () => {
                            this.map.getContainer().style.cursor = 'crosshair';
                        });
                        
                        this.map.on(L.Draw.Event.DRAWSTOP, () => {
                            this.map.getContainer().style.cursor = '';
                        });
                    },

                    initMapEvents() {
                        this.map.on('click', (e) => {
                            if (this.drawingMode === 'point') {
                                this.addPoint(e.latlng);
                            }
                        });
                    },

                    loadAllLayers() {
                        this.loadBoundaries();
                        this.loadInfrastructures();
                        this.loadLandUses();
                        this.loadPlaces();
                        this.loadRoads();
                    },

                    loadBoundaries() {
                        if (this.boundariesLayer) {
                            this.map.removeLayer(this.boundariesLayer);
                        }
                        this.boundariesLayer = L.geoJSON(null);
                        
                        fetch('{{ route("boundaries.geojson") }}')
                            .then(res => res.json())
                            .then(data => {
                                this.boundariesLayer = L.geoJSON(data.features || [], {
                                    style: { color: '#10b981', weight: 2, fillColor: '#10b981', fillOpacity: 0.2 },
                                    onEachFeature: (feature, layer) => {
                                        // Store feature reference in layer
                                        layer.feature = feature;
                                        layer.on('click', () => {
                                            this.selectedFeature = {
                                                ...feature.properties,
                                                geometry: feature.geometry,
                                                editUrl: `/admin/boundaries/${feature.properties.id}/edit`
                                            };
                                        });
                                    }
                                });
                                if (this.showBoundaries) {
                                    this.boundariesLayer.addTo(this.map);
                                }
                            });
                    },

                    loadInfrastructures() {
                        if (this.infrastructuresLayer) {
                            this.map.removeLayer(this.infrastructuresLayer);
                        }
                        
                        fetch('{{ route("infrastructures.geojson") }}')
                            .then(res => res.json())
                            .then(data => {
                                this.infrastructuresLayer = L.geoJSON(data.features || [], {
                                    style: (feature) => {
                                        const type = feature.properties.type;
                                        const color = type === 'river' ? '#3b82f6' : type === 'road' ? '#6b7280' : '#8b5cf6';
                                        return { color, weight: type === 'road' ? 4 : 3, opacity: 0.8 };
                                    },
                                    onEachFeature: (feature, layer) => {
                                        layer.feature = feature;
                                        layer.on('click', () => {
                                            this.selectedFeature = {
                                                ...feature.properties,
                                                geometry: feature.geometry,
                                                editUrl: `/admin/infrastructures/${feature.properties.id}/edit`
                                            };
                                        });
                                    }
                                });
                                if (this.showInfrastructures) {
                                    this.infrastructuresLayer.addTo(this.map);
                                }
                            });
                    },

                    loadLandUses() {
                        if (this.landUsesLayer) {
                            this.map.removeLayer(this.landUsesLayer);
                        }
                        
                        fetch('{{ route("land_uses.geojson") }}')
                            .then(res => res.json())
                            .then(data => {
                                this.landUsesLayer = L.geoJSON(data.features || [], {
                                    style: (feature) => {
                                        const type = feature.properties.type;
                                        const color = type === 'rice_field' ? '#fbbf24' : type === 'plantation' ? '#84cc16' : '#f59e0b';
                                        return { color, weight: 2, fillColor: color, fillOpacity: 0.3 };
                                    },
                                    onEachFeature: (feature, layer) => {
                                        layer.feature = feature;
                                        layer.on('click', () => {
                                            this.selectedFeature = {
                                                ...feature.properties,
                                                geometry: feature.geometry,
                                                editUrl: `/admin/land-uses/${feature.properties.id}/edit`
                                            };
                                        });
                                    }
                                });
                                if (this.showLandUses) {
                                    this.landUsesLayer.addTo(this.map);
                                }
                            });
                    },

                    loadPlaces() {
                        if (this.placesLayer) {
                            this.map.removeLayer(this.placesLayer);
                        }
                        
                        fetch('{{ route("places.geojson") }}')
                            .then(res => res.json())
                            .then(data => {
                                this.placesLayer = L.geoJSON(data.features || [], {
                                    pointToLayer: (feature, latlng) => {
                                        const color = feature.properties.category?.color || '#2563eb';
                                        const marker = L.marker(latlng, {
                                            icon: L.divIcon({
                                                className: 'custom-marker',
                                                html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                                                iconSize: [20, 20]
                                            })
                                        });
                                        marker.feature = feature;
                                        return marker;
                                    },
                                    onEachFeature: (feature, layer) => {
                                        layer.feature = feature;
                                        layer.on('click', () => {
                                            this.selectedFeature = {
                                                ...feature.properties,
                                                geometry: feature.geometry,
                                                editUrl: `/admin/places/${feature.properties.id}/edit`
                                            };
                                        });
                                    }
                                });
                                if (this.showPlaces) {
                                    this.placesLayer.addTo(this.map);
                                }
                            });
                    },

                    loadRoads() {
                        // Filter roads from infrastructures (already loaded)
                        this.roads = this.infrastructures.filter(i => i.type === 'road');
                    },

                    toggleLayer(type) {
                        switch(type) {
                            case 'boundaries':
                                if (this.showBoundaries) {
                                    this.boundariesLayer?.addTo(this.map);
                                } else {
                                    this.map.removeLayer(this.boundariesLayer);
                                }
                                break;
                            case 'infrastructures':
                                if (this.showInfrastructures) {
                                    this.infrastructuresLayer?.addTo(this.map);
                                } else {
                                    this.map.removeLayer(this.infrastructuresLayer);
                                }
                                break;
                            case 'landuses':
                                if (this.showLandUses) {
                                    this.landUsesLayer?.addTo(this.map);
                                } else {
                                    this.map.removeLayer(this.landUsesLayer);
                                }
                                break;
                            case 'places':
                                if (this.showPlaces) {
                                    this.placesLayer?.addTo(this.map);
                                } else {
                                    this.map.removeLayer(this.placesLayer);
                                }
                                break;
                        }
                    },

                    startDrawing(type) {
                        // Disable previous drawing if any
                        if (this.currentDrawHandler) {
                            this.currentDrawHandler.disable();
                        }
                        
                        this.drawingMode = type;
                        
                        if (type === 'point') {
                            // Point drawing handled by map click - already set up in initMapEvents
                            this.map.getContainer().style.cursor = 'crosshair';
                        } else if (type === 'line') {
                            this.currentDrawHandler = new L.Draw.Polyline(this.map, {
                                shapeOptions: { color: '#3388ff', weight: 4 }
                            });
                            this.currentDrawHandler.enable();
                        } else if (type === 'polygon') {
                            this.currentDrawHandler = new L.Draw.Polygon(this.map, {
                                shapeOptions: { color: '#3388ff', fillColor: '#3388ff', fillOpacity: 0.2 }
                            });
                            this.currentDrawHandler.enable();
                        }
                    },

                    addPoint(latlng) {
                        const marker = L.marker(latlng);
                        marker.addTo(this.drawnItems);
                        
                        const geometry = {
                            type: 'Point',
                            coordinates: [latlng.lng, latlng.lat]
                        };
                        const geometryJson = JSON.stringify(geometry);
                        
                        // Determine create URL based on active tab
                        let createUrl = '/admin/';
                        if (this.activeTab === 'place') {
                            createUrl += 'places/create';
                        } else {
                            // Default to places for point
                            createUrl += 'places/create';
                        }
                        
                        sessionStorage.setItem('newFeatureGeometry', geometryJson);
                        window.location.href = createUrl;
                    },

                    clearDrawing() {
                        this.drawnItems.clearLayers();
                        this.drawingMode = null;
                        if (this.currentDrawHandler) {
                            this.currentDrawHandler.disable();
                            this.currentDrawHandler = null;
                        }
                        this.map.getContainer().style.cursor = '';
                    },

                    toggleMeasure() {
                        this.measureMode = !this.measureMode;
                        // TODO: Implement measurement tool
                    },

                    focusOnFeature(item, type) {
                        // Try to find layer in map first
                        let foundLayer = null;
                        let geometry = item.geometry;
                        
                        // Search in loaded layers
                        const searchInLayer = (geoJsonLayer) => {
                            if (!geoJsonLayer) return;
                            geoJsonLayer.eachLayer((layer) => {
                                const feature = layer.feature;
                                if (feature && feature.properties && feature.properties.id == item.id) {
                                    foundLayer = layer;
                                    geometry = feature.geometry;
                                }
                            });
                        };
                        
                        searchInLayer(this.boundariesLayer);
                        searchInLayer(this.infrastructuresLayer);
                        searchInLayer(this.landUsesLayer);
                        searchInLayer(this.placesLayer);
                        
                        // If not found and it's a point, create geometry from lat/lng
                        if (!geometry && item.latitude && item.longitude) {
                            geometry = {
                                type: 'Point',
                                coordinates: [item.longitude, item.latitude]
                            };
                        }
                        
                        // Focus map on feature
                        if (foundLayer) {
                            if (foundLayer.getLatLng) {
                                // It's a marker/point
                                this.map.setView(foundLayer.getLatLng(), 16);
                            } else if (foundLayer.getBounds) {
                                // It's a polyline or polygon
                                this.map.fitBounds(foundLayer.getBounds(), { padding: [50, 50] });
                            }
                            // Highlight the layer temporarily
                            if (foundLayer.setStyle) {
                                const originalStyle = foundLayer.options;
                                foundLayer.setStyle({ weight: 5, color: '#ff0000' });
                                setTimeout(() => {
                                    if (foundLayer && foundLayer.setStyle) {
                                        foundLayer.setStyle(originalStyle);
                                    }
                                }, 2000);
                            }
                        } else if (geometry) {
                            // Fallback: create temporary layer to focus
                            if (geometry.type === 'Point') {
                                const [lng, lat] = geometry.coordinates;
                                this.map.setView([lat, lng], 16);
                            } else if (geometry.type === 'LineString') {
                                const latlngs = geometry.coordinates.map(coord => [coord[1], coord[0]]);
                                const polyline = L.polyline(latlngs);
                                this.map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
                                polyline.remove();
                            } else if (geometry.type === 'Polygon') {
                                const latlngs = geometry.coordinates[0].map(coord => [coord[1], coord[0]]);
                                const polygon = L.polygon(latlngs);
                                this.map.fitBounds(polygon.getBounds(), { padding: [50, 50] });
                                polygon.remove();
                            }
                        }
                        
                        this.selectedFeature = {
                            ...item,
                            geometry: geometry,
                            editUrl: this.getEditUrl(item, type)
                        };
                    },

                    getEditUrl(item, type) {
                        const baseUrl = '/admin/';
                        switch(type) {
                            case 'boundary':
                                return `${baseUrl}boundaries/${item.id}/edit`;
                            case 'infrastructure':
                            case 'road':
                                return `${baseUrl}infrastructures/${item.id}/edit`;
                            case 'landuse':
                                return `${baseUrl}land-uses/${item.id}/edit`;
                            case 'place':
                                return `${baseUrl}places/${item.id}/edit`;
                            default:
                                return '#';
                        }
                    },

                    deleteFeature(feature) {
                        if (!confirm('Hapus fitur ini?')) return;
                        
                        const url = feature.editUrl.replace('/edit', '');
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                            }
                        })
                        .then(res => {
                            if (res.ok) {
                                this.selectedFeature = null;
                                this.loadAllLayers();
                                alert('Fitur berhasil dihapus');
                            } else {
                                alert('Gagal menghapus fitur');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Terjadi kesalahan');
                        });
                    },

                    handleNewFeature(layer) {
                        const geometry = layer.toGeoJSON().geometry;
                        const geometryJson = JSON.stringify(geometry);
                        
                        // Determine create URL based on active tab
                        let createUrl = '/admin/';
                        const tab = this.activeTab;
                        
                        if (tab === 'boundary') {
                            createUrl += 'boundaries/create';
                        } else if (tab === 'infrastructure' || tab === 'road') {
                            createUrl += 'infrastructures/create';
                        } else if (tab === 'landuse') {
                            createUrl += 'land-uses/create';
                        } else if (tab === 'place') {
                            createUrl += 'places/create';
                        } else {
                            // Default based on geometry type
                            if (geometry.type === 'Polygon') {
                                createUrl += 'boundaries/create';
                            } else if (geometry.type === 'LineString') {
                                createUrl += 'infrastructures/create';
                            } else {
                                createUrl += 'places/create';
                            }
                        }
                        
                        // Store geometry in sessionStorage and redirect
                        sessionStorage.setItem('newFeatureGeometry', geometryJson);
                        window.location.href = createUrl;
                    }
                }
            }
        </script>
    @endPushOnce
</x-app-layout>

