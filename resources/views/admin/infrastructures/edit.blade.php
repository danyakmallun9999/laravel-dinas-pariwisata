<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm text-gray-500">Admin Panel · Infrastruktur</p>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Edit Infrastruktur
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <form action="{{ route('admin.infrastructures.update', $infrastructure) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Infrastruktur</label>
                                <input type="text" name="name" value="{{ old('name', $infrastructure->name) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                                <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="road" @selected(old('type', $infrastructure->type) === 'road')>Jalan</option>
                                    <option value="river" @selected(old('type', $infrastructure->type) === 'river')>Sungai</option>
                                    <option value="irrigation" @selected(old('type', $infrastructure->type) === 'irrigation')>Irigasi</option>
                                    <option value="electricity" @selected(old('type', $infrastructure->type) === 'electricity')>Listrik</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Panjang (m)</label>
                                    <input type="number" name="length_meters" value="{{ old('length_meters', $infrastructure->length_meters) }}" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <x-input-error :messages="$errors->get('length_meters')" class="mt-2" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Lebar (m)</label>
                                    <input type="number" name="width_meters" value="{{ old('width_meters', $infrastructure->width_meters) }}" step="0.01" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <x-input-error :messages="$errors->get('width_meters')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                                <select name="condition" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih kondisi</option>
                                    <option value="good" @selected(old('condition', $infrastructure->condition) === 'good')>Baik</option>
                                    <option value="fair" @selected(old('condition', $infrastructure->condition) === 'fair')>Cukup</option>
                                    <option value="poor" @selected(old('condition', $infrastructure->condition) === 'poor')>Buruk</option>
                                </select>
                                <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori (Opsional)</label>
                                <select name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Tidak ada kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id', $infrastructure->category_id) == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $infrastructure->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            </div>
                        </div>

                        <!-- Map Drawing Component -->
                        @include('admin.components.map-drawer', [
                            'drawType' => 'line',
                            'initialGeometry' => old('geometry', $infrastructure->geometry)
                        ])
                        <x-input-error :messages="$errors->get('geometry')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.infrastructures.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                            Perbarui Infrastruktur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

