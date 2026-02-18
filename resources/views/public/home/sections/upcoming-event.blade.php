@if($upcomingEvent)
<section class="relative w-full py-16 lg:py-24 overflow-hidden bg-white" id="upcoming-event"
    x-data="{
        getScrollAmount() {
            if (!$refs.container.firstElementChild) return 300;
            const itemWidth = $refs.container.firstElementChild.getBoundingClientRect().width;
            return itemWidth + 24; // Width + gap-6 (24px)
        },
        smoothScroll(direction) {
            const container = $refs.container;
            const scrollAmount = this.getScrollAmount();
            const start = container.scrollLeft;
            const target = direction === 'right' ? start + scrollAmount : start - scrollAmount;
            const duration = 600;
            const startTime = performance.now();

            const animateScroll = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Ease-in-out Cubic
                const ease = progress < 0.5 
                    ? 4 * progress * progress * progress 
                    : 1 - Math.pow(-2 * progress + 2, 3) / 2;

                container.scrollLeft = start + (target - start) * ease;

                if (progress < 1) {
                    requestAnimationFrame(animateScroll);
                }
            };
            
            requestAnimationFrame(animateScroll);
        },
        scrollLeft() { 
            this.smoothScroll('left');
        },
        scrollRight() { 
            this.smoothScroll('right');
        }
    }">
    
    <!-- Abstract Decoration -->
    <div class="absolute top-0 right-0 w-1/3 h-full bg-slate-50 rounded-l-[4rem] -mr-20 transform skew-x-[-10deg] hidden lg:block pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-8 lg:gap-16 items-start">
            
            <!-- Left Side: Main Event & Countdown (Span 5) -->
            <div class="lg:col-span-12 xl:col-span-5 space-y-8" data-aos="fade-right">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary-600 shadow-sm animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                    <span class="text-xs font-bold font-display uppercase tracking-wider text-primary">{{ __('EventSection.Badge') }}</span>
                </div>

                <!-- Title & Description -->
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold font-display text-slate-900 mb-4 leading-tight">
                        {{ $upcomingEvent->translated_title }}
                    </h2>
                    <p class="text-base text-slate-600 leading-relaxed font-sans line-clamp-3">
                        {{ Str::limit(strip_tags($upcomingEvent->translated_description), 150) }}
                    </p>
                </div>

                <!-- Countdown -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden"
                     x-data="{
                        target: {{ $upcomingEvent->start_date->timestamp * 1000 }},
                        days: '00', hours: '00', minutes: '00', seconds: '00',
                        update() {
                            const now = new Date().getTime();
                            const dist = this.target - now;
                            if (dist <= 0) {
                                this.days = this.hours = this.minutes = this.seconds = '00';
                                return;
                            }
                            this.days = String(Math.floor(dist / 86400000)).padStart(2, '0');
                            this.hours = String(Math.floor((dist % 86400000) / 3600000)).padStart(2, '0');
                            this.minutes = String(Math.floor((dist % 3600000) / 60000)).padStart(2, '0');
                            this.seconds = String(Math.floor((dist % 60000) / 1000)).padStart(2, '0');
                        },
                        init() {
                            this.update();
                            setInterval(() => this.update(), 1000);
                        }
                     }"
                     x-cloak>
                    
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>

                    <h3 class="text-center text-slate-400 font-bold font-display mb-6 uppercase tracking-widest text-xs relative z-10">{{ __('EventSection.Countdown') }}</h3>
                    
                    <div class="grid grid-cols-4 gap-2 text-center relative z-10">
                        <div class="space-y-2">
                            <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                                <span class="block text-2xl font-black font-display text-slate-900" x-text="days">--</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase">{{ __('EventSection.Days') }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                                <span class="block text-2xl font-black font-display text-slate-900" x-text="hours">--</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase">{{ __('EventSection.Hours') }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                                <span class="block text-2xl font-black font-display text-slate-900" x-text="minutes">--</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-bold uppercase">{{ __('EventSection.Minutes') }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="bg-primary/5 rounded-xl p-2 border border-primary/10">
                                <span class="block text-2xl font-black font-display text-primary" x-text="seconds">--</span>
                            </div>
                            <span class="text-[10px] text-primary font-bold uppercase">{{ __('EventSection.Seconds') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Event Details -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-100 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 text-primary">
                            <i class="fa-regular fa-calendar text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase">{{ __('EventSection.Date') }}</p>
                            <p class="text-slate-900 font-bold text-sm">
                                {{ \Carbon\Carbon::parse($upcomingEvent->start_date)->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-100 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 text-primary">
                            <i class="fa-solid fa-location-dot text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase">{{ __('EventSection.Location') }}</p>
                            <p class="text-slate-900 font-medium text-sm line-clamp-1">
                                {{ $upcomingEvent->location }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <a href="{{ route('events.public.show', $upcomingEvent->slug) }}" 
                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold transition-all shadow-lg shadow-primary/20"
                    wire:navigate>
                    <span>{{ __('EventSection.ViewDetails') }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Right Side: Carousel (Span 7) -->
            <div class="lg:col-span-12 xl:col-span-7 w-full overflow-hidden" data-aos="fade-left" data-aos-delay="100">
                
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold font-display text-slate-900">{{ __('EventSection.OtherEvents') }}</h3>
                    
                    <!-- Navigation Buttons -->
                     @if($nextEvents->count() > 2)
                    <div class="flex gap-2">
                        <button @click="scrollLeft()" 
                                class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 text-slate-600 transition-colors">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button @click="scrollRight()" 
                                class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center hover:bg-slate-50 text-slate-600 transition-colors">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Carousel Container -->
                <div class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory scrollbar-hide -mx-4 px-4 lg:mx-0 lg:px-0" x-ref="container">
                    @foreach($nextEvents as $event)
                    <a href="{{ route('events.public.show', $event->slug) }}" 
                       class="block min-w-[280px] sm:min-w-[320px] snap-center bg-white rounded-2xl border border-slate-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
                       wire:navigate>
                        
                        <!-- Image -->
                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            @if($event->image)
                                <img src="{{ asset($event->image) }}" alt="{{ $event->translated_title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <img src="{{ asset('images/agenda/logo-agenda.png') }}" alt="{{ $event->translated_title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @endif
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-lg text-xs font-bold text-primary shadow-sm">
                                {{ \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M') }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h4 class="font-bold text-slate-900 mb-2 line-clamp-2 group-hover:text-primary transition-colors text-lg">
                                {{ $event->translated_title }}
                            </h4>
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-50">
                                <span class="text-xs text-slate-400 font-medium uppercase tracking-wider">{{ __('EventSection.Location') }}</span>
                                <span class="text-xs font-bold text-slate-700 line-clamp-1 max-w-[60%] text-right">{{ $event->location }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach

                    @if($nextEvents->isEmpty())
                        <div class="w-full py-10 text-center text-slate-500 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            {{ __('EventSection.NoOtherEvents') }}
                        </div>
                    @endif
                </div>
                
                <div class="text-center mt-4 lg:hidden">
                     <a href="{{ route('events.public.index') }}" class="inline-flex items-center text-primary font-medium hover:text-primary-dark text-sm" wire:navigate>
                        {{ __('EventSection.ViewAllEvents') }} <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

@endif
