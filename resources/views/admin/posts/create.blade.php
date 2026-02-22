<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.posts.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="text-sm text-gray-500 mb-0.5">Berita & Agenda</p>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Tulis Postingan Baru
                    </h2>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" 
                     x-data="postForm({
                        uploadUrl: '{{ route('admin.posts.uploadImage') }}',
                        translateUrl: '{{ route('admin.posts.translate') }}',
                        csrf: '{{ csrf_token() }}'
                     })">
                    <!-- Left Column: Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Indonesian Content Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                                        <img src="https://flagcdn.com/w20/id.png" class="w-5 rounded-sm" alt="ID">
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Konten Bahasa Indonesia</h3>
                                        <p class="text-xs text-gray-500">Konten utama dalam Bahasa Indonesia</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fa-solid fa-heading text-gray-400 mr-1.5"></i>
                                        Judul Postingan
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 text-lg font-medium placeholder-gray-400 focus:bg-white focus:ring-0 focus:border-gray-400 transition-all"
                                           placeholder="Masukkan judul yang menarik..."
                                           required 
                                           autofocus>
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <!-- Content (Editor.js) -->
                                <x-admin.editorjs name="content" label="Isi Konten" />
                            </div>
                        </div>

                        <!-- English Content Card -->
                        <div class="bg-blue-50/50 rounded-2xl border border-blue-100 overflow-hidden">
                            <div class="px-6 py-4 bg-blue-100/50 border-b border-blue-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <img src="https://flagcdn.com/w20/gb.png" class="w-5 rounded-sm" alt="EN">
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-blue-900">English Content</h3>
                                            <p class="text-xs text-blue-600">Optional translation for international visitors</p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="autoTranslate"
                                            :disabled="isTranslating"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold text-sm rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/25 transition-all shadow-lg shadow-blue-500/25 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid" :class="isTranslating ? 'fa-spinner fa-spin' : 'fa-language'"></i>
                                        <span x-text="isTranslating ? 'Translating...' : 'Auto Translate'"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                <!-- English Title -->
                                <div>
                                    <label for="title_en" class="block text-sm font-semibold text-blue-800 mb-2">
                                        <i class="fa-solid fa-heading text-blue-400 mr-1.5"></i>
                                        Title (English)
                                    </label>
                                    <input type="text" 
                                           id="title_en" 
                                           name="title_en" 
                                           value="{{ old('title_en') }}"
                                           class="block w-full px-4 py-3 bg-white border border-blue-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-0 focus:border-gray-400 transition-all"
                                           placeholder="English title...">
                                    <x-input-error :messages="$errors->get('title_en')" class="mt-2" />
                                </div>

                                <!-- English Content (Editor.js) -->
                                <x-admin.editorjs name="content_en" label="Content (English)" formatName="content_format" />
                            </div>
                        </div>

                        <!-- Post Form Logic -->
                        <script>
                            window.postForm = function(config) {
                                return {
                                    isTranslating: false,
                                    async autoTranslate() {
                                        const title = document.getElementById('title').value;
                                        // Get content from Editor.js hidden input
                                        const contentInput = document.querySelector('input[name="content"]');
                                        const contentJson = contentInput?.value;
                                        
                                        // Extract plain text from Editor.js JSON for translation
                                        let plainText = '';
                                        try {
                                            const data = JSON.parse(contentJson);
                                            if (data?.blocks) {
                                                plainText = data.blocks.map(b => b.data?.text || b.data?.code || '').filter(Boolean).join('\n\n');
                                            }
                                        } catch(e) {}

                                        if (!title && !plainText) {
                                            alert('Isi judul atau konten bahasa Indonesia terlebih dahulu.');
                                            return;
                                        }

                                        this.isTranslating = true;

                                        try {
                                            if (title) {
                                                const res = await fetch(config.translateUrl, {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf },
                                                    body: JSON.stringify({ text: title, source: 'id', target: 'en' })
                                                });
                                                const data = await res.json();
                                                if (data.success) document.getElementById('title_en').value = data.translation;
                                            }

                                            if (plainText) {
                                                const res = await fetch(config.translateUrl, {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf },
                                                    body: JSON.stringify({ text: plainText, source: 'id', target: 'en' })
                                                });
                                                const data = await res.json();
                                                // Note: Auto-translate for Editor.js content is limited to plain text.
                                                // The translated text won't preserve block structure.
                                                if (data.success) {
                                                    alert('Title diterjemahkan. Untuk konten, harap terjemahkan manual di editor English.');
                                                }
                                            }
                                        } catch (e) {
                                            console.error(e);
                                            alert('Gagal melakukan translasi otomatis.');
                                        } finally {
                                            this.isTranslating = false;
                                        }
                                    }
                                };
                            };
                        </script>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                        <!-- Publish Settings -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-paper-plane text-blue-500"></i>
                                    <h3 class="font-bold text-gray-900">Pengaturan Publikasi</h3>
                                </div>
                            </div>
                            <div class="p-5 space-y-5">
                                <!-- Type -->
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Jenis Posting</label>
                                    <select id="type" 
                                            name="type" 
                                            class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-gray-400 transition-all">
                                        <option value="news" {{ old('type') == 'news' ? 'selected' : '' }}>🗞️ Berita</option>
                                        <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>📅 Agenda / Event</option>
                                    </select>
                                </div>

                                <!-- Published At -->
                                <div>
                                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Tayang</label>
                                    <input type="date" 
                                           id="published_at" 
                                           name="published_at" 
                                           value="{{ old('published_at') }}"
                                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-gray-400 transition-all">
                                    <p class="text-xs text-gray-400 mt-1.5">Kosongkan untuk publish sekarang</p>
                                </div>

                                <!-- Is Published Toggle -->
                                <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-rocket text-emerald-600 text-sm"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Langsung Terbitkan</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_published" value="1" class="sr-only peer" {{ old('is_published', true) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-emerald-300/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </div>

                                <hr class="border-gray-100">

                                <!-- Action Buttons -->
                                <div class="space-y-2">
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-500/25 transition-all shadow-lg shadow-blue-500/25">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Simpan & Terbitkan
                                    </button>
                                    <a href="{{ route('admin.posts.index') }}" 
                                       class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all" wire:navigate>
                                        <i class="fa-solid fa-xmark"></i>
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-image text-purple-500"></i>
                                    <h3 class="font-bold text-gray-900">Gambar Utama</h3>
                                </div>
                            </div>
                            <div class="p-5">
                                <x-admin.gallery-picker name="image" label="Gambar Utama" />
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-circle-info text-amber-500"></i>
                                    <h3 class="font-bold text-gray-900">Informasi Tambahan</h3>
                                </div>
                            </div>
                            <div class="p-5 space-y-4">
                                <div>
                                    <label for="author" class="block text-sm font-medium text-gray-700 mb-2">Penulis</label>
                                    <input type="text" 
                                           id="author" 
                                           name="author" 
                                           value="{{ old('author') }}"
                                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                                           placeholder="Default: Disparbudpora Jepara">
                                </div>
                                <div>
                                    <label for="image_credit" class="block text-sm font-medium text-gray-700 mb-2">Kredit Gambar</label>
                                    <input type="text" 
                                           id="image_credit" 
                                           name="image_credit" 
                                           value="{{ old('image_credit') }}"
                                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                                           placeholder="Contoh: Dok. Pribadi">
                                </div>
                            </div>
                        </div>

                        {{-- Stat Widget Picker --}}
                        <x-admin.stat-widget-picker />
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
