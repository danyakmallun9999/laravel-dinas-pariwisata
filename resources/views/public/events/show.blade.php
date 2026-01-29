<x-public-layout>
    @section('meta_title', $event->title)
    @section('meta_description', Str::limit(strip_tags($event->description), 150))
    @if($event->image)
        @section('meta_image', Storage::url($event->image))
    @endif

    <!-- Hero Section -->
    <div class="relative h-[60vh] min-h-[400px] flex items-end">
        <!-- Background Image with Parallax-like feel -->
        <div class="absolute inset-0 z-0">
            @if($event->image)
                <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-slate-300">event</span>
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
        </div>

        <!-- Content -->
        <div class="container mx-auto px-4 relative z-10 pb-12 lg:pb-20">
            <div class="max-w-4xl">
                <!-- Badges -->
                <div class="flex flex-wrap gap-3 mb-6">
                    <span class="px-3 py-1 bg-primary/90 backdrop-blur text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-lg shadow-primary/20">
                        Event
                    </span>
                    @if($event->start_date->isFuture())
                         <span class="px-3 py-1 bg-emerald-500/90 backdrop-blur text-white text-xs font-bold uppercase tracking-wider rounded-lg">
                            Upcoming
                        </span>
                    @else
                        <span class="px-3 py-1 bg-slate-500/90 backdrop-blur text-white text-xs font-bold uppercase tracking-wider rounded-lg">
                            Past Event
                        </span>
                    @endif
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-black text-white leading-tight mb-6 drop-shadow-sm">
                    {{ $event->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-6 text-white/90 text-sm md:text-base font-medium">
                    <div class="flex items-center gap-2.5 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/10">
                        <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
                         <span>{{ $event->start_date->translatedFormat('d F Y') }}</span>
                         @if($event->end_date && $event->end_date != $event->start_date)
                            <span> - {{ $event->end_date->translatedFormat('d F Y') }}</span>
                         @endif
                    </div>
                    @if($event->location)
                    <div class="flex items-center gap-2.5 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/10">
                        <span class="material-symbols-outlined text-red-400 text-xl">location_on</span>
                        <span>{{ $event->location }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="bg-slate-50 dark:bg-slate-900 min-h-screen relative z-20 -mt-8 rounded-t-3xl border-t border-white/10 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <!-- Left Column (Content) -->
                <div class="lg:col-span-8 space-y-12">
                    
                    <!-- Description -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                        <h2 class="text-2xl font-display font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-2 h-8 bg-primary rounded-full"></span>
                            Tentang Event
                        </h2>
                        <div class="prose prose-lg prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                             {!! nl2br(e($event->description)) !!}
                             {{-- Note: If description stores HTML, remove e(). If plain text, keep e() --}}
                        </div>
                    </div>

                    <!-- Map (If coordinates exist) -->
                    <!-- Placeholder: Event model might not have lat/long yet, using generic location logic -->
                    @if($event->location)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                        <h2 class="text-2xl font-display font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-2 h-8 bg-emerald-500 rounded-full"></span>
                            Lokasi
                        </h2>
                        <div class="aspect-video w-full rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 relative">
                             <!-- Simple Map Embed or Link -->
                             <iframe 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0" 
                                src="https://maps.google.com/maps?q={{ urlencode($event->location . ' Jepara') }}&t=&z=15&ie=UTF8&iwloc=&output=embed">
                            </iframe>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <a href="https://maps.google.com/maps?q={{ urlencode($event->location . ' Jepara') }}" target="_blank" class="inline-flex items-center gap-2 text-primary font-bold hover:text-primary-dark transition-colors">
                                Halaman Google Maps
                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                            </a>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Right Column (Sidebar) -->
                <div class="lg:col-span-4 space-y-8 sticky-sidebar h-fit" style="top: 100px;">
                    
                    <!-- Date Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">event</span>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-white">Jadwal Pelaksanaan</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Catat tanggalnya!</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50">
                                <div class="text-center w-12 shrink-0">
                                    <span class="block text-xs uppercase font-bold text-slate-400">{{ $event->start_date->translatedFormat('M') }}</span>
                                    <span class="block text-2xl font-black text-slate-800 dark:text-white">{{ $event->start_date->format('d') }}</span>
                                </div>
                                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
                                <div>
                                    <span class="block text-sm font-bold text-slate-700 dark:text-slate-200">{{ $event->start_date->translatedFormat('l') }}</span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400">Pukul {{ $event->start_time ? Carbon\Carbon::parse($event->start_time)->format('H:i') : '08:00' }} WIB</span>
                                </div>
                            </div>

                            @if($event->end_date && $event->end_date != $event->start_date)
                            <div class="flex justify-center">
                                <span class="material-symbols-outlined text-slate-300">arrow_downward</span>
                            </div>
                             <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50">
                                <div class="text-center w-12 shrink-0">
                                    <span class="block text-xs uppercase font-bold text-slate-400">{{ $event->end_date->translatedFormat('M') }}</span>
                                    <span class="block text-2xl font-black text-slate-800 dark:text-white">{{ $event->end_date->format('d') }}</span>
                                </div>
                                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
                                <div>
                                    <span class="block text-sm font-bold text-slate-700 dark:text-slate-200">{{ $event->end_date->translatedFormat('l') }}</span>
                                    <span class="block text-xs text-slate-500 dark:text-slate-400">Selesai</span>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                             <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($event->title) }}&dates={{ $event->start_date->format('Ymd') }}/{{ $event->end_date ? $event->end_date->addDay()->format('Ymd') : $event->start_date->addDay()->format('Ymd') }}&details={{ urlencode(Str::limit($event->description, 100)) }}&location={{ urlencode($event->location . ' Jepara') }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5">
                                <span class="material-symbols-outlined text-xl">calendar_add_on</span>
                                Simpan ke Kalender
                             </a>
                        </div>
                    </div>

                    <!-- Organizer/Info -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 shadow-xl text-white relative overflow-hidden">
                         <div class="absolute top-0 right-0 p-8 opacity-5">
                            <span class="material-symbols-outlined text-9xl">campaign</span>
                        </div>
                        <h3 class="font-display font-bold text-lg mb-4 relative z-10">Informasi</h3>
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-slate-400 mt-0.5">info</span>
                                <p class="text-sm text-slate-300">
                                    Event ini diselenggarakan oleh <strong class="text-white">Dinas Pariwisata & Kebudayaan Jepara</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-public-layout>
