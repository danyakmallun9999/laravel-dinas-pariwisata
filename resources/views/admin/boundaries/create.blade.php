<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Admin Panel · Batas Wilayah</p>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Tambah Batas Wilayah Baru
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <form action="{{ route('admin.boundaries.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Batas Wilayah</label>
                                    <input type="text" name="name" value="{{ old('name', $boundary->name) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                                    <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="village_boundary" @selected(old('type', $boundary->type) === 'village_boundary')>Batas Desa</option>
                                        <option value="hamlet" @selected(old('type', $boundary->type) === 'hamlet')>Dusun</option>
                                        <option value="other" @selected(old('type', $boundary->type) === 'other')>Lainnya</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Luas (Hektar)</label>
                                    <input type="number" name="area_hectares" value="{{ old('area_hectares', $boundary->area_hectares) }}" step="0.0001" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <x-input-error :messages="$errors->get('area_hectares')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                    <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $boundary->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Map Drawing Component -->
                        @include('admin.components.map-drawer', [
                            'drawType' => 'polygon',
                            'initialGeometry' => old('geometry', $boundary->geometry)
                        ])
                        @include('admin.components.load-geometry')
                        <x-input-error :messages="$errors->get('geometry')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.boundaries.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Simpan Batas Wilayah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

