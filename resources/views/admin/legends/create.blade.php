<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.legends.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-blue-600 transition-colors" wire:navigate>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <p class="hidden md:block text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Tambah Tokoh Sejarah
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.legends.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    {{-- Basic Info --}}
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Tokoh</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                       class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Foto Tokoh</label>
                                <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100" />
                                <p class="mt-1 text-xs text-gray-400">Rekomendasi: Aspek rasio 4:5 atau 1:1, Max 4MB.</p>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>

                            <div>
                                <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">Urutan Tampil</label>
                                <input id="order" name="order" type="number" value="{{ old('order', 0) }}"
                                       class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all" />
                                <x-input-error :messages="$errors->get('order')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- Translations - Quotes --}}
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-quote-left text-blue-500"></i>
                            Kutipan / Slogan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="quote_id" class="block text-sm font-semibold text-gray-700 mb-2">Kutipan (Indonesia)</label>
                                <input id="quote_id" name="quote_id" type="text" value="{{ old('quote_id') }}"
                                       class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all" />
                                <x-input-error :messages="$errors->get('quote_id')" class="mt-2" />
                            </div>
                            <div>
                                <label for="quote_en" class="block text-sm font-semibold text-gray-700 mb-2">Kutipan (English)</label>
                                <input id="quote_en" name="quote_en" type="text" value="{{ old('quote_en') }}"
                                       class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all" />
                                <x-input-error :messages="$errors->get('quote_en')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- Translations - Descriptions --}}
                    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-align-left text-blue-500"></i>
                            Deskripsi Singkat
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <label for="description_id" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Indonesia)</label>
                                <textarea name="description_id" id="description_id" rows="3" class="mt-1 block w-full bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm">{{ old('description_id') }}</textarea>
                                <x-input-error :messages="$errors->get('description_id')" class="mt-2" />
                            </div>
                            <div>
                                <label for="description_en" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (English)</label>
                                <textarea name="description_en" id="description_en" rows="3" class="mt-1 block w-full bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm">{{ old('description_en') }}</textarea>
                                <x-input-error :messages="$errors->get('description_en')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <label class="inline-flex items-center mr-4 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 font-medium">Aktifkan langsung</span>
                        </label>
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Simpan Tokoh
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
