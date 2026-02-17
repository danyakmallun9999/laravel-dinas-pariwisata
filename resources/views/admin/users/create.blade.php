<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="group inline-flex items-center justify-center w-10 h-10 bg-white rounded-xl border border-gray-200 shadow-sm hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-all duration-200" wire:navigate>
                <i class="fa-solid fa-arrow-left text-sm text-gray-500 group-hover:text-blue-600"></i>
            </a>
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Manajemen Admin</p>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight">
                    Tambah Admin Baru
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                     <i class="fa-solid fa-user-plus text-9xl text-blue-600 transform rotate-12"></i>
                </div>

                <div class="p-8 md:p-10 relative z-10">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8" x-data="{ role: '' }">
                        @csrf

                        <!-- Personal Info Section -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 border-b border-gray-100 pb-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 text-sm">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                Informasi Pribadi
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-600 font-medium" />
                                    <x-text-input id="name" class="block mt-2 w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500/20 py-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukan nama lengkap..." />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Email Address -->
                                <div>
                                    <x-input-label for="email" :value="__('Email Address')" class="text-gray-600 font-medium" />
                                    <x-text-input id="email" class="block mt-2 w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500/20 py-3" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="contoh@dinaspariwisata.go.id" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100 space-y-6">
                             <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 pb-2">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 text-sm">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                Keamanan Akun
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Password -->
                                <div>
                                    <x-input-label for="password" :value="__('Password')" class="text-gray-600 font-medium" />
                                    <x-text-input id="password" class="block mt-2 w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500/20 py-3"
                                                    type="password"
                                                    name="password"
                                                    required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-600 font-medium" />
                                    <x-text-input id="password_confirmation" class="block mt-2 w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500/20 py-3"
                                                    type="password"
                                                    name="password_confirmation"
                                                    required autocomplete="new-password" placeholder="Ulangi password..." />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Role & Permissions Section -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 border-b border-gray-100 pb-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 text-sm">
                                    <i class="fa-solid fa-user-gear"></i>
                                </span>
                                Peran & Akses
                            </h3>

                            <div>
                                <x-input-label for="role" :value="__('Peran Admin')" class="text-gray-600 font-medium" />
                                <div class="mt-2 relative">
                                    <select id="role" name="role" x-model="role" class="block w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500/20 py-3 pl-4 pr-10 appearance-none bg-no-repeat bg-right-4">
                                        <option value="" disabled selected>-- Pilih Peran Admin --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ ucwords(str_replace('_', ' ', $role->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                
                                <!-- Role Descriptions -->
                                <div class="mt-3 text-sm p-3 bg-blue-50/50 rounded-lg border border-blue-100 text-blue-700 flex items-start gap-2" x-show="role" x-transition>
                                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                                    <div>
                                        <p x-show="role === 'super_admin'"><strong>Super Admin:</strong> Memiliki akses penuh ke seluruh pengaturan sistem dan manajemen data.</p>
                                        <p x-show="role === 'admin_wisata'"><strong>Admin Wisata:</strong> Dapat mengelola data semua destinasi wisata, tiket, ki jategori, dan melihat laporan keuangan.</p>
                                        <p x-show="role === 'admin_berita'"><strong>Admin Berita:</strong> Fokus pada pengelolaan konten berita, artikel, dan event promosi.</p>
                                        <p x-show="role === 'pengelola_wisata'"><strong>Pengelola Wisata:</strong> Hanya dapat mengelola data dan validasi tiket untuk destinasi wisata tertentu yang ditugaskan.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Place Assignment (Conditional) -->
                            <div x-show="role === 'pengelola_wisata'" x-transition 
                                 class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100 relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-blue-900 font-bold text-base mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-map-location-dot text-blue-600"></i> 
                                        Penugasan Lokasi
                                    </h3>
                                    <p class="text-sm text-blue-700/80 mb-4">Pilih destinasi wisata spesifik yang akan dikelola oleh admin ini.</p>
                                    
                                    <div class="relative">
                                        <x-input-label for="place_id" :value="__('Destinasi Wisata')" class="text-blue-800 font-medium" />
                                        <select id="place_id" name="place_id" class="block mt-2 w-full rounded-xl border-blue-200 focus:border-blue-500 focus:ring-blue-500/20 py-3">
                                            <option value="">-- Pilih Destinasi --</option>
                                            @foreach($places as $place)
                                                <option value="{{ $place->id }}">{{ $place->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <x-input-error :messages="$errors->get('place_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 gap-3">
                            <a href="{{ route('admin.users.index') }}" 
                               class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-900 transition-colors" wire:navigate>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl font-bold text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:-translate-y-0.5 active:scale-95">
                                <i class="fa-regular fa-floppy-disk"></i>
                                <span>Simpan Admin</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
