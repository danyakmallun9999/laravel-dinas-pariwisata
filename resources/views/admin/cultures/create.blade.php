<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Budaya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.cultures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
                          x-data="{
                              selectedCategory: '{{ old('category', 'Kemahiran & Kerajinan Tradisional (Kriya)') }}',
                              get showLocationTime() {
                                  const hide = ['Kemahiran & Kerajinan Tradisional (Kriya)', 'Seni Pertunjukan', 'Kuliner Khas'];
                                  return !hide.includes(this.selectedCategory);
                              }
                          }">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Budaya')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category" :value="__('Kategori')" />
                            <select id="category" name="category" x-model="selectedCategory" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Kemahiran & Kerajinan Tradisional (Kriya)">Kemahiran & Kerajinan Tradisional (Kriya)</option>
                                <option value="Adat Istiadat, Ritus, & Perayaan Tradisional">Adat Istiadat, Ritus, & Perayaan Tradisional</option>
                                <option value="Seni Pertunjukan">Seni Pertunjukan</option>
                                <option value="Kawasan Cagar Budaya & Sejarah">Kawasan Cagar Budaya & Sejarah</option>
                                <option value="Kuliner Khas">Kuliner Khas</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category')" />
                        </div>

                        <!-- Image -->
                        <div>
                            <x-input-label for="image" :value="__('Gambar')" />
                            <input id="image" name="image" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" />
                            <x-input-error class="mt-2" :messages="$errors->get('image')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Deskripsi Singkat')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Content -->
                        <div>
                            <x-input-label for="content" :value="__('Konten Lengkap')" />
                            <textarea id="content" name="content" rows="6" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('content') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>

                        <!-- Location -->
                        <div x-show="showLocationTime" x-transition x-cloak>
                            <x-input-label for="location" :value="__('Lokasi')" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" placeholder="Contoh: Kota Jepara, Jawa Tengah" />
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>

                        <!-- Time -->
                        <div x-show="showLocationTime" x-transition x-cloak>
                            <x-input-label for="time" :value="__('Waktu / Jadwal')" />
                            <x-text-input id="time" name="time" type="text" class="mt-1 block w-full" :value="old('time')" placeholder="Contoh: Setiap hari, 08.00 - 17.00" />
                            <x-input-error class="mt-2" :messages="$errors->get('time')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('admin.cultures.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
