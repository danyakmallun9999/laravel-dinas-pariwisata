<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Budaya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Budaya')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $culture->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category" :value="__('Kategori')" />
                            <select id="category" name="category" x-model="selectedCategory" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(['Kemahiran & Kerajinan Tradisional (Kriya)', 'Adat Istiadat, Ritus, & Perayaan Tradisional', 'Seni Pertunjukan', 'Kawasan Cagar Budaya & Sejarah', 'Kuliner Khas'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $culture->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category')" />
                        </div>

                        <!-- Foto Utama -->
                        <div>
                            <x-input-label for="image" :value="__('Foto Utama')" />
                            @if($culture->image)
                                <div class="mb-2">
                                    <img src="{{ $culture->image_url }}" class="h-32 w-auto rounded-lg object-cover border border-gray-200">
                                    <p class="text-xs text-gray-500 mt-1">Foto utama saat ini. Pilih file baru untuk menggantinya.</p>
                                </div>
                            @endif
                            <input id="image" name="image" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" />
                            <x-input-error class="mt-2" :messages="$errors->get('image')" />
                        </div>

                        <!-- Galeri Foto Existing -->
                        @if($culture->images->isNotEmpty())
                        <div>
                            <x-input-label :value="__('Foto Galeri Saat Ini')" />
                            <p class="text-xs text-gray-500 mb-2">Klik tombol × untuk menghapus foto.</p>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 mt-2">
                                @foreach($culture->images as $img)
                                <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                                    <img src="{{ asset('storage/' . $img->image_path) }}"
                                         class="w-full h-full object-cover">
                                    <form action="{{ route('admin.cultures.images.destroy', $img) }}" method="POST"
                                          class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/40 transition-colors"
                                          onsubmit="return confirm('Hapus foto ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-7 h-7 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center shadow-md hover:bg-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
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
                            <x-input-label for="images" :value="__('Tambah Foto ke Galeri')" />
                            <p class="text-xs text-gray-500 mb-1">Pilih satu atau beberapa foto baru. Tahan Ctrl/Cmd untuk pilih banyak.</p>
                            <input id="images" name="images[]" type="file" accept="image/*" multiple
                                   @change="handleFiles($event)"
                                   class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" />
                            @if($errors->has('images.*'))
                                <p class="mt-2 text-sm text-red-600">{{ $errors->first('images.*') }}</p>
                            @endif
                            <div x-show="previews.length > 0" x-transition class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                <template x-for="(src, i) in previews" :key="i">
                                    <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                                        <img :src="src" class="w-full h-full object-cover">
                                        <span class="absolute top-1 right-1 bg-green-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">BARU</span>
                                    </div>
                                </template>
                            </div>
                        </div>


                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Deskripsi Singkat')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $culture->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Content -->
                        <div>
                            <x-input-label for="content" :value="__('Konten Lengkap')" />
                            <textarea id="content" name="content" rows="6" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('content', $culture->content) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>

                        <!-- Location -->
                        <div x-show="showLocationTime" x-transition x-cloak>
                            <x-input-label for="location" :value="__('Lokasi')" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $culture->location)" placeholder="Contoh: Kota Jepara, Jawa Tengah" />
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>

                        <!-- Time -->
                        <div x-show="showLocationTime" x-transition x-cloak>
                            <x-input-label for="time" :value="__('Waktu / Jadwal')" />
                            <x-text-input id="time" name="time" type="text" class="mt-1 block w-full" :value="old('time', $culture->time)" placeholder="Contoh: Setiap hari, 08.00 - 17.00" />
                            <x-input-error class="mt-2" :messages="$errors->get('time')" />
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
                            <x-input-label for="youtube_url" :value="__('Link YouTube (Embed)')" />
                            <x-text-input id="youtube_url" name="youtube_url" type="url"
                                class="mt-1 block w-full"
                                x-model="youtubeUrl"
                                placeholder="Contoh: https://www.youtube.com/watch?v=xxx" />
                            <p class="mt-1 text-xs text-gray-500">Masukkan link YouTube biasa, akan otomatis dikonversi ke embed.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('youtube_url')" />

                            <!-- Preview -->
                            <div x-show="embedUrl" x-transition class="mt-3">
                                <p class="text-sm font-medium text-gray-700 mb-1">Preview:</p>
                                <div class="relative w-full" style="padding-top: 56.25%">
                                    <iframe :src="embedUrl"
                                        class="absolute inset-0 w-full h-full rounded-lg shadow"
                                        frameborder="0" allowfullscreen
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                    </iframe>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                            <a href="{{ route('admin.cultures.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
