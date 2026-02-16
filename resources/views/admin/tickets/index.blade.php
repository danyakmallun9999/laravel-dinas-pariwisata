<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="hidden md:block text-sm text-gray-500">E-Tiket</p>
                <h2 class="font-semibold text-xl md:text-2xl text-gray-800 leading-tight">
                    Kelola Tiket
                </h2>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Bar -->
                <form action="{{ route('admin.tickets.index') }}" method="GET" class="relative flex-1 sm:w-64">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari tiket / destinasi..." 
                           class="w-full pl-9 pr-4 py-2 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm placeholder-gray-400 transition-all">
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('admin.tickets.orders') }}" class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm whitespace-nowrap">
                        <i class="fa-solid fa-inbox mr-2 text-blue-500"></i>
                        <span class="hidden sm:inline">Pesanan Masuk</span>
                        <span class="sm:hidden">Pesanan</span>
                    </a>
                    <a href="{{ route('admin.tickets.create') }}" class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm font-medium text-sm whitespace-nowrap">
                        <i class="fa-solid fa-plus mr-2"></i>
                        <span class="hidden sm:inline">Tambah Tiket</span>
                        <span class="sm:hidden">Buat Baru</span>
                    </a>
                </div>
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

            <!-- Tickets Table -->
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="rounded-[2rem] border border-gray-100 overflow-hidden bg-white" id="table-wrapper">
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tiket</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Destinasi</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Kuota</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Terjual</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($tickets as $ticket)
                                <tr class="hover:bg-blue-50/30 transition-colors group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                    {{-- Nama Tiket --}}
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 h-11 w-11 rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                                                @if($ticket->place && $ticket->place->image_path)
                                                    <img src="{{ str_starts_with($ticket->place->image_path, 'http') ? $ticket->place->image_path : (str_starts_with($ticket->place->image_path, 'images/') ? asset($ticket->place->image_path) : asset('storage/' . $ticket->place->image_path)) }}" 
                                                         alt="{{ $ticket->place->name }}" 
                                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-blue-50">
                                                        <i class="fa-solid fa-image text-blue-400 text-sm"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-gray-900">{{ $ticket->name }}</p>
                                                @if($ticket->valid_days > 1)
                                                    <p class="text-[11px] text-gray-400 mt-0.5">
                                                        <i class="fa-regular fa-clock"></i> Berlaku {{ $ticket->valid_days }} hari
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Destinasi --}}
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-map-pin text-gray-400 text-xs"></i>
                                            <span class="text-sm text-gray-700 font-medium">{{ $ticket->place->name ?? '-' }}</span>
                                        </div>
                                    </td>

                                    {{-- Tipe Tiket --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        @php
                                            $typeConfig = match($ticket->type) {
                                                'adult' => ['label' => 'Dewasa', 'classes' => 'bg-blue-50 text-blue-700 border-blue-100', 'icon' => 'fa-user'],
                                                'child' => ['label' => 'Anak', 'classes' => 'bg-amber-50 text-amber-700 border-amber-100', 'icon' => 'fa-child'],
                                                'foreigner' => ['label' => 'Mancanegara', 'classes' => 'bg-purple-50 text-purple-700 border-purple-100', 'icon' => 'fa-globe'],
                                                default => ['label' => 'Umum', 'classes' => 'bg-gray-50 text-gray-700 border-gray-200', 'icon' => 'fa-users'],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $typeConfig['classes'] }}">
                                            <i class="fa-solid {{ $typeConfig['icon'] }} text-[10px]"></i>
                                            {{ $typeConfig['label'] }}
                                        </span>
                                    </td>

                                    {{-- Harga --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                        </div>
                                        @if($ticket->price_weekend)
                                            <div class="text-[11px] text-orange-600 mt-0.5 font-medium">
                                                <i class="fa-solid fa-sun text-[9px]"></i> Weekend: Rp {{ number_format($ticket->price_weekend, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Kuota --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        @if($ticket->quota)
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-sm font-bold text-gray-900">{{ number_format($ticket->quota) }}</span>
                                                <span class="text-[10px] text-gray-400 font-medium">/hari</span>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs text-green-600 font-semibold">
                                                <i class="fa-solid fa-infinity text-[10px]"></i> Unlimited
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Terjual --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-bold text-gray-900">{{ number_format($ticket->orders_count) }}</span>
                                            <span class="text-[10px] text-gray-400 font-medium">Tiket</span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-5 text-center">
                                        @if($ticket->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.tickets.edit', $ticket) }}" 
                                               class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button type="button"
                                                    onclick="confirmDelete('{{ route('admin.tickets.destroy', $ticket) }}', '{{ $ticket->name }}')"
                                                    class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-ticket text-2xl text-blue-400"></i>
                                            </div>
                                            <p class="text-gray-600 font-medium mb-1">Belum ada tiket</p>
                                            <p class="text-sm text-gray-400 mb-4">Mulai dengan menambahkan tiket pertama</p>
                                            <a href="{{ route('admin.tickets.create') }}" 
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                                <i class="fa-solid fa-plus"></i>
                                                Tambah Tiket
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards (Stacked View) -->
                    <div class="md:hidden space-y-4 p-4">
                        @forelse ($tickets as $ticket)
                            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm relative">
                                {{-- Header: Status + Tipe --}}
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        @if($ticket->is_active)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-500">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>

                                    @php
                                        $typeConfig = match($ticket->type) {
                                            'adult' => ['label' => 'Dewasa', 'classes' => 'bg-blue-50 text-blue-700', 'icon' => 'fa-user'],
                                            'child' => ['label' => 'Anak', 'classes' => 'bg-amber-50 text-amber-700', 'icon' => 'fa-child'],
                                            'foreigner' => ['label' => 'Mancanegara', 'classes' => 'bg-purple-50 text-purple-700', 'icon' => 'fa-globe'],
                                            default => ['label' => 'Umum', 'classes' => 'bg-gray-50 text-gray-700', 'icon' => 'fa-users'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $typeConfig['classes'] }}">
                                        <i class="fa-solid {{ $typeConfig['icon'] }} text-[9px]"></i>
                                        {{ $typeConfig['label'] }}
                                    </span>
                                </div>

                                {{-- Ticket Info --}}
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                                        @if($ticket->place && $ticket->place->image_path)
                                            <img src="{{ str_starts_with($ticket->place->image_path, 'http') ? $ticket->place->image_path : (str_starts_with($ticket->place->image_path, 'images/') ? asset($ticket->place->image_path) : asset('storage/' . $ticket->place->image_path)) }}" 
                                                 alt="{{ $ticket->place->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-blue-50">
                                                <i class="fa-solid fa-image text-blue-400 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-gray-900 line-clamp-1 text-sm">{{ $ticket->name }}</h3>
                                        <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-map-pin text-[10px]"></i>
                                            {{ $ticket->place->name ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Price & Details Grid --}}
                                <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-50">
                                    {{-- Harga Weekday --}}
                                    <div>
                                        <span class="text-[10px] text-gray-400 font-medium uppercase">Harga Weekday</span>
                                        <p class="text-sm font-bold text-gray-900">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>
                                    </div>

                                    {{-- Harga Weekend --}}
                                    <div>
                                        <span class="text-[10px] text-gray-400 font-medium uppercase">Harga Weekend</span>
                                        @if($ticket->price_weekend)
                                            <p class="text-sm font-bold text-orange-600">Rp {{ number_format($ticket->price_weekend, 0, ',', '.') }}</p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-0.5">Sama</p>
                                        @endif
                                    </div>

                                    {{-- Kuota --}}
                                    <div>
                                        <span class="text-[10px] text-gray-400 font-medium uppercase">Kuota Harian</span>
                                        @if($ticket->quota)
                                            <p class="text-sm font-bold text-gray-900">{{ number_format($ticket->quota) }}</p>
                                        @else
                                            <p class="text-xs text-green-600 font-semibold mt-0.5">
                                                <i class="fa-solid fa-infinity text-[9px]"></i> Unlimited
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Terjual --}}
                                    <div>
                                        <span class="text-[10px] text-gray-400 font-medium uppercase">Terjual</span>
                                        <p class="text-sm font-bold text-gray-900">{{ number_format($ticket->orders_count) }} <span class="text-gray-400 font-normal text-xs">tiket</span></p>
                                    </div>
                                </div>

                                {{-- Masa Berlaku --}}
                                @if($ticket->valid_days > 1)
                                    <div class="mt-2 text-[11px] text-gray-500">
                                        <i class="fa-regular fa-clock"></i> Masa berlaku: {{ $ticket->valid_days }} hari
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-gray-50">
                                    <a href="{{ route('admin.tickets.edit', $ticket) }}" class="px-3 py-1.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg">
                                        Edit
                                    </a>
                                    <button type="button"
                                            onclick="confirmDelete('{{ route('admin.tickets.destroy', $ticket) }}', '{{ $ticket->name }}')"
                                            class="px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 rounded-lg">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <i class="fa-solid fa-ticket text-3xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 text-sm">Tidak ada tiket</p>
                            </div>
                        @endforelse
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

    <script>
        function confirmDelete(url, name) {
            const component = document.querySelector('[x-data]');
            if (component && component.__x) {
                component.__x.$data.deleteUrl = url;
                component.__x.$data.deleteName = name;
                component.__x.$data.showDeleteModal = true;
            }
        }
    </script>
</x-app-layout>
