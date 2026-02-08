@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit E-Tiket</h1>
        <a href="{{ route('admin.tickets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Place Selection -->
                <div class="md:col-span-2">
                    <label for="place_id" class="block text-sm font-medium text-gray-700 mb-2">Destinasi Wisata *</label>
                    <select name="place_id" id="place_id" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors"
                            onchange="updatePlaceImage(this)">
                        <option value="" data-image="">Pilih Destinasi</option>
                        @foreach($places as $place)
                            <option value="{{ $place->id }}" 
                                    data-image="{{ str_starts_with($place->image_path, 'http') ? $place->image_path : (str_starts_with($place->image_path, 'images/') ? asset($place->image_path) : asset('storage/' . $place->image_path)) }}"
                                    {{ old('place_id', $ticket->place_id) == $place->id ? 'selected' : '' }}>
                                {{ $place->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Image Preview -->
                    <div id="place_image_preview" class="mt-4 hidden transition-all duration-300">
                        <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wider">Preview Destinasi</p>
                        <div class="relative w-full aspect-video rounded-lg overflow-hidden border border-gray-200 shadow-sm group">
                            <img src="" alt="Preview Destinasi" id="preview_img" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                                <span class="text-white text-sm font-bold shadow-black drop-shadow-md" id="preview_name"></span>
                            </div>
                        </div>
                    </div>

                    @error('place_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

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
                                setTimeout(() => previewContainer.classList.remove('opacity-0'), 10);
                            } else {
                                previewContainer.classList.add('hidden');
                                previewImg.src = '';
                            }
                        }

                        // Run on load
                        document.addEventListener('DOMContentLoaded', () => {
                            const select = document.getElementById('place_id');
                            if (select.value) updatePlaceImage(select);
                        });
                    </script>
                </div>

                <!-- Ticket Name -->
                <div class="md:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Tiket *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $ticket->name) }}" required 
                           placeholder="Contoh: Tiket Masuk"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ticket Type -->
                <div class="md:col-span-1">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Tiket *</label>
                    <select name="type" id="type" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="general" {{ old('type', $ticket->type) == 'general' ? 'selected' : '' }}>Umum</option>
                        <option value="adult" {{ old('type', $ticket->type) == 'adult' ? 'selected' : '' }}>Dewasa</option>
                        <option value="child" {{ old('type', $ticket->type) == 'child' ? 'selected' : '' }}>Anak-anak</option>
                        <option value="foreigner" {{ old('type', $ticket->type) == 'foreigner' ? 'selected' : '' }}>Mancanegara</option>
                        <option value="vehicle" {{ old('type', $ticket->type) == 'vehicle' ? 'selected' : '' }}>Kendaraan</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" 
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                              placeholder="Deskripsi tiket (opsional)">{{ old('description', $ticket->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Weekday -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga Weekday (Rp) *</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $ticket->price) }}" required min="0" step="1000"
                           placeholder="50000"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Weekend -->
                <div>
                    <label for="price_weekend" class="block text-sm font-medium text-gray-700 mb-2">Harga Weekend (Rp)</label>
                    <input type="number" name="price_weekend" id="price_weekend" value="{{ old('price_weekend', $ticket->price_weekend) }}" min="0" step="1000"
                           placeholder="Kosongkan jika sama dengan weekday"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <p class="mt-1 text-xs text-gray-500">Harga khusus Sabtu, Minggu & Hari Libur</p>
                    @error('price_weekend')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quota -->
                <div>
                    <label for="quota" class="block text-sm font-medium text-gray-700 mb-2">Kuota per Hari</label>
                    <input type="number" name="quota" id="quota" value="{{ old('quota', $ticket->quota) }}" min="1"
                           placeholder="Kosongkan untuk unlimited"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ada batasan kuota</p>
                    @error('quota')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valid Days -->
                <div>
                    <label for="valid_days" class="block text-sm font-medium text-gray-700 mb-2">Masa Berlaku (Hari) *</label>
                    <input type="number" name="valid_days" id="valid_days" value="{{ old('valid_days', $ticket->valid_days) }}" required min="1"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <p class="mt-1 text-xs text-gray-500">Berapa hari tiket ini berlaku sejak tanggal kunjungan</p>
                    @error('valid_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $ticket->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <span class="ml-2 text-sm text-gray-700">Aktifkan Tiket</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Tiket yang tidak aktif tidak akan ditampilkan di halaman publik</p>
                </div>

                <!-- Terms & Conditions -->
                <div class="md:col-span-2">
                    <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Syarat & Ketentuan</label>
                    <textarea name="terms_conditions" id="terms_conditions" rows="4" 
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                              placeholder="Masukkan syarat dan ketentuan penggunaan tiket">{{ old('terms_conditions', $ticket->terms_conditions) }}</textarea>
                    @error('terms_conditions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.tickets.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    Batal
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Update Tiket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
