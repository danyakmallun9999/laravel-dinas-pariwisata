<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Geografis - Desa Mayong Lor</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Leaflet & Icon -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Instrument Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        #map { height: 100%; width: 100%; z-index: 10; border-radius: 0.75rem; }
        *:focus-visible { outline: 3px solid #3b82f6; outline-offset: 2px; }
        
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

        /* Map UI Controls */
        .map-btn { 
            width: 3rem; height: 3rem; 
            background: white; 
            border: 1px solid #e2e8f0; 
            color: #475569;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
            cursor: pointer;
        }
        .map-btn:hover { background: #eff6ff; color: #2563eb; }
    </style>
</head>
<body class="antialiased" x-data="mapComponent()">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" alt="Logo" class="h-10 w-auto drop-shadow-sm transition hover:scale-105">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Pemerintah Desa</p>
                        <h1 class="text-xl font-extrabold text-slate-900 leading-none tracking-tight">Mayong Lor</h1>
                    </div>
                </div>

                <nav class="hidden md:flex items-center bg-slate-100/50 p-1 rounded-full border border-slate-200">
                    <a href="#beranda" class="px-5 py-2 text-sm font-semibold text-slate-600 rounded-full hover:bg-white hover:text-blue-700 hover:shadow-sm transition">Beranda</a>
                    <a href="#peta" class="px-5 py-2 text-sm font-semibold text-slate-600 rounded-full hover:bg-white hover:text-blue-700 hover:shadow-sm transition">Peta Desa</a>
                    <a href="{{ route('explore.map') }}" class="px-5 py-2 text-sm font-semibold text-slate-600 rounded-full hover:bg-white hover:text-blue-700 hover:shadow-sm transition">Jelajahi</a>
                    <a href="#statistik" class="px-5 py-2 text-sm font-semibold text-slate-600 rounded-full hover:bg-white hover:text-blue-700 hover:shadow-sm transition">Statistik</a>
                </nav>

                <div>
                    @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 shadow-sm shadow-blue-500/30 text-sm font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition transform hover:-translate-y-0.5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-slate-200 shadow-sm text-sm font-bold rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Masuk
                        </a>
                    @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section id="beranda" class="relative bg-gradient-to-b from-blue-50/50 to-white overflow-hidden">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-blue-100 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-72 h-72 bg-teal-100 rounded-full blur-3xl opacity-50"></div>
        
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 md:py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left z-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-blue-100 text-blue-700 text-sm font-bold mb-8 shadow-sm animate-fade-in-up">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-600"></span>
                    </span>
                    Portal Informasi Geospasial
                </div>
                <h2 class="text-5xl md:text-6xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                    Satu Peta, <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-teal-500">Sejuta Potensi.</span>
                </h2>
                <p class="text-lg md:text-xl text-slate-600 mb-10 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Akses data lokasi infrastruktur, demografi, dan fasilitas publik Desa Mayong Lor dengan mudah, transparan, dan akurat.
                </p>

                <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-xl shadow-blue-900/5 max-w-lg mx-auto lg:mx-0 flex items-center transition-all focus-within:ring-4 focus-within:ring-blue-500/20 focus-within:border-blue-400">
                    <div class="pl-4 pr-3 text-slate-400"><i class="fa-solid fa-magnifying-glass text-lg"></i></div>
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Cari lokasi (cth: Balai Desa)..." 
                           class="flex-1 border-none focus:ring-0 text-lg px-0 text-slate-800 placeholder:text-slate-400 font-medium"
                           @keydown.enter="scrollToMap(); performSearch()">
                    <button @click="scrollToMap(); performSearch()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold text-base transition shadow-md shadow-blue-600/20">
                        Cari
                    </button>
                </div>
            </div>

            <div class="relative group">
                <div class="absolute inset-0 bg-blue-600 rounded-3xl rotate-3 opacity-10 group-hover:rotate-6 transition duration-500"></div>
                <div class="absolute inset-0 bg-teal-500 rounded-3xl -rotate-2 opacity-10 group-hover:-rotate-4 transition duration-500"></div>
                <img src="/images/balaidesa.jpeg" alt="Balai Desa" class="relative rounded-3xl shadow-2xl w-full object-cover h-[450px] border-4 border-white transform transition hover:scale-[1.01] duration-500">
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="statistik" class="py-20 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-blue-600 text-2xl mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <p class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-1">Total Penduduk</p>
                    <p class="text-4xl font-extrabold text-slate-900">{{ number_format($population->total_population ?? 0) }}</p>
                </div>
                
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-indigo-600 text-2xl mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fa-solid fa-person"></i>
                    </div>
                    <p class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-1">Laki-laki</p>
                    <p class="text-4xl font-extrabold text-slate-900">{{ number_format($population->total_male ?? 0) }}</p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-pink-200 hover:bg-pink-50/30 transition duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-pink-600 text-2xl mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fa-solid fa-person-dress"></i>
                    </div>
                    <p class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-1">Perempuan</p>
                    <p class="text-4xl font-extrabold text-slate-900">{{ number_format($population->total_female ?? 0) }}</p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-100 hover:border-teal-200 hover:bg-teal-50/30 transition duration-300 group">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-teal-600 text-2xl mb-4 group-hover:scale-110 transition duration-300">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <p class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-1">Fasilitas Desa</p>
                    <p class="text-4xl font-extrabold text-slate-900">{{ $totalPlaces }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section id="peta" class="bg-slate-50 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
                <div>
                    <h3 class="text-3xl font-bold text-slate-900">Peta Digital Interaktif</h3>
                    <p class="text-slate-600 mt-2 text-lg">Eksplorasi wilayah desa melalui peta detail di bawah ini.</p>
                </div>
                <div class="flex gap-2">
                    <button @click="resetFilters()" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                        <i class="fa-solid fa-rotate-left mr-2"></i> Reset Filter
                    </button>
                </div>
            </div>

            <!-- Dashboard Map Layout -->
            <div class="flex flex-col lg:flex-row gap-6 h-[750px] relative overflow-hidden rounded-3xl border border-slate-200 shadow-sm bg-white">
                
                <!-- Sidebar Controls -->
                <div class="w-full lg:w-96 flex-shrink-0 flex flex-col h-[500px] lg:h-full border-b lg:border-b-0 lg:border-r border-slate-200 bg-white z-10">
                    <!-- Search Sticky -->
                    <div class="p-4 border-b border-slate-100">
                         <div class="relative">
                            <i class="fa-solid fa-search absolute left-4 top-3.5 text-slate-400"></i>
                            <input type="text" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-slate-700 font-medium placeholder:text-slate-400 bg-slate-50 transition" placeholder="Cari desa, jalan, atau lokasi..." x-model="searchQuery" @input.debounce.300ms="performSearch()">
                        </div>
                        <!-- Search Results -->
                        <div x-show="searchResults.length > 0" class="absolute left-4 right-4 mt-2 bg-white border border-slate-100 rounded-xl shadow-xl z-50 max-h-80 overflow-y-auto custom-scroll" x-cloak>
                            <template x-for="result in searchResults" :key="result.id || result.name">
                                <button @click="selectFeature(result)" class="w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                                        <i class="fa-solid" :class="getIconForFeature(result)"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-sm group-hover:text-blue-600 truncate" x-text="result.name"></p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-500 truncate" x-text="result.type || 'Lokasi'"></span>
                                            <span x-show="result.distance" class="text-[10px] bg-slate-100 px-1.5 rounded text-slate-400" x-text="result.distance + ' km'"></span>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Sidebar Tabs -->
                    <div class="px-4 pt-4 pb-2">
                        <div class="flex p-1.5 bg-slate-100/80 rounded-xl">
                            <button @click="activeTab = 'layers'" :class="activeTab === 'layers' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all duration-200">
                                <i class="fa-solid fa-layer-group mr-1.5"></i> Layers
                            </button>
                            <button @click="activeTab = 'places'" :class="activeTab === 'places' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all duration-200">
                                <i class="fa-solid fa-map-marker-alt mr-1.5"></i> Lokasi
                            </button>
                            <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all duration-200">
                                <i class="fa-solid fa-chart-pie mr-1.5"></i> Info
                            </button>
                        </div>
                    </div>

                    <!-- Content Area -->
                    <div class="flex-1 overflow-y-auto custom-scroll p-4">
                        
                        <!-- LAYERS TAB -->
                        <div x-show="activeTab === 'layers'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                            <div class="space-y-6">
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider pl-1">Peta Dasar</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <button @click="setBaseLayer('streets')" :class="currentBaseLayer === 'streets' ? 'ring-2 ring-blue-500 border-transparent bg-blue-50/50' : 'border-slate-200 hover:bg-slate-50'" class="p-3 rounded-xl border text-left transition relative overflow-hidden group">
                                            <div class="flex gap-2 items-center mb-1">
                                                <i class="fa-solid fa-map text-blue-500"></i>
                                                <span class="text-xs font-bold text-slate-700">Jalan</span>
                                            </div>
                                            <div class="h-1 w-full bg-slate-100 rounded-full mt-2 overflow-hidden">
                                                <div class="h-full bg-blue-500 w-3/4"></div>
                                            </div>
                                        </button>
                                        <button @click="setBaseLayer('satellite')" :class="currentBaseLayer === 'satellite' ? 'ring-2 ring-blue-500 border-transparent bg-blue-50/50' : 'border-slate-200 hover:bg-slate-50'" class="p-3 rounded-xl border text-left transition relative overflow-hidden group">
                                            <div class="flex gap-2 items-center mb-1">
                                                <i class="fa-solid fa-satellite text-purple-500"></i>
                                                <span class="text-xs font-bold text-slate-700">Satelit</span>
                                            </div>
                                            <div class="h-1 w-full bg-slate-100 rounded-full mt-2 overflow-hidden">
                                                <div class="h-full bg-slate-700 w-3/4"></div>
                                            </div>
                                        </button>
                                    </div>
                                </div>

                                <div class="h-px bg-slate-100"></div>

                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider pl-1">Data Spasial</h4>
                                    
                                    <div class="space-y-2">
                                        <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 transition cursor-pointer group select-none">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition">
                                                    <i class="fa-solid fa-draw-polygon"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-700">Batas Wilayah</p>
                                                    <p class="text-[10px] text-slate-500">Area administratif desa</p>
                                                </div>
                                            </div>
                                            <div class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" x-model="showBoundaries" @change="updateLayers()" class="sr-only peer">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                        </label>

                                        <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 transition cursor-pointer group select-none">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600 group-hover:scale-110 transition">
                                                    <i class="fa-solid fa-water"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-700">Infrastruktur</p>
                                                    <p class="text-[10px] text-slate-500">Jalan & Sungai</p>
                                                </div>
                                            </div>
                                            <div class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" x-model="showInfrastructures" @change="updateLayers()" class="sr-only peer">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                        </label>

                                        <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50/30 transition cursor-pointer group select-none">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition">
                                                    <i class="fa-solid fa-wheat"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-700">Penggunaan Lahan</p>
                                                    <p class="text-[10px] text-slate-500">Pertanian & Pemukiman</p>
                                                </div>
                                            </div>
                                            <div class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" x-model="showLandUses" @change="updateLayers()" class="sr-only peer">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LOCATIONS TAB -->
                        <div x-show="activeTab === 'places'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
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
                                        <div @click="selectPlace(place)" class="group bg-white border border-slate-200 rounded-xl p-3 hover:border-blue-300 hover:shadow-md transition cursor-pointer flex gap-3 items-start">
                                            <div class="flex-shrink-0">
                                                <template x-if="place.image_path">
                                                    <img :src="'{{ url('/') }}/' + place.image_path" class="w-12 h-12 rounded-lg object-cover bg-slate-100">
                                                </template>
                                                <template x-if="!place.image_path">
                                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-lg bg-slate-100 text-slate-400">
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
                                    <div x-show="visiblePlaces.length === 0" class="text-center py-8 text-slate-400">
                                        <i class="fa-solid fa-map-location-dot text-3xl mb-3 text-slate-300"></i>
                                        <p class="text-sm">Tidak ada lokasi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- INFO TAB -->
                        <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                            <div class="space-y-6">
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                                    <div class="flex items-start gap-4">
                                        <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                            <i class="fa-solid fa-circle-info text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-blue-800 mb-1">Statistik Desa</h3>
                                            <p class="text-xs text-blue-700/80 leading-relaxed">
                                                Rekapitulasi data geospasial yang tercatat dalam sistem Desa Mayong Lor.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalPlaces }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Titik Lokasi</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalInfrastructures }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Infrastruktur</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalBoundaries }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Wilayah RT/RW</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center hover:bg-slate-100 transition duration-300">
                                        <p class="text-2xl font-black text-slate-800">{{ $totalLandUses }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider mt-1">Area Lahan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Canvas & Controls -->
                <div class="flex-1 relative bg-slate-100">
                    <div id="map" class="absolute inset-0 z-0"></div>
                    
                    <!-- Floating Controls Dock (Bottom Right) -->
                    <div class="absolute bottom-6 right-6 z-[400] flex flex-col gap-2">
                        <button @click="locateUser()" class="w-10 h-10 bg-white rounded-xl shadow-lg text-slate-600 hover:text-blue-600 hover:bg-blue-50 transition flex items-center justify-center" title="Lokasi Saya">
                            <i class="fa-solid fa-crosshairs"></i>
                        </button>
                        <div class="flex flex-col bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden">
                            <button onclick="mapComponent().map.zoomIn()" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-50 border-b border-slate-100 transition">
                                <i class="fa-solid fa-plus text-sm"></i>
                            </button>
                            <button onclick="mapComponent().map.zoomOut()" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition">
                                <i class="fa-solid fa-minus text-sm"></i>
                            </button>
                        </div>
                        <button onclick="document.getElementById('map').requestFullscreen()" class="w-10 h-10 bg-white rounded-xl shadow-lg text-slate-600 hover:text-slate-900 transition flex items-center justify-center" title="Fullscreen">
                            <i class="fa-solid fa-expand"></i>
                        </button>
                    </div>

                    <!-- Slide-over Detail Panel -->
                    <div x-show="selectedFeature" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="absolute top-4 right-4 bottom-4 w-80 bg-white/95 backdrop-blur rounded-2xl shadow-2xl z-[500] border border-slate-100 flex flex-col overflow-hidden"
                         x-cloak>
                        
                        <!-- Header Image -->
                        <div class="h-40 bg-slate-200 relative shrink-0">
                            <template x-if="selectedFeature?.image_url">
                                <img :src="selectedFeature.image_url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedFeature?.image_url">
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fa-solid fa-image text-3xl"></i>
                                </div>
                            </template>
                            <button @click="selectedFeature = null" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur flex items-center justify-center transition">
                                <i class="fa-solid fa-times"></i>
                            </button>
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                                <h3 class="text-white font-bold text-lg leading-tight text-shadow" x-text="selectedFeature?.name"></h3>
                                <p class="text-white/80 text-xs" x-text="selectedFeature?.type"></p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto custom-scroll p-4 space-y-4">
                            <p class="text-sm text-slate-600 leading-relaxed" x-text="selectedFeature?.description || 'Tidak ada deskripsi tersedia.'"></p>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <template x-if="selectedFeature?.area">
                                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                        <p class="text-[10px] uppercase text-slate-400 font-bold">Luas</p>
                                        <p class="font-bold text-slate-700"><span x-text="selectedFeature.area"></span> ha</p>
                                    </div>
                                </template>
                                <template x-if="selectedFeature?.owner">
                                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                        <p class="text-[10px] uppercase text-slate-400 font-bold">Pemilik</p>
                                        <p class="font-bold text-slate-700" x-text="selectedFeature.owner"></p>
                                    </div>
                                </template>
                            </div>

                            <button @click="zoomToFeature(selectedFeature)" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/30 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-location-dot"></i> Lihat di Peta
                            </button>
                        </div>
                    </div>

                    <!-- Loading Overlay -->
                    <div x-show="loading" class="absolute inset-0 bg-white/80 backdrop-blur-[2px] z-[1000] flex items-center justify-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-widest animate-pulse">Memuat Data...</span>
                        </div>
                    </div>
                </div>

            </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Sistem Informasi Geografis</p>
                        <p class="text-xs text-slate-500">Desa Mayong Lor &copy; {{ date('Y') }}</p>
                    </div>
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-400 hover:text-blue-600 transition"><i class="fa-brands fa-facebook text-xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-pink-600 transition"><i class="fa-brands fa-instagram text-xl"></i></a>
                    <a href="#" class="text-slate-400 hover:text-red-600 transition"><i class="fa-brands fa-youtube text-xl"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JS -->
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
                
                // Data
                categories: @json($categories),
                selectedCategories: [],
                
                // Toggles
                showBoundaries: true,
                showInfrastructures: true,
                showLandUses: true,

                // Computed Data
                allPlaces: [],
                geoFeatures: [],
                
                // Search & Selection
                searchQuery: '',
                searchResults: [],
                selectedFeature: null,
                userMarker: null,

                // Layers References
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
                    
                    // Define Base Layers
                    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                        maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });
                    const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                        maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });

                    this.baseLayers = {
                        'streets': googleStreets,
                        'satellite': googleSatellite
                    };

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

                        // Store raw features
                        this.geoFeatures = places.features || []; // Places with geometry
                        this.allPlaces = places.features || [];
                        
                        // Load Layers
                        this.loadBoundaries(boundaries.features || []);
                        this.loadInfrastructures(infrastructures.features || []);
                        this.loadLandUses(landUses.features || []);
                        
                        // Initial Markers Render
                        this.updateMapMarkers();

                    } catch (e) {
                        console.error('Error loading data:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                // --- Layer Rendering ---

                updateLayers() {
                    // Logic to re-render based on toggles is handled by individual load functions interacting with map
                    // But here we need to re-fetch or re-toggle?
                    // Actually, if data is already loaded, we just toggle visibility.
                    // But our current architecture re-fetches in the old code. 
                    // Let's improve: we should store the data and just toggle layers.
                    // For now, to be safe and simple, let's re-fetch which ensures sync.
                    this.fetchAllData(); 
                },

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
                        style: f => {
                            const type = f.properties.type;
                            const color = type === 'river' ? '#3b82f6' : '#64748b'; // Blue for river, gray for road
                            return { color: color, weight: type === 'river' ? 4 : 3, opacity: 0.8 };
                        },
                        onEachFeature: (f, l) => {
                            l.on('click', (e) => {
                                L.DomEvent.stop(e);
                                this.selectFeature({...f.properties, type: 'Infrastruktur'});
                            });
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
                            l.on('click', (e) => {
                                L.DomEvent.stop(e);
                                this.selectFeature({...f.properties, type: 'Penggunaan Lahan'});
                            });
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
                        
                        marker.on('click', () => {
                            this.selectPlace({...p, latitude: lat, longitude: lng});
                        });
                        
                        marker.addTo(this.map);
                        this.markers.push(marker);
                    });
                },

                // --- Interaction ---

                performSearch() {
                    if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                    const q = this.searchQuery.toLowerCase();
                    
                    // Search across all loaded data (we'd need to store boundaries/infra raw data to search them perfectly, but currently we only stored places features in allPlaces)
                    // Let's rely on what we have. Ideally we should store all boundaries/infras in arrays too.
                    // For now, let's just search places as that's safe.
                    // Ideally:
                    // this.searchResults = [...placesMatches, ...boundaryMatches, ...infraMatches];
                    
                    const matches = this.allPlaces.filter(p => p.properties.name.toLowerCase().includes(q))
                        .map(p => ({ 
                            ...p.properties, 
                            coords: [...p.geometry.coordinates].reverse(),
                            type: 'Lokasi',
                            feature: p
                        }));
                        
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
                         // Simplify: if polygon, get bounds
                         const layer = L.geoJSON(feature);
                         this.map.fitBounds(layer.getBounds(), { padding: [50, 50] });
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
