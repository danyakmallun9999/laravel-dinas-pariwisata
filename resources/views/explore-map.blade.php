<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Peta - Desa Mayong Lor</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #map { height: 100vh; width: 100%; }
        .sidebar {
            width: 400px;
            max-height: 100vh;
            overflow-y: auto;
        }
        .feature-card {
            transition: all 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                max-height: 50vh;
            }
        }
    </style>
</head>
<body>
    <div x-data="mapExplorer()" class="flex flex-col md:flex-row h-screen">
        <!-- Sidebar -->
        <div class="sidebar bg-white shadow-xl z-10 flex flex-col">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold">Peta Desa Mayong Lor</h1>
                    <a href="{{ route('welcome') }}" class="text-white hover:text-gray-200">
                        <i class="fa-solid fa-arrow-left text-xl"></i>
                    </a>
                </div>
                <p class="text-sm text-green-100">Jelajahi lokasi, infrastruktur, dan wilayah desa</p>
            </div>

            <!-- Search -->
            <div class="p-4 border-b">
                <div class="relative">
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        @input="performSearch()"
                        placeholder="Cari lokasi, tempat, atau fasilitas..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Layer Controls -->
            <div class="p-4 border-b bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Layer Peta</h3>
                <div class="space-y-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="showPlaces" @change="updateLayers()" class="mr-2">
                        <span class="text-sm text-gray-700">Titik Lokasi</span>
                        <span class="ml-auto text-xs text-gray-500">({{ $places->count() }})</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="showBoundaries" @change="updateLayers()" class="mr-2">
                        <span class="text-sm text-gray-700">Batas Wilayah</span>
                        <span class="ml-auto text-xs text-gray-500">({{ $boundaries->count() }})</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="showInfrastructures" @change="updateLayers()" class="mr-2">
                        <span class="text-sm text-gray-700">Infrastruktur</span>
                        <span class="ml-auto text-xs text-gray-500">({{ $infrastructures->count() }})</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="showLandUses" @change="updateLayers()" class="mr-2">
                        <span class="text-sm text-gray-700">Penggunaan Lahan</span>
                        <span class="ml-auto text-xs text-gray-500">({{ $landUses->count() }})</span>
                    </label>
                </div>
            </div>

            <!-- Results/Features List -->
            <div class="flex-1 overflow-y-auto p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <span x-text="selectedFeature ? 'Detail' : (searchQuery ? 'Hasil Pencarian' : 'Daftar Lokasi')"></span>
                </h3>

                <!-- Selected Feature Detail -->
                <div x-show="selectedFeature" x-cloak class="mb-4">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-start justify-between mb-3">
                            <h4 class="text-lg font-bold text-gray-900" x-text="selectedFeature.name"></h4>
                            <button @click="selectedFeature = null" class="text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-2 text-sm">
                            <template x-if="selectedFeature.type">
                                <p class="text-gray-600">
                                    <span class="font-semibold">Tipe:</span> 
                                    <span x-text="selectedFeature.type"></span>
                                </p>
                            </template>
                            <template x-if="selectedFeature.category">
                                <p class="text-gray-600">
                                    <span class="font-semibold">Kategori:</span> 
                                    <span x-text="selectedFeature.category.name"></span>
                                </p>
                            </template>
                            <template x-if="selectedFeature.area_hectares">
                                <p class="text-gray-600">
                                    <span class="font-semibold">Luas:</span> 
                                    <span x-text="selectedFeature.area_hectares + ' ha'"></span>
                                </p>
                            </template>
                            <template x-if="selectedFeature.length_meters">
                                <p class="text-gray-600">
                                    <span class="font-semibold">Panjang:</span> 
                                    <span x-text="selectedFeature.length_meters + ' m'"></span>
                                </p>
                            </template>
                            <template x-if="selectedFeature.description">
                                <p class="text-gray-600 mt-2" x-text="selectedFeature.description"></p>
                            </template>
                            <template x-if="selectedFeature.image_url">
                                <img :src="selectedFeature.image_url" :alt="selectedFeature.name" class="w-full h-48 object-cover rounded-lg mt-3">
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Search Results -->
                <div x-show="!selectedFeature && searchResults.length > 0" x-cloak class="space-y-3">
                    <template x-for="result in searchResults" :key="result.id">
                        <div 
                            @click="selectFeature(result)"
                            class="feature-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-green-500"
                        >
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" 
                                         :style="'background-color: ' + (result.category?.color || '#6b7280') + '20'">
                                        <i :class="result.category?.icon_class || 'fa-solid fa-map-marker-alt'" 
                                           :style="'color: ' + (result.category?.color || '#6b7280')"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900" x-text="result.name"></h4>
                                    <p class="text-xs text-gray-500 mt-1" x-text="result.category?.name || result.type || ''"></p>
                                    <p class="text-xs text-gray-400 mt-1 line-clamp-2" x-text="result.description || ''"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Places List -->
                <div x-show="!selectedFeature && searchResults.length === 0 && !searchQuery" class="space-y-3">
                    @foreach($places as $place)
                    <div 
                        @click="selectPlace(@js($place))"
                        class="feature-card bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-green-500"
                    >
                        <div class="flex items-start space-x-3">
                            @if($place->image_path)
                            <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $place->category->color }}20">
                                <i class="{{ $place->category->icon_class ?? 'fa-solid fa-map-marker-alt' }}" style="color: {{ $place->category->color }}"></i>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-900">{{ $place->name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $place->category->name }}</p>
                                <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $place->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- No Results -->
                <div x-show="!selectedFeature && searchResults.length === 0 && searchQuery" x-cloak class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-search text-4xl mb-2"></i>
                    <p>Tidak ada hasil ditemukan</p>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="flex-1 relative">
            <div id="map"></div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function mapExplorer() {
            return {
                map: null,
                markers: [],
                boundariesLayer: null,
                infrastructuresLayer: null,
                landUsesLayer: null,
                placesLayer: null,
                showPlaces: true,
                showBoundaries: true,
                showInfrastructures: true,
                showLandUses: true,
                searchQuery: '',
                searchResults: [],
                selectedFeature: null,
                allPlaces: [],
                allBoundaries: [],
                allInfrastructures: [],
                allLandUses: [],

                init() {
                    const center = [-6.7289, 110.7485];
                    this.map = L.map('map').setView(center, 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(this.map);

                    this.loadAllData();
                },

                async loadAllData() {
                    try {
                        // Load Places
                        const placesRes = await fetch('{{ route("places.geojson") }}');
                        const placesData = await placesRes.json();
                        this.allPlaces = placesData.features || [];
                        this.loadPlaces();

                        // Load Boundaries
                        const boundariesRes = await fetch('{{ route("boundaries.geojson") }}');
                        const boundariesData = await boundariesRes.json();
                        this.allBoundaries = boundariesData.features || [];
                        this.loadBoundaries();

                        // Load Infrastructures
                        const infrastructuresRes = await fetch('{{ route("infrastructures.geojson") }}');
                        const infrastructuresData = await infrastructuresRes.json();
                        this.allInfrastructures = infrastructuresData.features || [];
                        this.loadInfrastructures();

                        // Load Land Uses
                        const landUsesRes = await fetch('{{ route("land_uses.geojson") }}');
                        const landUsesData = await landUsesRes.json();
                        this.allLandUses = landUsesData.features || [];
                        this.loadLandUses();
                    } catch (error) {
                        console.error('Error loading data:', error);
                    }
                },

                loadPlaces() {
                    if (this.placesLayer) {
                        this.map.removeLayer(this.placesLayer);
                    }
                    if (!this.showPlaces) return;

                    this.placesLayer = L.layerGroup();
                    this.allPlaces.forEach(feature => {
                        const [lng, lat] = feature.geometry.coordinates;
                        const marker = L.marker([lat, lng]);
                        
                        const props = feature.properties;
                        const popupContent = `
                            <div class="p-2 min-w-[200px]">
                                ${props.image_url ? `<img src="${props.image_url}" class="w-full h-32 object-cover mb-2 rounded">` : ''}
                                <h3 class="font-bold text-gray-900 mb-1">${props.name}</h3>
                                <p class="text-xs text-gray-600 mb-1">${props.category?.name || ''}</p>
                                <p class="text-xs text-gray-500">${props.description || ''}</p>
                            </div>
                        `;
                        marker.bindPopup(popupContent);
                        marker.on('click', () => {
                            this.selectedFeature = props;
                            this.map.setView([lat, lng], 16);
                        });
                        this.placesLayer.addLayer(marker);
                    });
                    this.placesLayer.addTo(this.map);
                },

                loadBoundaries() {
                    if (this.boundariesLayer) {
                        this.map.removeLayer(this.boundariesLayer);
                    }
                    if (!this.showBoundaries) return;

                    this.boundariesLayer = L.geoJSON(this.allBoundaries, {
                        style: {
                            color: '#10b981',
                            weight: 2,
                            fillColor: '#10b981',
                            fillOpacity: 0.2
                        },
                        onEachFeature: (feature, layer) => {
                            const props = feature.properties;
                            const popupContent = `
                                <div class="p-2">
                                    <h3 class="font-bold text-gray-900 mb-1">${props.name}</h3>
                                    <p class="text-xs text-gray-600 mb-1">Tipe: ${props.type}</p>
                                    ${props.area_hectares ? `<p class="text-xs text-gray-600">Luas: ${props.area_hectares} ha</p>` : ''}
                                    ${props.description ? `<p class="text-xs text-gray-600 mt-1">${props.description}</p>` : ''}
                                </div>
                            `;
                            layer.bindPopup(popupContent);
                            layer.on('click', () => {
                                this.selectedFeature = props;
                            });
                        }
                    });
                    this.boundariesLayer.addTo(this.map);
                },

                loadInfrastructures() {
                    if (this.infrastructuresLayer) {
                        this.map.removeLayer(this.infrastructuresLayer);
                    }
                    if (!this.showInfrastructures) return;

                    this.infrastructuresLayer = L.geoJSON(this.allInfrastructures, {
                        style: (feature) => {
                            const type = feature.properties.type;
                            const color = type === 'river' ? '#3b82f6' : 
                                         type === 'road' ? '#6b7280' : 
                                         type === 'irrigation' ? '#06b6d4' : '#8b5cf6';
                            return {
                                color: color,
                                weight: type === 'road' ? 4 : 3,
                                opacity: 0.8
                            };
                        },
                        onEachFeature: (feature, layer) => {
                            const props = feature.properties;
                            const popupContent = `
                                <div class="p-2">
                                    <h3 class="font-bold text-gray-900 mb-1">${props.name}</h3>
                                    <p class="text-xs text-gray-600 mb-1">Tipe: ${props.type}</p>
                                    ${props.length_meters ? `<p class="text-xs text-gray-600">Panjang: ${props.length_meters} m</p>` : ''}
                                    ${props.description ? `<p class="text-xs text-gray-600 mt-1">${props.description}</p>` : ''}
                                </div>
                            `;
                            layer.bindPopup(popupContent);
                            layer.on('click', () => {
                                this.selectedFeature = props;
                            });
                        }
                    });
                    this.infrastructuresLayer.addTo(this.map);
                },

                loadLandUses() {
                    if (this.landUsesLayer) {
                        this.map.removeLayer(this.landUsesLayer);
                    }
                    if (!this.showLandUses) return;

                    this.landUsesLayer = L.geoJSON(this.allLandUses, {
                        style: (feature) => {
                            const type = feature.properties.type;
                            const color = type === 'rice_field' ? '#fbbf24' : 
                                         type === 'plantation' ? '#84cc16' : 
                                         type === 'forest' ? '#059669' : '#f59e0b';
                            return {
                                color: color,
                                weight: 2,
                                fillColor: color,
                                fillOpacity: 0.3
                            };
                        },
                        onEachFeature: (feature, layer) => {
                            const props = feature.properties;
                            const popupContent = `
                                <div class="p-2">
                                    <h3 class="font-bold text-gray-900 mb-1">${props.name}</h3>
                                    <p class="text-xs text-gray-600 mb-1">Tipe: ${props.type}</p>
                                    ${props.area_hectares ? `<p class="text-xs text-gray-600">Luas: ${props.area_hectares} ha</p>` : ''}
                                    ${props.description ? `<p class="text-xs text-gray-600 mt-1">${props.description}</p>` : ''}
                                </div>
                            `;
                            layer.bindPopup(popupContent);
                            layer.on('click', () => {
                                this.selectedFeature = props;
                            });
                        }
                    });
                    this.landUsesLayer.addTo(this.map);
                },

                updateLayers() {
                    this.loadPlaces();
                    this.loadBoundaries();
                    this.loadInfrastructures();
                    this.loadLandUses();
                },

                performSearch() {
                    if (!this.searchQuery.trim()) {
                        this.searchResults = [];
                        return;
                    }

                    const query = this.searchQuery.toLowerCase();
                    this.searchResults = [
                        ...this.allPlaces.filter(f => 
                            f.properties.name?.toLowerCase().includes(query) ||
                            f.properties.description?.toLowerCase().includes(query) ||
                            f.properties.category?.name?.toLowerCase().includes(query)
                        ).map(f => f.properties),
                        ...this.allBoundaries.filter(f => 
                            f.properties.name?.toLowerCase().includes(query) ||
                            f.properties.description?.toLowerCase().includes(query)
                        ).map(f => f.properties),
                        ...this.allInfrastructures.filter(f => 
                            f.properties.name?.toLowerCase().includes(query) ||
                            f.properties.description?.toLowerCase().includes(query)
                        ).map(f => f.properties),
                        ...this.allLandUses.filter(f => 
                            f.properties.name?.toLowerCase().includes(query) ||
                            f.properties.description?.toLowerCase().includes(query)
                        ).map(f => f.properties)
                    ];
                },

                selectFeature(feature) {
                    this.selectedFeature = feature;
                    // Find and zoom to feature
                    const allFeatures = [
                        ...this.allPlaces,
                        ...this.allBoundaries,
                        ...this.allInfrastructures,
                        ...this.allLandUses
                    ];
                    const found = allFeatures.find(f => f.properties.id === feature.id);
                    if (found) {
                        if (found.geometry.type === 'Point') {
                            const [lng, lat] = found.geometry.coordinates;
                            this.map.setView([lat, lng], 16);
                        } else {
                            const bounds = L.geoJSON(found).getBounds();
                            this.map.fitBounds(bounds);
                        }
                    }
                },

                selectPlace(place) {
                    this.selectedFeature = {
                        id: place.id,
                        name: place.name,
                        description: place.description,
                        category: {
                            name: place.category.name,
                            color: place.category.color
                        },
                        image_url: place.image_path ? '{{ url("/") }}/' + place.image_path : null
                    };
                    this.map.setView([place.latitude, place.longitude], 16);
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>

