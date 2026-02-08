<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">E-Tiket</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Kelola Tiket
                </h2>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.tickets.orders') }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
                    <i class="fa-solid fa-inbox mr-2 text-blue-500"></i>Pesanan Masuk
                </a>
                <a href="{{ route('admin.tickets.create') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm font-medium text-sm">
                    <i class="fa-solid fa-plus mr-2"></i>Tambah Tiket
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        showDeleteModal: false, 
        deleteUrl: '', 
        deleteName: '',
        openDeleteModal(url, name) {
            this.deleteUrl = url;
            this.deleteName = name;
            this.showDeleteModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <i class="fa-solid fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tickets Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Destinasi</th>
                                <th class="px-6 py-4">Tiket</th>
                                <th class="px-6 py-4">Harga</th>
                                <th class="px-6 py-4">Kuota</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $imgPath = $ticket->place->image_path;
                                                $imgUrl = str_starts_with($imgPath, 'http') ? $imgPath : (str_starts_with($imgPath, 'images/') ? asset($imgPath) : asset('storage/' . $imgPath));
                                            @endphp
                                            <img src="{{ $imgUrl }}" alt="{{ $ticket->place->name }}" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                                            <span class="font-medium text-gray-800">{{ $ticket->place->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $ticket->name }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-gray-100 text-gray-600 uppercase tracking-wide">
                                                {{ $ticket->type }}
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $ticket->valid_days }} hari</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">Rp {{ number_format($ticket->price, 0, ',', '.') }}</div>
                                        @if($ticket->price_weekend)
                                            <div class="text-xs text-purple-600 font-semibold mt-0.5">
                                                <i class="fa-solid fa-calendar-week mr-1"></i>Rp {{ number_format($ticket->price_weekend, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $ticket->quota ? number_format($ticket->quota) . '/hari' : '∞' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($ticket->is_active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('admin.tickets.edit', $ticket) }}" class="p-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button type="button" 
                                                    @click="openDeleteModal('{{ route('admin.tickets.destroy', $ticket) }}', '{{ $ticket->name }}')"
                                                    class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-ticket text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 mb-4">Belum ada tiket yang ditambahkan.</p>
                                            <a href="{{ route('admin.tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                                <i class="fa-solid fa-plus mr-2"></i>Tambah Tiket Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($tickets->hasPages())
                <div class="mt-4">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 transition-opacity" @click="showDeleteModal = false"></div>
                
                <div x-show="showDeleteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                    
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 text-2xl"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Konfirmasi Hapus</h3>
                    <p class="text-gray-600 text-center mb-6">
                        Apakah Anda yakin ingin menghapus tiket <strong x-text="deleteName"></strong>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                    
                    <div class="flex gap-3">
                        <button @click="showDeleteModal = false" 
                                class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-xl transition-colors">
                            Batal
                        </button>
                        <form :action="deleteUrl" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-colors">
                                <i class="fa-solid fa-trash-can mr-2"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
