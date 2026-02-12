<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500">E-Tiket</p>
                <h2 class="font-semibold text-xl md:text-2xl text-gray-800 leading-tight">
                    Edit Tiket
                </h2>
            </div>
            <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center gap-2 px-3 py-2 md:px-4 md:py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
                <i class="fa-solid fa-arrow-left text-xs md:text-sm"></i>
                <span class="hidden md:inline">Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section: Destinasi -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Destinasi Wisata</h3>
                            <p class="text-xs text-gray-500">Pilih destinasi untuk tiket ini</p>
                        </div>
                    </div>

                    <div>
                        <label for="place_id" class="block text-sm font-semibold text-gray-700 mb-2">Pilih Destinasi *</label>
                        <select name="place_id" id="place_id" required 
                                class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors"
                                onchange="updatePlaceImage(this)">
                            <option value="" data-image="">-- Pilih Destinasi --</option>
                            @foreach($places as $place)
                                <option value="{{ $place->id }}" 
                                        data-image="{{ str_starts_with($place->image_path, 'http') ? $place->image_path : (str_starts_with($place->image_path, 'images/') ? asset($place->image_path) : asset('storage/' . $place->image_path)) }}"
                                        {{ old('place_id', $ticket->place_id) == $place->id ? 'selected' : '' }}>
                                    {{ $place->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('place_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="place_image_preview" class="mt-4 hidden transition-all duration-300">
                            <div class="relative w-full aspect-video rounded-xl overflow-hidden border border-gray-100 shadow-sm group">
                                <img src="" alt="Preview Destinasi" id="preview_img" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-4">
                                    <span class="text-white text-sm font-bold" id="preview_name"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Informasi Tiket -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Informasi Tiket</h3>
                            <p class="text-xs text-gray-500">Detail nama dan tipe tiket</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Tiket *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $ticket->name) }}" required 
                                   placeholder="Contoh: Tiket Masuk"
                                   class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Tipe Tiket *</label>
                            <select name="type" id="type" required 
                                    class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <option value="general" {{ old('type', $ticket->type) == 'general' ? 'selected' : '' }}>Umum</option>
                                <option value="adult" {{ old('type', $ticket->type) == 'adult' ? 'selected' : '' }}>Dewasa</option>
                                <option value="child" {{ old('type', $ticket->type) == 'child' ? 'selected' : '' }}>Anak-anak</option>
                                <option value="foreigner" {{ old('type', $ticket->type) == 'foreigner' ? 'selected' : '' }}>Mancanegara</option>

                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                    </div>
                </div>

                <!-- Section: Harga -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Harga</h3>
                            <p class="text-xs text-gray-500">Atur harga weekday dan weekend</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Harga Weekday (Rp) *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                                <input type="number" name="price" id="price" value="{{ old('price', $ticket->price) }}" required min="0" step="1000"
                                       placeholder="50000"
                                       class="w-full pl-12 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price_weekend" class="block text-sm font-semibold text-gray-700 mb-2">Harga Weekend (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                                <input type="number" name="price_weekend" id="price_weekend" value="{{ old('price_weekend', $ticket->price_weekend) }}" min="0" step="1000"
                                       placeholder="Kosongkan jika sama"
                                       class="w-full pl-12 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            </div>
                            <p class="mt-1 text-xs text-gray-400">Sabtu, Minggu & Hari Libur</p>
                            @error('price_weekend')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Pengaturan -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                            <i class="fa-solid fa-gear"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Pengaturan</h3>
                            <p class="text-xs text-gray-500">Kuota, masa berlaku, dan status</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="quota" class="block text-sm font-semibold text-gray-700 mb-2">Kuota per Hari</label>
                            <input type="number" name="quota" id="quota" value="{{ old('quota', $ticket->quota) }}" min="1"
                                   placeholder="Unlimited"
                                   class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ada batasan</p>
                            @error('quota')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="valid_days" class="block text-sm font-semibold text-gray-700 mb-2">Masa Berlaku (Hari) *</label>
                            <input type="number" name="valid_days" id="valid_days" value="{{ old('valid_days', $ticket->valid_days) }}" required min="1"
                                   class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <p class="mt-1 text-xs text-gray-400">Sejak tanggal kunjungan</p>
                            @error('valid_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <label class="flex items-center gap-3 cursor-pointer p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors w-full">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $ticket->is_active) ? 'checked' : '' }}
                                       class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                <div>
                                    <span class="text-sm font-semibold text-gray-700">Aktifkan Tiket</span>
                                    <p class="text-xs text-gray-400">Tampilkan di halaman publik</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>



                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.tickets.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
                        <i class="fa-solid fa-save mr-2"></i>Update Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePlaceImage(select) {
            const option = select.options[select.selectedIndex];
            const imageUrl = option.getAttribute('data-image');
            const placeName = option.text.trim();
            const previewContainer = document.getElementById('place_image_preview');
            const previewImg = document.getElementById('preview_img');
            const previewName = document.getElementById('preview_name');
            
            if (imageUrl) {
                previewImg.src = imageUrl;
                previewName.textContent = placeName;
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
                previewImg.src = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('place_id');
            if (select.value) updatePlaceImage(select);
        });
    </script>
</x-app-layout>
