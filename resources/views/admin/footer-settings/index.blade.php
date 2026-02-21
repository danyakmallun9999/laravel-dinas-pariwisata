<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Pengaturan Footer
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

            <form action="{{ route('admin.footer-settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Informasi Umum -->
                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                Informasi Umum
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Tentang (ID)</label>
                                    <textarea name="about_id" rows="3" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm">{{ old('about_id', $setting->about_id) }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Tentang (EN)</label>
                                    <textarea name="about_en" rows="3" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm">{{ old('about_en', $setting->about_en) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-address-book text-blue-500"></i>
                                Kontak
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                                    <input type="text" name="address" value="{{ old('address', $setting->address) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:bg-white focus:ring-0 focus:border-blue-500 transition-all shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sosial Media -->
                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-share-nodes text-blue-500"></i>
                                Tautan Sosial Media
                            </h3>
                            <div class="space-y-6">
                                <div class="bg-blue-50 text-blue-800 text-xs p-4 rounded-2xl flex gap-3 border border-blue-100">
                                    <i class="fa-solid fa-circle-info mt-0.5 text-blue-500"></i>
                                    <p class="font-medium leading-relaxed">Kosongkan kolom tautan jika Anda tidak ingin menampilkan ikon sosial media tersebut di footer.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Facebook URL</label>
                                    <div class="mt-1 flex rounded-xl shadow-sm overflow-hidden border border-gray-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all">
                                      <span class="inline-flex items-center px-4 bg-gray-50 text-gray-500 text-sm border-r border-gray-200">
                                        <i class="fa-brands fa-facebook-f w-4 text-center"></i>
                                      </span>
                                      <input type="url" name="facebook_link" value="{{ old('facebook_link', $setting->facebook_link) }}" class="flex-1 min-w-0 block w-full px-4 py-3 bg-gray-50 text-gray-900 focus:bg-white border-0 focus:ring-0 sm:text-sm transition-all" placeholder="https://facebook.com/...">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Instagram URL</label>
                                    <div class="mt-1 flex rounded-xl shadow-sm overflow-hidden border border-gray-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all">
                                      <span class="inline-flex items-center px-4 bg-gray-50 text-gray-500 text-sm border-r border-gray-200">
                                        <i class="fa-brands fa-instagram w-4 text-center"></i>
                                      </span>
                                      <input type="url" name="instagram_link" value="{{ old('instagram_link', $setting->instagram_link) }}" class="flex-1 min-w-0 block w-full px-4 py-3 bg-gray-50 text-gray-900 focus:bg-white border-0 focus:ring-0 sm:text-sm transition-all" placeholder="https://instagram.com/...">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">YouTube URL</label>
                                    <div class="mt-1 flex rounded-xl shadow-sm overflow-hidden border border-gray-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all">
                                      <span class="inline-flex items-center px-4 bg-gray-50 text-gray-500 text-sm border-r border-gray-200">
                                        <i class="fa-brands fa-youtube w-4 text-center"></i>
                                      </span>
                                      <input type="url" name="youtube_link" value="{{ old('youtube_link', $setting->youtube_link) }}" class="flex-1 min-w-0 block w-full px-4 py-3 bg-gray-50 text-gray-900 focus:bg-white border-0 focus:ring-0 sm:text-sm transition-all" placeholder="https://youtube.com/...">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Twitter / X URL</label>
                                    <div class="mt-1 flex rounded-xl shadow-sm overflow-hidden border border-gray-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all">
                                      <span class="inline-flex items-center px-4 bg-gray-50 text-gray-500 text-sm border-r border-gray-200">
                                        <i class="fa-brands fa-x-twitter w-4 text-center"></i>
                                      </span>
                                      <input type="url" name="twitter_link" value="{{ old('twitter_link', $setting->twitter_link) }}" class="flex-1 min-w-0 block w-full px-4 py-3 bg-gray-50 text-gray-900 focus:bg-white border-0 focus:ring-0 sm:text-sm transition-all" placeholder="https://twitter.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                    <div class="lg:col-span-2 flex justify-end gap-3 mt-4">
                        <button type="submit" class="inline-flex items-center px-8 py-3.5 bg-blue-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            <i class="fa-solid fa-save mr-2"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
