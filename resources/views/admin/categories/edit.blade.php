<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Kategori') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200"
                     x-data="{ 
                        icon: '{{ old('icon_class', $category->icon_class) }}',
                        color: '{{ old('color', $category->color) }}'
                     }">
                    
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Kategori')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $category->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Icon Class -->
                        <div class="mb-4">
                            <x-input-label for="icon_class" :value="__('Kelas Ikon (FontAwesome)')" />
                            <div class="flex gap-2">
                                <x-text-input id="icon_class" class="block mt-1 w-full" type="text" name="icon_class" x-model="icon" />
                                <div class="mt-1 w-10 flex items-center justify-center bg-gray-100 rounded border border-gray-300">
                                    <i class="text-gray-600" :class="icon || 'fa-solid fa-question'"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('icon_class')" class="mt-2" />
                        </div>

                        <!-- Color -->
                        <div class="mb-4">
                            <x-input-label for="color" :value="__('Warna Penanda')" />
                            <div class="flex items-center gap-2 mt-1">
                                <input type="color" name="color" id="color" x-model="color" class="h-10 w-20 rounded border border-gray-300 p-1">
                                <x-text-input type="text" id="color_text" class="block w-32" x-model="color" readonly />
                            </div>
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2" wire:navigate>
                                Batal
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
</x-app-layout>
