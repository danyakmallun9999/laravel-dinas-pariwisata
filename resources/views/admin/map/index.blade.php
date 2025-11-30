<x-app-layout full-width="true">
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

    <div class="h-[calc(100vh-80px)] flex flex-col" x-data="interactiveMap()" x-init="init()">
        <div class="w-full flex-1 flex flex-col">
            <!-- Tab Navigation -->
            <div class="bg-white border-b border-gray-200">
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
            <div class="flex-1 flex overflow-hidden bg-white">
                <div class="flex w-full h-full">
                    <!-- Sidebar -->
                    <div class="w-80 border-r border-gray-200 bg-gray-50 flex flex-col">
                        <!-- Tab Content -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <!-- Batas Wilayah Tab -->
                            <div x-show="activeTab === 'boundary'" x-cloak class="space-y-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Batas Wilayah</h3>
                                    <button @click="startAdd('boundary')" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="boundaries.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data batas wilayah</p>
                                            <button @click="startAdd('boundary')" class="text-blue-600 hover:text-blue-800 mt-2 inline-block font-semibold">
                                                Tambah sekarang
                                            </button>
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
                                    <button @click="startAdd('infrastructure')" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="infrastructures.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data infrastruktur</p>
                                            <button @click="startAdd('infrastructure')" class="text-blue-600 hover:text-blue-800 mt-2 inline-block font-semibold">
                                                Tambah sekarang
                                            </button>
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
                                    <button @click="startAdd('landuse')" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="landUses.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data penggunaan lahan</p>
                                            <button @click="startAdd('landuse')" class="text-blue-600 hover:text-blue-800 mt-2 inline-block font-semibold">
                                                Tambah sekarang
                                            </button>
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
                                    <button @click="startAdd('road')" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="roads.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data jalan</p>
                                            <button @click="startAdd('road')" class="text-blue-600 hover:text-blue-800 mt-2 inline-block font-semibold">
                                                Tambah sekarang
                                            </button>
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
                                    <button @click="startAdd('place')" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-if="places.length === 0">
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center text-gray-500 text-sm">
                                            <i class="fa-solid fa-inbox mb-2 text-2xl"></i>
                                            <p>Belum ada data titik lokasi</p>
                                            <button @click="startAdd('place')" class="text-blue-600 hover:text-blue-800 mt-2 inline-block font-semibold">
                                                Tambah sekarang
                                            </button>
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
                            
                            <!-- GeoJSON Upload -->
                            <div class="mt-3 border-t border-gray-100 pt-3">
                                <input type="file" x-ref="geojsonInput" accept=".geojson,.json" class="hidden" @change="handleFileUpload($event)">
                                <button 
                                    @click="$refs.geojsonInput.click()"
                                    class="w-full px-3 py-2 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition flex items-center justify-center"
                                >
                                    <i class="fa-solid fa-file-import mr-2"></i> Upload GeoJSON (QGIS)
                                </button>
                                
                                <button 
                                    x-show="uploadedGeoJSON"
                                    @click="saveUploadedFeature()"
                                    class="w-full mt-2 px-3 py-2 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition flex items-center justify-center"
                                    x-transition
                                >
                                    <i class="fa-solid fa-save mr-2"></i> Simpan Fitur
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
                        
                        <!-- Floating Undo/Redo Controls -->
                        <div class="absolute top-4 left-1/2 transform -translate-x-1/2 z-[1000] flex space-x-2" 
                             x-show="drawingMode === 'line' || drawingMode === 'polygon'" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-4"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-4">
                            <button 
                                @click="undoPoint()"
                                class="bg-white text-gray-700 hover:text-gray-900 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-md border border-gray-200 text-sm font-semibold flex items-center transition"
                                title="Undo (Ctrl+Z)"
                            >
                                <i class="fa-solid fa-undo mr-2"></i> Undo
                            </button>
                            <button 
                                @click="redoPoint()"
                                class="bg-white text-gray-700 hover:text-gray-900 hover:bg-gray-50 px-4 py-2 rounded-lg shadow-md border border-gray-200 text-sm font-semibold flex items-center transition"
                                title="Redo (Ctrl+Y)"
                            >
                                <i class="fa-solid fa-redo mr-2"></i> Redo
                            </button>
                        </div>
                        
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

            <!-- Toast Notification -->
            <div x-show="notification.show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="absolute top-4 left-1/2 transform -translate-x-1/2 z-[2000] text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2"
                 :class="{
                    'bg-gray-800': notification.type === 'info',
                    'bg-green-600': notification.type === 'success',
                    'bg-red-600': notification.type === 'error'
                 }"
                 style="display: none;">
                <i class="fa-solid" 
                   :class="{
                       'fa-info-circle text-blue-400': notification.type === 'info',
                       'fa-check-circle text-white': notification.type === 'success',
                       'fa-exclamation-circle text-white': notification.type === 'error'
                   }"></i>
                <span x-text="notification.message"></span>
            </div>


        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak 
             class="fixed inset-0 z-[2000] overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="showDeleteModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDeleteModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Hapus Fitur
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus fitur <span class="font-bold" x-text="featureToDelete?.name"></span>? Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="confirmDelete()"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Hapus
                        </button>
                        <button type="button" 
                                @click="showDeleteModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Quick Add Modal -->
        <div x-show="showAddModal" x-cloak 
             class="fixed inset-0 z-[2000] overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="showAddModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showAddModal" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            Tambah <span x-text="getFeatureTypeName()"></span> Baru
                        </h3>
                        
                        <div class="space-y-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                <input type="text" x-model="newFeature.name" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea x-model="newFeature.description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                      <!-- Boundary Fields -->
                    <template x-if="activeTab === 'boundary'">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Batas</label>
                                <select x-model="newFeature.type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="village_boundary">Batas Desa</option>
                                    <option value="hamlet">Dusun</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Luas (Hektar)</label>
                                <input type="number" step="0.0001" x-model="newFeature.area_hectares" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                                <p class="text-xs text-gray-500 mt-1">Otomatis dihitung dari gambar.</p>
                            </div>
                        </div>
                    </template>

                    <!-- Infrastructure Fields -->
                    <template x-if="activeTab === 'infrastructure' || activeTab === 'road'">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Infrastruktur</label>
                                <select x-model="newFeature.type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="road">Jalan</option>
                                    <option value="river">Sungai</option>
                                    <option value="irrigation">Irigasi</option>
                                    <option value="electricity">Listrik</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (m)</label>
                                    <input type="number" step="0.01" x-model="newFeature.length_meters" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (m)</label>
                                    <input type="number" step="0.01" x-model="newFeature.width_meters" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                                <select x-model="newFeature.condition" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih kondisi</option>
                                    <option value="good">Baik</option>
                                    <option value="fair">Cukup</option>
                                    <option value="poor">Buruk</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori (Opsional)</label>
                                <select x-model="newFeature.category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Tidak ada kategori</option>
                                    <template x-for="category in categories" :key="category.id">
                                        <option :value="category.id" x-text="category.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </template>

                    <!-- Land Use Fields -->
                    <template x-if="activeTab === 'landuse'">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Penggunaan Lahan</label>
                                <select x-model="newFeature.type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="settlement">Permukiman</option>
                                    <option value="rice_field">Persawahan</option>
                                    <option value="plantation">Perkebunan</option>
                                    <option value="forest">Hutan</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pemilik</label>
                                <input type="text" x-model="newFeature.owner" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Nama pemilik lahan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Luas (Hektar)</label>
                                <input type="number" step="0.0001" x-model="newFeature.area_hectares" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                                <p class="text-xs text-gray-500 mt-1">Otomatis dihitung dari gambar.</p>
                            </div>
                        </div>
                    </template>

                    <!-- Place Fields -->
                    <template x-if="activeTab === 'place'">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select x-model="newFeature.category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih kategori</option>
                                    <template x-for="category in categories" :key="category.id">
                                        <option :value="category.id" x-text="category.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Lokasi</label>
                                <input type="file" @change="newFeature.image = $event.target.files[0]" accept="image/*" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <p class="text-xs text-gray-500 mt-1">JPG/PNG (Max. 2MB)</p>
                            </div>
                        </div>
                    </template>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="saveFeature()"
                                :disabled="isSaving"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span x-show="!isSaving">Simpan</span>
                            <span x-show="isSaving">Menyimpan...</span>
                        </button>
                        <button type="button" 
                                @click="cancelAdd()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @pushOnce('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
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
                    showDeleteModal: false,
                    selectedFeature: null,
                    showDeleteModal: false,
                    featureToDelete: null,
                    uploadedGeoJSON: null,
                    uploadedProperties: null,
                    
                    // Quick Add
                    showAddModal: false,
                    newFeature: { name: '', type: '', description: '', category_id: '' },
                    tempLayer: null,
                    isSaving: false,
                    categories: @json(\App\Models\Category::select('id', 'name')->orderBy('name')->get()),
                    notification: { show: false, message: '', type: 'info' },
                    
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
                            this.initKeyboardShortcuts();
                        });
                    },

                    initMap() {
                        this.map = L.map('interactiveMap').setView([-6.7289, 110.7485], 14);

                        // Google Maps Layers
                        const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&s=Galileo&apistyle=s.t%3Apoi%7Cp.v%3Aoff%2Cs.t%3Atransit%7Cp.v%3Aoff', {
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
                        this.tempLayer = layer;
                        
                        // Reset form first
                        this.newFeature = { 
                            name: '', 
                            description: '', 
                            type: '', 
                            category_id: '',
                            area_hectares: '',
                            length_meters: '',
                            width_meters: '',
                            condition: '',
                            owner: '',
                            image: null
                        };
                        
                        // Auto-fill type if possible
                        if (this.activeTab === 'boundary') this.newFeature.type = 'village_boundary';
                        if (this.activeTab === 'infrastructure') this.newFeature.type = 'road';
                        if (this.activeTab === 'road') this.newFeature.type = 'road';
                        if (this.activeTab === 'landuse') this.newFeature.type = 'settlement';

                        // Calculate area if polygon
                        if (layer instanceof L.Polygon) {
                            const latlngs = layer.getLatLngs()[0];
                            let area = 0;
                            if (L.GeometryUtil && L.GeometryUtil.geodesicArea) {
                                area = L.GeometryUtil.geodesicArea(latlngs);
                            } else {
                                // Fallback area calculation (simple spherical)
                                area = L.GeometryUtil ? L.GeometryUtil.geodesicArea(latlngs) : 0;
                                // If still 0/undefined, try a basic implementation or rely on backend?
                                // Let's try to use a simple approximation if needed, but Leaflet.draw usually has it.
                                // If L.GeometryUtil is missing, we can't easily calc geodesic area without a lib.
                                // Assuming it works or we use a simple planar approximation for small areas:
                                // area = L.GeometryUtil.geodesicArea(latlngs); 
                                // Let's check if we can access the draw control's utils
                            }
                            
                            // Try to use the one from Leaflet Draw if available
                            try {
                                area = L.GeometryUtil.geodesicArea(latlngs);
                            } catch (e) {
                                console.warn('L.GeometryUtil not found, using planar approximation');
                                // Planar approximation (not accurate for large areas but ok for village)
                                // area = ... 
                            }
                            
                            this.newFeature.area_hectares = (area / 10000).toFixed(4); // Convert sq meters to hectares
                        }
                        
                        // Calculate length if polyline
                        if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
                            let distance = 0;
                            const latlngs = layer.getLatLngs();
                            for (let i = 0; i < latlngs.length - 1; i++) {
                                distance += latlngs[i].distanceTo(latlngs[i + 1]);
                            }
                            this.newFeature.length_meters = distance.toFixed(2);
                        }

                        this.showAddModal = true;
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

                    redoStack: [],
                    isRedoing: false,

                    startDrawing(type) {
                        // Disable previous drawing if any
                        if (this.currentDrawHandler) {
                            this.currentDrawHandler.disable();
                        }
                        
                        this.drawingMode = type;
                        this.redoStack = [];
                        
                        if (type === 'point') {
                            // Point drawing handled by map click - already set up in initMapEvents
                            this.map.getContainer().style.cursor = 'crosshair';
                        } else if (type === 'line' || type === 'polygon') {
                            const options = type === 'line' 
                                ? { shapeOptions: { color: '#3388ff', weight: 4 } }
                                : { shapeOptions: { color: '#3388ff', fillColor: '#3388ff', fillOpacity: 0.2 } };
                                
                            const HandlerClass = type === 'line' ? L.Draw.Polyline : L.Draw.Polygon;
                            this.currentDrawHandler = new HandlerClass(this.map, options);
                            
                            // Monkey-patch deleteLastVertex to save to redo stack
                            const originalDelete = this.currentDrawHandler.deleteLastVertex;
                            this.currentDrawHandler.deleteLastVertex = () => {
                                const markers = this.currentDrawHandler._markers;
                                if (markers && markers.length > 0) {
                                    const lastMarker = markers[markers.length - 1];
                                    this.redoStack.push(lastMarker.getLatLng());
                                }
                                originalDelete.call(this.currentDrawHandler);
                            };

                            // Monkey-patch addVertex to clear redo stack (unless redoing)
                            const originalAddVertex = this.currentDrawHandler.addVertex;
                            this.currentDrawHandler.addVertex = (latlng) => {
                                if (!this.isRedoing) {
                                    this.redoStack = [];
                                }
                                originalAddVertex.call(this.currentDrawHandler, latlng);
                            };

                            this.currentDrawHandler.enable();
                        }
                    },

                    redoPoint() {
                        console.log('Redo triggered');
                        if (this.currentDrawHandler && this.redoStack.length > 0) {
                            const latlng = this.redoStack.pop();
                            this.isRedoing = true;
                            this.currentDrawHandler.addVertex(latlng);
                            this.isRedoing = false;
                            console.log('Redo successful', latlng);
                        } else {
                            console.log('Redo failed: stack empty or no handler');
                        }
                    },

                    startAdd(type) {
                        let drawType = '';
                        let message = '';

                        if (type === 'boundary') {
                            drawType = 'polygon';
                            message = 'Mode menggambar Polygon aktif. Silakan gambar batas wilayah di peta.';
                        } else if (type === 'infrastructure' || type === 'road') {
                            drawType = 'line';
                            message = 'Mode menggambar Garis aktif. Silakan gambar jalur/infrastruktur di peta.';
                        } else if (type === 'landuse') {
                            drawType = 'polygon';
                            message = 'Mode menggambar Polygon aktif. Silakan gambar area penggunaan lahan di peta.';
                        } else if (type === 'place') {
                            drawType = 'point';
                            message = 'Mode menggambar Titik aktif. Silakan klik lokasi di peta.';
                        }

                        if (drawType) {
                            this.startDrawing(drawType);
                            this.showNotification(message, 'info');
                        }
                    },

                    showNotification(message, type = 'info', duration = 3000) {
                        this.notification.message = message;
                        this.notification.type = type;
                        this.notification.show = true;
                        setTimeout(() => {
                            this.notification.show = false;
                        }, duration);
                    },

                    getFeatureTypeName() {
                        if (this.activeTab === 'boundary') return 'Batas Wilayah';
                        if (this.activeTab === 'infrastructure') return 'Infrastruktur';
                        if (this.activeTab === 'landuse') return 'Penggunaan Lahan';
                        if (this.activeTab === 'place') return 'Titik Lokasi';
                        return 'Fitur';
                    },

                    cancelAdd() {
                        this.showAddModal = false;
                        if (this.tempLayer) {
                            this.drawnItems.removeLayer(this.tempLayer);
                            this.tempLayer = null;
                        }
                    },

                    async saveFeature() {
                        if (!this.newFeature.name) {
                            this.showNotification('Nama harus diisi', 'error');
                            return;
                        }

                        this.isSaving = true;
                        const geoJSON = this.tempLayer.toGeoJSON();
                        const geometry = JSON.stringify(geoJSON.geometry);

                        let url = '';
                        // Use FormData for file upload support
                        let formData = new FormData();
                        formData.append('name', this.newFeature.name);
                        formData.append('description', this.newFeature.description || '');
                        formData.append('geometry', geometry);
                        formData.append('_token', '{{ csrf_token() }}');

                        if (this.activeTab === 'boundary') {
                            url = '{{ route("admin.boundaries.store") }}';
                            formData.append('type', this.newFeature.type || 'other');
                            if (this.newFeature.area_hectares) formData.append('area_hectares', this.newFeature.area_hectares);
                        } else if (this.activeTab === 'infrastructure' || this.activeTab === 'road') {
                            url = '{{ route("admin.infrastructures.store") }}';
                            formData.append('type', this.newFeature.type || 'road');
                            if (this.newFeature.length_meters) formData.append('length_meters', this.newFeature.length_meters);
                            if (this.newFeature.width_meters) formData.append('width_meters', this.newFeature.width_meters);
                            if (this.newFeature.condition) formData.append('condition', this.newFeature.condition);
                            if (this.newFeature.category_id) formData.append('category_id', this.newFeature.category_id);
                        } else if (this.activeTab === 'landuse') {
                            url = '{{ route("admin.land-uses.store") }}';
                            formData.append('type', this.newFeature.type || 'other');
                            if (this.newFeature.owner) formData.append('owner', this.newFeature.owner);
                            if (this.newFeature.area_hectares) formData.append('area_hectares', this.newFeature.area_hectares);
                        } else if (this.activeTab === 'place') {
                            url = '{{ route("admin.places.store") }}';
                            if (this.newFeature.category_id) formData.append('category_id', this.newFeature.category_id);
                            if (this.newFeature.image) formData.append('image', this.newFeature.image);
                            
                            // For places, we also need lat/lng separately for some controllers, but AdminController handles geometry string
                            // Extract lat/lng from geometry for safety if needed by backend validation
                            if (geoJSON.geometry.type === 'Point') {
                                formData.append('latitude', geoJSON.geometry.coordinates[1]);
                                formData.append('longitude', geoJSON.geometry.coordinates[0]);
                            }
                        }

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    // 'Content-Type': 'multipart/form-data' // Do NOT set this manually when using FormData, browser does it
                                },
                                body: formData
                            });

                            if (!response.ok) {
                                const errorData = await response.json();
                                throw new Error(errorData.message || 'Gagal menyimpan data');
                            }

                            const result = await response.json();
                            
                            // Success
                            this.showAddModal = false;
                            this.tempLayer = null; 
                            this.drawnItems.clearLayers(); 
                            
                            // Reload layers to show new data with correct style
                            this.loadAllLayers();
                            
                            // Update sidebar list
                            if (this.activeTab === 'boundary' && result.boundary) {
                                this.boundaries.push(result.boundary);
                            } else if ((this.activeTab === 'infrastructure' || this.activeTab === 'road') && result.infrastructure) {
                                if (this.activeTab === 'road' && result.infrastructure.type === 'road') {
                                    this.roads.push(result.infrastructure);
                                } else {
                                    this.infrastructures.push(result.infrastructure);
                                }
                            } else if (this.activeTab === 'landuse' && result.land_use) {
                                this.landUses.push(result.land_use);
                            } else if (this.activeTab === 'place' && result.place) {
                                this.places.push(result.place);
                            }

                            this.showNotification(result.message || 'Data berhasil disimpan', 'success');

                        } catch (error) {
                            console.error('Error:', error);
                            this.showNotification('Terjadi kesalahan: ' + error.message, 'error');
                        } finally {
                            this.isSaving = false;
                        }
                    },

                    undoPoint() {
                        console.log('Undo triggered');
                        if (this.currentDrawHandler && this.currentDrawHandler.deleteLastVertex) {
                            this.currentDrawHandler.deleteLastVertex();
                            console.log('Undo successful');
                        } else {
                            console.log('Undo failed: no handler or deleteLastVertex method');
                        }
                    },

                    initKeyboardShortcuts() {
                        document.addEventListener('keydown', (e) => {
                            if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                                e.preventDefault();
                                this.undoPoint();
                            }
                            if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
                                e.preventDefault();
                                this.redoPoint();
                            }
                        });
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

                    handleFileUpload(event) {
                        const file = event.target.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            try {
                                const geojson = JSON.parse(e.target.result);
                                const layer = L.geoJSON(geojson);
                                
                                // Clear existing drawn items
                                this.drawnItems.clearLayers();
                                this.uploadedGeoJSON = null;
                                this.uploadedProperties = null;

                                const layers = [];
                                layer.eachLayer((l) => {
                                    // Add click handler to select this feature
                                    l.on('click', () => {
                                        // Reset style of all layers in drawnItems (if they are paths)
                                        this.drawnItems.eachLayer(item => {
                                            if (item instanceof L.Path) {
                                                item.setStyle({ color: '#3388ff', weight: 4 });
                                            }
                                        });

                                        // Highlight clicked layer (if path)
                                        if (l instanceof L.Path) {
                                            l.setStyle({ color: '#ff0000', weight: 6 });
                                        }

                                        // Store geometry and properties
                                        this.uploadedGeoJSON = l.toGeoJSON().geometry;
                                        this.uploadedProperties = l.feature ? l.feature.properties : null;
                                        
                                        alert('Fitur dipilih! Klik "Simpan Fitur" untuk melanjutkan.');
                                    });

                                    this.drawnItems.addLayer(l);
                                    layers.push(l);
                                });
                                
                                // Auto-select if only one feature
                                if (layers.length === 1) {
                                    const l = layers[0];
                                    this.uploadedGeoJSON = l.toGeoJSON().geometry;
                                    this.uploadedProperties = l.feature ? l.feature.properties : null;
                                }

                                // Zoom to uploaded features
                                if (layer.getBounds().isValid()) {
                                    this.map.fitBounds(layer.getBounds());
                                }
                                
                                // Reset input
                                event.target.value = '';
                                
                                if (layers.length > 1) {
                                    alert('File GeoJSON berisi ' + layers.length + ' fitur. Silakan KLIK pada salah satu fitur di peta untuk memilihnya, lalu klik "Simpan Fitur".');
                                } else {
                                    alert('File GeoJSON berhasil diupload! Klik "Simpan Fitur" untuk menyimpan.');
                                }
                            } catch (error) {
                                console.error('Error parsing GeoJSON:', error);
                                alert('Gagal membaca file GeoJSON. Pastikan format file benar.');
                                this.uploadedGeoJSON = null;
                            }
                        };
                        reader.readAsText(file);
                    },

                    saveUploadedFeature() {
                        if (!this.uploadedGeoJSON) {
                            alert('Silakan pilih fitur di peta terlebih dahulu!');
                            return;
                        }

                        const geometry = this.uploadedGeoJSON;
                        const geometryJson = JSON.stringify(geometry);
                        const propertiesJson = this.uploadedProperties ? JSON.stringify(this.uploadedProperties) : null;
                        
                        // Determine create URL based on geometry type
                        let createUrl = '/admin/';
                        
                        if (geometry.type === 'Polygon' || geometry.type === 'MultiPolygon') {
                            createUrl += 'boundaries/create';
                        } else if (geometry.type === 'LineString' || geometry.type === 'MultiLineString') {
                            createUrl += 'infrastructures/create';
                        } else if (geometry.type === 'Point') {
                            createUrl += 'places/create';
                        } else {
                            // Default fallback
                            createUrl += 'boundaries/create';
                        }
                        
                        // Store geometry and properties in sessionStorage and redirect
                        sessionStorage.setItem('newFeatureGeometry', geometryJson);
                        if (propertiesJson) {
                            sessionStorage.setItem('newFeatureProperties', propertiesJson);
                        }
                        window.location.href = createUrl;
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
                        console.log('deleteFeature called', feature);
                        this.featureToDelete = feature;
                        this.showDeleteModal = true;
                        console.log('showDeleteModal set to true');
                    },

                    confirmDelete() {
                        if (!this.featureToDelete) return;

                        const url = this.featureToDelete.editUrl.replace('/edit', '');
                        
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => {
                            if (res.ok) {
                                // Remove from list
                                const id = this.featureToDelete.id;
                                if (this.activeTab === 'boundary') {
                                    this.boundaries = this.boundaries.filter(item => item.id !== id);
                                } else if (this.activeTab === 'infrastructure') {
                                    this.infrastructures = this.infrastructures.filter(item => item.id !== id);
                                } else if (this.activeTab === 'road') {
                                    this.roads = this.roads.filter(item => item.id !== id);
                                } else if (this.activeTab === 'landuse') {
                                    this.landUses = this.landUses.filter(item => item.id !== id);
                                } else if (this.activeTab === 'place') {
                                    this.places = this.places.filter(item => item.id !== id);
                                }

                                this.selectedFeature = null;
                                this.showDeleteModal = false;
                                this.featureToDelete = null;
                                this.loadAllLayers();
                                this.showNotification('Data berhasil dihapus', 'success');
                            } else {
                                this.showNotification('Gagal menghapus fitur. Silakan coba lagi.', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            this.showNotification('Terjadi kesalahan saat menghapus fitur.', 'error');
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
    </div>
</x-app-layout>

