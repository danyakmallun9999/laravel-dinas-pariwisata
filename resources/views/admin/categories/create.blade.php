<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.categories.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" wire:navigate>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="text-sm text-gray-500 mb-0.5">Kelola Kategori</p>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Tambah Kategori Baru
                    </h2>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" 
                     x-data="{ 
                        icon: '{{ old('icon_class') }}',
                        color: '{{ old('color', '#3b82f6') }}'
                     }">
                    
                    <!-- Left Column: Main Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Informasi Dasar</h3>
                                        <p class="text-xs text-gray-500">Nama dan identitas utama kategori</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 space-y-6" x-data="{ 
                                isTranslating: false,
                                async translateName() {
                                    const nameVal = document.getElementById('name').value;
                                    if(!nameVal) return;
                                    
                                    this.isTranslating = true;
                                    try {
                                        const response = await fetch('{{ route('admin.posts.translate') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                text: nameVal,
                                                target: 'en'
                                            })
                                        });
                                        const data = await response.json();
                                        if(data.success) {
                                            document.getElementById('name_en').value = data.translation;
                                        }
                                    } catch (error) {
                                        console.error('Translation failed:', error);
                                    } finally {
                                        this.isTranslating = false;
                                    }
                                }
                            }">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Kategori
                                    </label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all"
                                           placeholder="Contoh: Alam, Budaya, Kuliner..."
                                           required 
                                           autofocus>
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label for="name_en" class="block text-sm font-semibold text-gray-700">
                                            Nama Kategori (English)
                                        </label>
                                        <button type="button" 
                                                @click="translateName()"
                                                class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 disabled:opacity-50"
                                                :disabled="isTranslating">
                                            <i class="fa-solid fa-language" :class="isTranslating ? 'animate-pulse' : ''"></i>
                                            <span x-text="isTranslating ? 'Translating...' : 'Translate Automatically'"></span>
                                        </button>
                                    </div>
                                    <input type="text" 
                                           id="name_en" 
                                           name="name_en" 
                                           value="{{ old('name_en') }}"
                                           class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all"
                                           placeholder="Example: Nature, Culture, Culinary...">
                                    <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Appearance & Actions -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Appearance Card -->
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-palette text-purple-500"></i>
                                    <h3 class="font-bold text-gray-900">Tampilan</h3>
                                </div>
                            </div>
                            <div class="p-5 space-y-5">
                                <!-- Preview -->
                                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-2xl border border-gray-100 mb-4">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white shadow-lg mb-3 transition-all duration-300"
                                         :style="'background-color: ' + color">
                                        <i class="text-2xl" :class="icon || 'fa-solid fa-question'"></i>
                                    </div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Preview Ikon</p>
                                </div>

                                <!-- Icon Class -->
                                <div>
                                    <label for="icon_class" class="block text-sm font-medium text-gray-700 mb-2 text-center">Kelas Ikon (FA 6)</label>
                                    <input type="text" 
                                           id="icon_class" 
                                           name="icon_class" 
                                           x-model="icon"
                                           class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all text-sm font-mono"
                                           placeholder="fa-solid fa-star">
                                    <x-input-error :messages="$errors->get('icon_class')" class="mt-2" />
                                </div>

                                <!-- Color Picker -->
                                <div>
                                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2 text-center">Warna Penanda</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" 
                                               id="color" 
                                               name="color" 
                                               x-model="color"
                                               class="h-10 w-16 bg-gray-50 border border-gray-200 rounded-lg p-1 cursor-pointer">
                                        <input type="text" 
                                               x-model="color"
                                               class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 text-sm font-mono focus:ring-0 pointer-events-none"
                                               readonly>
                                    </div>
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Action Card -->
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 space-y-3">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/25 active:scale-[0.98]">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan Kategori
                            </button>
                            <a href="{{ route('admin.categories.index') }}" 
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
