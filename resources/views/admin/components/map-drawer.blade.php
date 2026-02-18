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
        <!-- Drawing Controls Removed as per user request -->
        </div>

        <!-- Info Text -->
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-sm font-semibold text-gray-900">Gambar di Peta</h4>
                <p class="text-xs text-gray-500">
                    Klik tombol "Titik" lalu klik di peta untuk menambahkan lokasi
                </p>
            </div>
        </div>
    </div>

    <div class="flex-1 relative min-h-[300px] rounded-2xl overflow-hidden border border-gray-200 bg-slate-50">
        <!-- Loading Overlay -->
        <div x-show="isLoading" 
             class="absolute inset-0 z-20 flex items-center justify-center bg-gray-50/80 backdrop-blur-sm transition-opacity duration-300">
            <div class="flex flex-col items-center">
                 <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-600 mb-2"></i>
                 <span class="text-sm text-gray-600 font-medium">Memuat Peta...</span>
            </div>
        </div>
        
        <div x-ref="mapContainer" class="absolute inset-0 w-full h-full bg-slate-50 z-10" style="will-change: transform;"></div>
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



