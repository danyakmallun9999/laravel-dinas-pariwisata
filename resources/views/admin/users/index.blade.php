<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Admin Panel / Users</p>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight">
                    Manajemen Admin
                </h2>
            </div>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl font-semibold text-sm text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Admin</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        search: '{{ request('search') }}',
        isLoading: false,
        updateList() {
            this.isLoading = true;
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            
            const url = `${window.location.pathname}?${params.toString()}`;
            history.pushState(null, '', url);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('user-list-container').innerHTML;
                    document.getElementById('user-list-container').innerHTML = newContent;
                    this.isLoading = false;
                })
                .catch(() => this.isLoading = false);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Information Alert -->
            <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4 backdrop-blur-sm relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fa-solid fa-users-gear text-6xl text-blue-600"></i>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="fa-solid fa-circle-info text-lg"></i>
                </div>
                <div class="text-sm text-blue-800 z-10">
                    <h3 class="font-bold text-blue-900 text-base mb-1">Panduan Hak Akses</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-blue-400 text-xs"></i>
                            <span><strong class="font-semibold">Super Admin:</strong> Akses penuh sistem.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-blue-400 text-xs"></i>
                            <span><strong class="font-semibold">Admin Wisata:</strong> Kelola destinasi & tiket.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-blue-400 text-xs"></i>
                            <span><strong class="font-semibold">Admin Berita:</strong> Kelola berita & event.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-blue-400 text-xs"></i>
                            <span><strong class="font-semibold">Pengelola Wisata:</strong> Kelola destinasi pribadi.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative max-w-lg w-full group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                     <i class="fa-solid fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                </div>
                <input 
                    type="text" 
                    x-model="search"
                    @input.debounce.500ms="updateList()"
                    class="block w-full pl-11 pr-11 py-3.5 border-gray-200 rounded-2xl bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm hover:border-blue-300 transition-all duration-300 placeholder-gray-400" 
                    placeholder="Cari admin berdasarkan nama atau email..."
                >
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <i class="fa-solid fa-spinner fa-spin text-blue-500" x-show="isLoading" style="display: none;"></i>
                    <button 
                        type="button" 
                        x-show="!isLoading && search.length > 0" 
                        @click="search = ''; updateList()"
                        class="text-gray-400 hover:text-red-500 cursor-pointer transition-colors"
                        style="display: none;"
                        x-transition
                    >
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 overflow-hidden" id="user-list-container">
                
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">User Profile</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Role & Permissions</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Destinasi</th>
                                <th class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($users as $user)
                                <tr class="group hover:bg-blue-50/30 transition-all duration-200">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center font-bold text-lg shadow-sm border border-blue-50 group-hover:scale-110 transition-transform duration-300">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="text-sm font-bold text-gray-900 hover:text-blue-600 transition-colors line-clamp-1">
                                                    {{ $user->name }}
                                                </a>
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <i class="fa-regular fa-envelope text-gray-400 text-xs"></i>
                                                    <span class="text-xs text-gray-500">{{ $user->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($user->roles as $role)
                                                @php
                                                    $style = match($role->name) {
                                                        'super_admin' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-100', 'icon' => 'fa-crown'],
                                                        'admin_wisata' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'icon' => 'fa-map'],
                                                        'admin_berita' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'fa-newspaper'],
                                                        'pengelola_wisata' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => 'fa-ticket'],
                                                        default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-100', 'icon' => 'fa-user'],
                                                    };
                                                    $label = ucwords(str_replace('_', ' ', $role->name));
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">
                                                    <i class="fa-solid {{ $style['icon'] }} opacity-70"></i>
                                                    {{ $label }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($user->ownedPlaces->count() > 0)
                                            <div class="flex flex-col gap-1.5">
                                                @foreach($user->ownedPlaces as $place)
                                                    <div class="flex items-center gap-2 text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded-md w-fit border border-gray-100">
                                                        <i class="fa-solid fa-location-dot text-red-500"></i>
                                                        <span class="font-medium truncate max-w-[150px]">{{ $place->name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs italic flex items-center gap-1.5">
                                                <i class="fa-regular fa-circle-xmark"></i> Tidak ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all" 
                                               title="Edit User">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            @if($user->id !== auth('admin')->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all" 
                                                            title="Hapus User">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-user-slash text-4xl text-gray-300"></i>
                                            </div>
                                            <h3 class="text-gray-900 font-bold text-lg mb-1">Tidak ada admin ditemukan</h3>
                                            <p class="text-gray-500 text-sm max-w-sm mx-auto">
                                                Coba ubah kata kunci pencarian Anda atau tambahkan admin baru.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden">
                    <div class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <div class="p-5 space-y-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-sm">{{ $user->name }}</h3>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-gray-400 hover:text-blue-600">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        @if($user->id !== auth('admin')->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                        </span>
                                    @endforeach
                                </div>

                                @if($user->ownedPlaces->count() > 0)
                                    <div class="pt-3 border-t border-gray-50">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Destinasi</p>
                                        <div class="space-y-1">
                                            @foreach($user->ownedPlaces as $place)
                                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                                    <i class="fa-solid fa-location-dot text-red-400 text-[10px]"></i>
                                                    {{ $place->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <p class="text-gray-500 text-sm">Tidak ada data.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
