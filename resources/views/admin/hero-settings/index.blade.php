<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Pengaturan Hero
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-green-50/50 border border-green-200 flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-green-500 mt-0.5"></i>
                    <div>
                        <h3 class="text-sm font-bold text-green-800">Berhasil</h3>
                        <p class="text-xs font-medium text-green-700 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-50/50 border border-red-200">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                        <h3 class="text-sm font-bold text-red-800">Terdapat Kesalahan</h3>
                    </div>
                    <ul class="list-disc pl-8">
                        @foreach ($errors->all() as $error)
                            <li class="text-xs font-medium text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.hero-settings.update') }}" method="POST" enctype="multipart/form-data" 
                  x-data="{ 
                      type: '{{ old('type', $setting->type ?? 'map') }}',
                      previewLang: 'id',
                      badge_id: {{ \Illuminate\Support\Js::from(old('badge_id', $setting->badge_id ?? '')) }},
                      badge_en: {{ \Illuminate\Support\Js::from(old('badge_en', $setting->badge_en ?? '')) }},
                      title_id: {{ \Illuminate\Support\Js::from(old('title_id', $setting->title_id ?? '')) }},
                      title_en: {{ \Illuminate\Support\Js::from(old('title_en', $setting->title_en ?? '')) }},
                      subtitle_id: {{ \Illuminate\Support\Js::from(old('subtitle_id', $setting->subtitle_id ?? '')) }},
                      subtitle_en: {{ \Illuminate\Support\Js::from(old('subtitle_en', $setting->subtitle_en ?? '')) }},
                      btn_id: {{ \Illuminate\Support\Js::from(old('button_text_id', $setting->button_text_id ?? '')) }},
                      btn_en: {{ \Illuminate\Support\Js::from(old('button_text_en', $setting->button_text_en ?? '')) }},
                      
                      previewVideo: '{{ ($setting->type === 'video' && !empty($setting->media_paths)) ? Storage::url($setting->media_paths[0]) : '' }}',
                      previewImages: {{ ($setting->type === 'image' && !empty($setting->media_paths)) ? json_encode(array_values(array_map(fn($p) => Storage::url($p), $setting->media_paths))) : '[]' }},
                      currentSlide: 0,
                      
                      init() {
                          if(this.previewImages.length > 1) {
                              setInterval(() => {
                                  this.currentSlide = (this.currentSlide + 1) % this.previewImages.length;
                              }, 3000);
                          }
                      },

                      handleVideoUpload(event) {
                          const file = event.target.files[0];
                          if(file) {
                              this.type = 'video';
                              this.previewVideo = URL.createObjectURL(file);
                          }
                      },
                      
                      handleImageUpload(event) {
                          const files = event.target.files;
                          if(files && files.length > 0) {
                              this.type = 'image';
                              this.previewImages = [];
                              for(let i = 0; i < files.length; i++) {
                                  this.previewImages.push(URL.createObjectURL(files[i]));
                              }
                              this.currentSlide = 0;
                          }
                      }
                  }" 
                  class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Background Media -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Type Selection -->
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-blue-500"></i>
                                Tipe Latar Belakang
                            </h3>
                            <div class="space-y-4">
                                <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all"
                                    :class="type === 'map' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 bg-gray-50 hover:bg-white content-center'">
                                    <input type="radio" name="type" value="map" x-model="type" class="mt-1 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-900">Animasi Peta 3D (Default)</span>
                                        <span class="block text-xs text-gray-500 mt-1">Menggunakan integrasi MapLibre yang interaktif sebagai latar belakang.</span>
                                    </div>
                                </label>
                                
                                <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all"
                                    :class="type === 'video' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 bg-gray-50 hover:bg-white'">
                                    <input type="radio" name="type" value="video" x-model="type" class="mt-1 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-900">Video Latar (Auto Play)</span>
                                        <span class="block text-xs text-gray-500 mt-1">Satu buah video yang berputar secara dinamis berulang tanpa suara. (Maks 50MB)</span>
                                    </div>
                                </label>
                                
                                <label class="flex items-start gap-3 p-4 rounded-2xl border cursor-pointer transition-all"
                                    :class="type === 'image' ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 bg-gray-50 hover:bg-white'">
                                    <input type="radio" name="type" value="image" x-model="type" class="mt-1 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-900">Gambar Slider (Carousel)</span>
                                        <span class="block text-xs text-gray-500 mt-1">Beberapa gambar yang dapat bergeser otomatis secara pudar (fade).</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Media Uploads -->
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm" x-show="type !== 'map'">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-photo-film text-blue-500"></i>
                                Berkas Media
                            </h3>
                            <div class="space-y-5">
                                <!-- Video Target -->
                                <div x-show="type === 'video'">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Unggah Video Baru</label>
                                    <input type="file" name="video_file" @change="handleVideoUpload" accept="video/mp4,video/webm,video/ogg" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-400 mt-2">Format yang disarankan: MP4. Maks: 50MB.</p>

                                    @if($setting->type === 'video' && !empty($setting->media_paths))
                                        <div class="mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                                            <span class="text-xs font-bold text-gray-700 block mb-2">Video Aktif:</span>
                                            <video src="{{ Storage::url($setting->media_paths[0]) }}" controls class="w-full h-auto rounded-xl aspect-video object-cover"></video>
                                            <label class="flex items-center gap-2 mt-3 cursor-pointer">
                                                <input type="checkbox" name="remove_media" value="1" class="text-red-500 focus:ring-red-500 border-gray-300 rounded">
                                                <span class="text-xs text-red-600 font-semibold">Hapus Video Ini</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>

                                <!-- Image Target -->
                                <div x-show="type === 'image'">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tambah Gambar Baru</label>
                                    <input type="file" name="image_files[]" @change="handleImageUpload" multiple accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-400 mt-2">Anda dapat memilih lebih dari satu file sekaligus. Maks 10MB/foto.</p>

                                    @if($setting->type === 'image' && !empty($setting->media_paths))
                                        <div class="mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                                            <span class="text-xs font-bold text-gray-700 block mb-2">Gambar Slider Masing-Masing:</span>
                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach($setting->media_paths as $path)
                                                    <div class="relative group aspect-video">
                                                        <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover rounded-xl border border-gray-200">
                                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity rounded-xl">
                                                             <label class="text-white text-xs font-medium cursor-pointer flex flex-col items-center">
                                                                 <input type="checkbox" name="existing_media[]" value="{{ $path }}" checked class="mb-1 text-blue-500 focus:ring-blue-500 border-gray-300 rounded">
                                                                 Pertahankan
                                                             </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-2 italic">*Hapus centang pada gambar yang ingin dibuang dari putaran.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Content Texts -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-font text-blue-500"></i>
                                Konten Teks (Opsional)
                            </h3>
                            <div class="space-y-6">
                                <div class="bg-blue-50 text-blue-800 text-xs p-4 rounded-2xl flex gap-3 border border-blue-100">
                                     <i class="fa-solid fa-circle-info mt-0.5 text-blue-500"></i>
                                     <p class="font-medium leading-relaxed">Seluruh teks bersifat *opsional*. Jika Anda membiarkan formulir di bawah ini kosong, bagian *Hero* hanya akan menampilkan visual media layar penuh (tanpa tulisan apapun).</p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Badge (ID)</label>
                                        <input type="text" name="badge_id" value="{{ old('badge_id', $setting->badge_id) }}" x-model="badge_id" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Contoh: Jelajahi Jepara">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Badge (EN)</label>
                                        <input type="text" name="badge_en" value="{{ old('badge_en', $setting->badge_en) }}" x-model="badge_en" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Ex: Explore Jepara">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Utama (ID)</label>
                                        <input type="text" name="title_id" value="{{ old('title_id', $setting->title_id) }}" x-model="title_id" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Contoh: Temukan Keajaiban">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Utama (EN)</label>
                                        <input type="text" name="title_en" value="{{ old('title_en', $setting->title_en) }}" x-model="title_en" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Ex: Discover Wonders">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Subjudul (ID)</label>
                                        <textarea name="subtitle_id" rows="3" x-model="subtitle_id" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Deskripsi pendek...">{{ old('subtitle_id', $setting->subtitle_id) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Subjudul (EN)</label>
                                        <textarea name="subtitle_en" rows="3" x-model="subtitle_en" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Short description...">{{ old('subtitle_en', $setting->subtitle_en) }}</textarea>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teks Tombol (ID)</label>
                                        <input type="text" name="button_text_id" value="{{ old('button_text_id', $setting->button_text_id) }}" x-model="btn_id" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Mulai Petualangan">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teks Tombol (EN)</label>
                                        <input type="text" name="button_text_en" value="{{ old('button_text_en', $setting->button_text_en) }}" x-model="btn_en" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="Start Adventure">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">URL / Link Tombol</label>
                                        <input type="text" name="button_link" value="{{ old('button_link', $setting->button_link) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm" placeholder="#jelajah">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bottom Full Row: Realtime Preview -->
                    <div class="lg:col-span-3 mt-4">
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm space-y-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-1">
                                        <i class="fa-solid fa-eye text-blue-500"></i>
                                        Pratinjau Langsung (Preview)
                                    </h3>
                                    <p class="text-xs text-gray-500">Lihat perkiraan tampilan Hero Section secara langsung berdasarkan data tulisan dan tipe media Anda.</p>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" @click="previewLang = 'id'" :class="previewLang === 'id' ? 'bg-blue-600 text-white ring-2 ring-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-5 py-2 text-xs font-bold rounded-xl transition-all">Preview ID</button>
                                    <button type="button" @click="previewLang = 'en'" :class="previewLang === 'en' ? 'bg-blue-600 text-white ring-2 ring-blue-500/30' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-5 py-2 text-xs font-bold rounded-xl transition-all">Preview EN</button>
                                </div>
                            </div>
                            
                            <div class="relative w-full h-[400px] md:h-[500px] rounded-[2rem] overflow-hidden bg-slate-900 ring-1 ring-slate-900/10 shadow-lg flex flex-col items-center justify-center transition-all duration-500">
                                <!-- Dynamic Simulated Background based on current Type selection -->
                                <div class="absolute inset-0 z-0 flex items-center justify-center transition-opacity duration-300"
                                     :class="type === 'map' ? 'bg-[#1e293b]' : (type === 'video' ? 'bg-[#0f172a]' : 'bg-[#334155]')">
                                    
                                    <!-- Map Fallback -->
                                    <div x-show="type === 'map'" class="absolute inset-0 flex flex-col items-center justify-center opacity-30 gap-3">
                                        <i class="fa-solid fa-map-location-dot text-6xl text-white"></i>
                                        <span class="text-white text-xs font-bold uppercase tracking-widest">MAP Background (Simulated)</span>
                                    </div>
                                    
                                    <!-- Video Preview -->
                                    <div x-show="type === 'video'" class="absolute inset-0 w-full h-full">
                                        <template x-if="previewVideo">
                                            <video :src="previewVideo" autoplay loop muted playsinline class="w-full h-full object-cover"></video>
                                        </template>
                                        <template x-if="!previewVideo">
                                            <div class="absolute inset-0 flex flex-col items-center justify-center opacity-30 gap-3">
                                                <i class="fa-solid fa-film text-6xl text-white"></i>
                                                <span class="text-white text-xs font-bold uppercase tracking-widest">Video Background (Upload Video)</span>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- Image Preview -->
                                    <div x-show="type === 'image'" class="absolute inset-0 w-full h-full bg-slate-900">
                                         <template x-if="previewImages.length > 0">
                                             <div>
                                                 <template x-for="(imgSrc, idx) in previewImages" :key="idx">
                                                     <div x-show="currentSlide === idx" 
                                                          x-transition:enter="transition-opacity ease-in-out duration-1000"
                                                          x-transition:enter-start="opacity-0"
                                                          x-transition:enter-end="opacity-100"
                                                          x-transition:leave="transition-opacity ease-in-out duration-1000"
                                                          x-transition:leave-start="opacity-100"
                                                          x-transition:leave-end="opacity-0"
                                                          class="absolute inset-0 w-full h-full">
                                                          <img :src="imgSrc" class="w-full h-full object-cover">
                                                     </div>
                                                 </template>
                                             </div>
                                         </template>
                                         <template x-if="previewImages.length === 0">
                                             <div class="absolute inset-0 flex flex-col items-center justify-center opacity-30 gap-3">
                                                 <i class="fa-solid fa-images text-6xl text-white"></i>
                                                 <span class="text-white text-xs font-bold uppercase tracking-widest">Image Background (Upload Image)</span>
                                             </div>
                                         </template>
                                    </div>
                                </div>
                                
                                <!-- Overlay to darken text background -->
                                <div class="absolute inset-0 z-10 bg-gradient-to-t from-slate-900 via-slate-900/40 to-slate-900/40" x-show="badge_id || title_id || subtitle_id"></div>

                                <!-- Texts overlay -->
                                <div class="absolute inset-0 z-20 flex flex-col items-center justify-center px-4 text-center pointer-events-none p-6">
                                    <div class="w-full max-w-4xl mx-auto space-y-6 flex flex-col items-center">
                                        
                                        <!-- Badge -->
                                        <div class="h-8 flex items-center justify-center">
                                            <span x-show="(previewLang === 'id' && badge_id) || (previewLang === 'en' && badge_en)" 
                                                  x-transition.opacity
                                                  x-text="previewLang === 'id' ? badge_id : badge_en" 
                                                  class="inline-block px-5 py-2 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-white text-xs font-bold uppercase tracking-widest shadow-lg">
                                            </span>
                                        </div>
                                        
                                        <!-- Title -->
                                        <div class="h-auto min-h-[4rem] flex items-center justify-center">
                                            <h1 x-show="(previewLang === 'id' && title_id) || (previewLang === 'en' && title_en)" 
                                                x-transition.opacity
                                                x-html="previewLang === 'id' ? title_id : title_en" 
                                                class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-black leading-tight tracking-tight drop-shadow-2xl selection:bg-blue-500/30">
                                            </h1>
                                        </div>
                                        
                                        <!-- Subtitle -->
                                        <div class="h-auto min-h-[3rem] flex items-center justify-center w-full">
                                            <p x-show="(previewLang === 'id' && subtitle_id) || (previewLang === 'en' && subtitle_en)" 
                                               x-transition.opacity
                                               x-text="previewLang === 'id' ? subtitle_id : subtitle_en" 
                                               class="text-slate-200 text-sm md:text-lg font-medium max-w-2xl mx-auto leading-relaxed drop-shadow-lg shadow-black/50">
                                            </p>
                                        </div>
                                        
                                        <!-- Button -->
                                        <div class="h-14 flex items-center justify-center pt-2">
                                            <div x-show="(previewLang === 'id' && btn_id) || (previewLang === 'en' && btn_en)" x-transition.opacity>
                                                <span class="inline-flex items-center justify-center h-12 px-8 rounded-full bg-blue-600 text-white text-sm font-bold shadow-xl overflow-hidden relative group">
                                                    <span class="relative z-10" x-text="previewLang === 'id' ? btn_id : btn_en"></span>
                                                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                    <div class="lg:col-span-3 flex justify-end gap-3 mt-4">
                        <button type="submit" class="inline-flex items-center px-8 py-3.5 bg-blue-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
