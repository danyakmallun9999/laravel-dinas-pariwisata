<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Berita & Agenda
                </h2>
            </div>
            <a href="{{ route('admin.posts.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-semibold text-sm text-white hover:shadow-blue-500/40 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus text-xs"></i>
                Buat Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center border border-blue-100">
                            <i class="fa-solid fa-newspaper text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $posts->total() }}</p>
                            <p class="text-sm text-gray-500 font-medium">Total Postingan</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center border border-emerald-100">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $posts->where('is_published', true)->count() }}</p>
                            <p class="text-sm text-gray-500 font-medium">Dipublikasikan</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center border border-amber-100">
                            <i class="fa-solid fa-file-pen text-amber-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $posts->where('is_published', false)->count() }}</p>
                            <p class="text-sm text-gray-500 font-medium">Draft</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="mb-6" 
                x-data="{ 
                    query: '{{ request('search') }}',
                    type: '{{ request('type') }}',
                    updateList() {
                        const params = new URLSearchParams();
                        if (this.query) params.set('search', this.query);
                        if (this.type) params.set('type', this.type);
                        
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
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative group flex-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                                <input 
                                    type="text" 
                                    x-model="query"
                                    @input.debounce.500ms="updateList()"
                                    class="block w-full pl-11 pr-10 py-3 border-0 bg-white rounded-xl text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:bg-white sm:text-sm transition-all placeholder-gray-400 shadow-sm" 
                                    placeholder="Cari judul postingan..."
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
                            <select 
                                x-model="type"
                                class="border-0 bg-white rounded-xl text-gray-700 focus:ring-2 focus:ring-blue-500/20 focus:bg-white sm:text-sm transition-all py-3 pl-4 pr-10 w-full sm:w-48 shadow-sm"
                                @change="updateList()"
                            >
                                <option value="">Semua Tipe</option>
                                <option value="news">🗞️ Berita</option>
                                <option value="event">📅 Agenda</option>
                            </select>
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
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Konten</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($posts as $post)
                                    <tr class="hover:bg-blue-50/30 transition-colors duration-200 group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-shrink-0 h-16 w-24 rounded-2xl overflow-hidden bg-gray-50 border border-gray-200/50">
                                                    @if($post->image_path)
                                                        <img src="{{ $post->image_path }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500" alt="">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-300">
                                                            <i class="fa-regular fa-image text-2xl"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <div class="text-sm font-bold text-gray-900 line-clamp-1 group-hover:text-blue-600 transition-colors">{{ $post->title }}</div>
                                                        @if($post->title_en || $post->content_en)
                                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 bg-blue-50 rounded" title="Tersedia dalam Bahasa Inggris">
                                                                <i class="fa-solid fa-language"></i> EN
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500 line-clamp-1 max-w-md">{{ Str::limit(strip_tags($post->content), 80) }}</div>
                                                    @if($post->author)
                                                        <div class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1">
                                                            <i class="fa-solid fa-user-pen"></i>
                                                            <span>{{ $post->author }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($post->type == 'event')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-purple-50 text-purple-700 border border-purple-100">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    Agenda
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-50 text-blue-700 border border-blue-100">
                                                    <i class="fa-solid fa-newspaper"></i>
                                                    Berita
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $post->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($post->is_published)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                    Tayang
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600">
                                                    <i class="fa-solid fa-file-lines text-[10px]"></i>
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.posts.edit', $post) }}" 
                                                   class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200" 
                                                   title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline-block delete-form">
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
                                        <td colspan="5" class="px-6 py-16 whitespace-nowrap text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                                    <i class="fa-regular fa-newspaper text-3xl text-gray-300"></i>
                                                </div>
                                                <p class="text-gray-600 font-medium mb-1">Belum ada postingan</p>
                                                <p class="text-sm text-gray-400 mb-4">Mulai dengan membuat berita atau agenda baru</p>
                                                <a href="{{ route('admin.posts.create') }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                    <i class="fa-solid fa-plus"></i>
                                                    Buat Postingan
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($posts->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
