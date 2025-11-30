<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jelajahi Peta - Desa Mayong Lor</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        #map { height: 100vh; width: 100%; z-index: 0; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 999px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }
        
        /* Leaflet Customization */
        .leaflet-control-layers {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            color: #1e293b !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }
        .leaflet-control-zoom a {
            background: rgba(255, 255, 255, 0.95) !important;
            color: #1e293b !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
        }
        .leaflet-control-zoom a:first-child {
            border-top-left-radius: 8px !important;
            border-top-right-radius: 8px !important;
        }
        .leaflet-control-zoom a:last-child {
            border-bottom-left-radius: 8px !important;
            border-bottom-right-radius: 8px !important;
        }
        .leaflet-popup-content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            color: #1e293b;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .leaflet-popup-tip {
            background: rgba(255, 255, 255, 0.95);
        }
        .leaflet-container a.leaflet-popup-close-button {
            color: #64748b;
        }
        [x-cloak] { display: none !important; }
        .custom-marker {
            background: transparent;
            border: none;
        }
    </style>
</head>
<body class="antialiased font-sans text-slate-600 bg-slate-50 overflow-hidden">

    <div x-data="mapExplorer()" class="relative h-screen w-full flex">
        
        <!-- Toast Notification -->
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900/90 backdrop-blur text-white px-6 py-3 rounded-full shadow-xl flex items-center gap-3"
             x-cloak>
            <i class="fa-solid fa-info-circle text-blue-400"></i>
            <span x-text="toastMessage" class="text-sm font-medium"></span>
        </div>
        
        <!-- Map Container -->
        <div class="absolute inset-0 z-0">
            <div id="map"></div>
        </div>

        <!-- Floating Sidebar -->
        <div class="absolute top-0 left-0 h-full w-full md:w-[420px] z-10 p-4 md:p-6 pointer-events-none flex flex-col">
            <!-- Header & Search -->
            <div class="glass-panel rounded-3xl p-5 shadow-2xl shadow-slate-200/50 pointer-events-auto flex flex-col max-h-full">
                
                <!-- Top Bar -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('welcome') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 transition text-slate-700">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-lg font-bold text-slate-800 leading-tight">Peta Desa Mayong Lor</h1>
                            <p class="text-xs text-slate-500">Eksplorasi Data Spasial</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="resetView()" class="p-2 text-slate-400 hover:text-slate-600 transition" title="Reset View">
                            <i class="fa-solid fa-compress"></i>
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div class="relative mb-6">
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        @input="performSearch()"
                        placeholder="Cari lokasi, kategori, atau infrastruktur..."
                        class="w-full rounded-xl bg-slate-50 border border-slate-200 py-3 pl-11 pr-4 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                    >
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="searchQuery.length > 1 && searchResults.length > 0" 
                         x-transition
                         class="absolute top-full left-0 right-0 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl overflow-hidden z-50 max-h-60 overflow-y-auto custom-scroll">
                        <template x-for="result in searchResults" :key="result.id">
                            <button @click="selectFeature(result)" class="w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition flex items-center gap-3">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                    <i class="fa-solid fa-location-dot text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800" x-text="result.name"></p>
                                    <p class="text-xs text-slate-500" x-text="result.type || 'Lokasi'"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="flex p-1 bg-slate-100 rounded-xl mb-4 border border-slate-200">
                    <button @click="activeTab = 'layers'" :class="activeTab === 'layers' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2 text-xs font-semibold rounded-lg transition">Layers</button>
                    <button @click="activeTab = 'places'" :class="activeTab === 'places' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2 text-xs font-semibold rounded-lg transition">Lokasi</button>
                    <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2 text-xs font-semibold rounded-lg transition">Info</button>
                </div>

                <!-- Content Area -->
                <div class="flex-1 overflow-y-auto custom-scroll pr-2 -mr-2">
                    
                    <!-- Layers Tab -->
                    <div x-show="activeTab === 'layers'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="space-y-4">
                            <!-- Main Toggles -->
                            <div class="space-y-2">
                                <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition cursor-pointer group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                                            <i class="fa-solid fa-map"></i>
                                        </div>
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Batas Wilayah</span>
                                    </div>
                                    <input type="checkbox" x-model="showBoundaries" @change="updateLayers()" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition cursor-pointer group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                            <i class="fa-solid fa-road"></i>
                                        </div>
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Infrastruktur</span>
                                    </div>
                                    <input type="checkbox" x-model="showInfrastructures" @change="updateLayers()" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </label>

                                <label class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition cursor-pointer group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                                            <i class="fa-solid fa-seedling"></i>
                                        </div>
                                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900">Penggunaan Lahan</span>
                                    </div>
                                    <input type="checkbox" x-model="showLandUses" @change="updateLayers()" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                </label>
                            </div>

                            <div class="h-px bg-slate-200 my-4"></div>

                            <!-- Categories Filter -->
                            <div>
                                <h3 class="text-xs uppercase tracking-wider text-slate-500 font-bold mb-3">Filter Kategori Lokasi</h3>
                                <div class="space-y-2">
                                    <template x-for="category in categories" :key="category.id">
                                        <label x-show="category.places_count > 0" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-slate-50 transition cursor-pointer">
                                            <input type="checkbox" :value="category.id" x-model="selectedCategories" @change="updateMapMarkers()" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span class="w-2 h-2 rounded-full" :style="`background-color: ${category.color}`"></span>
                                            <span class="text-sm text-slate-600 flex-1" x-text="category.name"></span>
                                            <span class="text-xs bg-slate-100 px-2 py-0.5 rounded-full text-slate-500" x-text="category.places_count"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Places Tab -->
                    <div x-show="activeTab === 'places'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="space-y-3">
                            <template x-for="place in visiblePlaces" :key="place.id">
                                <div @click="selectPlace(place)" class="group bg-white border border-slate-200 rounded-xl p-3 hover:border-blue-300 hover:shadow-md transition cursor-pointer">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <template x-if="place.image_path">
                                                <img :src="'{{ url('/') }}/' + place.image_path" class="w-12 h-12 rounded-lg object-cover bg-slate-100">
                                            </template>
                                            <template x-if="!place.image_path">
                                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-lg" :style="`background-color: ${place.category.color}20; color: ${place.category.color}`">
                                                    <i :class="place.category.icon_class || 'fa-solid fa-map-marker-alt'"></i>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition truncate" x-text="place.name"></h4>
                                            <p class="text-xs text-slate-500 mt-0.5" x-text="place.category.name"></p>
                                            <p class="text-xs text-slate-500 mt-1 line-clamp-2" x-text="place.description"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="visiblePlaces.length === 0" class="text-center py-8 text-slate-500">
                                <p>Tidak ada lokasi yang sesuai filter.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Tab -->
                    <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="space-y-6">
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <h3 class="text-sm font-bold text-blue-700 mb-2">Tentang Peta Ini</h3>
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    Peta digital Desa Mayong Lor menyajikan data geospasial yang terintegrasi, mencakup batas wilayah, infrastruktur, penggunaan lahan, dan fasilitas publik.
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Total Lokasi</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $places->count() }}</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Infrastruktur</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $infrastructures->count() }}</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Wilayah</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $boundaries->count() }}</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                    <p class="text-xs text-slate-500 uppercase tracking-wider">Lahan</p>
                                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $landUses->count() }}</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Legenda</h4>
                                <div class="space-y-2 text-sm text-slate-600">
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                        <span>Batas Wilayah (Polygon)</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                        <span>Sungai / Irigasi (Line)</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full bg-slate-500"></span>
                                        <span>Jalan (Line)</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                        <span>Penggunaan Lahan (Polygon)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Detail Panel (Slide Over) -->
        <div x-show="selectedFeature" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="absolute top-0 right-0 h-full w-full md:w-[400px] z-20 p-4 md:p-6 pointer-events-none flex flex-col"
             x-cloak>
            
            <div class="glass-panel rounded-3xl h-full shadow-2xl shadow-slate-200/50 pointer-events-auto flex flex-col overflow-hidden">
                <!-- Header Image -->
                <div class="relative h-48 bg-slate-100 flex-shrink-0">
                    <template x-if="selectedFeature?.image_url">
                        <img :src="selectedFeature.image_url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedFeature?.image_url">
                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                            <i class="fa-solid fa-image text-4xl"></i>
                        </div>
                    </template>
                    <button @click="selectedFeature = null" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white text-slate-500 shadow-md flex items-center justify-center hover:bg-slate-50 hover:text-slate-700 transition">
                        <i class="fa-solid fa-times"></i>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-white to-transparent">
                        <span class="inline-block px-2 py-1 rounded bg-blue-600 text-white text-xs font-bold uppercase tracking-wider mb-1" x-text="selectedFeature?.category?.name || selectedFeature?.type || 'Detail'"></span>
                        <h2 class="text-xl font-bold text-slate-900 leading-tight" x-text="selectedFeature?.name"></h2>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
                    
                    <div x-show="selectedFeature?.description">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi</h3>
                        <p class="text-sm text-slate-600 leading-relaxed" x-text="selectedFeature?.description"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <template x-if="selectedFeature?.area_hectares">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase tracking-wider">Luas Area</p>
                                <p class="text-lg font-bold text-slate-800 mt-1"><span x-text="selectedFeature.area_hectares"></span> ha</p>
                            </div>
                        </template>
                        <template x-if="selectedFeature?.length_meters">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase tracking-wider">Panjang</p>
                                <p class="text-lg font-bold text-slate-800 mt-1"><span x-text="selectedFeature.length_meters"></span> m</p>
                            </div>
                        </template>
                        <template x-if="selectedFeature?.condition">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase tracking-wider">Kondisi</p>
                                <p class="text-lg font-bold text-slate-800 mt-1" x-text="selectedFeature.condition"></p>
                            </div>
                        </template>
                        <template x-if="selectedFeature?.owner">
                            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <p class="text-xs text-slate-500 uppercase tracking-wider">Pemilik</p>
                                <p class="text-lg font-bold text-slate-800 mt-1" x-text="selectedFeature.owner"></p>
                            </div>
                        </template>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                        <button @click="zoomToFeature(selectedFeature)" class="w-full py-3 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-500 transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-crosshairs"></i>
                            Fokus di Peta
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function mapExplorer() {
            return {
                map: null,
                activeTab: 'layers',
                searchQuery: '',
                searchResults: [],
                selectedFeature: null,
                selectedFeature: null,
                initialCenterSet: false,
                showToast: false,
                toastMessage: '',
                
                // Data
                categories: @json($categories),
                selectedCategories: [],
                
                // Toggles
                showBoundaries: true,
                showInfrastructures: true,
                showLandUses: true,

                // Raw Data
                allPlaces: [],
                allBoundaries: [],
                allInfrastructures: [],
                allLandUses: [],

                // Layers
                markers: [],
                boundariesLayer: null,
                infrastructuresLayer: null,
                landUsesLayer: null,

                get visiblePlaces() {
                    if (!this.allPlaces.length) return [];
                    return this.allPlaces
                        .map(f => ({
                            ...f.properties,
                            latitude: f.geometry.coordinates[1],
                            longitude: f.geometry.coordinates[0]
                        }))
                        .filter(p => this.selectedCategories.includes(p.category.id));
                },

                init() {
                    this.selectedCategories = this.categories.map(c => c.id);
                    this.initMap();
                    this.loadAllData().then(() => {
                        this.checkUrlQuery();
                    });
                },

                checkUrlQuery() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const query = urlParams.get('q');
                    const categoryId = urlParams.get('category');

                    if (categoryId) {
                        // Filter by category
                        const catId = parseInt(categoryId);
                        if (this.categories.find(c => c.id === catId)) {
                            this.selectedCategories = [catId];
                            this.updateMapMarkers();
                            const catName = this.categories.find(c => c.id === catId).name;
                            this.showNotification(`Menampilkan kategori: "${catName}"`);
                        }
                    }

                    if (query) {
                        this.searchQuery = query;
                        this.performSearch();
                        
                        if (this.searchResults.length > 0) {
                            // Auto select first result
                            const firstResult = this.searchResults[0];
                            this.selectFeature(firstResult);
                            this.showNotification(`Menampilkan hasil untuk: "${query}"`);
                        } else {
                            this.showNotification(`Tidak ditemukan hasil untuk: "${query}"`);
                        }
                    }
                },

                showNotification(message) {
                    this.toastMessage = message;
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 3000);
                },

                initMap() {
                    this.map = L.map('map', {
                        zoomControl: false,
                        attributionControl: false,
                        maxZoom: 22
                    }).setView([-6.7289, 110.7485], 14);

                    // Custom Zoom Control
                    L.control.zoom({
                        position: 'bottomright'
                    }).addTo(this.map);

                    // Google Maps Layers
                    // CartoDB Voyager (Clean Streets)
                    const googleStreets = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                        subdomains: 'abcd',
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        updateWhenIdle: true,
                        keepBuffer: 2
                    });
                    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        updateWhenIdle: true,
                        keepBuffer: 2
                    });
                    const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        updateWhenIdle: true,
                        keepBuffer: 2
                    });
                    const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        updateWhenIdle: true,
                        keepBuffer: 2
                    });

                    googleSatellite.addTo(this.map);

                    const baseLayers = {
                        "Satelit (Hybrid)": googleHybrid,
                        "Peta Jalan (Bersih)": googleStreets,
                        "Satelit (Polos)": googleSatellite,
                        "Topografi": googleTerrain
                    };

                    L.control.layers(baseLayers, null, { position: 'bottomright' }).addTo(this.map);
                },

                async loadAllData() {
                    try {
                        const [places, boundaries, infrastructures, landUses] = await Promise.all([
                            fetch('{{ route("places.geojson") }}').then(r => r.json()),
                            fetch('{{ route("boundaries.geojson") }}').then(r => r.json()),
                            fetch('{{ route("infrastructures.geojson") }}').then(r => r.json()),
                            fetch('{{ route("land_uses.geojson") }}').then(r => r.json())
                        ]);

                        this.allPlaces = places.features || [];
                        this.allBoundaries = boundaries.features || [];
                        this.allInfrastructures = infrastructures.features || [];
                        this.allLandUses = landUses.features || [];

                        this.updateLayers();
                        this.updateMapMarkers();

                    } catch (e) {
                        console.error("Failed to load map data", e);
                    }
                },

                updateLayers() {
                    // Boundaries
                    if (this.boundariesLayer) this.map.removeLayer(this.boundariesLayer);
                    if (this.showBoundaries && this.allBoundaries.length) {
                        this.boundariesLayer = L.geoJSON(this.allBoundaries, {
                            style: { color: '#10b981', weight: 2, fillOpacity: 0.1 },
                            onEachFeature: (f, l) => this.bindFeaturePopup(f, l)
                        }).addTo(this.map);

                        // Fit bounds on initial load
                        if (!this.initialCenterSet) {
                            this.map.fitBounds(this.boundariesLayer.getBounds(), { padding: [50, 50] });
                            this.initialCenterSet = true;
                        }
                    }

                    // Infrastructures
                    if (this.infrastructuresLayer) this.map.removeLayer(this.infrastructuresLayer);
                    if (this.showInfrastructures && this.allInfrastructures.length) {
                        this.infrastructuresLayer = L.geoJSON(this.allInfrastructures, {
                            style: (f) => {
                                const type = f.properties.type;
                                const color = type === 'river' ? '#3b82f6' : 
                                             type === 'road' ? '#94a3b8' : 
                                             type === 'irrigation' ? '#06b6d4' : '#8b5cf6';
                                return { color: color, weight: type === 'road' ? 4 : 3, opacity: 0.9 };
                            },
                            onEachFeature: (f, l) => this.bindFeaturePopup(f, l)
                        }).addTo(this.map);
                    }

                    // Land Uses
                    if (this.landUsesLayer) this.map.removeLayer(this.landUsesLayer);
                    if (this.showLandUses && this.allLandUses.length) {
                        this.landUsesLayer = L.geoJSON(this.allLandUses, {
                            style: (f) => {
                                const type = f.properties.type;
                                const color = type === 'rice_field' ? '#fbbf24' : 
                                             type === 'plantation' ? '#84cc16' : 
                                             type === 'forest' ? '#059669' : '#f59e0b';
                                return { color: color, weight: 1, fillOpacity: 0.4, fillColor: color };
                            },
                            onEachFeature: (f, l) => this.bindFeaturePopup(f, l)
                        }).addTo(this.map);
                    }
                },

                updateMapMarkers() {
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];

                    this.allPlaces.forEach(feature => {
                        const props = feature.properties;
                        if (!this.selectedCategories.includes(props.category.id)) return;

                        const [lng, lat] = feature.geometry.coordinates;
                        
                        const iconHtml = `
                            <div class="rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-xs" style="background-color: ${props.category.color}; width: 32px; height: 32px;">
                                <i class="${props.category.icon_class || 'fa-solid fa-map-marker-alt'}"></i>
                            </div>
                        `;
                        
                        const icon = L.divIcon({
                            html: iconHtml,
                            className: 'custom-marker',
                            iconSize: [32, 32],
                            iconAnchor: [16, 16]
                        });

                        const marker = L.marker([lat, lng], { icon: icon });
                        
                        marker.on('click', () => {
                            this.selectedFeature = {
                                ...props,
                                image_url: props.image_url ? props.image_url : null,
                                latitude: lat,
                                longitude: lng
                            };
                        });

                        marker.addTo(this.map);
                        this.markers.push(marker);
                    });
                },

                bindFeaturePopup(feature, layer) {
                    layer.on('click', () => {
                        this.selectedFeature = {
                            ...feature.properties,
                            _geo: feature
                        };
                        L.DomEvent.stop(event); // Prevent map click
                    });
                },

                performSearch() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    const q = this.searchQuery.toLowerCase();
                    
                    const places = this.allPlaces.filter(f => f.properties.name.toLowerCase().includes(q)).map(f => ({...f.properties, type: 'Lokasi', _geo: f}));
                    const bounds = this.allBoundaries.filter(f => f.properties.name.toLowerCase().includes(q)).map(f => ({...f.properties, type: 'Wilayah', _geo: f}));
                    const infras = this.allInfrastructures.filter(f => f.properties.name.toLowerCase().includes(q)).map(f => ({...f.properties, type: 'Infrastruktur', _geo: f}));
                    
                    this.searchResults = [...places, ...bounds, ...infras].slice(0, 5);
                },

                selectFeature(result) {
                    this.selectedFeature = result;
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.zoomToFeature(result);
                },

                selectPlace(place) {
                    this.selectedFeature = {
                        ...place,
                        image_url: place.image_url || (place.image_path ? '{{ asset("") }}/' + place.image_path : null)
                    };
                    this.zoomToFeature({
                        latitude: place.latitude,
                        longitude: place.longitude,
                        _geo: { geometry: { type: 'Point', coordinates: [place.longitude, place.latitude] } }
                    });
                },

                zoomToFeature(feature) {
                    // Handle both raw feature objects and processed result objects
                    const geo = feature._geo || (feature.geometry ? feature : null);
                    
                    if (geo) {
                        if (geo.geometry.type === 'Point') {
                            const [lng, lat] = geo.geometry.coordinates;
                            this.map.setView([lat, lng], 18, { animate: true, duration: 1.5 });
                        } else {
                            const bounds = L.geoJSON(geo).getBounds();
                            this.map.fitBounds(bounds, { padding: [50, 50], animate: true, duration: 1.5 });
                        }
                    } else if (feature.latitude && feature.longitude) {
                         this.map.setView([feature.latitude, feature.longitude], 18, { animate: true, duration: 1.5 });
                    }
                },

                resetView() {
                    this.map.setView([-6.7289, 110.7485], 14, { animate: true, duration: 1 });
                    this.selectedFeature = null;
                }
            }
        }
    </script>
</body>
</html>
