<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Admin Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6" x-data="{ role: '' }">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                            type="password"
                                            name="password"
                                            required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                            type="password"
                                            name="password_confirmation"
                                            required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div>
                            <x-input-label for="role" :value="__('Peran / Role')" />
                            <select id="role" name="role" x-model="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" disabled selected>Pilih Peran</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500" x-show="role === 'super_admin'">Akses penuh ke seluruh sistem.</p>
                            <p class="mt-1 text-sm text-gray-500" x-show="role === 'admin_wisata'">Mengelola semua destinasi, tiket, dan laporan keuangan.</p>
                            <p class="mt-1 text-sm text-gray-500" x-show="role === 'admin_berita'">Mengelola berita dan event.</p>
                            <p class="mt-1 text-sm text-gray-500" x-show="role === 'pengelola_wisata'">Hanya mengelola destinasi yang ditugaskan.</p>
                        </div>

                        <!-- Place Assignment (Conditional) -->
                        <div x-show="role === 'pengelola_wisata'" x-transition class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <h3 class="text-blue-800 font-bold text-sm mb-2"> <i class="fa-solid fa-map-location-dot"></i> Penugasan Destinasi</h3>
                            <p class="text-xs text-blue-600 mb-3">Pilih destinasi wisata yang akan dikelola oleh admin ini.</p>
                            
                            <x-input-label for="place_id" :value="__('Destinasi Wisata')" />
                            <select id="place_id" name="place_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Destinasi --</option>
                                @foreach($places as $place)
                                    <option value="{{ $place->id }}">{{ $place->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('place_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.users.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Batal</a>
                            <x-primary-button>
                                {{ __('Simpan Admin') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
