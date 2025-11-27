@pushOnce('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
@endPushOnce

@pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endPushOnce

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Admin Panel</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.map.index') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    <i class="fa-solid fa-map"></i>
                    Peta Interaktif
                </a>
                <a href="{{ route('admin.places.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Lokasi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Titik Lokasi</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['places_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-map-marker-alt text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Batas Wilayah</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['boundaries_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-map text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Infrastruktur</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['infrastructures_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-road text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Penggunaan Lahan</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['land_uses_count'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-seedling text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold text-gray-700">Total Luas Batas Wilayah</h3>
                        <i class="fa-solid fa-map text-green-600 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_boundary_area'] ?? 0, 2) }} ha</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['boundaries_count'] }} batas wilayah</p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold text-gray-700">Total Panjang Infrastruktur</h3>
                        <i class="fa-solid fa-road text-purple-600 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(($stats['total_infrastructure_length'] ?? 0) / 1000, 2) }} km</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['infrastructures_count'] }} infrastruktur</p>
                </div>

                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold text-gray-700">Total Luas Penggunaan Lahan</h3>
                        <i class="fa-solid fa-seedling text-yellow-600 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_land_use_area'] ?? 0, 2) }} ha</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['land_uses_count'] }} penggunaan lahan</p>
                </div>
            </div>

            <!-- Charts and Mini Map -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart: Categories -->
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Lokasi per Kategori</h3>
                    <div class="relative h-64">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>

                <!-- Chart: Infrastructure Types -->
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Jenis Infrastruktur</h3>
                    <div class="relative h-64">
                        <canvas id="infrastructureChart"></canvas>
                    </div>
                </div>

                <!-- Chart: Land Use Types -->
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Jenis Penggunaan Lahan</h3>
                    <div class="relative h-64">
                        <canvas id="landUseChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Places -->
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Lokasi Terbaru</h3>
                        <a href="{{ route('admin.places.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($stats['recent_places'] as $place)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            @if($place->image_path)
                            <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $place->category->color }}20">
                                <i class="{{ $place->category->icon_class ?? 'fa-solid fa-map-marker-alt' }}" style="color: {{ $place->category->color }}"></i>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $place->name }}</p>
                                <p class="text-xs text-gray-500">{{ $place->category->name }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $place->created_at->diffForHumans() }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada lokasi</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Boundaries -->
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Batas Wilayah Terbaru</h3>
                        <a href="{{ route('admin.boundaries.index') }}" class="text-sm text-green-600 hover:text-green-800">Lihat Semua</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($stats['recent_boundaries'] as $boundary)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                <i class="fa-solid fa-map text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $boundary->name }}</p>
                                <p class="text-xs text-gray-500">{{ $boundary->type }} @if($boundary->area_hectares) · {{ number_format($boundary->area_hectares, 2) }} ha @endif</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $boundary->created_at->diffForHumans() }}</span>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada batas wilayah</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Mini Map -->
            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Peta Overview</h3>
                <div id="miniMap" class="w-full h-64 rounded-lg border border-gray-200"></div>
            </div>

            <!-- Places Table -->
            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Lokasi</h3>
                    <p class="text-sm text-gray-500">Total: {{ $places->total() }} lokasi</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Koordinat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($places as $place)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center space-x-3">
                                            @if($place->image_path)
                                                <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                                    <i class="fa-solid fa-map-location-dot"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $place->name }}</p>
                                                <p class="text-xs text-gray-500 line-clamp-1">{{ $place->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-white" style="background-color: {{ $place->category->color }}">
                                            <i class="{{ $place->category->icon_class ?? 'fa-solid fa-map-marker-alt' }}"></i>
                                            {{ $place->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        {{ $place->latitude }}, {{ $place->longitude }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.places.edit', $place) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.places.destroy', $place) }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-800">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data lokasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $places->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Categories Chart
            const categoriesCtx = document.getElementById('categoriesChart');
            if (categoriesCtx) {
                new Chart(categoriesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($stats['categories']->pluck('name')->values()),
                        datasets: [{
                            data: @json($stats['categories']->pluck('places_count')->values()),
                            backgroundColor: @json($stats['categories']->pluck('color')->values()),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            }

            // Infrastructure Chart
            const infrastructureCtx = document.getElementById('infrastructureChart');
            if (infrastructureCtx) {
                new Chart(infrastructureCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($stats['infrastructure_types']->pluck('type')->map(fn($t) => ucfirst($t))->values()),
                        datasets: [{
                            label: 'Jumlah',
                            data: @json($stats['infrastructure_types']->pluck('count')->values()),
                            backgroundColor: '#8b5cf6',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Land Use Chart
            const landUseCtx = document.getElementById('landUseChart');
            if (landUseCtx) {
                new Chart(landUseCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($stats['land_use_types']->pluck('type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->values()),
                        datasets: [{
                            data: @json($stats['land_use_types']->pluck('count')->values()),
                            backgroundColor: ['#fbbf24', '#84cc16', '#059669', '#f59e0b', '#10b981'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            }

            // Mini Map
            const miniMapElement = document.getElementById('miniMap');
            if (miniMapElement) {
                const miniMap = L.map('miniMap').setView([-6.7289, 110.7485], 13);
                
                const baseLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: '&copy; Google Maps'
                }).addTo(miniMap);

                // Layer Groups
                const boundariesLayer = L.geoJSON(null, {
                    style: function(feature) {
                        return {
                            color: '#10b981', // green-500
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.1
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.name) {
                            layer.bindPopup(`
                                <div class="font-semibold">${feature.properties.name}</div>
                                <div class="text-xs text-gray-500">${feature.properties.type}</div>
                            `);
                        }
                    }
                }).addTo(miniMap);

                const infrastructuresLayer = L.geoJSON(null, {
                    style: function(feature) {
                        return {
                            color: '#8b5cf6', // purple-500
                            weight: 3,
                            opacity: 0.8
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.name) {
                            layer.bindPopup(`
                                <div class="font-semibold">${feature.properties.name}</div>
                                <div class="text-xs text-gray-500">${feature.properties.type}</div>
                            `);
                        }
                    }
                }).addTo(miniMap);

                const landUsesLayer = L.geoJSON(null, {
                    style: function(feature) {
                        return {
                            color: '#f59e0b', // amber-500
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.3
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.name) {
                            layer.bindPopup(`
                                <div class="font-semibold">${feature.properties.name}</div>
                                <div class="text-xs text-gray-500">${feature.properties.type}</div>
                            `);
                        }
                    }
                }).addTo(miniMap);

                const placesLayer = L.geoJSON(null, {
                    pointToLayer: function(feature, latlng) {
                        const color = feature.properties.category?.color || '#3b82f6';
                        const iconClass = feature.properties.category?.icon_class || 'fa-solid fa-map-marker-alt';
                        
                        const iconHtml = `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            <i class="${iconClass}" style="color: white; font-size: 12px;"></i>
                        </div>`;

                        return L.marker(latlng, {
                            icon: L.divIcon({
                                html: iconHtml,
                                className: 'custom-div-icon',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12]
                            })
                        });
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties && feature.properties.name) {
                            layer.bindPopup(`
                                <div class="font-semibold">${feature.properties.name}</div>
                                <div class="text-xs text-gray-500">${feature.properties.category?.name || 'Lokasi'}</div>
                            `);
                        }
                    }
                }).addTo(miniMap);

                // Load Data
                const fetchData = (url) => fetch(url).then(r => {
                    if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
                    return r.json();
                }).catch(e => {
                    console.warn(`Failed to fetch ${url}:`, e);
                    return { type: 'FeatureCollection', features: [] }; // Return empty collection on error
                });

                Promise.all([
                    fetchData('{{ route('boundaries.geojson') }}'),
                    fetchData('{{ route('infrastructures.geojson') }}'),
                    fetchData('{{ route('land_uses.geojson') }}'),
                    fetchData('{{ route('places.geojson') }}')
                ]).then(([boundaries, infrastructures, landUses, places]) => {
                    if (boundaries.features && boundaries.features.length) boundariesLayer.addData(boundaries);
                    if (infrastructures.features && infrastructures.features.length) infrastructuresLayer.addData(infrastructures);
                    if (landUses.features && landUses.features.length) landUsesLayer.addData(landUses);
                    if (places.features && places.features.length) placesLayer.addData(places);

                    // Fit bounds to all data
                    const group = new L.FeatureGroup([boundariesLayer, infrastructuresLayer, landUsesLayer, placesLayer]);
                    if (group.getLayers().length > 0) {
                        try {
                            miniMap.fitBounds(group.getBounds(), { padding: [20, 20] });
                        } catch (e) {
                            console.log('No bounds to fit');
                        }
                    }
                });

                // Layer Control
                const overlays = {
                    "Batas Wilayah": boundariesLayer,
                    "Infrastruktur": infrastructuresLayer,
                    "Penggunaan Lahan": landUsesLayer,
                    "Lokasi": placesLayer
                };
                L.control.layers(null, overlays).addTo(miniMap);
            }
        });
    </script>
</x-app-layout>

