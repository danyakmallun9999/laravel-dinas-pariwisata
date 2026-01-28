<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Berita / Agenda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <!-- Title -->
                            <div class="mb-4">
                                <x-input-label for="title" :value="__('Judul Article / Event')" class="text-lg font-bold" />
                                <x-text-input id="title" class="block mt-1 w-full text-lg" type="text" name="title" :value="old('title', $post->title)" required autofocus />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Content -->
                            <div class="mb-4">
                                <x-input-label for="content" :value="__('Isi Konten')" />
                                <div class="mt-1">
                                    <textarea id="content" name="content" class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 settings-tiny">{{ old('content', $post->content) }}</textarea>
                                </div>
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />
                            </div>
                        </div>

                        <!-- TinyMCE Initialization -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                tinymce.init({
                                    selector: '.settings-tiny',
                                    height: 800,
                                    menubar: false,
                                    plugins: 'lists link image table code wordcount',
                                    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
                                    // Fix: Ensure content fits width and images are responsive
                                    content_style: 'body { font-family:Figtree,sans-serif; font-size:16px; overflow-x: hidden; word-wrap: break-word; } img { max-width: 100%; height: auto; }',
                                    
                                    // URL Configuration
                                    relative_urls: false,
                                    remove_script_host: false,
                                    document_base_url: '{{ url('/') }}',

                                    // Image Upload Handler
                                    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                                        const xhr = new XMLHttpRequest();
                                        xhr.withCredentials = false;
                                        xhr.open('POST', '{{ route('admin.posts.uploadImage') }}');
                                        
                                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                        xhr.setRequestHeader('X-CSRF-TOKEN', token);

                                        xhr.upload.onprogress = (e) => {
                                            progress(e.loaded / e.total * 100);
                                        };

                                        xhr.onload = () => {
                                            if (xhr.status === 403) {
                                                reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                                                return;
                                            }

                                            if (xhr.status < 200 || xhr.status >= 300) {
                                                reject('HTTP Error: ' + xhr.status);
                                                return;
                                            }

                                            const json = JSON.parse(xhr.responseText);

                                            if (!json || typeof json.location != 'string') {
                                                reject('Invalid JSON: ' + xhr.responseText);
                                                return;
                                            }

                                            resolve(json.location);
                                        };

                                        xhr.onerror = () => {
                                            reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                                        };

                                        const formData = new FormData();
                                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                                        xhr.send(formData);
                                    })
                                });
                            });
                        </script>

                        <!-- SEO / Meta (Optional, can be expanded later) -->

                    </div>

                    <!-- Right Column: Sidebar settings -->
                    <div class="space-y-6">
                        <!-- Publish Status -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-bold text-gray-800 mb-4">Pengaturan Publikasi</h3>
                            
                            <!-- Type -->
                            <div class="mb-4">
                                <x-input-label for="type" :value="__('Jenis Posting')" />
                                <select id="type" name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="news" {{ old('type', $post->type) == 'news' ? 'selected' : '' }}>Berita</option>
                                    <option value="event" {{ old('type', $post->type) == 'event' ? 'selected' : '' }}>Agenda / Event</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Published At -->
                            <div class="mb-4">
                                <x-input-label for="published_at" :value="__('Tanggal Tayang')" />
                                <x-text-input id="published_at" class="block mt-1 w-full" type="date" name="published_at" :value="old('published_at', $post->published_at ? $post->published_at->format('Y-m-d') : '')" />
                                <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk publish sekarang.</p>
                                <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                            </div>

                            <!-- Is Published -->
                             <div class="flex items-center mb-6">
                                <label for="is_published" class="inline-flex items-center cursor-pointer group">
                                    <input id="is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="is_published" value="1" {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-indigo-600 transition">{{ __('Langsung Terbitkan?') }}</span>
                                </label>
                            </div>

                            <hr class="border-gray-200 my-4">

                            <div class="flex flex-col gap-2">
                                <x-primary-button class="justify-center w-full">
                                    {{ __('Simpan Perubahan') }}
                                </x-primary-button>
                                <a href="{{ route('admin.posts.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 w-full">
                                    Batal
                                </a>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <x-input-label for="image" :value="__('Gambar Utama')" class="mb-2" />
                            
                            <div x-data="{ preview: '{{ $post->image_path ?? null }}' }" class="space-y-4">
                                <!-- Image Preview Area -->
                                <div class="relative w-full aspect-video bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:bg-gray-50 transition"
                                     @click="$refs.fileInput.click()">
                                    
                                    <template x-if="!preview">
                                        <div class="text-center p-4 cursor-pointer">
                                            <i class="fa-regular fa-image text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-500 font-medium">Klik untuk upload gambar</p>
                                            <p class="text-xs text-gray-400">PNG, JPG up to 2MB</p>
                                        </div>
                                    </template>
                                    
                                    <template x-if="preview">
                                        <div class="relative w-full h-full group">
                                            <img :src="preview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                                                <span class="text-white text-sm font-medium"><i class="fa-solid fa-pen mr-1"></i> Ganti Gambar</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <input type="file" x-ref="fileInput" id="image" name="image" class="hidden" 
                                       @change="const file = $event.target.files[0]; 
                                                const reader = new FileReader(); 
                                                reader.onload = (e) => preview = e.target.result; 
                                                reader.readAsDataURL(file)">
                                
                            <x-input-error :messages="$errors->get('image')" />
                            </div>
                        </div>

                        <!-- Informasi Tambahan (Moved here) -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-bold text-gray-800 mb-4">Informasi Tambahan</h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="author" :value="__('Penulis (Opsional)')" />
                                    <x-text-input id="author" class="block mt-1 w-full" type="text" name="author" :value="old('author', $post->author)" placeholder="Default: Dinas Pariwisata Jepara" />
                                    <x-input-error :messages="$errors->get('author')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="image_credit" :value="__('Kredit Gambar (Opsional)')" />
                                    <x-text-input id="image_credit" class="block mt-1 w-full" type="text" name="image_credit" :value="old('image_credit', $post->image_credit)" placeholder="Contoh: Dok. Pribadi / Unsplash" />
                                    <x-input-error :messages="$errors->get('image_credit')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
