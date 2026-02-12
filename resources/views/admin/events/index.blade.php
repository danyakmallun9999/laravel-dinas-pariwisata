<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-0.5">Kelola</p>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Kalender Event
                </h2>
            </div>
            <a href="{{ route('admin.events.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 text-white font-semibold text-sm rounded-xl hover:from-violet-700 hover:to-purple-700 focus:ring-4 focus:ring-violet-500/25 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Event</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-violet-50 rounded-2xl flex items-center justify-center border border-violet-100">
                            <i class="fa-solid fa-calendar-days text-violet-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                            <p class="text-sm text-gray-500 font-medium">Total Event</p>
                            <p class="text-xs text-gray-400">Semua data tersimpan</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center border border-emerald-100">
                            <i class="fa-solid fa-check-circle text-emerald-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['published'] }}</p>
                            <p class="text-sm text-gray-500 font-medium">Dipublikasikan</p>
                            <p class="text-xs text-gray-400">Tampil di kalender publik</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="flex items-center gap-4 p-5 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center border border-amber-100">
                            <i class="fa-solid fa-clock text-amber-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming'] }}</p>
                            <p class="text-sm text-gray-500 font-medium">Akan Datang</p>
                            <p class="text-xs text-gray-400">Event mulai hari ini atau nanti</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
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
                        class="block w-full pl-11 pr-10 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 sm:text-sm transition-all shadow-sm placeholder-gray-400" 
                        placeholder="Cari event..."
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
            </div>

            <!-- Events Table -->
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="rounded-[2rem] border border-gray-100 overflow-hidden bg-white" id="table-wrapper">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu & Lokasi</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($events as $event)
                            <tr class="hover:bg-violet-50/30 transition-colors group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 h-14 w-14 rounded-2xl overflow-hidden bg-violet-50 border border-violet-100 flex items-center justify-center">
                                            <div class="text-center">
                                                <span class="text-[10px] font-bold text-violet-600 uppercase tracking-wider">{{ $event->start_date->isoFormat('MMM') }}</span>
                                                <p class="text-lg font-bold text-violet-800 -mt-0.5">{{ $event->start_date->format('d') }}</p>
                                            </div>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-bold text-gray-900 line-clamp-1">{{ $event->title }}</p>
                                                @if($event->title_en || $event->description_en)
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium text-blue-700 bg-blue-50 rounded" title="Tersedia dalam Bahasa Inggris">
                                                        <i class="fa-solid fa-language"></i> EN
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 line-clamp-1 mt-0.5 max-w-xs">{{ Str::limit(strip_tags($event->description), 60) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                            <i class="fa-regular fa-calendar-days text-violet-500 w-3.5"></i>
                                            <span class="font-medium">
                                                {{ $event->start_date->isoFormat('D MMM Y') }}
                                                @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                                    <span class="text-gray-400">→</span> {{ $event->end_date->isoFormat('D MMM Y') }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <i class="fa-solid fa-location-dot text-gray-400 w-3.5"></i>
                                            <span class="line-clamp-1">{{ $event->location }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    @if($event->is_published)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.events.edit', $event) }}" 
                                           class="p-2.5 text-gray-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition-colors"
                                           title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mb-4">
                                            <i class="fa-regular fa-calendar-xmark text-2xl text-violet-400"></i>
                                        </div>
                                        <p class="text-gray-600 font-medium mb-1">Belum ada event</p>
                                        <p class="text-sm text-gray-400 mb-4">Mulai dengan menambahkan event pertama</p>
                                        <a href="{{ route('admin.events.create') }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 transition-colors">
                                            <i class="fa-solid fa-plus"></i>
                                            Tambah Event
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($events->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $events->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
