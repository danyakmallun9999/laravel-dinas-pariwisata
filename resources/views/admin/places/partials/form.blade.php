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

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" 
      class="flex flex-col lg:flex-row h-auto lg:h-[calc(100vh-80px)] pb-20 lg:pb-0"
      x-data="placeForm()">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <!-- Left Side: Form Inputs (40%) -->
    <div class="w-full lg:w-2/5 bg-white border-r border-gray-200 flex flex-col z-10 h-auto lg:h-full">
        <div class="flex-1 overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-5 py-3">
                <h3 class="text-base font-bold text-gray-900">Detail Lokasi</h3>
                <p class="text-xs text-gray-500">Lengkapi informasi detail lokasi wisata</p>
            </div>

            <div class="p-5 space-y-5">
                <!-- Basic Information Section -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-circle-info text-indigo-500 text-xs"></i>
                        Informasi Dasar
                    </h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Lokasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name_id" name="name" 
                               value="{{ old('name', $place->name) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               required placeholder="Contoh: Pantai Kartini"
                               x-ref="name"
                               @input="sourceName = $el.value">
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $place->category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Alamat Lengkap
                        </label>
                        <input type="text" name="address" 
                               value="{{ old('address', $place->address) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               placeholder="Alamat lengkap lokasi...">
                        <x-input-error :messages="$errors->get('address')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Deskripsi
                        </label>
                        <textarea id="description_id" name="description" rows="8"
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                  placeholder="Deskripsi singkat lokasi..."
                                  x-ref="description"
                                  @input="sourceDesc = $el.value">{{ old('description', $place->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Rating (0-5)
                        </label>
                        <input type="number" step="0.1" min="0" max="5" name="rating" 
                               value="{{ old('rating', $place->rating) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               placeholder="0.0">
                    </div>

                    <!-- Flagship Destination Toggle -->
                    <div class="flex items-start bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <div class="flex items-center h-5">
                            <input id="is_flagship" name="is_flagship" type="checkbox" value="1"
                                   @checked(old('is_flagship', $place->is_flagship))
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_flagship" class="font-bold text-gray-800">Tandai sebagai Flagship Destination <i class="fa-solid fa-star text-amber-400 hidden lg:inline-block ml-1"></i></label>
                            <p class="text-gray-500 text-xs mt-0.5">Destinasi flagship akan ditampilkan secara khusus di beranda dan memiliki halaman profil eksklusif (contoh: Karimunjawa). Anda harus menambahkan Biro Wisata dan Paket Liburan untuk destinasi flagship.</p>
                        </div>
                    </div>
                </div>

                <!-- English Translation Section -->
                <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-blue-800 flex items-center gap-2">
                            <i class="fa-solid fa-language text-blue-600 text-xs"></i>
                            English Translation
                        </h4>
                        <button type="button" 
                                @click="autoTranslate"
                                :disabled="isTranslating"
                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="!isTranslating">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-wand-magic-sparkles text-xs"></i> Translate</span>
                            </template>
                             <template x-if="isTranslating">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Translating...</span>
                            </template>
                        </button>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Name (English)</label>
                        <input type="text" id="name_en" name="name_en" 
                               value="{{ old('name_en', $place->name_en) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white"
                               placeholder="English name..."
                               x-ref="name_en">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Description (English)</label>
                        <textarea id="description_en" name="description_en" rows="6"
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white"
                                  placeholder="English description..."
                                  x-ref="description_en">{{ old('description_en', $place->description_en) }}</textarea>
                    </div>
                </div>

                <!-- Operational Info Section -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-clock text-green-500 text-xs"></i>
                        Operasional & Kontak
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jam Operasional</label>
                            <input type="text" name="opening_hours" 
                                   value="{{ old('opening_hours', $place->opening_hours) }}"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   placeholder="08:00 - 17:00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kontak</label>
                            <input type="text" name="contact_info" 
                                   value="{{ old('contact_info', $place->contact_info) }}"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   placeholder="Nomor telepon">
                        </div>
                    </div>
                    
                    <!-- Social Media Repeater -->
                    <div x-data="{
                        socials: {{ Illuminate\Support\Js::from(old('social_media', $place->social_media ?? [])) }},
                        add() {
                            this.socials.push({ platform: '', url: '' });
                        },
                        remove(index) {
                            this.socials.splice(index, 1);
                        }
                    }">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Media Sosial</label>
                        
                        <div class="space-y-2">
                            <template x-for="(social, index) in socials" :key="index">
                                <div class="flex gap-2 items-start">
                                    <div class="w-1/3">
                                        <input type="text" :name="`social_media[${index}][platform]`" x-model="social.platform" 
                                            placeholder="Platform (e.g. IG)"
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div class="flex-1">
                                        <input type="url" :name="`social_media[${index}][url]`" x-model="social.url" 
                                            placeholder="https://..."
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                                    </div>
                                    <button type="button" @click="remove(index)" 
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="add()" 
                            class="mt-2 text-sm text-blue-600 font-medium hover:text-blue-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-plus-circle"></i> Tambah Media Sosial
                        </button>
                    </div>
                </div>

                <!-- Facilities Section -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-ticket text-amber-500 text-xs"></i>
                        Wahana & Fasilitas
                    </h4>
                    
                    @php
                        $ridesInput = old('rides', $place->rides);
                        if (is_array($ridesInput)) {
                            $lines = [];
                            foreach ($ridesInput as $r) {
                                if (is_array($r)) {
                                    $str = $r['name'];
                                    if (!empty($r['price'])) {
                                        $str .= ' - ' . $r['price'];
                                    }
                                    $lines[] = $str;
                                } else {
                                    $lines[] = $r;
                                }
                            }
                            $ridesInput = implode("\n", $lines);
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Wahana</label>
                        <textarea name="rides" rows="2"
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                  placeholder="Daftar wahana (Format: Nama - Harga)">{{ $ridesInput }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Contoh: Jetsky - Rp 150.000</p>
                    </div>

                    @php
                        $facilitiesInput = old('facilities', $place->facilities);
                        if (is_array($facilitiesInput)) {
                            $facilitiesInput = implode("\n", $facilitiesInput);
                        }
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fasilitas</label>
                        <textarea name="facilities" rows="2"
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                  placeholder="Daftar fasilitas yang tersedia...">{{ $facilitiesInput }}</textarea>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-images text-rose-500 text-xs"></i>
                        Foto & Media
                    </h4>
                    
                    <!-- Main Photo -->
                    <div x-data="{ photoName: null, photoPreview: null }">
                        <input type="file" class="hidden" x-ref="photo" name="image"
                            accept="image/*"
                            @change="
                                photoName = $refs.photo.files[0].name;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL($refs.photo.files[0]);
                            " />

                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Utama</label>

                        <!-- Current Photo -->
                        <div class="mb-3" x-show="!photoPreview">
                            @if ($place->image_path)
                                <div class="relative group rounded-lg overflow-hidden">
                                    <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}"
                                        class="w-full h-32 object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">Klik untuk mengganti</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- New Photo Preview -->
                        <div class="mb-3" x-show="photoPreview" style="display: none;">
                            <div class="relative">
                                <span class="block w-full h-32 rounded-lg bg-cover bg-no-repeat bg-center border-2 border-blue-300"
                                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                </span>
                                <span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full font-medium">
                                    Baru
                                </span>
                            </div>
                        </div>

                        <div @click.prevent="$refs.photo.click()"
                            class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all">
                            <i class="fa-solid fa-cloud-upload-alt text-lg text-gray-400 mb-1"></i>
                            <p class="text-xs text-gray-500"><span class="font-semibold text-blue-600">Klik untuk upload</span> (MAX 2MB)</p>
                        </div>
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <!-- Gallery Upload -->
                    <div x-data="{ 
                        previews: [],
                        handleFileSelect(e) {
                            const files = Array.from(e.target.files);
                            this.previews = [];
                            files.forEach(file => {
                                const reader = new FileReader();
                                reader.onload = (event) => {
                                    this.previews.push({
                                        file: file,
                                        url: event.target.result
                                    });
                                };
                                reader.readAsDataURL(file);
                            });
                        },
                        removePreview(index) {
                            this.previews.splice(index, 1);
                            const dt = new DataTransfer();
                            this.previews.forEach(p => dt.items.add(p.file));
                            $refs.galleryInput.files = dt.files;
                        }
                    }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Galeri Foto</label>
                        
                        <input type="file" name="gallery_images[]" multiple x-ref="galleryInput"
                            @change="handleFileSelect($event)"
                            class="block w-full text-sm text-gray-500
                            file:mr-3 file:py-1.5 file:px-3
                            file:rounded-lg file:border-0
                            file:text-xs file:font-medium
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100 transition-all
                        "/>
                        <p class="text-xs text-gray-400 mt-1">Bisa pilih banyak foto sekaligus.</p>

                        <!-- New Upload Previews -->
                        <div class="mt-3 grid grid-cols-4 gap-2" x-show="previews.length > 0" style="display: none;">
                            <template x-for="(preview, index) in previews" :key="index">
                                <div class="relative group aspect-square">
                                    <img :src="preview.url" class="w-full h-full object-cover rounded-lg border-2 border-blue-300">
                                    <span class="absolute top-1 right-1 bg-blue-500 text-white text-[8px] px-1 py-0.5 rounded font-medium">Baru</span>
                                    <button type="button" 
                                        @click="removePreview(index)"
                                        class="absolute top-1 left-1 bg-red-500 hover:bg-red-600 text-white w-4 h-4 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fa-solid fa-times text-[8px]"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Existing Gallery -->
                        @if($place->images && $place->images->count() > 0)
                        <div class="mt-3 grid grid-cols-4 gap-2">
                            @foreach($place->images as $img)
                                <div class="relative group aspect-square" x-data="{ deleting: false }" x-show="!deleting">
                                    <img src="{{ asset($img->image_path) }}" class="w-full h-full object-cover rounded-lg border border-gray-200">
                                    <button type="button" 
                                        @click="
                                            window.confirmAction('Hapus Foto?', 'Foto yang dihapus tidak dapat dikembalikan.', () => {
                                                deleting = true;
                                                fetch('{{ route('admin.places.images.destroy', $img->id) }}', {
                                                    method: 'DELETE',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    }
                                                })
                                                .then(response => {
                                                    if (!response.ok) throw new Error('Gagal menghapus');
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Foto berhasil dihapus', type: 'success' } }));
                                                })
                                                .catch(error => {
                                                    deleting = false;
                                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Gagal menghapus foto', type: 'error' } }));
                                                });
                                            });
                                        "
                                        class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg cursor-pointer">
                                        <i class="fa-solid fa-trash text-white text-sm"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions (Desktop Only) -->
        <div class="hidden lg:flex p-4 border-t border-gray-200 bg-gray-50 items-center justify-between gap-3">
            <a href="{{ route('admin.places.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-white text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i>Batal
            </a>
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors text-sm flex items-center gap-2">
                <i class="fa-solid fa-check text-xs"></i>{{ $submitLabel }}
            </button>
        </div>
    </div>

    <!-- Right Side: Map (60%) -->
    <div class="w-full h-96 lg:h-auto lg:w-3/5 bg-gray-100 relative">
        <div class="absolute inset-0 p-4">
            @include('admin.components.map-drawer', [
                'drawType' => 'point',
                'initialGeometry' => old('geometry', $initialGeometry),
                'center' => [$initialCoordinates['lat'], $initialCoordinates['lng']],
                'zoom' => 15,
                'height' => 'h-full',
            ])
        </div>
    </div>

    <!-- Footer Actions (Mobile Only) -->
    <div class="lg:hidden p-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-3 fixed bottom-0 left-0 right-0 z-[15] shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <a href="{{ route('admin.places.index') }}"
           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 text-sm font-medium flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-xs"></i>Batal
        </a>
        <button type="submit"
                class="px-5 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors text-sm flex items-center gap-2">
            <i class="fa-solid fa-check text-xs"></i>{{ $submitLabel }}
        </button>
    </div>
</form>

<script>
    window.placeForm = function() {
        return {
            sourceName: @js(old('name', $place->name ?? '')),
            sourceDesc: @js(old('description', $place->description ?? '')),
            isTranslating: false,

            init() {
                // Initialize if needed
            },

            async autoTranslate() {
                this.isTranslating = true;
                const translateUrl = "{{ route('admin.posts.translate') }}";
                let successCount = 0;
                let errorCount = 0;

                const translateText = async (text, targetRef) => {
                    if (!text) return;
                    try {
                        const response = await fetch(translateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json' 
                            },
                            body: JSON.stringify({
                                text: text,
                                source: 'id',
                                target: 'en'
                            })
                        });

                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        const data = await response.json();
                        
                        if (data.success) {
                            if (this.$refs[targetRef]) {
                                this.$refs[targetRef].value = data.translation;
                                successCount++;
                            }
                        } else {
                            errorCount++;
                        }
                    } catch (e) {
                         console.error(e);
                         errorCount++;
                    }
                };

                // Get values from refs to ensure we have latest input
                const titleSource = this.$refs.name.value;
                await translateText(titleSource, 'name_en');

                await new Promise(resolve => setTimeout(resolve, 1500));

                const contentSource = this.$refs.description.value;
                await translateText(contentSource, 'description_en');

                if (successCount > 0) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Terjemahan berhasil!', type: 'success' } }));
                } else if (errorCount > 0) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Gagal menerjemahkan.', type: 'error' } }));
                }

                this.isTranslating = false;
            }
        };
    };
</script>
