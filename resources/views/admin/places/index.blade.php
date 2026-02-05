<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Destinasi Wisata') }}
            </h2>
            <a href="{{ route('admin.places.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Lokasi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter Bar -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-center" 
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
                <div class="w-full sm:w-auto flex-1 max-w-lg">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                             <i class="fa-solid fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input 
                            type="text" 
                            x-model="query"
                            @input.debounce.500ms="updateList()"
                            class="block w-full pl-11 pr-10 py-2.5 border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 sm:text-sm transition-all shadow-sm hover:bg-white placeholder-gray-400" 
                            placeholder="Cari lokasi..."
                        >
                        <button 
                            type="button" 
                            x-show="query.length > 0" 
                            @click="query = ''; updateList()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 cursor-pointer transition-colors"
                            style="display: none;"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90"
                        >
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" id="table-wrapper">
                    
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Alamat & Koordinat</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($places as $place)
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-shrink-0 h-16 w-24 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                                    @if($place->image_path)
                                                        <img src="{{ asset($place->image_path) }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $place->name }}">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                            <i class="fa-solid fa-map-location-dot text-xl"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-bold text-gray-900 line-clamp-1 mb-0.5">{{ $place->name }}</div>
                                                    <div class="text-xs text-gray-500 line-clamp-1 max-w-md">{{ Str::limit($place->description, 80) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full text-white" style="background-color: {{ $place->category->color }}">
                                                <i class="{{ $place->category->icon_class ?? 'fa-solid fa-tag' }} text-[10px]"></i>
                                                {{ $place->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <div class="text-xs text-gray-600 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-location-dot text-red-500 w-3"></i>
                                                    <span class="truncate max-w-xs">{{ $place->address ?? '-' }}</span>
                                                </div>
                                                <div class="text-[10px] text-gray-400 font-mono pl-4.5">
                                                    {{ $place->latitude }}, {{ $place->longitude }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.places.edit', $place) }}" class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                                                </a>
                                                <form action="{{ route('admin.places.destroy', $place) }}" method="POST" class="inline-block delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                        <i class="fa-solid fa-trash-can text-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fa-solid fa-map-location-dot text-2xl text-gray-400"></i>
                                                </div>
                                                <p class="text-gray-500 text-sm mb-1">Tidak ada lokasi ditemukan</p>
                                                <p class="text-xs text-gray-400">Coba ubah pencarian atau tambahkan lokasi baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $places->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
