<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Event Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <!-- Title -->
                            <div class="mb-4">
                                <x-input-label for="title" :value="__('Nama Event')" class="text-lg font-bold" />
                                <x-text-input id="title" class="block mt-1 w-full text-lg" type="text" name="title" :value="old('title')" required autofocus placeholder="Contoh: Festival Baratan" />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <x-input-label for="description" :value="__('Deskripsi Event')" />
                                <div class="mt-1">
                                    <textarea id="description" name="description" class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 settings-tiny">{{ old('description') }}</textarea>
                                </div>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>

                         <!-- TinyMCE Initialization -->
                         <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                tinymce.init({
                                    selector: '.settings-tiny',
                                    height: 500,
                                    menubar: false,
                                    plugins: 'lists link image table code wordcount',
                                    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code',
                                    content_style: 'body { font-family:Figtree,sans-serif; font-size:16px; overflow-x: hidden; word-wrap: break-word; } img { max-width: 100%; height: auto; }',
                                    relative_urls: false,
                                    remove_script_host: false,
                                    document_base_url: '{{ url('/') }}',
                                });
                            });
                        </script>
                    </div>

                    <!-- Right Column: Sidebar settings -->
                    <div class="space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-bold text-gray-800 mb-4">Detail Event</h3>
                            
                            <!-- Location -->
                            <div class="mb-4">
                                <x-input-label for="location" :value="__('Lokasi')" />
                                <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" placeholder="Contoh: Alun-alun Jepara" required />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            <!-- Start Date -->
                            <div class="mb-4">
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date')" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>

                            <!-- End Date -->
                            <div class="mb-4">
                                <x-input-label for="end_date" :value="__('Tanggal Selesai (Opsional)')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="old('end_date')" />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>

                            <!-- Is Published -->
                             <div class="flex items-center mb-6">
                                <label for="is_published" class="inline-flex items-center cursor-pointer group">
                                    <input id="is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 group-hover:text-indigo-600 transition">{{ __('Tampilkan di Kalender?') }}</span>
                                </label>
                            </div>

                            <hr class="border-gray-200 my-4">

                            <div class="flex flex-col gap-2">
                                <x-primary-button class="justify-center w-full">
                                    {{ __('Simpan Event') }}
                                </x-primary-button>
                                <a href="{{ route('admin.events.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 w-full">
                                    Batal
                                </a>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <x-input-label for="image" :value="__('Gambar Poster (Opsional)')" class="mb-2" />
                            
                            <div x-data="{ preview: null }" class="space-y-4">
                                <div class="relative w-full aspect-[3/4] bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden hover:bg-gray-50 transition"
                                     @click="$refs.fileInput.click()">
                                    
                                    <template x-if="!preview">
                                        <div class="text-center p-4 cursor-pointer">
                                            <i class="fa-regular fa-image text-3xl text-gray-400 mb-2"></i>
                                            <p class="text-sm text-gray-500 font-medium">Upload Poster</p>
                                            <p class="text-xs text-gray-400">Vertical (Portrait) Recommended</p>
                                        </div>
                                    </template>
                                    
                                    <template x-if="preview">
                                        <div class="relative w-full h-full group">
                                            <img :src="preview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                                                <span class="text-white text-sm font-medium"><i class="fa-solid fa-pen mr-1"></i> Ganti</span>
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
