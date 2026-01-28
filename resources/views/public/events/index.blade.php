<x-public-layout>
    <div class="bg-background-light dark:bg-background-dark min-h-screen py-12 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-black text-text-light dark:text-text-dark tracking-tight mb-4">
                    Calendar of Events <span class="text-primary">#{{ date('Y') }}</span>
                </h1>
                <p class="text-lg text-text-light/70 dark:text-text-dark/70 max-w-2xl mx-auto">
                    Temukan dan ramaikan berbagai agenda wisata, budaya, dan festival menarik di Kabupaten Jepara sepanjang tahun ini.
                </p>
            </div>

            <!-- Events Grid -->
            @if($groupedEvents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 masonry-grid">
                    @foreach($groupedEvents as $monthYear => $events)
                        <div class="bg-white dark:bg-surface-dark rounded-2xl shadow-xl overflow-hidden border border-surface-light dark:border-white/5 break-inside-avoid mb-6 relative group hover:-translate-y-1 transition-transform duration-300">
                            <!-- Decorative Month Header -->
                            <div class="bg-gradient-to-r from-primary to-blue-600 p-4 text-center relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-4 opacity-10">
                                    <i class="fa-regular fa-calendar text-6xl text-white transform rotate-12"></i>
                                </div>
                                <h2 class="text-2xl font-black text-white uppercase tracking-wider relative z-10">{{ $monthYear }}</h2>
                            </div>

                            <!-- Events List -->
                            <div class="p-5 space-y-6">
                                @foreach($events as $event)
                                    <div class="relative pl-4 border-l-2 border-primary/30 hover:border-primary transition-colors">
                                        
                                        <!-- Event Image (if any) -->
                                        @if($event->image)
                                            <div class="mb-3 rounded-lg overflow-hidden h-32 w-full">
                                                <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
                                            </div>
                                        @endif

                                        <h3 class="text-lg font-bold text-text-light dark:text-text-dark leading-tight mb-1">
                                            {{ $event->title }}
                                        </h3>
                                        
                                        <div class="flex items-center gap-2 text-sm text-text-light/70 dark:text-text-dark/70 mb-2">
                                            <i class="fa-solid fa-location-dot text-primary text-xs"></i>
                                            <span class="truncate">{{ $event->location }}</span>
                                        </div>

                                        @if($event->description)
                                            <div class="text-xs text-text-light/60 dark:text-text-dark/60 line-clamp-2 mb-2 prose prose-sm dark:prose-invert">
                                                {!! Str::limit(strip_tags($event->description), 80) !!}
                                            </div>
                                        @endif

                                        <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-white/5 rounded-full text-xs font-semibold text-text-light dark:text-text-dark">
                                            <i class="fa-regular fa-clock text-primary"></i>
                                            @if($event->end_date)
                                                {{ $event->start_date->format('d') }} - {{ $event->end_date->format('d M') }}
                                            @else
                                                {{ $event->start_date->format('d F Y') }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-white/5 mb-6">
                        <i class="fa-regular fa-calendar-xmark text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-text-light dark:text-text-dark mb-2">Belum Ada Event</h3>
                    <p class="text-text-light/60 dark:text-text-dark/60">Saat ini belum ada jadwal event yang dipublikasikan.</p>
                </div>
            @endif

        </div>
    </div>

    <style>
        /* Simple CSS Masonry/Column layout for masonry effect */
        @media (min-width: 768px) {
            .masonry-grid {
                column-count: 2;
            }
        }
        @media (min-width: 1024px) {
            .masonry-grid {
                column-count: 3;
            }
        }
    </style>
</x-public-layout>
