<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Manajemen Admin
                </h2>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 md:px-5 md:py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-semibold text-xs md:text-sm text-white hover:shadow-blue-500/40 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden md:inline">Tambah Admin</span>
                <span class="md:hidden">Tambah</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Information Alert -->
            <div class="mb-6 bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
                <div class="text-sm text-blue-700">
                    <p class="font-bold mb-1">Panduan Hak Akses:</p>
                    <ul class="list-disc ml-4 space-y-1 text-blue-600/80">
                        <li><strong>Super Admin:</strong> Akses penuh ke seluruh sistem.</li>
                        <li><strong>Admin Wisata:</strong> Mengelola semua destinasi, tiket, dan kategori.</li>
                        <li><strong>Admin Berita:</strong> Mengelola berita dan event.</li>
                        <li><strong>Pengelola Wisata:</strong> Hanya mengelola destinasi yang ditugaskan kepadanya.</li>
                    </ul>
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
                        placeholder="Cari admin berdasarkan nama atau email..."
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
                    
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role / Peran</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Destinasi Kelolaan</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-blue-50/30 transition-colors group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors line-clamp-1">
                                                        {{ $user->name }}
                                                    </a>
                                                    <div class="text-xs text-gray-500 mt-0.5">
                                                        {{ $user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @foreach($user->roles as $role)
                                                @php
                                                    $color = match($role->name) {
                                                        'super_admin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                                        'admin_wisata' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                        'admin_berita' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                        'pengelola_wisata' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                                    };
                                                    $label = ucwords(str_replace('_', ' ', $role->name));
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                                    {{ $label }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->ownedPlaces->count() > 0)
                                                <div class="flex flex-col gap-1">
                                                    @foreach($user->ownedPlaces as $place)
                                                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                                                            <i class="fa-solid fa-map-pin text-red-500"></i>
                                                            {{ $place->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs italic">- Tidak ada -</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                   class="p-2.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" 
                                                   title="Edit">
                                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block delete-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" 
                                                            title="Hapus">
                                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-16 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                                    <i class="fa-solid fa-users-slash text-3xl text-gray-300"></i>
                                                </div>
                                                <p class="text-gray-600 font-medium mb-1">Tidak ada admin ditemukan</p>
                                                <a href="{{ route('admin.users.create') }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors mt-4">
                                                    <i class="fa-solid fa-plus text-xs"></i>
                                                    Tambah Admin
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4 p-4">
                         @forelse ($users as $user)
                            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm relative">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                         {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $user->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-500">Role:</span>
                                        @foreach($user->roles as $role)
                                            <span class="font-medium text-gray-700 bg-gray-100 px-2 py-0.5 rounded">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                                        @endforeach
                                    </div>
                                     <div class="flex items-start justify-between text-xs">
                                        <span class="text-gray-500 mt-1">Kelolaan:</span>
                                         @if($user->ownedPlaces->count() > 0)
                                            <div class="text-right">
                                                @foreach($user->ownedPlaces as $place)
                                                    <div class="text-gray-700 font-medium">{{ $place->name }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-50">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg">
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-2 text-xs font-medium text-red-600 bg-red-50 rounded-lg">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                             <div class="text-center py-10">
                                <p class="text-gray-500 text-sm">Tidak ada admin.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($users->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
