<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.tour-packages.index') }}" 
                   class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200"
                   wire:navigate>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                        Edit Paket Liburan
                    </h2>
                    <p class="text-sm text-gray-500">Ubah informasi paket wisata khusus</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden p-6 md:p-8">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.tour-packages.update', $tourPackage) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-8">
                        
                        <!-- SECTION 1: Informasi Dasar -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Biro Wisata -->
                                <div>
                                    <label for="travel_agency_id" class="block text-sm font-semibold text-gray-900 mb-2">Pilih Biro Wisata <span class="text-red-500">*</span></label>
                                    <select name="travel_agency_id" id="travel_agency_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">-- Pilih Biro Wisata --</option>
                                        @foreach($agencies as $agency)
                                            <option value="{{ $agency->id }}" {{ old('travel_agency_id', $tourPackage->travel_agency_id) == $agency->id ? 'selected' : '' }}>{{ $agency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Destinasi Terkait -->
                                <div>
                                    <label for="place_id" class="block text-sm font-semibold text-gray-900 mb-2">Destinasi Terkait <span class="text-red-500">*</span></label>
                                    <select name="place_id" id="place_id" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">-- Pilih Destinasi --</option>
                                        @foreach($places as $place)
                                            <option value="{{ $place->id }}" {{ old('place_id', $tourPackage->place_id) == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nama Paket -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Nama Paket Wisata <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" required value="{{ old('name', $tourPackage->name) }}"
                                           class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                           placeholder="Contoh: Paket Honeymoon 3H 2M Spesial Karimunjawa">
                                </div>

                                <!-- Harga Mulai Dari -->
                                <div>
                                    <label for="price_start" class="block text-sm font-semibold text-gray-900 mb-2">Harga (Mulai Dari) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="price_start" id="price_start" required value="{{ old('price_start', $tourPackage->price_start) }}" min="0" step="1000"
                                               class="pl-12 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                </div>

                                <!-- Durasi -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-2">Durasi Wisata <span class="text-red-500">*</span></label>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 relative">
                                            <input type="number" name="duration_days" required value="{{ old('duration_days', $tourPackage->duration_days) }}" min="1"
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-center">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-400 text-xs">Hari</span>
                                            </div>
                                        </div>
                                        <div class="text-gray-400 font-bold">/</div>
                                        <div class="flex-1 relative">
                                            <input type="number" name="duration_nights" required value="{{ old('duration_nights', $tourPackage->duration_nights) }}" min="0"
                                                   class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-center">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-400 text-xs">Malam</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                                    <textarea id="description" name="description" rows="4" required
                                              class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-4 placeholder-gray-400">{{ old('description', $tourPackage->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Fasilitas / Inclusions -->
                        <div x-data="{
                            inclusions: {{ json_encode(old('inclusions', is_array($tourPackage->inclusions) && count($tourPackage->inclusions) > 0 ? $tourPackage->inclusions : [''])) }},
                            addInclusion() {
                                this.inclusions.push('');
                            },
                            removeInclusion(index) {
                                if (this.inclusions.length > 1) {
                                    this.inclusions.splice(index, 1);
                                } else {
                                    this.inclusions[0] = '';
                                }
                            }
                        }">
                            <div class="flex items-center justify-between mb-4 border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-900">Fasilitas Termasuk (Inclusions)</h3>
                                <button type="button" @click="addInclusion()" class="text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-lg hover:bg-blue-100 transition-colors font-semibold">
                                    <i class="fa-solid fa-plus mr-1"></i> Tambah
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                <template x-for="(inclusion, index) in inclusions" :key="index">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 flex items-center justify-center bg-gray-50 rounded-xl border border-gray-200 text-gray-400">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <input type="text" x-model="inclusions[index]" :name="'inclusions['+index+']'" required
                                               class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                               placeholder="Contoh: Tiket Kapal PP Kelas Eksekutif">
                                        <button type="button" @click="removeInclusion(index)" class="w-10 h-10 flex items-center justify-center text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- SECTION 3: Itinerary -->
                        <div x-data="{
                            itinerary: {{ json_encode(old('itinerary', is_array($tourPackage->itinerary) && count($tourPackage->itinerary) > 0 ? $tourPackage->itinerary : [['day' => 1, 'time' => '07:00', 'activity' => '']])) }},
                            addItinerary() {
                                let lastDay = this.itinerary.length > 0 ? this.itinerary[this.itinerary.length - 1].day : 1;
                                this.itinerary.push({ day: lastDay, time: '', activity: '' });
                            },
                            removeItinerary(index) {
                                if (this.itinerary.length > 1) {
                                    this.itinerary.splice(index, 1);
                                }
                            }
                        }">
                            <div class="flex items-center justify-between mb-4 border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-900">Itinerary (Jadwal Perjalanan)</h3>
                                <button type="button" @click="addItinerary()" class="text-sm text-blue-600 bg-blue-50 px-3 py-1 rounded-lg hover:bg-blue-100 transition-colors font-semibold">
                                    <i class="fa-solid fa-plus mr-1"></i> Tambah Jadwal
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <template x-for="(item, index) in itinerary" :key="index">
                                    <div class="flex items-start gap-3 bg-gray-50/50 p-4 rounded-2xl border border-gray-200">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 flex-1">
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-semibold text-gray-500 mb-1">Hari Ke-</label>
                                                <input type="number" x-model="item.day" :name="'itinerary['+index+'][day]'" required min="1"
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-center">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-semibold text-gray-500 mb-1">Waktu</label>
                                                <input type="text" x-model="item.time" :name="'itinerary['+index+'][time]'" required
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: 08:00 - 10:00">
                                            </div>
                                            <div class="md:col-span-7">
                                                <label class="block text-xs font-semibold text-gray-500 mb-1">Aktivitas</label>
                                                <input type="text" x-model="item.activity" :name="'itinerary['+index+'][activity]'" required
                                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Deskripsi aktivitas...">
                                            </div>
                                        </div>
                                        <button type="button" @click="removeItinerary(index)" class="mt-6 w-10 h-10 flex items-center justify-center text-red-500 hover:bg-red-50 rounded-xl transition-colors shrink-0">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.tour-packages.index') }}" 
                           class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-100 border border-gray-200 transition-all">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                            <i class="fa-solid fa-save"></i>
                            Perbarui Paket
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
