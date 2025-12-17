<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jelajahi Peta - Desa Mayong Lor</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Leaflet & Icon -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        #map { height: 100%; width: 100%; z-index: 0; }
        [x-cloak] { display: none !important; }
        
        /* Custom Scroll */
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Animations */
        .custom-marker { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .custom-marker:hover { transform: scale(1.25); z-index: 1000 !important; }
        
        .marker-pulse { animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }
    </style>
</head>
<body class="antialiased overflow-hidden bg-slate-50" x-data="mapComponent()">

    <!-- Main Container -->
    <div class="relative h-screen w-full flex">
        
        <!-- Back Button (Floating Top Left) -->
        <div class="absolute top-4 left-4 z-[500]">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2 px-4 py-2.5 bg-white/90 backdrop-blur shadow-lg border border-slate-200 rounded-xl text-slate-700 font-bold text-sm hover:bg-white hover:text-blue-600 transition group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Floating Sidebar (Left) -->
        <div class="absolute top-20 left-4 bottom-4 w-96 z-[400] flex flex-col gap-4 pointer-events-none">
            <!-- Search & Content Container -->
            <div class="flex flex-col h-full pointer-events-auto">
                
                <!-- Search Box -->
                <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl border border-white/20 p-3 mb-4">
                    <div class="relative group">
                        <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400 group-focus-within:text-blue-500 transition"></i>
                        <input type="text" 
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 font-medium placeholder:text-slate-400 bg-white/50 focus:bg-white transition" 
                               placeholder="Cari lokasi, jalan, wilayah..." 
                               x-model="searchQuery" 
                               @input.debounce.300ms="performSearch()">
                        
                        <!-- Search Results -->
                        <div x-show="searchResults.length > 0" 
                             class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-100 max-h-60 overflow-y-auto custom-scroll overflow-hidden" 
                             x-cloak>
                            <template x-for="result in searchResults" :key="result.id">
                                <button @click="selectFeature(result)" class="w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                                        <i class="fa-solid" :class="getIconForFeature(result)"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-sm truncate" x-text="result.name"></p>
                                        <p class="text-[10px] text-slate-500 truncate" x-text="result.type"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Tabbed Panel -->
                <div class="flex-1 bg-white/90 backdrop-blur rounded-2xl shadow-xl border border-white/20 overflow-hidden flex flex-col min-h-0">
                    <!-- Tabs -->
                    <div class="flex p-2 bg-slate-100/50 border-b border-slate-200/50">
                        <button @click="activeTab = 'layers'" :class="activeTab === 'layers' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2.5 text-xs font-bold rounded-xl transition-all duration-200">
                            <i class="fa-solid fa-layer-group mr-1.5"></i> Layers
                        </button>
                        <button @click="activeTab = 'places'" :class="activeTab === 'places' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2.5 text-xs font-bold rounded-xl transition-all duration-200">
                            <i class="fa-solid fa-map-marker-alt mr-1.5"></i> Lokasi
                        </button>
                        <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2.5 text-xs font-bold rounded-xl transition-all duration-200">
                            <i class="fa-solid fa-chart-pie mr-1.5"></i> Info
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto custom-scroll p-4">
                        
                        <!-- LAYERS -->
                        <div x-show="activeTab === 'layers'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="space-y-6">
                                <!-- Base Maps -->
                                <div class="space-y-3">
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Peta Dasar</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <button @click="setBaseLayer('streets')" :class="currentBaseLayer === 'streets' ? 'ring-2 ring-blue-500 bg-blue-50/50' : 'border-slate-200 hover:bg-slate-50'" class="p-3 rounded-xl border text-left transition relative overflow-hidden group">
                                            <div class="flex gap-2 items-center mb-2">
                                                <i class="fa-solid fa-map text-blue-500"></i>
                                                <span class="text-xs font-bold text-slate-700">Jalan</span>
                                            </div>
                                            <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-500 w-3/4"></div>
                                            </div>
                                        </button>
                                        <button @click="setBaseLayer('satellite')" :class="currentBaseLayer === 'satellite' ? 'ring-2 ring-blue-500 bg-blue-50/50' : 'border-slate-200 hover:bg-slate-50'" class="p-3 rounded-xl border text-left transition relative overflow-hidden group">
                                            <div class="flex gap-2 items-center mb-2">
                                                <i class="fa-solid fa-satellite text-purple-500"></i>
                                                <span class="text-xs font-bold text-slate-700">Satelit</span>
                                            </div>
                                            <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-slate-700 w-3/4"></div>
                                            </div>
                                        </button>
                                    </div>
                                </div>

                                <div class="h-px bg-slate-100"></div>

                                <!-- Data Layers -->
                                <div class="space-y-3">
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Data Spasial</h4>
                                    <div class="space-y-2">
                                        <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 transition cursor-pointer select-none">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                                                    <i class="fa-solid fa-draw-polygon text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-700">Batas Wilayah</p>
                                                    <p class="text-[10px] text-slate-500">RT/RW & Dusun</p>
                                                </div>
                                            </div>
                                            <div class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" x-model="showBoundaries" @change="updateLayers()" class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                        </label>

                                        <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 transition cursor-pointer select-none">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600">
                                                    <i class="fa-solid fa-road text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-700">Infrastruktur</p>
                                                    <p class="text-[10px] text-slate-500">Jalan & Sungai</p>
                                                </div>
                                            </div>
                                            <div class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" x-model="showInfrastructures" @change="updateLayers()" class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                        </label>

                                        <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 transition cursor-pointer select-none">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                                                    <i class="fa-solid fa-wheat text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-700">Lahan</p>
                                                    <p class="text-[10px] text-slate-500">Pertanian & Pemukiman</p>
                                                </div>
                                            </div>
                                            <div class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" x-model="showLandUses" @change="updateLayers()" class="sr-only peer">
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PLACES -->
                        <div x-show="activeTab === 'places'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="space-y-4">
                                <div x-data="{ open: true }" class="border rounded-xl border-slate-200 overflow-hidden">
                                    <button @click="open = !open" class="flex items-center justify-between w-full p-3 bg-slate-50 hover:bg-slate-100 transition">
                                        <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider">Filter Kategori</h4>
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-300" :class="{ 'rotate-180': !open }" ></i>
                                    </button>
                                    <div x-show="open" x-collapse class="p-3 space-y-2 bg-white">
                                        @foreach($categories as $category)
                                        <label class="flex items-center group cursor-pointer p-2 rounded-lg hover:bg-slate-50 transition">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" value="{{ $category->id }}" x-model="selectedCategories" @change="updateMapMarkers()" class="peer sr-only">
                                                <div class="w-4 h-4 rounded border border-slate-300 peer-checked:bg-[{{ $category->color }}] peer-checked:border-[{{ $category->color }}] transition flex items-center justify-center">
                                                    <i class="fa-solid fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100"></i>
                                                </div>
                                            </div>
                                            <span class="ml-3 text-sm text-slate-600 font-medium group-hover:text-slate-900">{{ $category->name }}</span>
                                            <span class="ml-auto text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">{{ $category->places_count }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    <template x-for="place in visiblePlaces" :key="place.id">
                                        <div @click="selectPlace(place)" class="group bg-slate-50 border border-slate-200 rounded-xl p-3 hover:bg-white hover:border-blue-300 hover:shadow-md transition cursor-pointer flex gap-3 items-start">
                                            <div class="flex-shrink-0">
                                                <template x-if="place.image_path">
                                                    <img :src="'{{ url('/') }}/' + place.image_path" class="w-12 h-12 rounded-lg object-cover bg-white">
                                                </template>
                                                <template x-if="!place.image_path">
                                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-lg bg-white text-slate-400">
                                                        <i class="fa-solid fa-map-marker-alt"></i>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition truncate" x-text="place.name"></h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="w-2 h-2 rounded-full" :style="`background-color: ${place.category.color}`"></span>
                                                    <p class="text-xs text-slate-500" x-text="place.category.name"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- INFO -->
                        <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="space-y-6">
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                                    <div class="flex items-start gap-4">
                                        <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                            <i class="fa-solid fa-circle-info text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-blue-800 mb-1">Statistik Desa</h3>
                                            <p class="text-xs text-blue-700/80 leading-relaxed">
                                                Tinjauan umum data geospasial Desa Mayong Lor.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalPlaces }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Lokasi</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalInfrastructures }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Infrastruktur</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalBoundaries }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Area Admin</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalLandUses }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Guna Lahan</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Map Canvas -->
        <div id="map" class="w-full h-full bg-slate-200"></div>

        <!-- Floating Map Controls (Right Bottom) -->
        <div class="absolute bottom-6 right-6 z-[400] flex flex-col gap-3">
             <button @click="locateUser()" class="w-12 h-12 bg-white rounded-xl shadow-lg border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition flex items-center justify-center group" title="Lokasi Saya">
                <i class="fa-solid fa-crosshairs text-lg group-hover:scale-110 transition-transform"></i>
            </button>
            <div class="flex flex-col bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                <button onclick="mapComponent().map.zoomIn()" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-50 border-b border-slate-100 transition">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button onclick="mapComponent().map.zoomOut()" class="w-12 h-12 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </div>
            <button onclick="document.getElementById('map').requestFullscreen()" class="w-12 h-12 bg-white rounded-xl shadow-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition flex items-center justify-center group" title="Fullscreen">
                <i class="fa-solid fa-expand group-hover:scale-110 transition-transform"></i>
            </button>
        </div>

        <!-- Detail Slide-Over (Right) -->
        <div x-show="selectedFeature" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="absolute top-4 right-4 bottom-4 w-96 bg-white/95 backdrop-blur rounded-2xl shadow-2xl z-[500] border border-slate-100 flex flex-col overflow-hidden"
             x-cloak>
            
            <!-- Header Image -->
            <div class="h-48 bg-slate-200 relative shrink-0">
                <template x-if="selectedFeature?.image_url">
                    <img :src="selectedFeature.image_url" class="w-full h-full object-cover">
                </template>
                <template x-if="!selectedFeature?.image_url">
                    <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                        <i class="fa-solid fa-image text-4xl"></i>
                    </div>
                </template>
                <button @click="selectedFeature = null" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur flex items-center justify-center transition">
                    <i class="fa-solid fa-times"></i>
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-white/20 text-white backdrop-blur uppercase tracking-wider" x-text="selectedFeature?.type || 'LOKASI'"></span>
                    </div>
                    <h3 class="text-white font-bold text-xl leading-tight text-shadow-sm" x-text="selectedFeature?.name"></h3>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-6">
                <!-- Description -->
                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Deskripsi</h4>
                    <p class="text-sm text-slate-600 leading-relaxed" x-text="selectedFeature?.description || 'Tidak ada deskripsi tersedia.'"></p>
                </div>
                
                <!-- Metadata Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <template x-if="selectedFeature?.area">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <p class="text-[10px] uppercase text-slate-400 font-bold mb-1">Luas Area</p>
                            <p class="font-bold text-slate-700 text-lg"><span x-text="selectedFeature.area"></span> <span class="text-xs text-slate-500 font-normal">ha</span></p>
                        </div>
                    </template>
                    <template x-if="selectedFeature?.owner">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <p class="text-[10px] uppercase text-slate-400 font-bold mb-1">Pemilik</p>
                            <p class="font-bold text-slate-700 text-lg" x-text="selectedFeature.owner"></p>
                        </div>
                    </template>
                    <template x-if="selectedFeature?.length">
                         <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <p class="text-[10px] uppercase text-slate-400 font-bold mb-1">Panjang</p>
                            <p class="font-bold text-slate-700 text-lg"><span x-text="selectedFeature.length"></span> <span class="text-xs text-slate-500 font-normal">km</span></p>
                        </div>
                    </template>
                </div>

                <!-- Actions -->
                <button @click="zoomToFeature(selectedFeature)" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/30 transition flex items-center justify-center gap-2 transform active:scale-95">
                    <i class="fa-solid fa-location-dot"></i> Zoom ke Lokasi
                </button>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-[1000] flex items-center justify-center" x-transition.opacity>
            <div class="bg-white p-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4 border border-slate-100">
                <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-800">Memuat Peta...</p>
                    <p class="text-xs text-slate-500">Mohon tunggu sebentar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        function mapComponent() {
            return {
                map: null,
                loading: true,
                activeTab: 'layers',
                currentBaseLayer: 'streets',
                baseLayers: {},
                categories: @json($categories),
                selectedCategories: [],
                showBoundaries: true,
                showInfrastructures: true,
                showLandUses: true,
                allPlaces: [],
                geoFeatures: [],
                searchQuery: '',
                searchResults: [],
                selectedFeature: null,
                userMarker: null,
                markers: [],
                boundariesLayer: null,
                infrastructuresLayer: null,
                landUsesLayer: null,

                get visiblePlaces() {
                    const selectedIds = this.selectedCategories.map(Number);
                    return this.allPlaces.filter(p => selectedIds.includes(p.properties.category?.id))
                        .map(p => ({
                            ...p.properties,
                            image_path: p.properties.image_path,
                            category: p.properties.category,
                            latitude: p.geometry.coordinates[1],
                            longitude: p.geometry.coordinates[0]
                        }));
                },

                init() {
                    this.selectedCategories = this.categories.map(c => c.id);
                    this.initMap();
                    this.fetchAllData();
                    this.$watch('selectedCategories', () => this.updateMapMarkers());
                },

                initMap() {
                    this.map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-6.7289, 110.7485], 14);
                    
                    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'] });
                    const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'] });

                    this.baseLayers = { 'streets': googleStreets, 'satellite': googleSatellite };
                    this.baseLayers['streets'].addTo(this.map);
                },

                setBaseLayer(type) {
                    if (this.currentBaseLayer === type) return;
                    this.map.removeLayer(this.baseLayers[this.currentBaseLayer]);
                    this.currentBaseLayer = type;
                    this.baseLayers[type].addTo(this.map);
                },

                async fetchAllData() {
                    try {
                        this.loading = true;
                        const [places, boundaries, infrastructures, landUses] = await Promise.all([
                            fetch('{{ route('places.geojson') }}').then(r => r.json()),
                            fetch('{{ route('boundaries.geojson') }}').then(r => r.json()),
                            fetch('{{ route('infrastructures.geojson') }}').then(r => r.json()),
                            fetch('{{ route('land_uses.geojson') }}').then(r => r.json())
                        ]);

                        this.geoFeatures = places.features || [];
                        this.allPlaces = places.features || [];
                        
                        this.loadBoundaries(boundaries.features || []);
                        this.loadInfrastructures(infrastructures.features || []);
                        this.loadLandUses(landUses.features || []);
                        this.updateMapMarkers();

                    } catch (e) {
                        console.error('Error loading data:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                updateLayers() { this.fetchAllData(); },

                loadBoundaries(features) {
                    if (this.boundariesLayer) this.map.removeLayer(this.boundariesLayer);
                    if (!this.showBoundaries) return;
                    this.boundariesLayer = L.geoJSON(features, {
                        style: { color: '#059669', weight: 2, fillColor: '#10b981', fillOpacity: 0.1, dashArray: '5, 5' },
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => {
                                L.DomEvent.stop(e);
                                this.selectFeature({...f.properties, type: 'Batas Wilayah'});
                            });
                        }
                    }).addTo(this.map);

                    // Center map on village boundaries
                    if (features.length > 0) {
                        this.map.fitBounds(this.boundariesLayer.getBounds(), { padding: [50, 50] });
                    }
                },

                loadInfrastructures(features) {
                    if (this.infrastructuresLayer) this.map.removeLayer(this.infrastructuresLayer);
                    if (!this.showInfrastructures) return;
                    this.infrastructuresLayer = L.geoJSON(features, {
                        style: f => ({ color: f.properties.type === 'river' ? '#3b82f6' : '#64748b', weight: f.properties.type === 'river' ? 4 : 3, opacity: 0.8 }),
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => { L.DomEvent.stop(e); this.selectFeature({...f.properties, type: 'Infrastruktur'}); });
                        }
                    }).addTo(this.map);
                },

                loadLandUses(features) {
                    if (this.landUsesLayer) this.map.removeLayer(this.landUsesLayer);
                    if (!this.showLandUses) return;
                    this.landUsesLayer = L.geoJSON(features, {
                        style: f => {
                            const colors = { rice_field: '#fbbf24', forest: '#15803d', settlement: '#f97316', plantation: '#84cc16' };
                            return { color: colors[f.properties.type] || '#94a3b8', weight: 1, fillOpacity: 0.3, fillColor: colors[f.properties.type] };
                        },
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => { L.DomEvent.stop(e); this.selectFeature({...f.properties, type: 'Penggunaan Lahan'}); });
                        }
                    }).addTo(this.map);
                },

                updateMapMarkers() {
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];
                    const filtered = this.geoFeatures.filter(f => this.selectedCategories.includes(f.properties.category?.id));
                    
                    filtered.forEach(feature => {
                        const [lng, lat] = feature.geometry.coordinates;
                        const p = feature.properties;
                        const color = p.category ? p.category.color : '#3b82f6';
                        
                        const iconHtml = `
                            <div class="w-9 h-9 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-sm custom-marker bg-gradient-to-br from-[${color}] to-slate-600" style="background-color: ${color}">
                                <i class="${p.category?.icon_class ?? 'fa-solid fa-map-marker-alt'}"></i>
                            </div>
                        `;
                        
                        const marker = L.marker([lat, lng], {
                            icon: L.divIcon({ html: iconHtml, className: '', iconSize: [36, 36], iconAnchor: [18, 18] })
                        });
                        
                        marker.on('click', () => { this.selectPlace({...p, latitude: lat, longitude: lng}); });
                        marker.addTo(this.map);
                        this.markers.push(marker);
                    });
                },

                performSearch() {
                    if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                    const q = this.searchQuery.toLowerCase();
                    const matches = this.allPlaces.filter(p => p.properties.name.toLowerCase().includes(q))
                        .map(p => ({ ...p.properties, coords: [...p.geometry.coordinates].reverse(), type: 'Lokasi', feature: p }));
                    this.searchResults = matches.slice(0, 5);
                },

                selectFeature(result) {
                    this.selectedFeature = result;
                    this.zoomToFeature(result);
                    this.searchResults = [];
                },

                selectPlace(place) {
                     this.selectedFeature = {
                        ...place,
                        type: 'Lokasi',
                        image_url: place.image_url || (place.image_path ? '{{ url('/') }}/' + place.image_path : null)
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
                         this.map.fitBounds(layer.getBounds(), { padding: [100, 100] });
                    }
                },

                locateUser() {
                    if (!navigator.geolocation) { alert('Browser tidak mendukung geolokasi'); return; }
                    this.loading = true;
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const { latitude, longitude } = pos.coords;
                            this.map.flyTo([latitude, longitude], 17);
                            if (this.userMarker) this.map.removeLayer(this.userMarker);
                            this.userMarker = L.marker([latitude, longitude], {
                                icon: L.divIcon({ html: '<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-lg marker-pulse"></div>', iconSize: [16, 16] })
                            }).addTo(this.map).bindPopup('Lokasi Anda').openPopup();
                            this.loading = false;
                        },
                        () => { this.loading = false; alert('Gagal mendeteksi lokasi'); }
                    );
                },
                getIconForFeature(result) {
                    if (result.type === 'Lokasi') return result.category?.icon_class || 'fa-map-marker-alt';
                    if (result.type === 'Batas Wilayah') return 'fa-draw-polygon';
                    if (result.type === 'Infrastruktur') return 'fa-road';
                    return 'fa-map-pin';
                }
            };
        }
    </script>
</body>
</html>
