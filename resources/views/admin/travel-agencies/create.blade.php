<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.travel-agencies.index') }}" 
                   class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all duration-200"
                   wire:navigate>
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                        Tambah Biro Wisata
                    </h2>
                    <p class="text-sm text-gray-500">Tambahkan informasi biro wisata tour dan travel baru</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm overflow-hidden p-6 md:p-8">
                
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.travel-agencies.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Logo Biro Wisata</label>
                            <div class="mt-2 flex justify-center rounded-xl border border-dashed border-gray-300 px-6 py-10 hover:bg-gray-50 transition-colors"
                                 x-data="{ imagePreview: null }"
                                 @dragover.prevent="$el.classList.add('bg-blue-50', 'border-blue-400')"
                                 @dragleave.prevent="$el.classList.remove('bg-blue-50', 'border-blue-400')"
                                 @drop.prevent="
                                     $el.classList.remove('bg-blue-50', 'border-blue-400');
                                     const file = $event.dataTransfer.files[0];
                                     if (file && file.type.startsWith('image/')) {
                                         $refs.fileInput.files = $event.dataTransfer.files;
                                         const reader = new FileReader();
                                         reader.onload = (e) => imagePreview = e.target.result;
                                         reader.readAsDataURL(file);
                                     }
                                 ">
                                
                                <div class="text-center" x-show="!imagePreview">
                                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-300 mb-3"></i>
                                    <div class="mt-4 flex text-sm leading-6 text-gray-600 justify-center">
                                        <label for="logo" class="relative cursor-pointer rounded-md bg-white font-semibold text-blue-600 focus-within:outline-none hover:text-blue-500">
                                            <span>Upload a file</span>
                                            <input id="logo" name="logo" type="file" class="sr-only" x-ref="fileInput" accept="image/*"
                                                   @change="
                                                       const file = $event.target.files[0];
                                                       if (file) {
                                                           const reader = new FileReader();
                                                           reader.onload = (e) => imagePreview = e.target.result;
                                                           reader.readAsDataURL(file);
                                                       }
                                                   ">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs leading-5 text-gray-500">PNG, JPG up to 2MB</p>
                                </div>

                                <div class="relative w-full max-w-sm mx-auto" x-show="imagePreview" style="display: none;">
                                    <img :src="imagePreview" class="w-full h-48 object-contain rounded-lg">
                                    <button type="button" 
                                            @click="imagePreview = null; $refs.fileInput.value = ''" 
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition shadow-lg">
                                        <i class="fa-solid fa-xmark w-4 h-4 flex items-center justify-center"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Nama Biro Wisata <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-store text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                           class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                           placeholder="Contoh: Jeparise Tour & Travel">
                                </div>
                            </div>

                            <!-- Nama Pemilik -->
                            <div>
                                <label for="owner_name" class="block text-sm font-semibold text-gray-900 mb-2">Nama Pemilik</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}"
                                           class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                           placeholder="Contoh: Budi Santoso">
                                </div>
                            </div>

                            <!-- Jenis Badan Usaha -->
                            <div>
                                <label for="business_type" class="block text-sm font-semibold text-gray-900 mb-2">Jenis Badan Usaha</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-building text-gray-400"></i>
                                    </div>
                                    <select name="business_type" id="business_type"
                                            class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">-- Pilih --</option>
                                        <option value="Perorangan" {{ old('business_type') == 'Perorangan' ? 'selected' : '' }}>Perorangan</option>
                                        <option value="CV" {{ old('business_type') == 'CV' ? 'selected' : '' }}>CV</option>
                                        <option value="PT" {{ old('business_type') == 'PT' ? 'selected' : '' }}>PT</option>
                                        <option value="PT Perorangan" {{ old('business_type') == 'PT Perorangan' ? 'selected' : '' }}>PT Perorangan</option>
                                    </select>
                                </div>
                            </div>

                            <!-- NIB -->
                            <div>
                                <label for="nib" class="block text-sm font-semibold text-gray-900 mb-2">Nomor Induk Berusaha (NIB)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-id-card text-gray-400"></i>
                                    </div>
                                    <input type="text" name="nib" id="nib" value="{{ old('nib') }}"
                                           class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                           placeholder="Contoh: 1211000121011">
                                </div>
                            </div>

                            <!-- Alamat Kantor -->
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-900 mb-2">Alamat Kantor</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-location-dot text-gray-400"></i>
                                    </div>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                                           class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                           placeholder="Contoh: Jl. Pemuda No. 5, Karimunjawa">
                                </div>
                            </div>

                            <!-- WhatsApp -->
                            <div>
                                <label for="contact_wa" class="block text-sm font-semibold text-gray-900 mb-2">Nomor WhatsApp</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-brands fa-whatsapp text-green-500"></i>
                                    </div>
                                    <input type="text" name="contact_wa" id="contact_wa" value="{{ old('contact_wa') }}"
                                           class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                           placeholder="Contoh: 628123456789">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gunakan format 62xxx (tanpa '+' atau '0')</p>
                            </div>

                            <!-- Instagram -->
                            <div>
                                <label for="instagram" class="block text-sm font-semibold text-gray-900 mb-2">Instagram</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-brands fa-instagram text-pink-500"></i>
                                    </div>
                                    <input type="text" name="instagram" id="instagram" value="{{ old('instagram') }}"
                                           class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                                           placeholder="Contoh: @jepartise">
                                </div>
                            </div>
                            
                            <!-- Website -->
                            <div class="md:col-span-2">
                                <label for="website" class="block text-sm font-semibold text-gray-900 mb-2">Website</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-globe text-gray-400"></i>
                                    </div>
                                    <input type="url" name="website" id="website" value="{{ old('website') }}"
                                           class="pl-11 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                           placeholder="Contoh: https://jeparisetour.com">
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">Deskripsi & Tentang Biro <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="5" required
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-4 placeholder-gray-400"
                                      placeholder="Jelaskan mengenai reputasi biro wisata, lisensi yang dimiliki, dan fokus utama wisatanya...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Destinasi yang Dilayani -->
                        @if($flagshipPlaces->count() > 0)
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-3">
                                <i class="fa-solid fa-map-pin text-blue-500 mr-1"></i> Destinasi yang Dilayani
                            </label>
                            <p class="text-xs text-gray-500 mb-3">Pilih destinasi unggulan yang dilayani oleh biro wisata ini</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($flagshipPlaces as $fp)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-blue-300 hover:bg-blue-50/50 transition-all cursor-pointer group">
                                    <input type="checkbox" name="places[]" value="{{ $fp->id }}" 
                                           {{ in_array($fp->id, old('places', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                                    <div class="flex items-center gap-2 min-w-0">
                                        @if($fp->image_path)
                                            <img src="{{ asset($fp->image_path) }}" class="w-8 h-8 rounded-lg object-cover shrink-0" alt="{{ $fp->name }}">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-mountain-sun text-gray-400 text-xs"></i>
                                            </div>
                                        @endif
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600 transition-colors truncate">{{ $fp->name }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.travel-agencies.index') }}" 
                           class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-100 border border-gray-200 transition-all">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm hover:bg-blue-700 shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                            <i class="fa-solid fa-save"></i>
                            Simpan Biro
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
