@php
    $initialCoordinates = [
        'lat' => (float) old('latitude', $place->latitude ?? -6.7289),
        'lng' => (float) old('longitude', $place->longitude ?? 110.7485),
    ];
    $initialGeometry = null;
    if ($place->latitude && $place->longitude) {
        $initialGeometry = [
            'type' => 'Point',
            'coordinates' => [(float) $place->longitude, (float) $place->latitude],
        ];
    }
@endphp

@php
    $initialCoordinates = [
        'lat' => (float) old('latitude', $place->latitude ?? -6.7289),
        'lng' => (float) old('longitude', $place->longitude ?? 110.7485),
    ];
    $initialGeometry = null;
    if ($place->latitude && $place->longitude) {
        $initialGeometry = [
            'type' => 'Point',
            'coordinates' => [(float) $place->longitude, (float) $place->latitude],
        ];
    }
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="flex h-[calc(100vh-80px)]">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <!-- Left Sidebar: Form Inputs -->
    <div class="w-96 min-w-[380px] bg-white border-r border-gray-200 flex flex-col z-10">
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Detail Lokasi</h3>
                <p class="text-xs text-gray-500">Isi informasi detail mengenai lokasi ini.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lokasi</label>
                <input type="text" name="name" value="{{ old('name', $place->name) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required placeholder="Contoh: Balai Desa Mayonglor">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="category_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $place->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Deskripsi singkat lokasi...">{{ old('description', $place->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                <!-- Photo File Input -->
                <input type="file" class="hidden" x-ref="photo" name="image"
                    @change="
                        photoName = $refs.photo.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.photo.files[0]);
                    " />

                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Lokasi</label>

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    @if ($place->image_path)
                        <div class="mb-3 relative group">
                            <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}"
                                class="w-full h-40 object-cover rounded-lg border">
                            <div
                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition rounded-lg">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block w-full h-40 rounded-lg border bg-cover bg-no-repeat bg-center"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <div class="flex items-center justify-center w-full mt-2">
                    <label @click.prevent="$refs.photo.click()"
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fa-solid fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                            <p class="text-xs text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau
                                drag & drop</p>
                            <p class="text-xs text-gray-500">JPG/PNG (MAX. 2MB)</p>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-3">
            <a href="{{ route('admin.places.index') }}"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-white transition text-sm font-medium">
                Batal
            </a>
            <button type="submit"
                class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition text-sm shadow-md hover:shadow-lg">
                {{ $submitLabel }}
            </button>
        </div>
    </div>

    <!-- Right Area: Map -->
    <div class="flex-1 bg-gray-100 relative">
        <div class="absolute inset-0 p-4">
            <!-- Map Drawing Component for Point -->
            @include('admin.components.map-drawer', [
                'drawType' => 'point',
                'initialGeometry' => old('geometry', $initialGeometry),
                'center' => [$initialCoordinates['lat'], $initialCoordinates['lng']],
                'zoom' => 15,
                'height' => 'h-full',
            ])
            @include('admin.components.load-geometry')
        </div>
    </div>
</form>


