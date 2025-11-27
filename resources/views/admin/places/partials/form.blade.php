@php
    $initialCoordinates = [
        'lat' => (float) old('latitude', $place->latitude ?? -6.7289),
        'lng' => (float) old('longitude', $place->longitude ?? 110.7485),
    ];
    $initialGeometry = null;
    if ($place->latitude && $place->longitude) {
        $initialGeometry = [
            'type' => 'Point',
            'coordinates' => [(float)$place->longitude, (float)$place->latitude]
        ];
    }
@endphp

<section>
    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi</label>
                    <input type="text" name="name" value="{{ old('name', $place->name) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $place->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $place->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Lokasi</label>
                    @if($place->image_path)
                        <div class="mb-3">
                            <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-48 h-32 object-cover rounded-lg border">
                        </div>
                    @endif
                    <input type="file" name="image" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    <p class="text-xs text-gray-500 mt-2">Format JPG/PNG, maksimal 2MB.</p>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Map Drawing Component for Point -->
                @include('admin.components.map-drawer', [
                    'drawType' => 'point',
                    'initialGeometry' => old('geometry', $initialGeometry),
                    'center' => [$initialCoordinates['lat'], $initialCoordinates['lng']],
                    'zoom' => 15
                ])
                @include('admin.components.load-geometry')
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.places.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</section>

