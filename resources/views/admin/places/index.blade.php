<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Destinasi Wisata
                </h2>
            </div>
            <a href="{{ route('admin.places.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-semibold text-sm text-white  hover:shadow-blue-500/40 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Lokasi
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center border border-blue-100">
                            <i class="fa-solid fa-map-location-dot text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                            <p class="text-sm text-gray-500 font-medium">Total Lokasi</p>
                            <p class="text-xs text-gray-400">Semua destinasi</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center border border-green-100">
                            <i class="fa-solid fa-star text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_rating'], 1) }}</p>
                            <p class="text-sm text-gray-500 font-medium">Rata-rata Rating</p>
                            <p class="text-xs text-gray-400">Dari semua lokasi</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center border border-purple-100">
                            <i class="fa-solid fa-images text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['with_photo'] }}</p>
                            <p class="text-sm text-gray-500 font-medium">Dengan Foto</p>
                            <p class="text-xs text-gray-400">Sudah ada gambar</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Bar -->
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
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                         <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        x-model="query"
                        @input.debounce.500ms="updateList()"
                        class="block w-full pl-11 pr-11 py-3 border-0 rounded-2xl bg-white text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all shadow-sm placeholder-gray-400" 
                        placeholder="Cari lokasi wisata..."
                    >
                    <button 
                        type="button" 
                        x-show="query.length > 0" 
                        @click="query = ''; updateList()"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500 cursor-pointer transition-colors"
                        style="display: none;"
                        x-transition
                    >
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="rounded-[2rem] border border-gray-100 overflow-hidden bg-white" id="table-wrapper">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Alamat</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($places as $place)
                                    <tr class="hover:bg-blue-50/30 transition-colors group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-shrink-0 h-14 w-20 rounded-2xl overflow-hidden bg-gray-50 border border-gray-200/50">
                                                    @if($place->image_path)
                                                        <img src="{{ asset($place->image_path) }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $place->name }}">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-300">
                                                            <i class="fa-solid fa-image text-xl"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <a href="{{ route('admin.places.edit', $place) }}" class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors line-clamp-1">
                                                        {{ $place->name }}
                                                    </a>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        @if($place->rating)
                                                            <span class="inline-flex items-center gap-1 text-xs text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">
                                                                <i class="fa-solid fa-star text-[10px]"></i>
                                                                {{ number_format($place->rating, 1) }}
                                                            </span>
                                                        @endif
                                                    @if($place->name_en)
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 bg-blue-50 rounded">
                                                            <i class="fa-solid fa-language"></i> EN
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg text-white border border-white/20" style="background-color: {{ $place->category->color }}">
                                                <i class="{{ $place->category->icon_class ?? 'fa-solid fa-tag' }} text-[10px] opacity-90"></i>
                                                {{ $place->category->name }}
                                            </span>
                                        </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            @if($place->address)
                                                <div class="text-sm text-gray-600 flex items-start gap-2">
                                                    <i class="fa-solid fa-location-dot text-red-400 mt-0.5 text-xs"></i>
                                                    <span class="line-clamp-2">{{ $place->address }}</span>
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-400 flex items-center gap-2">
                                                    <i class="fa-solid fa-location-dot text-xs"></i>
                                                    <span>Tidak ada alamat</span>
                                                </div>
                                            @endif
                                            <div class="text-[10px] text-gray-400 font-mono mt-1 ml-4">
                                                {{ number_format($place->latitude, 5) }}, {{ number_format($place->longitude, 5) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('places.show', $place) }}" target="_blank"
                                               class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" 
                                               title="Lihat">
                                                <i class="fa-solid fa-external-link text-sm"></i>
                                            </a>
                                            <a href="{{ route('admin.places.edit', $place) }}" 
                                               class="p-2.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" 
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                                            </a>
                                            <form action="{{ route('admin.places.destroy', $place) }}" method="POST" class="inline-block delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" 
                                                        title="Hapus">
                                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                                <i class="fa-solid fa-map-location-dot text-3xl text-gray-300"></i>
                                            </div>
                                            <p class="text-gray-600 font-medium mb-1">Tidak ada lokasi ditemukan</p>
                                            <p class="text-sm text-gray-400 mb-4">Coba ubah pencarian atau tambahkan lokasi baru</p>
                                            <a href="{{ route('admin.places.create') }}" 
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                                                <i class="fa-solid fa-plus text-xs"></i>
                                                Tambah Lokasi
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($places->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                        {{ $places->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
