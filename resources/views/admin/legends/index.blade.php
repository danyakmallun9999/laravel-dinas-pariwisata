<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Tokoh Sejarah & Legenda
                </h2>
            </div>
            <a href="{{ route('admin.legends.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 md:px-5 md:py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-semibold text-xs md:text-sm text-white hover:shadow-blue-500/40 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:-translate-y-0.5" wire:navigate>
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden md:inline">Tambah Tokoh</span>
                <span class="md:hidden">Tambah</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Table --}}
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="rounded-[2rem] border border-gray-100 overflow-hidden bg-white">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50/50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tokoh</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Urutan</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($legends as $legend)
                                    <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($legend->image)
                                                    <img src="{{ $legend->image_url }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100" alt="">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                                                        <i class="fa-solid fa-user text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900">{{ $legend->name }}</p>
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($legend->quote_id, 60) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-medium text-gray-600">{{ $legend->order }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <form action="{{ route('admin.legends.toggle-active', $legend) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all duration-200
                                                        {{ $legend->is_active ? 'bg-green-100 text-green-700 border border-green-200 hover:bg-green-200' : 'bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200' }}">
                                                    {{ $legend->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.legends.edit', $legend) }}"
                                                   class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200"
                                                   title="Edit" wire:navigate>
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form action="{{ route('admin.legends.destroy', $legend) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus tokoh ini?')">
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
                                        <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                            Belum ada data tokoh sejarah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
