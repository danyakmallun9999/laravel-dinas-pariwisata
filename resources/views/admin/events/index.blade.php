<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Calendar of Events') }}
            </h2>
            <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Event
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter Bar -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <form action="{{ route('admin.events.index') }}" method="GET" class="w-full sm:w-auto flex-1 max-w-lg" x-data="{ query: '{{ request('search') }}' }">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                             <i class="fa-solid fa-search text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            x-model="query"
                            @input.debounce.500ms="$el.form.submit()"
                            class="block w-full pl-11 pr-10 py-2.5 border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 sm:text-sm transition-all shadow-sm hover:bg-white placeholder-gray-400" 
                            placeholder="Cari event..."
                        >
                        <button 
                            type="button" 
                            x-show="query.length > 0" 
                            @click="query = ''; $nextTick(() => { $el.closest('form').submit() })"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 cursor-pointer transition-colors"
                            style="display: none;"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90"
                        >
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Event Details</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu & Tempat</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($events as $event)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0 h-16 w-16 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 text-center">
                                                @if($event->image)
                                                    <img class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ Storage::url($event->image) }}" alt="">
                                                @else
                                                    <div class="h-full w-full flex flex-col items-center justify-center text-gray-500 bg-gray-50">
                                                        <span class="text-xs font-bold text-red-500 uppercase tracking-wide">{{ $event->start_date->isoFormat('MMM') }}</span>
                                                        <span class="text-xl font-bold text-gray-800">{{ $event->start_date->format('d') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-bold text-gray-900 line-clamp-1 mb-0.5">{{ $event->title }}</div>
                                                <div class="text-xs text-gray-500 line-clamp-2 max-w-xs">{{ Str::limit(strip_tags($event->description), 80) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                                 <i class="fa-regular fa-calendar-days text-indigo-500 w-3"></i>
                                                 <span class="font-medium">
                                                    {{ $event->start_date->isoFormat('D MMMM Y') }}
                                                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                                        - {{ $event->end_date->isoFormat('D MMMM Y') }}
                                                    @endif
                                                 </span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                                <i class="fa-solid fa-location-dot text-gray-400 w-3"></i>
                                                {{ $event->location }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($event->is_published)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <i class="fa-solid fa-check-circle mr-1.5"></i> Published
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                <i class="fa-solid fa-file-dashed mr-1.5"></i> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.events.edit', $event) }}" class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-lg"></i>
                                            </a>
                                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block delete-form">
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
                                    <td colspan="4" class="px-6 py-12 whitespace-nowrap text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                <i class="fa-regular fa-calendar-xmark text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-gray-500 text-sm mb-1">Belum ada event yang dibuat</p>
                                            <p class="text-xs text-gray-400">Silakan tambahkan event baru</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
