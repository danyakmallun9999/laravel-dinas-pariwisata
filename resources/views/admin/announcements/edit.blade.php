<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.announcements.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" wire:navigate>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <p class="text-sm text-gray-500 mb-0.5">Kelola Pengumuman</p>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">Edit Pengumuman</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
                     x-data="{
                        preview: '{{ $announcement->image ? Storage::url($announcement->image) : null }}',
                        format: '{{ old('image_format', $announcement->image_format ?? 'landscape') }}',
                        btnText: '{{ old('button_text', $announcement->button_text) }}',
                        handleImage(e) {
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (ev) => { this.preview = ev.target.result; };
                                reader.readAsDataURL(file);
                            }
                        }
                     }">

                    {{-- Left: Main Info --}}
                    <div class="lg:col-span-2 space-y-6">


                        {{-- Tombol Aksi --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600">
                                    <i class="fa-solid fa-link"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Tombol (Opsional)</h3>
                                    <p class="text-xs text-gray-500">Tambahkan tombol CTA di dalam popup</p>
                                </div>
                            </div>
                            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Teks Tombol</label>
                                    <input type="text" name="button_text" value="{{ old('button_text', $announcement->button_text) }}"
                                           x-model="btnText"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all"
                                           placeholder="Contoh: Lihat Destinasi">
                                    <x-input-error :messages="$errors->get('button_text')" class="mt-2" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link Tombol</label>
                                    <input type="url" name="button_link" value="{{ old('button_link', $announcement->button_link) }}"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all"
                                           placeholder="https://...">
                                    <x-input-error :messages="$errors->get('button_link')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Jadwal --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Jadwal Tampil</h3>
                                    <p class="text-xs text-gray-500">Kosongkan jika ingin selalu tampil</p>
                                </div>
                            </div>
                            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mulai Tampil</label>
                                    <input type="datetime-local" name="starts_at"
                                           value="{{ old('starts_at', $announcement->starts_at?->format('Y-m-d\TH:i')) }}"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all">
                                    <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Selesai Tampil</label>
                                    <input type="datetime-local" name="ends_at"
                                           value="{{ old('ends_at', $announcement->ends_at?->format('Y-m-d\TH:i')) }}"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all">
                                    <x-input-error :messages="$errors->get('ends_at')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Gambar & Publish --}}
                    <div class="lg:col-span-1 space-y-6">

                        {{-- Gambar --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                                <i class="fa-solid fa-image text-green-500"></i>
                                <h3 class="font-bold text-gray-900">Gambar Banner</h3>
                            </div>
                            <div class="p-5">
                                {{-- Format Picker --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Format Gambar</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="image_format" value="landscape" x-model="format" class="sr-only">
                                            <div :class="format === 'landscape' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                                                 class="border-2 rounded-xl p-2.5 text-center transition-all">
                                                <div class="w-full h-6 bg-current opacity-20 rounded mb-1" style="aspect-ratio:16/9; height:18px;"></div>
                                                <p class="text-xs font-bold">Landscape</p>
                                                <p class="text-[10px] opacity-60">16:9</p>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="image_format" value="portrait" x-model="format" class="sr-only">
                                            <div :class="format === 'portrait' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                                                 class="border-2 rounded-xl p-2.5 text-center transition-all">
                                                <div class="w-5 h-8 bg-current opacity-20 rounded mb-1 mx-auto"></div>
                                                <p class="text-xs font-bold">Portrait</p>
                                                <p class="text-[10px] opacity-60">9:16</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Preview --}}
                                <div class="mb-4 rounded-2xl overflow-hidden border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center transition-all"
                                     :style="format === 'portrait'  ? 'aspect-ratio:9/16; max-height:280px;' :
                                             format === 'square'    ? 'aspect-ratio:1/1;'  :
                                             format === 'banner'    ? 'aspect-ratio:3/1;'  :
                                                                      'aspect-ratio:16/9;'">
                                    <template x-if="preview">
                                        <img :src="preview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!preview">
                                        <div class="text-center text-gray-400 py-8">
                                            <i class="fa-solid fa-image text-3xl mb-2"></i>
                                            <p class="text-xs">Belum ada gambar</p>
                                        </div>
                                    </template>
                                </div>
                                <label class="block w-full cursor-pointer">
                                    <div class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                                        <i class="fa-solid fa-upload"></i>
                                        Ganti Gambar
                                    </div>
                                    <input type="file" name="image" class="sr-only" accept="image/*" @change="handleImage($event)">
                                </label>
                                <p class="text-xs text-gray-400 text-center mt-1">JPG, PNG, WEBP. Maks 4MB</p>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>
                        </div>



                        {{-- Status & Publish --}}
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Aktifkan Popup</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Tampilkan di halaman utama</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <p class="text-xs text-blue-600 bg-blue-50 p-3 rounded-xl border border-blue-100">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Maksimal 4 pengumuman aktif yang akan ditampilkan di halaman utama sebagai carousel.
                            </p>
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/25 active:scale-[0.98]">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.announcements.index') }}"
                               class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all" wire:navigate>
                                Batal
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
