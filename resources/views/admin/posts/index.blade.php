<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Berita & Agenda') }}
            </h2>
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                Buat Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter Bar -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <form action="{{ route('admin.posts.index') }}" method="GET" class="w-full sm:w-auto flex-1 max-w-lg flex gap-2">
                    <x-text-input name="search" placeholder="Cari judul..." value="{{ request('search') }}" class="w-full" />
                    <select name="type" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Tipe</option>
                        <option value="news" {{ request('type') == 'news' ? 'selected' : '' }}>Berita</option>
                        <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Agenda</option>
                    </select>
                    <x-primary-button>Cari</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Konten</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($posts as $post)
                                    <tr class="hover:bg-gray-50 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-shrink-0 h-16 w-24 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                                    @if($post->image_path)
                                                        <img src="{{ $post->image_path }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" alt="">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                            <i class="fa-regular fa-image text-xl"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-bold text-gray-900 line-clamp-1 mb-0.5">{{ $post->title }}</div>
                                                    <div class="text-xs text-gray-500 line-clamp-1 max-w-md">{{ Str::limit(strip_tags($post->content), 80) }}</div>
                                                    @if($post->author)
                                                        <div class="text-[10px] text-gray-400 mt-1"><i class="fa-solid fa-user-pen mr-1"></i> {{ $post->author }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($post->type == 'event')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                    Agenda
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Berita
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">{{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}</div>
                                            <div class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($post->is_published)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fa-solid fa-check-circle mr-1"></i> Tayang
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fa-solid fa-file-lines mr-1"></i> Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                                                </a>
                                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline-block delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                        <i class="fa-solid fa-trash-can text-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fa-regular fa-folder-open text-2xl text-gray-400"></i>
                                                </div>
                                                <p class="text-gray-500 text-sm mb-1">Tidak ada data ditemukan</p>
                                                <p class="text-xs text-gray-400">Coba ubah filter atau buat postingan baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
