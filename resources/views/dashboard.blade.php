<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg md:text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Header Section --}}
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-4">
                        <h3 class="text-base md:text-lg font-bold text-gray-900">Daftar Lokasi</h3>
                        <a href="{{ route('places.create') }}" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center transition duration-150 ease-in-out">
                            + Tambah Lokasi
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 md:mb-6" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Mobile View (Cards) --}}
                    <div class="grid grid-cols-1 gap-4 md:hidden">
                        @foreach($places as $place)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-3 md:p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm md:text-base font-bold text-gray-900">{{ $place->name }}</h4>
                                    <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $place->category->color }}">
                                        {{ $place->category->name }}
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('places.edit', $place->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('places.destroy', $place->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mb-2">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $place->latitude }}, {{ $place->longitude }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Desktop View (Table) --}}
                    <div class="hidden md:block overflow-x-auto rounded-lg shadow ring-1 ring-black ring-opacity-5">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nama</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kategori</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Koordinat</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 hover:bg-gray-50 text-right text-sm font-semibold text-gray-900">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($places as $place)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $place->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white shadow-sm" style="background-color: {{ $place->category->color }}">
                                            {{ $place->category->name }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-mono">
                                        {{ $place->latitude }}, {{ $place->longitude }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('places.edit', $place->id) }}" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                                        <form action="{{ route('places.destroy', $place->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
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
