<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Geografis Desa Mayong Lor</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        #map { height: 600px; z-index: 1; border-radius: 1.5rem; }
        .leaflet-popup-content-wrapper { border-radius: 1rem; border: none; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .leaflet-popup-content { margin: 0; width: 300px !important; }
        
        /* Custom Scrollbar */
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .custom-marker { display: flex; align-items: center; justify-content: center; transition: transform 0.2s; }
        .custom-marker:hover { transform: scale(1.1); }

        /* Leaflet Control Customization */
        .leaflet-control-zoom { border: none !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; border-radius: 0.75rem !important; overflow: hidden; }
        .leaflet-control-zoom a { background: white !important; color: #1e293b !important; border-bottom: 1px solid #f1f5f9 !important; }
        .leaflet-control-zoom a:hover { background: #f8fafc !important; }
    </style>
</head>
<body class="antialiased text-slate-600 bg-white">

    <!-- Navigation -->
    <!-- Navigation -->
    <header class="absolute inset-x-0 top-0 z-50 transition-all duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 text-white shadow-lg">
                        <i class="fa-solid fa-leaf text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-green-300">Desa Mayong Lor</p>
                        <p class="text-lg font-bold text-white leading-none">GIS Portal</p>
                    </div>
                </div>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-1 bg-white/10 backdrop-blur-md px-2 py-1.5 rounded-full border border-white/20">
                    <a href="#hero" class="px-5 py-2 text-sm font-medium text-white rounded-full hover:bg-white/20 transition">Beranda</a>
                    <a href="#about" class="px-5 py-2 text-sm font-medium text-white/80 rounded-full hover:text-white hover:bg-white/20 transition">Profil</a>
                    <a href="#stats" class="px-5 py-2 text-sm font-medium text-white/80 rounded-full hover:text-white hover:bg-white/20 transition">Statistik</a>
                    <a href="#map-section" class="px-5 py-2 text-sm font-medium text-white/80 rounded-full hover:text-white hover:bg-white/20 transition">Peta Digital</a>
                    <a href="#contact" class="px-5 py-2 text-sm font-medium text-white/80 rounded-full hover:text-white hover:bg-white/20 transition">Kontak</a>
                </nav>

                <!-- Action Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-white/90 hover:text-white transition">
                        Masuk Admin
                    </a>
                    <a href="{{ route('explore.map') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-slate-900 transition-all bg-white rounded-full hover:bg-green-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-transparent">
                        Jelajahi Peta
                        <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="relative h-screen min-h-[700px] flex items-center justify-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="/images/balaidesa.jpeg" alt="Desa Mayong Lor" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-900/80"></div>
        </div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white text-sm font-medium tracking-wide mb-8 animate-fade-in-up">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                Sistem Informasi Geografis Desa Cerdas
            </div>

            <!-- Main Title -->
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-tight mb-6 drop-shadow-lg">
                Jelajahi Potensi <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-300 to-emerald-200">
                    Desa Mayong Lor
                </span>
            </h1>

            <p class="mt-6 text-xl text-slate-200 max-w-2xl mx-auto leading-relaxed drop-shadow-md">
                Platform pemetaan digital terintegrasi untuk transparansi data, pembangunan infrastruktur, dan pelayanan publik yang lebih baik.
            </p>

            <!-- Search Bar -->
            <div class="mt-12 max-w-3xl mx-auto" x-data="{ query: '' }">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-green-400 to-emerald-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative flex items-center bg-white rounded-full p-2 shadow-2xl">
                        <div class="flex-shrink-0 pl-4 pr-2 text-slate-400">
                            <i class="fa-solid fa-search text-lg"></i>
                        </div>
                        <input type="text" 
                               x-model="query"
                               @keydown.enter="window.location.href = '{{ route('explore.map') }}?q=' + query"
                               class="flex-1 bg-transparent border-none focus:ring-0 text-slate-800 placeholder-slate-400 text-lg h-12"
                               placeholder="Cari lokasi, fasilitas, atau alamat...">
                        <button @click="window.location.href = '{{ route('explore.map') }}?q=' + query" 
                                class="flex-shrink-0 bg-green-600 text-white px-8 py-3 rounded-full font-bold hover:bg-green-700 transition shadow-lg hover:shadow-green-600/30">
                            Cari
                        </button>
                    </div>
                </div>
                
                <!-- Quick Tags -->
                <div class="mt-6 flex flex-wrap justify-center gap-3 text-sm text-white/80">
                    <span>Pencarian Populer:</span>
                    <a href="{{ route('explore.map') }}?q=Balai Desa" class="hover:text-white hover:underline decoration-green-400 underline-offset-4 transition">Balai Desa</a>
                    <span class="text-white/30">•</span>
                    <a href="{{ route('explore.map') }}?q=Masjid" class="hover:text-white hover:underline decoration-green-400 underline-offset-4 transition">Masjid</a>
                    <span class="text-white/30">•</span>
                    <a href="{{ route('explore.map') }}?q=Sekolah" class="hover:text-white hover:underline decoration-green-400 underline-offset-4 transition">Sekolah</a>
                </div>
            </div>

            <!-- Glass Stats -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center hover:bg-white/20 transition cursor-default">
                    <p class="text-3xl font-bold text-white">{{ $totalPlaces }}+</p>
                    <p class="text-xs text-green-200 uppercase tracking-wider mt-1">Lokasi</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center hover:bg-white/20 transition cursor-default">
                    <p class="text-3xl font-bold text-white">{{ $totalCategories }}</p>
                    <p class="text-xs text-green-200 uppercase tracking-wider mt-1">Kategori</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center hover:bg-white/20 transition cursor-default">
                    <p class="text-3xl font-bold text-white">100%</p>
                    <p class="text-xs text-green-200 uppercase tracking-wider mt-1">Akurat</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-2xl p-4 text-center hover:bg-white/20 transition cursor-default">
                    <p class="text-3xl font-bold text-white">24/7</p>
                    <p class="text-xs text-green-200 uppercase tracking-wider mt-1">Akses</p>
                </div>
            </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce text-white/50">
            <i class="fa-solid fa-chevron-down"></i>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1 relative">
                    <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl shadow-slate-200 border-4 border-white">
                        <img src="/images/balaidesa.jpeg" alt="Profil Desa" class="w-full h-[500px] object-cover">
                    </div>
                    <!-- Experience Badge -->
                    <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-[2rem] shadow-xl max-w-xs">
                        <p class="text-4xl font-bold text-green-600 mb-1">24/7</p>
                        <p class="text-slate-600 font-medium leading-tight">Akses data informasi publik secara online.</p>
                    </div>
                </div>
                
                <div class="order-1 lg:order-2">
                    <p class="text-green-600 font-bold uppercase tracking-wider text-sm mb-3">Tentang Kami</p>
                    <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 mb-6">Mewujudkan Desa Cerdas Berbasis Data</h2>
                    <p class="text-slate-600 text-lg mb-6 leading-relaxed">
                        Sistem Informasi Geografis (SIG) Desa Mayong Lor hadir sebagai wujud transparansi dan modernisasi pelayanan publik. Kami memetakan setiap potensi dan aset desa untuk memudahkan perencanaan pembangunan dan akses informasi bagi warga.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-layer-group text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Data Terintegrasi</h3>
                                <p class="text-slate-600 mt-2">Menyatukan data kependudukan, infrastruktur, dan pertanahan dalam satu peta digital.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-yellow-600">
                                <i class="fa-solid fa-chart-pie text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Analisis Wilayah</h3>
                                <p class="text-slate-600 mt-2">Membantu pengambilan keputusan berbasis bukti untuk pembangunan desa yang tepat sasaran.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats / Categories Section -->
    <section id="stats" class="py-24 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <p class="text-green-600 font-bold uppercase tracking-wider text-sm mb-3">Statistik & Data</p>
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900">Sebaran Fasilitas Desa</h2>
                <p class="mt-4 text-lg text-slate-600">
                    Gambaran umum jumlah fasilitas dan infrastruktur yang telah terpetakan dalam sistem kami.
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-8">
                @foreach($categories as $category)
                @if($category->places_count > 0)
                <div class="group relative bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 w-full md:w-[calc(50%-1rem)] lg:w-[calc(25%-1.5rem)] min-w-[280px]">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }} text-6xl" style="color: {{ $category->color }}"></i>
                    </div>
                    
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-xl mb-6 shadow-lg" style="background-color: {{ $category->color }}">
                        <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }}"></i>
                    </div>
                    
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ $category->places_count }}</h3>
                    <p class="text-lg font-bold text-slate-800 mb-2">{{ $category->name }}</p>
                    <p class="text-sm text-slate-500">Titik lokasi terverifikasi</p>
                    
                    <a href="{{ route('explore.map', ['category' => $category->id]) }}" class="mt-6 pt-6 border-t border-slate-100 flex items-center text-sm font-semibold text-slate-400 group-hover:text-green-600 transition-colors">
                        Lihat Detail <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section id="map-section" class="py-24 bg-slate-50 relative overflow-hidden" x-data="mapComponent()">
        <!-- Background Blobs -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-0 w-[500px] h-[500px] bg-green-200/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-blue-200/20 rounded-full blur-3xl"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white shadow-sm border border-slate-100 text-green-700 text-sm font-bold mb-6">
                    <i class="fa-solid fa-satellite-dish"></i> Live GIS Data
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900">Peta Digital Interaktif</h2>
                <p class="mt-4 text-lg text-slate-600">
                    Gunakan fitur filter dan pencarian untuk menemukan lokasi spesifik di Desa Mayong Lor.
                </p>
            </div>
            <div class="grid lg:grid-cols-[350px_1fr] gap-8 items-start">
                <!-- Sidebar Controls -->
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 h-full flex flex-col lg:sticky lg:top-24">
                    <!-- Search -->
                    <div class="mb-8">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Pencarian</label>
                        <div class="relative">
                            <input type="text" 
                                   x-model="searchQuery" 
                                   @input="performSearch()"
                                   placeholder="Cari lokasi..."
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all placeholder-slate-400">
                            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        
                        <!-- Search Results -->
                        <div x-show="searchResults.length > 0" 
                             x-cloak
                             class="mt-2 max-h-60 overflow-y-auto custom-scroll bg-white border border-slate-100 rounded-xl shadow-lg absolute w-full z-20 left-0">
                            <template x-for="result in searchResults" :key="result.id">
                                <button @click="zoomToResult(result)" 
                                        class="w-full text-left px-4 py-3 hover:bg-green-50 transition border-b border-slate-50 last:border-0 group">
                                    <p class="font-bold text-slate-800 text-sm group-hover:text-green-700" x-text="result.name"></p>
                                    <p class="text-xs text-slate-500" x-text="result.type"></p>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Layers -->
                    <div class="mb-8">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Layer Peta</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer transition">
                                <input type="checkbox" x-model="showBoundaries" @change="updateLayers()" class="w-5 h-5 text-green-600 rounded border-slate-300 focus:ring-green-500">
                                <span class="ml-3 text-sm font-medium text-slate-700">Batas Wilayah</span>
                            </label>
                            <label class="flex items-center p-3 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer transition">
                                <input type="checkbox" x-model="showInfrastructures" @change="updateLayers()" class="w-5 h-5 text-green-600 rounded border-slate-300 focus:ring-green-500">
                                <span class="ml-3 text-sm font-medium text-slate-700">Jaringan Jalan & Sungai</span>
                            </label>
                            <label class="flex items-center p-3 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer transition">
                                <input type="checkbox" x-model="showLandUses" @change="updateLayers()" class="w-5 h-5 text-green-600 rounded border-slate-300 focus:ring-green-500">
                                <span class="ml-3 text-sm font-medium text-slate-700">Penggunaan Lahan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="flex-1 flex flex-col min-h-0">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Filter Kategori</label>
                            <button @click="resetFilters()" class="text-xs font-bold text-green-600 hover:text-green-700">Reset</button>
                        </div>
                        <div class="space-y-2 flex-1 overflow-y-auto custom-scroll pr-2">
                            <template x-for="category in categories" :key="category.id">
                                <label x-show="category.places_count > 0" class="flex items-center p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition group">
                                    <input type="checkbox" 
                                           :value="category.id" 
                                           x-model="selectedCategories" 
                                           class="w-4 h-4 text-green-600 rounded border-slate-300 focus:ring-green-500">
                                    <span class="w-3 h-3 rounded-full ml-3" :style="`background-color: ${category.color}`"></span>
                                    <span class="ml-2 flex-1 text-sm font-medium text-slate-600 group-hover:text-slate-900" x-text="category.name"></span>
                                    <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md" x-text="category.places_count"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="bg-white p-2 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                    <div id="map" class="w-full h-[600px] sm:h-[700px] rounded-[1.5rem] z-0"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contact" class="py-24 bg-white">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="relative bg-green-600 rounded-[3rem] overflow-hidden px-8 py-16 md:px-16 md:py-20 text-center">
                <!-- Decor -->
                <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none opacity-20">
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-yellow-300 rounded-full blur-3xl"></div>
                </div>

                <div class="relative z-10">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">Butuh Informasi Lebih Lanjut?</h2>
                    <p class="text-green-100 text-lg mb-10 max-w-2xl mx-auto">
                        Silakan hubungi perangkat desa atau datang langsung ke Balai Desa Mayong Lor untuk pelayanan administrasi dan informasi lainnya.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="mailto:desa@mayonglor.id" class="w-full sm:w-auto px-8 py-4 bg-white text-green-700 font-bold rounded-full hover:bg-green-50 transition shadow-lg">
                            Hubungi Kami
                        </a>
                        <a href="https://goo.gl/maps/..." target="_blank" class="w-full sm:w-auto px-8 py-4 bg-green-700 text-white font-bold rounded-full hover:bg-green-800 transition border border-green-500">
                            Lokasi Kantor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-green-600 text-white">
                            <i class="fa-solid fa-leaf"></i>
                        </div>
                        <span class="text-xl font-bold text-white">Desa Mayong Lor</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed max-w-sm">
                        Sistem Informasi Geografis untuk pemetaan potensi, infrastruktur, dan fasilitas publik Desa Mayong Lor secara digital dan transparan.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-6">Navigasi</h4>
                    <ul class="space-y-3">
                        <li><a href="#hero" class="hover:text-green-400 transition">Beranda</a></li>
                        <li><a href="#about" class="hover:text-green-400 transition">Profil Desa</a></li>
                        <li><a href="#stats" class="hover:text-green-400 transition">Data Statistik</a></li>
                        <li><a href="#map-section" class="hover:text-green-400 transition">Peta Digital</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6">Kontak</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-green-500"></i>
                            <span>Jl. Raya Mayong - Jepara, Mayong Lor, Kec. Mayong, Kabupaten Jepara, Jawa Tengah</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-green-500"></i>
                            <span>admin@mayonglor.desa.id</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} Pemerintah Desa Mayong Lor. All rights reserved.</p>
                <div class="flex gap-4 text-lg text-slate-500">
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        function mapComponent() {
            return {
                map: null,
                markers: [],
                boundaries: [],
                boundariesLayer: null,
                infrastructures: [],
                infrastructuresLayer: null,
                landUses: [],
                landUsesLayer: null,
                categories: @json($categories),
                selectedCategories: [],
                showBoundaries: true,
                showInfrastructures: true,
                showLandUses: true,
                searchQuery: '',
                searchResults: [],
                allPlaces: [],
                geoJsonUrl: '{{ route('places.geojson') }}',
                boundariesUrl: '{{ route('boundaries.geojson') }}',
                infrastructuresUrl: '{{ route('infrastructures.geojson') }}',
                landUsesUrl: '{{ route('land_uses.geojson') }}',
                geoFeatures: [],

                init() {
                    // Initialize selected categories (all selected by default)
                    this.selectedCategories = this.categories.map(c => c.id);

                    // Watch for changes in selectedCategories
                    this.$watch('selectedCategories', () => {
                        this.updateMapMarkers();
                    });

                    // Initialize Map
                    const center = [-6.7289, 110.7485]; 
                    
                    this.map = L.map('map', {
                        zoomControl: false,
                        attributionControl: false
                    }).setView(center, 14);

                    // Custom Zoom Control
                    L.control.zoom({
                        position: 'bottomright'
                    }).addTo(this.map);

                    // Google Maps Layers
                    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });
                    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });
                    const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });
                    const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
                        maxNativeZoom: 20,
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                    });

                    googleStreets.addTo(this.map);

                    const baseLayers = {
                        "Streets": googleStreets,
                        "Hybrid": googleHybrid,
                        "Satellite": googleSatellite,
                        "Terrain": googleTerrain
                    };

                    L.control.layers(baseLayers, null, { position: 'bottomright' }).addTo(this.map);

                    this.fetchAllData();
                },

                async fetchAllData() {
                    try {
                        // Fetch places
                        const placesResponse = await fetch(this.geoJsonUrl + '?t=' + Date.now());
                        const placesData = await placesResponse.json();
                        this.geoFeatures = placesData.features || [];
                        this.allPlaces = placesData.features || [];
                        this.updateMapMarkers();

                        // Fetch boundaries
                        const boundariesResponse = await fetch(this.boundariesUrl + '?t=' + Date.now());
                        const boundariesData = await boundariesResponse.json();
                        this.loadBoundaries(boundariesData.features || []);

                        // Fetch infrastructures
                        const infrastructuresResponse = await fetch(this.infrastructuresUrl + '?t=' + Date.now());
                        const infrastructuresData = await infrastructuresResponse.json();
                        this.loadInfrastructures(infrastructuresData.features || []);

                        // Fetch land uses
                        const landUsesResponse = await fetch(this.landUsesUrl + '?t=' + Date.now());
                        const landUsesData = await landUsesResponse.json();
                        this.loadLandUses(landUsesData.features || []);
                    } catch (error) {
                        console.error('Gagal memuat data peta', error);
                    }
                },

                loadBoundaries(features) {
                    // Clear existing boundaries
                    if (this.boundariesLayer) {
                        this.map.removeLayer(this.boundariesLayer);
                    }
                    this.boundaries = [];

                    if (!this.showBoundaries) return;

                    // Create new layer group
                    this.boundariesLayer = L.featureGroup();
                    
                    features.forEach(feature => {
                        const layer = L.geoJSON(feature, {
                            style: {
                                color: '#16a34a', // green-600
                                weight: 2,
                                fillColor: '#16a34a',
                                fillOpacity: 0.1
                            },
                            onEachFeature: (feature, layer) => {
                                const props = feature.properties;
                                const popupContent = `
                                    <div class="p-4">
                                        <h3 class="font-bold text-slate-900 mb-1 text-lg">${props.name}</h3>
                                        <div class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded mb-2">Batas Wilayah</div>
                                        <p class="text-sm text-slate-600 mb-1"><span class="font-semibold">Tipe:</span> ${props.type}</p>
                                        ${props.area_hectares ? `<p class="text-sm text-slate-600"><span class="font-semibold">Luas:</span> ${props.area_hectares} ha</p>` : ''}
                                        ${props.description ? `<p class="text-sm text-slate-600 mt-2 pt-2 border-t border-slate-100">${props.description}</p>` : ''}
                                    </div>
                                `;
                                layer.bindPopup(popupContent);
                            }
                        });
                        this.boundariesLayer.addLayer(layer);
                        this.boundaries.push(layer);
                    });
                    
                    this.boundariesLayer.addTo(this.map);

                    // Fit bounds if boundaries exist
                    if (this.boundaries.length > 0) {
                        this.map.fitBounds(this.boundariesLayer.getBounds(), { padding: [50, 50] });
                    }
                },

                loadInfrastructures(features) {
                    // Clear existing infrastructures
                    if (this.infrastructuresLayer) {
                        this.map.removeLayer(this.infrastructuresLayer);
                    }
                    this.infrastructures = [];

                    if (!this.showInfrastructures) return;

                    // Create new layer group
                    this.infrastructuresLayer = L.layerGroup();

                    features.forEach(feature => {
                        const props = feature.properties;
                        const color = props.type === 'river' ? '#3b82f6' : 
                                     props.type === 'road' ? '#475569' : 
                                     props.type === 'irrigation' ? '#06b6d4' : '#8b5cf6';
                        
                        const layer = L.geoJSON(feature, {
                            style: {
                                color: color,
                                weight: props.type === 'road' ? 4 : 3,
                                opacity: 0.8
                            },
                            onEachFeature: (feature, layer) => {
                                const popupContent = `
                                    <div class="p-4">
                                        <h3 class="font-bold text-slate-900 mb-1 text-lg">${props.name}</h3>
                                        <div class="inline-block px-2 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded mb-2">Infrastruktur</div>
                                        <p class="text-sm text-slate-600 mb-1"><span class="font-semibold">Tipe:</span> ${props.type}</p>
                                        ${props.length_meters ? `<p class="text-sm text-slate-600"><span class="font-semibold">Panjang:</span> ${props.length_meters} m</p>` : ''}
                                        ${props.condition ? `<p class="text-sm text-slate-600"><span class="font-semibold">Kondisi:</span> ${props.condition}</p>` : ''}
                                        ${props.description ? `<p class="text-sm text-slate-600 mt-2 pt-2 border-t border-slate-100">${props.description}</p>` : ''}
                                    </div>
                                `;
                                layer.bindPopup(popupContent);
                            }
                        });
                        this.infrastructuresLayer.addLayer(layer);
                        this.infrastructures.push(layer);
                    });
                    
                    this.infrastructuresLayer.addTo(this.map);
                },

                loadLandUses(features) {
                    // Clear existing land uses
                    if (this.landUsesLayer) {
                        this.map.removeLayer(this.landUsesLayer);
                    }
                    this.landUses = [];

                    if (!this.showLandUses) return;

                    // Create new layer group
                    this.landUsesLayer = L.layerGroup();

                    features.forEach(feature => {
                        const props = feature.properties;
                        const color = props.type === 'rice_field' ? '#fbbf24' : 
                                     props.type === 'plantation' ? '#84cc16' : 
                                     props.type === 'forest' ? '#059669' : '#f59e0b';
                        
                        const layer = L.geoJSON(feature, {
                            style: {
                                color: color,
                                weight: 2,
                                fillColor: color,
                                fillOpacity: 0.3
                            },
                            onEachFeature: (feature, layer) => {
                                const popupContent = `
                                    <div class="p-4">
                                        <h3 class="font-bold text-slate-900 mb-1 text-lg">${props.name}</h3>
                                        <div class="inline-block px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded mb-2">Penggunaan Lahan</div>
                                        <p class="text-sm text-slate-600 mb-1"><span class="font-semibold">Tipe:</span> ${props.type}</p>
                                        ${props.area_hectares ? `<p class="text-sm text-slate-600"><span class="font-semibold">Luas:</span> ${props.area_hectares} ha</p>` : ''}
                                        ${props.owner ? `<p class="text-sm text-slate-600"><span class="font-semibold">Pemilik:</span> ${props.owner}</p>` : ''}
                                        ${props.description ? `<p class="text-sm text-slate-600 mt-2 pt-2 border-t border-slate-100">${props.description}</p>` : ''}
                                    </div>
                                `;
                                layer.bindPopup(popupContent);
                            }
                        });
                        this.landUsesLayer.addLayer(layer);
                        this.landUses.push(layer);
                    });
                    
                    this.landUsesLayer.addTo(this.map);
                },

                updateLayers() {
                    // Reload all layers
                    this.fetchAllData();
                },

                updateMapMarkers() {
                    // Clear existing markers
                    this.markers.forEach(marker => this.map.removeLayer(marker));
                    this.markers = [];

                    const filteredFeatures = this.geoFeatures.filter(feature => {
                        const category = feature.properties && feature.properties.category;
                        const categoryId = category ? category.id : null;
                        return this.selectedCategories.some(id => id == categoryId);
                    });

                    filteredFeatures.forEach(feature => {
                        const [lng, lat] = feature.geometry.coordinates;
                        const place = feature.properties;
                        
                        const iconHtml = `
                            <div class="w-10 h-10 rounded-full border-4 border-white shadow-lg flex items-center justify-center text-white text-sm" style="background-color: ${place.category ? place.category.color : '#3b82f6'}">
                                <i class="${place.category && place.category.icon_class ? place.category.icon_class : 'fa-solid fa-map-marker-alt'}"></i>
                            </div>
                        `;
                        
                        const icon = L.divIcon({
                            html: iconHtml,
                            className: 'custom-marker',
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        });

                        const marker = L.marker([lat, lng], { icon: icon });
                        
                        const popupContent = `
                            <div class="overflow-hidden rounded-xl">
                                ${place.image_url ? `<img src="${place.image_url}" class="w-full h-40 object-cover">` : ''}
                                <div class="p-4 bg-white">
                                    <span class="text-xs font-bold uppercase tracking-wider text-green-600 mb-1 block">${place.category ? place.category.name : ''}</span>
                                    <h3 class="text-lg font-bold text-slate-900 mb-2 leading-tight">${place.name}</h3>
                                    <p class="text-sm text-slate-600 mb-0 line-clamp-3">${place.description || 'Tidak ada deskripsi.'}</p>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent, {
                            maxWidth: 300,
                            className: 'custom-popup'
                        });

                        marker.addTo(this.map);
                        this.markers.push(marker);
                    });
                },

                performSearch() {
                    if (!this.searchQuery || this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    const query = this.searchQuery.toLowerCase();
                    this.searchResults = [];

                    // Search in places
                    this.allPlaces.forEach(place => {
                        const name = (place.properties.name || '').toLowerCase();
                        const desc = (place.properties.description || '').toLowerCase();
                        const catName = (place.properties.category?.name || '').toLowerCase();
                        
                        if (name.includes(query) || desc.includes(query) || catName.includes(query)) {
                            this.searchResults.push({
                                id: place.properties.id,
                                name: place.properties.name,
                                type: place.properties.category?.name || 'Lokasi',
                                coordinates: place.geometry.coordinates,
                                feature: place
                            });
                        }
                    });
                },

                zoomToResult(result) {
                    const [lng, lat] = result.coordinates;
                    this.map.flyTo([lat, lng], 18, {
                        duration: 1.5
                    });
                    this.searchResults = [];
                    this.searchQuery = result.name;
                    
                    // Find and open popup
                    // Note: This is a bit simplified, ideally we'd track markers by ID
                }
            }
        }
    </script>
</body>
</html>
