<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500">E-Tiket</p>
                <h2 class="font-semibold text-xl md:text-2xl text-gray-800 leading-tight">
                    Tambah Tiket Baru
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
            <form action="{{ route('admin.tickets.store') }}" method="POST" class="space-y-6" 
                  x-data="{ 
                      tickets: [
                          { id: Date.now(), name: '', type: '', price: '', price_weekend: '', quota: '', valid_days: 1, is_active: true }
                      ],
                      addTicket() {
                          this.tickets.push({ 
                              id: Date.now(), 
                              name: '', 
                              type: 'general', 
                              price: '', 
                              price_weekend: '', 
                              quota: '', 
                              valid_days: 1, 
                              is_active: true 
                          });
                      },
                      removeTicket(index) {
                          this.tickets.splice(index, 1);
                      }
                  }">
                @csrf

                <!-- Section: Destinasi (Tetap Satu) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Destinasi Wisata</h3>
                            <p class="text-xs text-gray-500">Pilih destinasi untuk tiket yang akan dibuat</p>
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
                                        {{ old('place_id') == $place->id ? 'selected' : '' }}>
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

                <!-- Dynamic Ticket Input Section -->
                <div class="space-y-6">
                    <template x-for="(ticket, index) in tickets" :key="ticket.id">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative group transition-all duration-300 hover:shadow-md">
                            
                            <!-- Header per Item -->
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center font-bold text-lg">
                                        <span x-text="index + 1"></span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800">Detail Tiket</h3>
                                        <p class="text-xs text-gray-500">Informasi, Harga & Pengaturan</p>
                                    </div>
                                </div>
                                
                                <button type="button" @click="removeTicket(index)" x-show="tickets.length > 1" 
                                        class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                        title="Hapus Tiket Ini">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
    
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Informasi Tiket -->
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-700 text-sm border-l-4 border-green-500 pl-3">Informasi</h4>
                                    
                                    <div>
                                        <label :for="'name_'+index" class="block text-sm font-medium text-gray-700 mb-1">Nama Tiket *</label>
                                        <input type="text" :name="'tickets['+index+'][name]'" :id="'name_'+index" x-model="ticket.name" required 
                                               placeholder="Contoh: Tiket Masuk Dewasa"
                                               class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm">
                                    </div>
    
                                    <div>
                                        <label :for="'type_'+index" class="block text-sm font-medium text-gray-700 mb-1">Tipe Tiket *</label>
                                        <select :name="'tickets['+index+'][type]'" :id="'type_'+index" x-model="ticket.type" required 
                                                class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm">
                                            <option value="">-- Pilih Tipe --</option>
                                            <option value="general">Umum</option>
                                            <option value="adult">Dewasa</option>
                                            <option value="child">Anak-anak</option>
                                            <option value="foreigner">Mancanegara</option>
                                        </select>
                                    </div>
                                </div>
    
                                <!-- Harga -->
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-700 text-sm border-l-4 border-purple-500 pl-3">Harga</h4>
                                    
                                    <div>
                                        <label :for="'price_'+index" class="block text-sm font-medium text-gray-700 mb-1">Harga Weekday (Rp) *</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">Rp</span>
                                            <input type="number" :name="'tickets['+index+'][price]'" :id="'price_'+index" x-model="ticket.price" required min="0" step="1000"
                                                   placeholder="50000"
                                                   class="w-full pl-9 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm">
                                        </div>
                                    </div>
    
                                    <div>
                                        <label :for="'price_weekend_'+index" class="block text-sm font-medium text-gray-700 mb-1">Harga Weekend (Rp)</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-sm">Rp</span>
                                            <input type="number" :name="'tickets['+index+'][price_weekend]'" :id="'price_weekend_'+index" x-model="ticket.price_weekend" min="0" step="1000"
                                                   placeholder="Kosongkan jika sama"
                                                   class="w-full pl-9 border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm">
                                        </div>
                                    </div>
                                </div>
    
                                <!-- Pengaturan -->
                                <div class="md:col-span-2 space-y-4 pt-4 border-t border-gray-50">
                                    <h4 class="font-semibold text-gray-700 text-sm border-l-4 border-yellow-500 pl-3">Pengaturan Tambahan</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label :for="'quota_'+index" class="block text-sm font-medium text-gray-700 mb-1">Kuota Harian</label>
                                            <input type="number" :name="'tickets['+index+'][quota]'" :id="'quota_'+index" x-model="ticket.quota" min="1"
                                                   placeholder="Unlimited"
                                                   class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm">
                                        </div>
    
                                        <div>
                                            <label :for="'valid_days_'+index" class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku (Hari) *</label>
                                            <input type="number" :name="'tickets['+index+'][valid_days]'" :id="'valid_days_'+index" x-model="ticket.valid_days" required min="1"
                                                   class="w-full border-gray-200 rounded-xl shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm">
                                        </div>
    
                                        <div class="flex items-center pt-6">
                                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                                <input type="checkbox" :name="'tickets['+index+'][is_active]'" value="1" x-model="ticket.is_active"
                                                       class="w-4 h-4 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                                                <span class="text-sm font-medium text-gray-700">Aktifkan Tiket Ini</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Add More Button -->
                <div class="flex justify-center">
                    <button type="button" @click="addTicket()" 
                            class="group flex items-center gap-2 px-6 py-3 bg-white border-2 border-dashed border-gray-300 rounded-xl text-gray-600 font-semibold hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-all duration-300">
                        <div class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <span>Tambah Varian Tiket Lain</span>
                    </button>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.tickets.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-save"></i>
                        <span>Simpan Semua Tiket</span>
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
