<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.cultures.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm" wire:navigate>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <p class="text-sm text-gray-500 font-medium mb-0.5">Pembaruan Data</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-800 leading-tight">
                    Edit Budaya
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.cultures.update', $culture) }}" method="POST" enctype="multipart/form-data" class="space-y-6"
                  x-data="{
                      selectedCategory: '{{ old('category', $culture->category) }}',
                      get showLocationTime() {
                          const hide = ['Kemahiran & Kerajinan Tradisional (Kriya)', 'Seni Pertunjukan', 'Kuliner Khas'];
                          return !hide.includes(this.selectedCategory);
                      }
                  }">
                @csrf
                @method('PUT')

                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200 shadow-sm">
                    <div class="p-6 md:p-8 rounded-[2rem] border border-gray-100 bg-white space-y-8">
                        
                        <!-- Informasi Umum -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Umum
                            </h3>
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Budaya <span class="text-red-500">*</span></label>
                                    <x-text-input id="name" name="name" type="text" class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" :value="old('name', $culture->name)" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Category Dropdown -->
                                <div>
                                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="category" :value="selectedCategory">
                                        <x-dropdown align="left" width="full" contentClasses="py-1 bg-white max-h-60 overflow-y-auto">
                                            <x-slot name="trigger">
                                                <button type="button" class="w-full flex items-center justify-between text-left px-4 py-3 border border-gray-300 rounded-xl bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors shadow-sm cursor-pointer">
                                                    <span x-text="selectedCategory" class="block truncate font-medium"></span>
                                                    <i class="fa-solid fa-chevron-down text-gray-400"></i>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                @foreach(['Kemahiran & Kerajinan Tradisional (Kriya)', 'Adat Istiadat, Ritus, & Perayaan Tradisional', 'Seni Pertunjukan', 'Kuliner Khas'] as $cat)
                                                    <button type="button" @click="selectedCategory = '{{ $cat }}'" class="block w-full text-left px-4 py-3 text-sm hover:bg-gray-50 transition-colors" :class="selectedCategory === '{{ $cat }}' ? 'font-bold text-blue-600 bg-blue-50/50' : 'text-gray-700'">
                                                        {{ $cat }}
                                                    </button>
                                                @endforeach
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('category')" />
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Media & Deskripsi -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-images text-blue-500"></i> Media & Konten
                            </h3>
                            <div class="space-y-6">
                                <!-- Foto Utama -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Utama</label>
                                    @if($culture->image)
                                        <div class="mb-3 flex items-start gap-4 p-3 bg-gray-50 border border-gray-200 rounded-2xl w-fit shadow-sm">
                                            <img src="{{ $culture->image_url }}" class="h-20 w-auto rounded-xl object-cover border border-gray-200 shadow-sm bg-white">
                                            <div class="py-1">
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200 mb-1">Saat Ini</span>
                                                <p class="text-[11px] text-gray-500">Upload file baru di bawah<br>untuk mengganti foto ini.</p>
                                            </div>
                                        </div>
                                    @endif
                                    <input id="image" name="image" type="file" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors" />
                                    <x-input-error class="mt-2" :messages="$errors->get('image')" />
                                </div>

                                <!-- Galeri Foto Existing -->
                                @if($culture->images->isNotEmpty())
                                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 shadow-sm">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Galeri Foto Saat Ini</label>
                                    <p class="text-[11px] text-gray-500 mb-3"><i class="fa-solid fa-info-circle text-blue-500"></i> Klik tombol hapus (merah) untuk menghapus foto dari galeri.</p>
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                        @foreach($culture->images as $img)
                                        <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm hover:ring-2 hover:ring-red-400 transition-all duration-200">
                                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                                            <!-- Note: Destroy form here should ideally be an ajax call or separate, but maintaining existing code behavior -->
                                            <form action="{{ route('admin.cultures.images.destroy', $img) }}" method="POST"
                                                  class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/50 transition-colors backdrop-blur-[1px] group-hover:backdrop-blur-sm"
                                                  onsubmit="return confirm('Hapus foto ini dari galeri?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="w-8 h-8 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center shadow-lg hover:bg-red-600 hover:scale-110">
                                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Upload Foto Baru (Tambah ke Galeri) -->
                                <div x-data="{
                                    previews: [],
                                    handleFiles(event) {
                                        this.previews = [];
                                        Array.from(event.target.files).forEach(file => {
                                            const reader = new FileReader();
                                            reader.onload = (e) => this.previews.push(e.target.result);
                                            reader.readAsDataURL(file);
                                        });
                                    }
                                }">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tambah Foto Galeri Baru</label>
                                    <input id="images" name="images[]" type="file" accept="image/*" multiple
                                           @change="handleFiles($event)"
                                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors" />
                                    @if($errors->has('images.*'))
                                        <p class="mt-2 text-sm text-red-600">{{ $errors->first('images.*') }}</p>
                                    @endif
                                    
                                    <div x-show="previews.length > 0" x-transition class="mt-4 p-4 rounded-xl border border-dashed border-gray-300 bg-gray-50/50">
                                        <p class="text-[11px] font-bold text-gray-500 uppercase mb-3 text-center">Preview Penambahan Galeri</p>
                                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                            <template x-for="(src, i) in previews" :key="i">
                                                <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm">
                                                    <img :src="src" class="w-full h-full object-cover">
                                                    <span class="absolute top-1.5 right-1.5 bg-green-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">BARU</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Singkat</label>
                                    <textarea id="description" name="description" rows="3" class="block w-full border-gray-300 rounded-xl focus:border-blue-500 focus:ring-blue-500 shadow-sm">{{ old('description', $culture->description) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                </div>

                                <!-- Content -->
                                <div>
                                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-1">Konten Lengkap</label>
                                    <textarea id="content" name="content" rows="6" class="block w-full border-gray-300 rounded-xl focus:border-blue-500 focus:ring-blue-500 shadow-sm">{{ old('content', $culture->content) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('content')" />
                                </div>

                                <!-- YouTube Embed URL -->
                                <div x-data="{
                                    youtubeUrl: '{{ old('youtube_url', $culture->youtube_url) }}',
                                    get embedUrl() {
                                        if (!this.youtubeUrl) return null;
                                        let id = '';
                                        const url = this.youtubeUrl.trim();
                                        const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
                                        if (match) id = match[1];
                                        return id ? 'https://www.youtube.com/embed/' + id : null;
                                    }
                                }">
                                    <label for="youtube_url" class="block text-sm font-semibold text-gray-700 mb-1">Link YouTube</label>
                                    <div class="flex rounded-xl shadow-sm border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                                        <span class="inline-flex items-center px-4 bg-gray-50 text-gray-500 border-r border-gray-300">
                                            <i class="fa-brands fa-youtube text-red-500 text-lg"></i>
                                        </span>
                                        <input id="youtube_url" name="youtube_url" type="url"
                                            class="flex-1 block w-full border-0 px-3 py-2 sm:text-sm focus:ring-0"
                                            x-model="youtubeUrl"
                                            placeholder="https://youtube.com/watch?v=..." />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('youtube_url')" />

                                    <!-- Preview -->
                                    <div x-show="embedUrl" x-transition class="mt-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Video Preview</p>
                                        <div class="relative w-full rounded-lg overflow-hidden shadow-sm border border-gray-300 bg-black" style="padding-top: 56.25%">
                                            <iframe :src="embedUrl"
                                                class="absolute inset-0 w-full h-full"
                                                frameborder="0" allowfullscreen
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                            </iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location & Time Details -->
                        <div x-show="showLocationTime" x-transition x-cloak>
                            <hr class="border-gray-100 my-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-blue-500"></i> Detail Acara / Tempat
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-blue-50/30 p-5 rounded-2xl border border-blue-50">
                                <!-- Location -->
                                <div>
                                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi</label>
                                    <x-text-input id="location" name="location" type="text" class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" :value="old('location', $culture->location)" placeholder="Cth: Alun-Alun Jepara" />
                                    <x-input-error class="mt-2" :messages="$errors->get('location')" />
                                </div>

                                <!-- Time -->
                                <div>
                                    <label for="time" class="block text-sm font-semibold text-gray-700 mb-1">Waktu / Jadwal</label>
                                    <x-text-input id="time" name="time" type="text" class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" :value="old('time', $culture->time)" placeholder="Cth: Minggu ke-2, Pukul 10:00 WIB" />
                                    <x-input-error class="mt-2" :messages="$errors->get('time')" />
                                </div>
                            </div>
                        </div>

                        <!-- Lokasi Rekomendasi (Khusus Kuliner) -->
                        <div x-show="selectedCategory === 'Kuliner Khas'" x-transition x-cloak
                             x-data="{ 
                                locations: @js(old('locations', $culture->locations->map(fn($l) => ['name' => $l->name, 'address' => $l->address, 'google_maps_url' => $l->google_maps_url]))) 
                             }" x-init="if(locations.length === 0) locations.push({name: '', address: '', google_maps_url: ''})">
                            <hr class="border-gray-100 my-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fa-solid fa-map-location-dot text-red-500"></i> Titik Lokasi Rekomendasi
                                </h3>
                                <button type="button" @click="locations.push({name: '', address: '', google_maps_url: ''})" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-xl text-xs font-bold hover:bg-blue-100 transition-colors border border-blue-200">
                                    <i class="fa-solid fa-plus"></i> Tambah Lokasi
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(loc, index) in locations" :key="index">
                                    <div class="bg-gray-50 p-6 rounded-[2rem] border border-gray-200 relative group">
                                        <button type="button" @click="locations.splice(index, 1)" 
                                                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors"
                                                x-show="locations.length > 1">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="md:col-span-2">
                                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1.5 ml-1">Nama Tempat <span class="text-red-500">*</span></label>
                                                <input type="text" :name="`locations[${index}][name]`" x-model="loc.name" 
                                                       class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                                       placeholder="Cth: Warung Makan Bu Sum" required>
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1.5 ml-1">Alamat Singkat</label>
                                                <input type="text" :name="`locations[${index}][address]`" x-model="loc.address" 
                                                       class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                                       placeholder="Cth: Jl. Kartini No. 12">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1.5 ml-1">Google Maps URL</label>
                                                <div class="flex gap-2">
                                                    <input type="url" :name="`locations[${index}][google_maps_url]`" x-model="loc.google_maps_url" 
                                                           class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                                           placeholder="https://maps.google.com/...">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1.5 ml-1">Latitude</label>
                                                <input type="text" :name="`locations[${index}][latitude]`" x-model="loc.latitude" 
                                                       class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                                       placeholder="Cth: -6.589123">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1.5 ml-1">Longitude</label>
                                                <input type="text" :name="`locations[${index}][longitude]`" x-model="loc.longitude" 
                                                       class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm"
                                                       placeholder="Cth: 110.678123">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <p class="mt-3 text-[11px] text-gray-400 italic">
                                * Berikan titik lokasi yang direkomendasikan untuk menikmati kuliner ini.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 px-2">
                    <a href="{{ route('admin.cultures.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">Batal</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl font-bold text-sm text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
