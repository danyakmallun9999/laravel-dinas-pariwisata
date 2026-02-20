<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 font-medium mb-1">Admin Panel / Culture</p>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight">
                    Manajemen Budaya
                </h2>
            </div>
            <a href="{{ route('admin.cultures.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl font-semibold text-sm text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Budaya</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Search & Filter -->
            <form method="GET" action="{{ route('admin.cultures.index') }}" class="flex flex-col sm:flex-row gap-4 max-w-2xl w-full">
                <!-- Category Filter -->
                <div class="relative min-w-[200px]">
                    <select name="category" onchange="this.form.submit()" class="block w-full pl-4 pr-10 py-3.5 border-gray-200 rounded-2xl bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm hover:border-blue-300 transition-all duration-300 appearance-none cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="relative w-full group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                         <i class="fa-solid fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        class="block w-full pl-11 pr-11 py-3.5 border-gray-200 rounded-2xl bg-white text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm hover:border-blue-300 transition-all duration-300 placeholder-gray-400" 
                        placeholder="Cari budaya..."
                    >
                </div>
            </form>

            <!-- Main Content Card -->
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-100/50 overflow-hidden">
                
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Category</th>
                                <th class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($cultures as $culture)
                                <tr class="group hover:bg-blue-50/30 transition-all duration-200">
                                    <td class="px-8 py-5">
                                        <div class="h-16 w-24 rounded-lg overflow-hidden bg-gray-100">
                                            @if($culture->image)
                                                <img src="{{ $culture->image_url }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <i class="fa-solid fa-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-bold text-gray-900">{{ $culture->name }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $culture->category }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <a href="{{ route('admin.cultures.edit', $culture) }}" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all" 
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('admin.cultures.destroy', $culture) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus budaya ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all" 
                                                        title="Hapus">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-24 text-center">
                                        <p class="text-gray-500 text-sm">Tidak ada budaya ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($cultures->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $cultures->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
