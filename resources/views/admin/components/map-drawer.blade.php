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



