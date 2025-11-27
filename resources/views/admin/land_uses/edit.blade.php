<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Admin Panel · Penggunaan Lahan</p>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Edit Penggunaan Lahan
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <form action="{{ route('admin.land-uses.update', $landUse) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lahan</label>
                                <input type="text" name="name" value="{{ old('name', $landUse->name) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                                <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="settlement" @selected(old('type', $landUse->type) === 'settlement')>Permukiman</option>
                                    <option value="rice_field" @selected(old('type', $landUse->type) === 'rice_field')>Persawahan</option>
                                    <option value="plantation" @selected(old('type', $landUse->type) === 'plantation')>Perkebunan</option>
                                    <option value="forest" @selected(old('type', $landUse->type) === 'forest')>Hutan</option>
                                    <option value="other" @selected(old('type', $landUse->type) === 'other')>Lainnya</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Luas (Hektar)</label>
                                <input type="number" name="area_hectares" value="{{ old('area_hectares', $landUse->area_hectares) }}" step="0.0001" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <x-input-error :messages="$errors->get('area_hectares')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pemilik</label>
                                <input type="text" name="owner" value="{{ old('owner', $landUse->owner) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <x-input-error :messages="$errors->get('owner')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $landUse->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            </div>
                        </div>

                        <!-- Map Drawing Component -->
                        @include('admin.components.map-drawer', [
                            'drawType' => 'polygon',
                            'initialGeometry' => old('geometry', $landUse->geometry)
                        ])
                        <x-input-error :messages="$errors->get('geometry')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.land-uses.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Perbarui Penggunaan Lahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

