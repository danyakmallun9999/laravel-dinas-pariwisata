<form action="{{ $action }}" method="POST" class="flex h-[calc(100vh-80px)]">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <!-- Left Sidebar: Form Inputs -->
    <div class="w-96 min-w-[380px] bg-white border-r border-gray-200 flex flex-col z-10">
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Detail Infrastruktur</h3>
                <p class="text-xs text-gray-500">Isi informasi detail mengenai infrastruktur ini.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Infrastruktur</label>
                <input type="text" name="name" value="{{ old('name', $infrastructure->name) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required placeholder="Contoh: Jalan Raya Mayong">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
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
                    <input type="number" name="length_meters"
                        value="{{ old('length_meters', $infrastructure->length_meters) }}" step="0.01"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('length_meters')" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lebar (m)</label>
                    <input type="number" name="width_meters"
                        value="{{ old('width_meters', $infrastructure->width_meters) }}" step="0.01"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('width_meters')" class="mt-2" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                <select name="condition"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih kondisi</option>
                    <option value="good" @selected(old('condition', $infrastructure->condition) === 'good')>Baik</option>
                    <option value="fair" @selected(old('condition', $infrastructure->condition) === 'fair')>Cukup</option>
                    <option value="poor" @selected(old('condition', $infrastructure->condition) === 'poor')>Buruk</option>
                </select>
                <x-input-error :messages="$errors->get('condition')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori (Opsional)</label>
                <select name="category_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tidak ada kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $infrastructure->category_id) == $category->id)>
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
                    placeholder="Deskripsi singkat...">{{ old('description', $infrastructure->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-3">
            <a href="{{ route('admin.infrastructures.index') }}"
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
            <!-- Map Drawing Component for Line -->
            @include('admin.components.map-drawer', [
                'drawType' => 'line',
                'initialGeometry' => old('geometry', $infrastructure->geometry),
                'height' => 'h-full',
            ])
            @include('admin.components.load-geometry')
            <x-input-error :messages="$errors->get('geometry')" class="mt-2" />
        </div>
    </div>
</form>
