<x-app-layout>
    <style>
        .tox-tinymce {
            border: 1px solid #d1d5db !important; /* gray-300 */
            border-radius: 0.75rem !important;
        }
        /* Override all focus states */
        .tox-tinymce:focus,
        .tox-tinymce.tox-edit-focus,
        .tox-tinymce-focused,
        .tox-tinymce:focus-within {
            border-color: #9ca3af !important; /* gray-400 */
            box-shadow: none !important;
            outline: none !important;
        }
        
        /* Remove blue outline on sticky toolbar mode */
        .tox-tinymce--toolbar-sticky-on {
             border-color: #9ca3af !important;
             box-shadow: none !important;
        }
    </style>
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

                                <!-- Content -->
                                <div>
                                    <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fa-solid fa-align-left text-gray-400 mr-1.5"></i>
                                        Isi Konten
                                    </label>
                                    <textarea id="content" 
                                              name="content" 
                                              class="settings-tiny">{{ old('content') }}</textarea>
                                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                </div>
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

                                <!-- English Content -->
                                <div>
                                    <label for="content_en" class="block text-sm font-semibold text-blue-800 mb-2">
                                        <i class="fa-solid fa-align-left text-blue-400 mr-1.5"></i>
                                        Content (English)
                                    </label>
                                    <textarea id="content_en" 
                                              name="content_en" 
                                              class="settings-tiny">{{ old('content_en') }}</textarea>
                                    <x-input-error :messages="$errors->get('content_en')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- TinyMCE Initialization -->
                        <script>
                            // Define the data function globally so it's available for Alpine
                            window.postForm = function(config) {
                                return {
                                    isTranslating: false,
                                    init() {
                                        this.initEditors();
                                    },
                                    initEditors() {
                                        if (typeof tinymce === 'undefined') return;
                                        
                                        // Specific init for both editors
                                        const editors = ['content', 'content_en'];
                                        
                                        editors.forEach(id => {
                                            const el = document.getElementById(id);
                                            if(!el) return;

                                            // Destroy existing instance if any (important for SPA)
                                            if (tinymce.get(id)) {
                                                tinymce.get(id).remove();
                                            }

                                            tinymce.init({
                                                target: el,
                                                height: 500,
                                                menubar: false,
                                                plugins: 'lists link image table code wordcount',
                                                toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
                                                content_style: 'body { font-family:Figtree,sans-serif; font-size:16px; overflow-x: hidden; word-wrap: break-word; } img { max-width: 100%; height: auto; }',
                                                relative_urls: false,
                                                remove_script_host: false,
                                                document_base_url: '{{ url('/') }}',
                                                images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                                                    const xhr = new XMLHttpRequest();
                                                    xhr.withCredentials = false;
                                                    xhr.open('POST', config.uploadUrl);
                                                    xhr.setRequestHeader('X-CSRF-TOKEN', config.csrf);
                                                    xhr.upload.onprogress = (e) => { progress(e.loaded / e.total * 100); };
                                                    xhr.onload = () => {
                                                        if (xhr.status < 200 || xhr.status >= 300) {
                                                            reject('HTTP Error: ' + xhr.status);
                                                            return;
                                                        }
                                                        const json = JSON.parse(xhr.responseText);
                                                        if (!json || typeof json.location != 'string') {
                                                            reject('Invalid JSON');
                                                            return;
                                                        }
                                                        resolve(json.location);
                                                    };
                                                    xhr.onerror = () => { reject('Upload failed'); };
                                                    const formData = new FormData();
                                                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                                                    xhr.send(formData);
                                                }),
                                                setup: (editor) => {
                                                    editor.on('remove', () => {
                                                        // Cleanup logic
                                                    });
                                                }
                                            });
                                        });

                                        // Cleanup on component destruction
                                        this.$cleanup(() => {
                                            editors.forEach(id => {
                                                const editor = tinymce.get(id);
                                                if (editor) editor.remove();
                                            });
                                        });
                                    },
                                    async autoTranslate() {
                                        const title = document.getElementById('title').value;
                                        const content = tinymce.get('content')?.getContent();

                                        if (!title && !content) {
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

                                            if (content) {
                                                const res = await fetch(config.translateUrl, {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf },
                                                    body: JSON.stringify({ text: content, source: 'id', target: 'en' })
                                                });
                                                const data = await res.json();
                                                if (data.success) tinymce.get('content_en')?.setContent(data.translation);
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
                            <div class="p-5" x-data="{ preview: null }">
                                <div class="relative w-full aspect-video bg-gray-100 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden hover:border-blue-400 hover:bg-blue-50/30 transition-all cursor-pointer group"
                                     @click="$refs.fileInput.click()">
                                    
                                    <template x-if="!preview">
                                        <div class="text-center p-4">
                                            <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-100 transition-colors">
                                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 text-xl group-hover:text-blue-500 transition-colors"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-600 group-hover:text-blue-600 transition-colors">Klik untuk upload</p>
                                            <p class="text-xs text-gray-400 mt-1">PNG, JPG hingga 2MB</p>
                                        </div>
                                    </template>
                                    
                                    <template x-if="preview">
                                        <div class="relative w-full h-full">
                                            <img :src="preview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span class="text-white font-medium text-sm"><i class="fa-solid fa-pen mr-1"></i> Ganti</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <input type="file" 
                                       x-ref="fileInput" 
                                       id="image" 
                                       name="image" 
                                       class="hidden" 
                                       accept="image/*"
                                       @change="const file = $event.target.files[0]; 
                                                const reader = new FileReader(); 
                                                reader.onload = (e) => preview = e.target.result; 
                                                reader.readAsDataURL(file)">
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
