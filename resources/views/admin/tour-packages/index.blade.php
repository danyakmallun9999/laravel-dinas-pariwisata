<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Kelola Paket Liburan
                </h2>
            </div>
            <a href="{{ route('admin.tour-packages.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 md:px-5 md:py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-semibold text-xs md:text-sm text-white hover:shadow-blue-500/40 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:-translate-y-0.5" wire:navigate>
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden md:inline">Tambah Paket</span>
                <span class="md:hidden">Paket</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="mb-6" 
                x-data="{ 
                    query: '{{ request('search') }}',
                    updateList() {
                        const params = new URLSearchParams();
                        if (this.query) params.set('search', this.query);
                        
                        const url = `${window.location.pathname}?${params.toString()}`;
                        history.pushState(null, '', url);

                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContent = doc.getElementById('table-wrapper').innerHTML;
                                document.getElementById('table-wrapper').innerHTML = newContent;
                            });
                    }
                }"
            >
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-4 rounded-[2rem] border border-gray-100 bg-gray-50/30">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input 
                                type="text" 
                                x-model="query"
                                @input.debounce.500ms="updateList()"
                                class="block w-full pl-11 pr-10 py-3 border-0 bg-white rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:bg-white sm:text-sm transition-all placeholder-gray-400 shadow-sm" 
                                placeholder="Cari nama paket wisata..."
                            >
                            <button 
                                type="button" 
                                x-show="query.length > 0" 
                                @click="query = ''; updateList()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 cursor-pointer transition-colors"
                                style="display: none;"
                            >
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="rounded-[2rem] border border-gray-100 overflow-hidden bg-white" id="table-wrapper">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Paket</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Biro / Destinasi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Harga & Durasi</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($packages as $pkg)
                                    <tr class="hover:bg-blue-50/30 transition-colors duration-200 group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm overflow-hidden bg-blue-100 border border-blue-200 text-blue-500">
                                                    <i class="fa-solid fa-map text-xl"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $pkg->name }}</div>
                                                    <div class="text-xs text-green-600 font-semibold flex items-center gap-1 mt-0.5"><i class="fa-solid fa-check-circle text-xs"></i> {{ is_array($pkg->inclusions) ? count($pkg->inclusions) : 0 }} Fasilitas</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 item-center">
                                            <div class="flex flex-col gap-1">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold border border-blue-200 bg-blue-50 text-blue-700 w-fit">
                                                    <i class="fa-solid fa-store text-blue-400"></i>
                                                    {{ $pkg->travelAgency->name ?? '-' }}
                                                </span>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs border border-gray-200 bg-gray-50 text-gray-600 w-fit">
                                                    <i class="fa-solid fa-location-dot text-gray-400"></i>
                                                    {{ $pkg->place->name ?? '-' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 item-center">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm font-extrabold text-blue-700">Rp {{ number_format($pkg->price_start, 0, ',', '.') }}</span>
                                                <span class="text-xs text-gray-500 font-medium">
                                                    <i class="fa-regular fa-clock"></i> {{ $pkg->duration_days }}H {{ $pkg->duration_nights }}M
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.tour-packages.edit', $pkg) }}" 
                                                   class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200" 
                                                   title="Edit" wire:navigate>
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form action="{{ route('admin.tour-packages.destroy', $pkg) }}" method="POST" class="inline-block delete-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket tour ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200" 
                                                            title="Hapus">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-16 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                                    <i class="fa-solid fa-map-location-dot text-3xl text-gray-300"></i>
                                                </div>
                                                <p class="text-gray-600 font-medium mb-1">Belum ada paket liburan yang ditambahkan</p>
                                                <a href="{{ route('admin.tour-packages.create') }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 mt-2 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 transition-colors" wire:navigate>
                                                    <i class="fa-solid fa-plus"></i>
                                                    Tambah Paket
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($packages->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                            {{ $packages->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
