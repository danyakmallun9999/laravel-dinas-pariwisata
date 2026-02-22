{{--
Gallery Picker Component
Usage: <x-admin.gallery-picker name="image" :value="$model->image ?? null" label="Gambar Utama" />

Props:
- name (string): form field name
- value (string|null): existing image URL
- label (string): label text
- required (bool): whether the field is required
--}}

@props([
    'name' => 'images',
    'values' => [],
    'label' => 'Gambar',
    'required' => false,
])

<div x-data="galleryPicker_{{ Str::camel($name) }}()" class="space-y-2">
    {{-- Label --}}
    <label class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>

    {{-- Hidden Inputs --}}
    <template x-for="(url, index) in selectedUrls" :key="index">
        <input type="hidden" :name="`${name}[]`" :value="url">
    </template>
    
    {{-- Current Preview / Selected Images --}}
    <div x-show="selectedUrls.length > 0 || hasFileSelected" class="flex flex-wrap gap-4 mt-2 mb-4">
        <template x-for="(url, index) in selectedUrls" :key="index">
            <template x-if="!hasFileSelected">
                <div class="relative group">
                    <img :src="url" class="w-24 h-24 object-cover rounded-xl border border-gray-200 shadow-sm">
                    <button type="button" @click="removeSelection(index)" 
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition shadow-sm opacity-0 group-hover:opacity-100">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="absolute bottom-1 left-1 px-2 py-0.5 bg-blue-600/80 backdrop-blur-sm text-white text-[10px] rounded-full font-medium">
                        Galeri
                    </div>
                </div>
            </template>
        </template>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        {{-- Traditional Upload --}}
        <label class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 cursor-pointer transition">
            <i class="fa-solid fa-upload text-gray-400"></i>
            Upload Baru
            <input type="file" :name="`${name}[]`" multiple accept="image/*" class="hidden" 
                   @change="hasFileSelected = $event.target.files.length > 0; if(hasFileSelected) selectedUrls = [];">
        </label>

        {{-- Pick from Gallery --}}
        <button type="button" @click="openGallery()" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition border border-blue-100">
            <i class="fa-solid fa-photo-film"></i>
            Pilih dari Galeri
        </button>
    </div>

    {{-- Gallery Modal --}}
    <template x-teleport="body">
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="isOpen = false"
             @keydown.escape.window="isOpen = false"
             class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
             style="display: none;">
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.stop
             class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[85vh] flex flex-col overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Pilih dari Galeri</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Klik gambar untuk memilih</p>
                </div>
                <button @click="isOpen = false" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Search --}}
            <div class="px-6 py-3 border-b border-gray-50">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Cari gambar..." 
                           x-model.debounce.300ms="gallerySearch"
                           @input="fetchGallery(1)"
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400">
                </div>
            </div>

            {{-- Grid --}}
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                    <template x-for="item in galleryItems" :key="item.id">
                        <div @click="selectImage(item)"
                             class="aspect-square rounded-xl overflow-hidden border-2 cursor-pointer transition-all duration-200 hover:shadow-md relative"
                             :class="isTempSelected(item) ? 'border-blue-500 ring-2 ring-blue-200 shadow-md scale-[1.02]' : 'border-gray-100 hover:border-gray-300'">
                            <img :src="item.url" :alt="item.filename" class="w-full h-full object-cover" loading="lazy">
                            <div x-show="isTempSelected(item)" class="absolute top-2 right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs shadow-md">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty --}}
                <template x-if="galleryItems.length === 0 && !galleryLoading">
                    <div class="text-center py-12">
                        <i class="fa-solid fa-photo-film text-3xl text-gray-300 mb-3 block"></i>
                        <p class="text-sm text-gray-500">Tidak ada gambar ditemukan</p>
                    </div>
                </template>

                {{-- Loading --}}
                <template x-if="galleryLoading">
                    <div class="flex items-center justify-center py-12">
                        <div class="w-7 h-7 border-3 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    </div>
                </template>

                {{-- Load More --}}
                <div x-show="galleryPage < galleryLastPage" class="text-center mt-4">
                    <button type="button" @click="fetchGallery(galleryPage + 1, true)"
                            class="px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-xl transition font-medium">
                        Muat lebih banyak...
                    </button>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                <div class="text-sm text-gray-500 flex items-center gap-2 overflow-x-auto max-w-[50%]">
                    <template x-if="tempSelected.length > 0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900" x-text="`${tempSelected.length} dipilih`"></span>
                            <div class="flex -space-x-2">
                                <template x-for="item in tempSelected.slice(0, 5)" :key="item.id">
                                    <img :src="item.url" class="w-8 h-8 rounded-full object-cover border-2 border-white shadow-sm inline-block">
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="tempSelected.length === 0">
                        <span class="text-gray-400">Belum ada gambar dipilih</span>
                    </template>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="isOpen = false" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="button" @click="confirmSelection()" 
                            :disabled="tempSelected.length === 0"
                            :class="tempSelected.length > 0 ? 'bg-blue-600 hover:bg-blue-700 shadow-sm' : 'bg-gray-300 cursor-not-allowed'"
                            class="px-4 py-2 text-sm font-medium text-white rounded-xl transition">
                        <i class="fa-solid fa-check mr-1"></i> Pilih Gambar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function galleryPicker_{{ Str::camel($name) }}() {
    return {
        name: '{{ $name }}',
        isOpen: false,
        selectedUrls: @json($values ?? []),
        hasFileSelected: false,
        galleryItems: [],
        galleryLoading: false,
        gallerySearch: '',
        galleryPage: 1,
        galleryLastPage: 1,
        tempSelected: [],

        openGallery() {
            this.isOpen = true;
            this.tempSelected = [];
            if (this.galleryItems.length === 0) {
                this.fetchGallery(1);
            }
        },

        async fetchGallery(page = 1, append = false) {
            this.galleryLoading = true;
            const params = new URLSearchParams();
            params.set('page', page);
            if (this.gallerySearch) params.set('search', this.gallerySearch);

            try {
                const response = await fetch('{{ route("admin.media.api") }}?' + params.toString(), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                
                if (append) {
                    this.galleryItems = [...this.galleryItems, ...data.data];
                } else {
                    this.galleryItems = data.data;
                }
                this.galleryPage = data.current_page;
                this.galleryLastPage = data.last_page;
            } catch (e) {
                console.error('Failed to fetch gallery:', e);
            } finally {
                this.galleryLoading = false;
            }
        },

        isTempSelected(item) {
            return this.tempSelected.some(i => i.id === item.id);
        },

        selectImage(item) {
            const index = this.tempSelected.findIndex(i => i.id === item.id);
            if (index > -1) {
                this.tempSelected.splice(index, 1);
            } else {
                this.tempSelected.push(item);
            }
        },

        confirmSelection() {
            if (this.tempSelected.length === 0) return;
            
            this.selectedUrls = this.tempSelected.map(i => i.url);
            this.hasFileSelected = false;
            // Clear file input
            const fileInput = this.$el.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
            this.isOpen = false;
        },

        removeSelection(index) {
            this.selectedUrls.splice(index, 1);
        },

        clearSelection() {
            this.selectedUrls = [];
            this.tempSelected = [];
        },
    }
}
</script>
