<x-public-layout>
    @push('seo')
        <x-seo 
            :title="$event->title . ' - Agenda Jelajah Jepara'"
            :description="Str::limit(strip_tags($event->description), 150)"
            :image="$event->image ? asset($event->image) : asset('images/agenda/logo-agenda.png')"
            type="article"
        />
    @endpush
    @section('meta_title', $event->title)
    @section('meta_description', Str::limit(strip_tags($event->description), 150))
    @section('meta_image', $event->image ? asset($event->image) : asset('images/agenda/logo-agenda.png'))

    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans -mt-20 pt-4">
        
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row">
                
                <!-- Left Side: Sticky Visuals (50%) -->
                <div class="lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative h-[60vh] bg-white dark:bg-slate-950 z-10 group flex flex-col p-4 lg:pl-16 lg:pr-8 lg:pt-24">
                    
                    <!-- Breadcrumbs -->
                    <div class="mb-6">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                                <li class="inline-flex items-center">
                                    <a href="{{ route('welcome') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                        <span class="material-symbols-outlined text-lg mr-1">home</span>
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                        <a href="{{ route('events.public.index') }}" class="text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                            Agenda
                                        </a>
                                    </div>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                        <span class="text-sm font-medium text-slate-900 dark:text-white line-clamp-1 max-w-[150px] md:max-w-xs">
                                            {{ $event->title }}
                                        </span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>
    
                    <!-- Main Image Area -->
                    <div class="flex-1 relative w-full flex items-start justify-start overflow-hidden perspective-[1000px]">
                        <div class="relative w-full h-full rounded-3xl overflow-hidden text-transparent">
                            @if($event->image)
                                <img src="{{ asset($event->image) }}" 
                                     alt="{{ $event->title }}" 
                                     class="w-full h-full object-cover transition-all duration-500 ease-in-out transform hover:scale-105">
                            @else
                                <img src="{{ asset('images/agenda/logo-agenda.png') }}" 
                                     alt="{{ $event->title }}" 
                                     class="w-full h-full object-cover transition-all duration-500 ease-in-out transform hover:scale-105">
                            @endif
                        </div>
                    </div>
    
                    <!-- Optional: Thumbnail Gallery could go here if events had multiple images -->
                    <div class="w-full pb-6 pt-3"></div>
                </div>
    
                <!-- Right Side: Scrollable Content (50%) -->
                <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                    <main class="max-w-3xl mx-auto px-6 py-12 md:py-16 lg:px-16 lg:py-24">
                        
                        <!-- Top Meta: Badge & Status -->
                        <div class="flex flex-wrap items-center gap-3 mb-6 animate-fade-in-up">
                            <span class="px-3 py-1 rounded-full bg-primary/5 dark:bg-primary/10 text-primary dark:text-primary font-bold uppercase tracking-wider text-xs border border-primary/20 dark:border-primary/20">
                                {{ __('Events.Badge') }}
                            </span>
                            @if($event->start_date->isFuture())
                                <span class="px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider text-xs border border-emerald-100 dark:border-emerald-800/30">
                                    {{ __('Events.Status.Upcoming') }}
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-xs border border-slate-200 dark:border-slate-700">
                                    {{ __('Events.Status.Past') }}
                                </span>
                            @endif
                        </div>
    
                        <!-- Title & Location -->
                        <div class="mb-10 animate-fade-in-up delay-100">
                            <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-tight mb-4">
                                {{ $event->title }}
                            </h1>
                            @if($event->location)
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                                <span class="material-symbols-outlined text-xl flex-shrink-0 text-red-500">location_on</span>
                                <span class="font-light">{{ $event->location }}</span>
                            </div>
                            @endif
                        </div>
    
                        <!-- Horizontal Divider -->
                        <hr class="border-slate-100 dark:border-slate-800 mb-10">
    
                        <!-- Content Body -->
                        <div class="space-y-12 animate-fade-in-up delay-200">
                            
                            <!-- Description -->
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-4 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                                    {{ __('Events.Detail.About') }}
                                </h3>
                                <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed text-justify">
                                    {!! \App\Services\ContentSanitizer::sanitizeAllowHtml($event->description) !!}
                                </div>
                            </section>
    
                            <!-- Key Information Grid (Date & Time) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Date Info -->
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">{{ __('Events.Detail.Time') }}</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary dark:text-primary">
                                            <span class="material-symbols-outlined text-xl">calendar_month</span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1">
                                            <div class="flex flex-col gap-1">
                                                <div class="text-sm font-bold">{{ $event->start_date->translatedFormat('d F Y') }}</div>
                                                @if($event->end_date && $event->end_date != $event->start_date)
                                                    <div class="text-xs text-slate-500">{{ __('Events.Detail.Until') }} {{ $event->end_date->translatedFormat('d F Y') }}</div>
                                                @endif
                                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[10px]">schedule</span>
                                                    {{ $event->start_time ? Carbon\Carbon::parse($event->start_time)->format('H:i') : '08:00' }} WIB
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Organizer / Contact -->
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">{{ __('Events.Detail.Organizer') }}</div>
                                    <div class="text-slate-900 dark:text-white font-semibold flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary dark:text-primary">
                                            <span class="material-symbols-outlined text-xl">campaign</span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1">
                                            <div class="text-sm">{{ __('Events.Detail.OrganizerName') }}</div>
                                            <div class="text-xs text-slate-500 mt-1">{{ __('Events.Detail.InfoCenter') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <!-- Actions: Calendar & Share -->
                            <section>
                                 <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">{{ __('Events.Detail.Actions') }}</h3>
                                 <div class="flex flex-wrap gap-4">
                                    <!-- Google Calendar -->
                                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text={{ urlencode($event->title) }}&dates={{ $event->start_date->format('Ymd') }}/{{ $event->end_date ? $event->end_date->addDay()->format('Ymd') : $event->start_date->addDay()->format('Ymd') }}&details={{ urlencode(Str::limit($event->description, 100)) }}&location={{ urlencode($event->location . ' Jepara') }}" 
                                       target="_blank" 
                                       class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-primary-dark transition-colors shadow-lg shadow-primary/25 hover:translate-y-[-2px]">
                                        <span class="material-symbols-outlined text-xl">calendar_add_on</span>
                                        <span>{{ __('Events.Detail.SaveCalendar') }}</span>
                                    </a>
                                 </div>
                            </section>
    
                            <!-- Map / Location Section -->
                            @if($event->location)
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                                    {{ __('Events.Detail.LocationTitle') }}
                                </h3>
                                <div class="relative w-full h-[400px] md:h-auto md:aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm group">
                                     <iframe 
                                        width="100%" 
                                        height="100%" 
                                        frameborder="0" 
                                        scrolling="no" 
                                        marginheight="0" 
                                        marginwidth="0" 
                                        src="https://maps.google.com/maps?q={{ urlencode($event->location . ' Jepara') }}&t=&z=14&ie=UTF8&iwloc=&output=embed">
                                    </iframe>
                                    
                                    <!-- Click Overlay to Open External Map -->
                                    <a href="https://maps.google.com/maps?q={{ urlencode($event->location . ' Jepara') }}" 
                                       target="_blank"
                                       class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center z-10"
                                       title="{{ __('Events.Detail.MapsLink') }}">
                                        <div class="bg-white/90 backdrop-blur text-slate-900 px-4 py-2 rounded-full font-bold text-sm shadow-lg opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all flex items-center gap-2">
                                            <span class="material-symbols-outlined text-red-500">map</span>
                                            {{ __('Events.Detail.MapsLink') }}
                                        </div>
                                    </a>
                                </div>
                            </section>
                            @endif
    
                        </div>
    
                        <!-- Footer Area -->
                        <div class="mt-20 pt-10 border-t border-slate-100 dark:border-slate-800 text-center">
                            <p class="text-slate-400 text-sm">
                                &copy; {{ date('Y') }} Dinas Pariwisata & Kebudayaan Kabupaten Jepara
                            </p>
                        </div>
    
                    </main>
                </div>
    
            </div>
        </div>
    </div>
</x-public-layout>
