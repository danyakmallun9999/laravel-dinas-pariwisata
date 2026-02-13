<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500 mb-0.5">Event</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Kalender Tahunan {{ $year }}
                </h2>
            </div>
            <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-2xl border border-gray-200 shadow-sm">
                <a href="{{ route('admin.events.calendar', ['year' => $year - 1]) }}" 
                   class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all border border-gray-100 shadow-sm"
                   title="Tahun Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                <a href="{{ route('admin.events.calendar', ['year' => now()->year]) }}" 
                   class="px-4 py-2 rounded-xl bg-white text-xs font-bold text-gray-700 hover:text-blue-600 transition-all border border-gray-100 shadow-sm">
                    Tahun Ini
                </a>
                <a href="{{ route('admin.events.calendar', ['year' => $year + 1]) }}" 
                   class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all border border-gray-100 shadow-sm"
                   title="Tahun Berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @for ($m = 1; $m <= 12; $m++)
                    @php
                        $date = \Carbon\Carbon::create($year, $m, 1);
                        $daysInMonth = $date->daysInMonth;
                        $firstDayOfWeek = $date->dayOfWeek; // 0 (Sun) to 6 (Sat)
                        $monthEvents = $eventsByMonth->get($m, collect());
                        $eventDays = $monthEvents->pluck('start_date')->map(fn($d) => $d->day)->unique()->toArray();
                    @endphp
                    
                    <div class="bg-white rounded-[2rem] border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="bg-gray-50/50 px-5 py-3 border-b border-gray-100">
                            <h3 class="font-bold text-gray-800">{{ $date->isoFormat('MMMM') }}</h3>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid grid-cols-7 gap-1 text-center mb-2">
                                @foreach(['M', 'S', 'S', 'R', 'K', 'J', 'S'] as $dayName)
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $dayName }}</span>
                                @endforeach
                            </div>
                            
                            <div class="grid grid-cols-7 gap-1 text-center">
                                @for ($i = 0; $i < $firstDayOfWeek; $i++)
                                    <div class="aspect-square"></div>
                                @endfor
                                
                                @for ($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $hasEvent = in_array($day, $eventDays);
                                        $isToday = $year == now()->year && $m == now()->month && $day == now()->day;
                                    @endphp
                                    <div class="aspect-square flex items-center justify-center relative">
                                        @if($hasEvent)
                                            <a href="#month-{{ $m }}" 
                                               class="w-full h-full rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center group cursor-pointer hover:bg-blue-100 transition-colors"
                                               title="{{ $monthEvents->filter(fn($e) => $e->start_date->day == $day)->pluck('title')->implode(', ') }}">
                                                <span class="text-xs font-bold text-blue-700 relative z-10">{{ $day }}</span>
                                                <div class="absolute bottom-1 w-1 h-1 rounded-full bg-blue-500"></div>
                                            </a>
                                        @else
                                            <span class="text-xs {{ $isToday ? 'font-black text-blue-600 bg-blue-50 rounded-lg w-full h-full flex items-center justify-center border border-blue-200' : 'text-gray-500' }}">
                                                {{ $day }}
                                            </span>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Detailed Monthly List with Accordion -->
            <div class="mt-12 space-y-4" x-data="{ activeMonth: null }">
                <h2 class="text-2xl font-bold text-gray-900 px-4 sm:px-0 mb-6">Daftar Agenda {{ $year }}</h2>
                
                @foreach($eventsByMonth as $monthNum => $events)
                    <div id="month-{{ $monthNum }}" 
                         x-data="{ 
                            open: false,
                            init() {
                                // Open if hash matches on load or change
                                const checkHash = () => {
                                    if (window.location.hash === '#month-{{ $monthNum }}') {
                                        this.open = true;
                                    }
                                };
                                checkHash();
                                window.addEventListener('hashchange', checkHash);
                            }
                         }"
                         class="bg-white rounded-[2rem] border border-gray-200 overflow-hidden shadow-sm transition-all duration-300 scroll-mt-24"
                         :class="open ? 'ring-2 ring-blue-500/20 border-blue-200' : ''">
                        
                        <!-- Accordion Header -->
                        <button @click="open = !open" 
                                class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition-colors text-left focus:outline-none">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800">
                                        {{ \Carbon\Carbon::create($year, $monthNum)->isoFormat('MMMM') }}
                                    </h3>
                                    <p class="text-xs text-gray-500 font-medium">{{ $events->count() }} Agenda Terjadwal</p>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 transition-transform duration-300"
                                 :class="open ? 'rotate-180 bg-blue-50 text-blue-600' : ''">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </button>

                        <!-- Accordion Content -->
                        <div x-show="open" x-collapse>
                            <div class="divide-y divide-gray-100 border-t border-gray-100 bg-gray-50/30">
                                @foreach($events as $event)
                                    <div class="px-8 py-6 hover:bg-white transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 rounded-2xl bg-white border border-gray-100 shadow-sm flex flex-col items-center justify-center flex-shrink-0">
                                                <span class="text-[10px] font-bold text-blue-600 uppercase">{{ $event->start_date->isoFormat('MMM') }}</span>
                                                <span class="text-lg font-black text-blue-700 -mt-1">{{ $event->start_date->format('d') }}</span>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900">{{ $event->title }}</h4>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-xs text-gray-500 flex items-center gap-1.5">
                                                        <i class="fa-regular fa-clock text-blue-400"></i>
                                                        {{ $event->start_date->format('H:i') }} WIB
                                                    </span>
                                                    <span class="text-xs text-gray-500 flex items-center gap-1.5">
                                                        <i class="fa-solid fa-location-dot text-gray-400"></i>
                                                        {{ $event->location }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.events.edit', $event) }}" class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-all shadow-sm">
                                                Kelola
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($eventsByMonth->isEmpty())
                    <div class="bg-white rounded-[2.5rem] border border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <i class="fa-regular fa-calendar-xmark text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak ada agenda di tahun {{ $year }}</h3>
                        <p class="text-gray-500 text-sm max-w-sm mx-auto">Anda belum menambahkan event apapun untuk tahun ini.</p>
                        <a href="{{ route('admin.events.create') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                            <i class="fa-solid fa-plus"></i>
                            Tambah Event Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
