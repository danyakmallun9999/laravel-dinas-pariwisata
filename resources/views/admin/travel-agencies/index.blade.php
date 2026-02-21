<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Kelola Biro Wisata
                </h2>
            </div>
            <a href="{{ route('admin.travel-agencies.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 md:px-5 md:py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-semibold text-xs md:text-sm text-white hover:shadow-blue-500/40 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:-translate-y-0.5" wire:navigate>
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden md:inline">Tambah Biro</span>
                <span class="md:hidden">Biro</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-1 gap-4">
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center border border-blue-100">
                            <i class="fa-solid fa-store text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $agencies->total() }}</p>
                            <p class="text-sm text-gray-500 font-medium">Total Biro Wisata</p>
                        </div>
                    </div>
                </div>
            </div>

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
                                placeholder="Cari nama biro..."
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
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Logo & Nama Biro</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kontak / Web</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($agencies as $agency)
                                    <tr class="hover:bg-blue-50/30 transition-colors duration-200 group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm overflow-hidden bg-white border border-gray-200">
                                                    @if($agency->logo_path)
                                                        <img src="{{ asset($agency->logo_path) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <i class="fa-solid fa-store text-xl text-gray-300"></i>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $agency->name }}</div>
                                                    <div class="text-[10px] text-gray-500 mt-0.5 line-clamp-1">{{ Str::limit($agency->description, 50) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 item-center">
                                            <div class="flex flex-col gap-1">
                                                @if($agency->contact_wa)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold border border-green-200 bg-green-50 text-green-700 w-fit">
                                                    <i class="fa-brands fa-whatsapp text-green-500"></i>
                                                    {{ $agency->contact_wa }}
                                                </span>
                                                @endif
                                                @if($agency->website)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold border border-gray-200 bg-gray-50 text-gray-600 w-fit">
                                                    <i class="fa-solid fa-globe text-gray-400"></i>
                                                    <a href="{{ $agency->website }}" target="_blank" class="hover:text-blue-600 hover:underline">Website</a>
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.travel-agencies.edit', $agency) }}" 
                                                   class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200" 
                                                   title="Edit" wire:navigate>
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form action="{{ route('admin.travel-agencies.destroy', $agency) }}" method="POST" class="inline-block delete-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus biro ini? Semua paket wisata yang terhubung akan ikut terhapus.')">
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
                                        <td colspan="3" class="px-6 py-16 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                                    <i class="fa-solid fa-store text-3xl text-gray-300"></i>
                                                </div>
                                                <p class="text-gray-600 font-medium mb-1">Belum ada biro wisata</p>
                                                <a href="{{ route('admin.travel-agencies.create') }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 mt-2 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 transition-colors" wire:navigate>
                                                    <i class="fa-solid fa-plus"></i>
                                                    Tambah Biro
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($agencies->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                            {{ $agencies->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
