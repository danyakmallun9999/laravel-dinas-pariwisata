@pushOnce('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endPushOnce

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Admin Panel</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Daftar Lokasi
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.places.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Lokasi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Places Table -->
            <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Lokasi</h3>
                    <p class="text-sm text-gray-500">Total: {{ $places->total() }} lokasi</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Koordinat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($places as $place)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center space-x-3">
                                            @if($place->image_path)
                                                <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                                    <i class="fa-solid fa-map-location-dot"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $place->name }}</p>
                                                <p class="text-xs text-gray-500 line-clamp-1">{{ $place->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold text-white" style="background-color: {{ $place->category->color }}">
                                            <i class="{{ $place->category->icon_class ?? 'fa-solid fa-map-marker-alt' }}"></i>
                                            {{ $place->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        {{ $place->latitude }}, {{ $place->longitude }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.places.edit', $place) }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-800">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.places.destroy', $place) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-800">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada data lokasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $places->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
