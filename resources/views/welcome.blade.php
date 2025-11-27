<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Geografis Desa Mayong Lor</title>
    
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
        #map { height: 600px; z-index: 1; }
        .leaflet-popup-content-wrapper { border-radius: 0.5rem; }
        .leaflet-popup-content { margin: 0; width: 300px !important; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 999px; }
        .glass-card { background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); }
    </style>
</head>
<body class="antialiased font-sans text-gray-800 bg-slate-950">

    <!-- Navigation -->
    <header class="fixed inset-x-0 top-0 z-40">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mt-6 rounded-2xl bg-white/80 backdrop-blur border border-white/50 shadow-lg shadow-black/5">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white font-bold">ML</span>
                        <div>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Geographic Insight</p>
                            <p class="text-base font-semibold text-slate-900">Desa Mayong Lor</p>
                        </div>
                    </div>
                    <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-600">
                        <a href="#hero" class="hover:text-slate-900 transition">Beranda</a>
                        <a href="#about" class="hover:text-slate-900 transition">Profil</a>
                        <a href="#stats" class="hover:text-slate-900 transition">Statistik</a>
                        <a href="#map-section" class="hover:text-slate-900 transition">Peta</a>
                        <a href="#contact" class="hover:text-slate-900 transition">Kontak</a>
                    </nav>
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-600 transition">Login Admin</a>
                        <a href="{{ route('explore.map') }}" class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 hover:-translate-y-0.5 transition">
                            Jelajahi Peta
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="relative min-h-screen flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Desa Mayong Lor" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-slate-900/60"></div>
        </div>
        
        <div class="relative z-10 w-full">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
                <div class="grid lg:grid-cols-[1.3fr_0.7fr] gap-10 items-center text-white">
                    <div>
                        <p class="inline-flex items-center gap-2 text-xs uppercase tracking-[0.3em] text-blue-300">
                            <span class="h-px w-6 bg-blue-300"></span>
                            Smart Village Map
                        </p>
                        <h1 class="mt-6 text-4xl sm:text-6xl lg:text-7xl font-bold leading-tight tracking-tight text-white">
                            Digital Twin Desa <span class="text-blue-300">Mayong Lor</span>
                        </h1>
                        <p class="mt-6 text-lg text-slate-200 max-w-2xl">
                            Temukan potensi ekonomi, fasilitas umum, dan cerita warga desa melalui peta interaktif yang selalu diperbarui. Dibangun dengan data yang akurat dan visual yang imersif.
                        </p>
                        <div class="mt-10 flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('explore.map') }}" class="inline-flex items-center justify-center gap-3 rounded-full bg-blue-500 px-8 py-4 text-lg font-semibold text-white shadow-xl shadow-blue-600/30 hover:bg-blue-400 transition">
                                Jelajahi Peta
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </a>
                            <a href="#about" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/40 px-8 py-4 text-lg font-semibold text-white hover:bg-white/10 transition">
                                Lihat Profil Desa
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div class="mt-12 grid grid-cols-2 gap-8 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
                                <p class="text-3xl font-bold text-white">32+</p>
                                <p class="mt-2 text-sm text-slate-200">Fasilitas Umum Terdata</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
                                <p class="text-3xl font-bold text-white">7</p>
                                <p class="mt-2 text-sm text-slate-200">Layer Kategori Prioritas</p>
                            </div>
                            <div class="rounded-2xl border border-white/20 bg-white/5 p-5">
                                <p class="text-3xl font-bold text-white">Realtime</p>
                                <p class="mt-2 text-sm text-slate-200">Pembaruan Data Lapangan</p>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card rounded-3xl border border-white/20 bg-white/5 p-6 backdrop-blur">
                        <div class="space-y-5">
                            <div class="flex items-center justify-between">
                                <p class="text-sm uppercase tracking-[0.3em] text-blue-200">Insight Visual</p>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white">Live Data</span>
                            </div>
                            <div class="rounded-2xl bg-slate-950/50 border border-white/10 p-4">
                                <div class="flex items-center justify-between text-xs uppercase tracking-wide text-slate-300">
                                    <span>Kategori Aktif</span>
                                    <span>{{ $categories->count() }} Kategori</span>
                                </div>
                                <div class="mt-4 space-y-3">
                                    @foreach($categories->take(4) as $category)
                                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-3 py-2">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-white text-lg" style="background-color: {{ $category->color }}">
                                                    <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }}"></i>
                                                </span>
                                                <div>
                                                    <p class="text-sm font-semibold text-white">{{ $category->name }}</p>
                                                    <p class="text-xs text-slate-300">Layer tematik</p>
                                                </div>
                                            </div>
                                            <span class="text-sm font-semibold text-white">{{ $category->places_count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="rounded-2xl bg-gradient-to-r from-blue-500 to-cyan-400 p-5 text-slate-900 shadow-xl shadow-blue-900/20">
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/80">Insight Harian</p>
                                <p class="mt-3 text-2xl font-bold text-white">Peta Desa Berbasis Bukti</p>
                                <p class="mt-2 text-sm text-white/90">Semua titik koordinat diverifikasi langsung oleh perangkat desa.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section id="about" class="relative bg-white py-24">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-slate-950 via-slate-900 to-white opacity-40"></div>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-12 items-center">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-600">Profil Desa</p>
                    <h2 class="mt-4 text-4xl font-bold text-slate-900">Eksplorasi Data, Narasi, dan Potensi Desa Mayong Lor</h2>
                    <p class="mt-6 text-lg text-slate-600">
                        Sistem Informasi Geografis ini menyatukan aset desa dalam satu tampilan terpadu. Mulai dari fasilitas pendidikan, sentra UMKM, hingga ruang terbuka hijau—semuanya memiliki data spasial lengkap yang mudah diakses.
                    </p>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 mb-4">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Layer Tematik</h3>
                            <p class="mt-2 text-sm text-slate-600">Kategori lokasi dibuat tematik sehingga memudahkan analisis lintas sektor.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 mb-4">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Realtime Insight</h3>
                            <p class="mt-2 text-sm text-slate-600">Dashboard admin menampilkan data terbaru dengan verifikasi perangkat desa.</p>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-blue-500 to-cyan-400 blur-lg opacity-30"></div>
                    <div class="relative rounded-3xl border border-slate-100 bg-white/90 shadow-2xl overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Desa Mayong Lor" class="h-64 w-full object-cover">
                        <div class="space-y-6 p-8">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-500 uppercase tracking-[0.4em]">Desa Pintar</p>
                                <span class="text-xs rounded-full bg-slate-900/5 px-3 py-1 font-semibold text-slate-700">Beta v1.3</span>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Koordinat Referensi</p>
                                    <p class="text-base font-semibold text-slate-900">-6.7289, 110.7485</p>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Kontributor Data</p>
                                    <p class="text-base font-semibold text-slate-900">Perangkat Desa · Warga</p>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-500">Terakhir Diperbarui</p>
                                    <p class="text-base font-semibold text-slate-900">{{ now()->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-sm text-slate-500">Catatan</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">Data spasial dipadukan dengan narasi budaya untuk menjaga identitas desa.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-24 bg-gradient-to-b from-white to-slate-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-600">Statistik Desa</p>
                <h2 class="mt-4 text-4xl font-bold text-slate-900">Distribusi Titik Lokasi per Kategori</h2>
                <p class="mt-4 text-lg text-slate-600 max-w-3xl mx-auto">Angka-angka ini membantu prioritas pembangunan dan memetakan pelayanan publik secara adil.</p>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach($categories as $category)
                <div class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-lg shadow-slate-200/60 hover:-translate-y-1 transition">
                    <div class="absolute inset-0 bg-gradient-to-br" style="opacity: 0.12; background: linear-gradient(135deg, {{ $category->color }} 0%, #0ea5e9 100%);"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl text-white text-xl" style="background-color: {{ $category->color }}">
                                <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }}"></i>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Layer</span>
                        </div>
                        <div class="mt-8">
                            <p class="text-sm tracking-[0.3em] uppercase text-slate-400">Total Titik</p>
                            <p class="mt-2 text-4xl font-bold text-slate-900">{{ $category->places_count }}</p>
                            <p class="mt-3 text-lg font-semibold text-slate-900">{{ $category->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">Dipetakan secara detail oleh admin desa.</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Feature Highlights -->
    <section class="py-24 bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-300">Fitur Unggulan</p>
                <h2 class="mt-4 text-4xl font-bold">Memadukan Data, Narasi, dan Aksi</h2>
                <p class="mt-4 text-lg text-slate-200">Setiap titik pada peta dilengkapi dokumentasi visual, deskripsi singkat, dan keterhubungan antar kategori.</p>
            </div>

            <div class="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/20 text-blue-300 text-xl mb-5">
                        <i class="fa-solid fa-mountain-sun"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Profil Visual Desa</h3>
                    <p class="mt-3 text-sm text-slate-200">Hero, statistik, dan narasi dibangun dari data asli lapangan untuk memperkuat identitas desa.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-300 text-xl mb-5">
                        <i class="fa-solid fa-satellite-dish"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Layer Spasial Interaktif</h3>
                    <p class="mt-3 text-sm text-slate-200">Filter kategori real-time dan popup detail memudahkan eksplorasi fasilitas.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/20 text-amber-300 text-xl mb-5">
                        <i class="fa-solid fa-people-group"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Dashboard Admin</h3>
                    <p class="mt-3 text-sm text-slate-200">Kelola lokasi, unggah foto, dan tentukan titik koordinat hanya dengan satu klik pada mini-map.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section id="map-section" class="relative py-24 bg-gradient-to-b from-slate-900 to-slate-950" x-data="mapComponent()">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.25),_transparent_50%)]"></div>
        </div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-200">Peta Interaktif</p>
                <h2 class="mt-4 text-4xl font-bold">Navigasi Spasial Desa Satu Klik</h2>
                <p class="mt-4 text-lg text-slate-300">Filter kategori, eksplorasi detail, dan dapatkan insight langsung dari peta desa.</p>
            </div>

            <div class="mt-16 grid lg:grid-cols-[0.9fr_1.1fr] gap-8">
                <!-- Sidebar Filters -->
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-white lg:sticky lg:top-24 self-start">
                    <!-- Search Bar -->
                    <div class="mb-6">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-300 mb-2">Pencarian Lokasi</p>
                        <div class="relative">
                            <input type="text" 
                                   x-model="searchQuery" 
                                   @input="performSearch()"
                                   placeholder="Cari lokasi, kategori, atau alamat..."
                                   class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <i class="fa-solid fa-search absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        </div>
                        <div x-show="searchResults.length > 0" 
                             x-cloak
                             class="mt-2 max-h-48 overflow-y-auto rounded-xl border border-white/10 bg-white/10">
                            <template x-for="result in searchResults" :key="result.id">
                                <button @click="zoomToResult(result)" 
                                        class="w-full text-left px-4 py-2 text-sm hover:bg-white/10 transition">
                                    <p class="font-semibold" x-text="result.name"></p>
                                    <p class="text-xs text-slate-300" x-text="result.type"></p>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Layer Controls -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-300">Layer Peta</p>
                                <p class="text-xl font-semibold">Kontrol Layer</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium transition hover:bg-white/10 cursor-pointer">
                                <input type="checkbox" x-model="showBoundaries" @change="updateLayers()" class="h-5 w-5 rounded border-white/30 text-blue-400 focus:ring-blue-300 bg-transparent">
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                                <span class="flex-1">Batas Wilayah</span>
                            </label>
                            <label class="flex items-center space-x-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium transition hover:bg-white/10 cursor-pointer">
                                <input type="checkbox" x-model="showInfrastructures" @change="updateLayers()" class="h-5 w-5 rounded border-white/30 text-blue-400 focus:ring-blue-300 bg-transparent">
                                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                <span class="flex-1">Infrastruktur</span>
                            </label>
                            <label class="flex items-center space-x-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium transition hover:bg-white/10 cursor-pointer">
                                <input type="checkbox" x-model="showLandUses" @change="updateLayers()" class="h-5 w-5 rounded border-white/30 text-blue-400 focus:ring-blue-300 bg-transparent">
                                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                <span class="flex-1">Penggunaan Lahan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Category Filters -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-300">Filter Kategori</p>
                                <p class="text-xl font-semibold">Titik Lokasi</p>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">{{ $categories->count() }} kategori</span>
                        </div>
                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1 custom-scroll">
                            <template x-for="category in categories" :key="category.id">
                                <label class="flex items-center space-x-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium transition hover:bg-white/10 cursor-pointer">
                                    <input type="checkbox" 
                                           :value="category.id" 
                                           x-model="selectedCategories" 
                                           @change="updateMapMarkers()"
                                           class="h-5 w-5 rounded border-white/30 text-blue-400 focus:ring-blue-300 bg-transparent">
                                    <span class="w-2.5 h-2.5 rounded-full" :style="`background-color: ${category.color}`"></span>
                                    <span class="flex-1" x-text="category.name"></span>
                                    <span class="text-xs rounded-full bg-white/10 px-2 py-0.5" x-text="category.places_count"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                    <button @click="resetFilters()" class="mt-6 w-full rounded-2xl bg-white text-slate-900 py-3 text-sm font-semibold hover:bg-slate-100 transition">
                        Reset Filter
                    </button>
                    <p class="mt-4 text-xs text-slate-300">Tip: klik marker/polygon/polyline untuk melihat detail.</p>
                </div>

                <!-- Map Container -->
                <div class="rounded-[32px] border border-white/10 bg-slate-900/30 p-2 shadow-2xl shadow-blue-900/30">
                    <div class="relative rounded-[28px] overflow-hidden border border-white/10 bg-slate-950">
                        <div id="map" class="w-full h-[640px]"></div>
                        <div class="absolute left-6 top-6 rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-slate-900 shadow z-[1000]">
                            Live Map · Leaflet + GeoJSON
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact / CTA -->
    <section id="contact" class="bg-white py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-[36px] bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-400 p-1 shadow-2xl">
                <div class="rounded-[34px] bg-white p-10">
                    <div class="grid md:grid-cols-[1.2fr_0.8fr] gap-10 items-center">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.4em] text-blue-600">Kolaborasi</p>
                            <h2 class="mt-4 text-3xl font-bold text-slate-900">Tertarik bermitra atau ingin kontribusi data baru?</h2>
                            <p class="mt-4 text-base text-slate-600">Hubungi perangkat desa Mayong Lor untuk inisiatif digital, wisata, ataupun layanan publik.</p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="mailto:desa@mayonglor.id" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition">
                                    Email Desa
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0l-3 3m3-3l-3-3" />
                                    </svg>
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50 transition">
                                    Masuk Admin
                                    <i class="fa-solid fa-shield-halved"></i>
                                </a>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 space-y-5">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Alamat Kantor</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">Balai Desa Mayong Lor</p>
                                <p class="text-sm text-slate-600">Kecamatan Mayong, Kabupaten Jepara, Jawa Tengah</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Jam Layanan</p>
                                <p class="mt-2 text-sm text-slate-600">Senin - Jumat · 08.00 - 15.00 WIB</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Hotline</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">(+62) 812-3456-7890</p>
                                <p class="text-sm text-slate-500">Sekretariat Pemerintah Desa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 text-white py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-2">
                <div>
                    <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Desa Mayong Lor</p>
                    <h3 class="mt-3 text-3xl font-bold">Sistem Informasi Geografis</h3>
                    <p class="mt-4 text-sm text-slate-400 max-w-md">Didukung data spasial resmi Pemerintah Desa untuk menghadirkan transparansi dan pelayanan prima.</p>
                </div>
                <div class="flex items-end justify-end">
                    <div class="flex space-x-4 text-sm text-slate-400">
                        <a href="#hero" class="hover:text-white transition">Beranda</a>
                        <a href="#stats" class="hover:text-white transition">Statistik</a>
                        <a href="#map-section" class="hover:text-white transition">Peta</a>
                        <a href="#contact" class="hover:text-white transition">Kontak</a>
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t border-white/10 pt-6 text-xs text-slate-500 flex flex-col sm:flex-row justify-between">
                <p>&copy; {{ date('Y') }} Pemerintah Desa Mayong Lor. All rights reserved.</p>
                <p>Built with Laravel · Leaflet · TailwindCSS</p>
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

                    // Initialize Map
                    const center = [-6.7289, 110.7485]; 
                    
                    this.map = L.map('map').setView(center, 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(this.map);

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
                    this.boundariesLayer = L.layerGroup();
                    
                    features.forEach(feature => {
                        const layer = L.geoJSON(feature, {
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
                            }
                        });
                        this.boundariesLayer.addLayer(layer);
                        this.boundaries.push(layer);
                    });
                    
                    this.boundariesLayer.addTo(this.map);
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
                                     props.type === 'road' ? '#6b7280' : 
                                     props.type === 'irrigation' ? '#06b6d4' : '#8b5cf6';
                        
                        const layer = L.geoJSON(feature, {
                            style: {
                                color: color,
                                weight: props.type === 'road' ? 4 : 3,
                                opacity: 0.8
                            },
                            onEachFeature: (feature, layer) => {
                                const popupContent = `
                                    <div class="p-2">
                                        <h3 class="font-bold text-gray-900 mb-1">${props.name}</h3>
                                        <p class="text-xs text-gray-600 mb-1">Tipe: ${props.type}</p>
                                        ${props.length_meters ? `<p class="text-xs text-gray-600">Panjang: ${props.length_meters} m</p>` : ''}
                                        ${props.condition ? `<p class="text-xs text-gray-600">Kondisi: ${props.condition}</p>` : ''}
                                        ${props.description ? `<p class="text-xs text-gray-600 mt-1">${props.description}</p>` : ''}
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
                                    <div class="p-2">
                                        <h3 class="font-bold text-gray-900 mb-1">${props.name}</h3>
                                        <p class="text-xs text-gray-600 mb-1">Tipe: ${props.type}</p>
                                        ${props.area_hectares ? `<p class="text-xs text-gray-600">Luas: ${props.area_hectares} ha</p>` : ''}
                                        ${props.owner ? `<p class="text-xs text-gray-600">Pemilik: ${props.owner}</p>` : ''}
                                        ${props.description ? `<p class="text-xs text-gray-600 mt-1">${props.description}</p>` : ''}
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
                        return this.selectedCategories.includes(categoryId);
                    });

                    filteredFeatures.forEach(feature => {
                        const [lng, lat] = feature.geometry.coordinates;
                        const marker = L.marker([lat, lng]);
                        const place = feature.properties;
                        
                        const popupContent = `
                            <div class="overflow-hidden">
                                ${place.image_url ? `<img src="${place.image_url}" class="w-full h-32 object-cover mb-3">` : ''}
                                <div class="p-4 ${place.image_url ? 'pt-0' : ''}">
                                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-1 block">${place.category ? place.category.name : ''}</span>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 leading-tight">${place.name}</h3>
                                    <p class="text-sm text-gray-600 mb-0 line-clamp-3">${place.description || 'Tidak ada deskripsi.'}</p>
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
                    this.map.setView([lat, lng], 16);
                    
                    // Open popup if it's a marker
                    this.markers.forEach(marker => {
                        const markerLatLng = marker.getLatLng();
                        if (Math.abs(markerLatLng.lat - lat) < 0.0001 && Math.abs(markerLatLng.lng - lng) < 0.0001) {
                            marker.openPopup();
                        }
                    });

                    this.searchQuery = '';
                    this.searchResults = [];
                },

                resetFilters() {
                    this.selectedCategories = this.categories.map(c => c.id);
                    this.showBoundaries = true;
                    this.showInfrastructures = true;
                    this.showLandUses = true;
                    this.updateMapMarkers();
                    this.updateLayers();
                }
            }
        }
    </script>
</body>
</html>
