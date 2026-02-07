    <!-- SECTION: Tourism Potency -->
    <div class="w-full py-10 lg:py-16 scroll-mt-20" id="potency" x-data="{
        currentIndex: 0,
        totalItems: {{ $places->count() }},
        autoplay: null,
        scrollLeft() { 
            $refs.container.scrollBy({ left: -300, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        scrollRight() { 
            $refs.container.scrollBy({ left: 300, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        scrollToIndex(index) {
            const container = $refs.container;
            if (!container) return;
            
            // Proportional scrolling to match the index
            const maxScroll = container.scrollWidth - container.clientWidth;
            if (maxScroll <= 0) return;
            
            const targetProp = index / (this.totalItems - 1);
            const targetPos = targetProp * maxScroll;
            
            container.scrollTo({ left: targetPos, behavior: 'smooth' });
            setTimeout(() => this.updateCurrentIndex(), 300);
        },
        updateCurrentIndex() {
            const container = $refs.container;
            if (!container || !container.children.length) return;
            
            const scrollLeft = container.scrollLeft;
            const maxScroll = container.scrollWidth - container.clientWidth;
            
            if (maxScroll <= 0) {
                this.currentIndex = 0;
                return;
            }
            
            // Map scroll percentage to index range
            const scrollValues = scrollLeft / maxScroll;
            this.currentIndex = Math.round(scrollValues * (this.totalItems - 1));
        },
        startAutoplay() {
            // Only autoplay on desktop (md breakpoint and above)
            if (window.innerWidth < 768) return;
            
            this.stopAutoplay();
            this.autoplay = setInterval(() => {
                if (this.currentIndex >= this.totalItems - 1) {
                    this.scrollToIndex(0);
                } else {
                    this.scrollRight();
                }
            }, 3000);
        },
        stopAutoplay() {
            if (this.autoplay) {
                clearInterval(this.autoplay);
                this.autoplay = null;
            }
        },
        init() {
            this.$refs.container?.addEventListener('scroll', () => this.updateCurrentIndex());
            this.startAutoplay();
            
            // Stop autoplay on resize to mobile
            window.addEventListener('resize', () => {
                if (window.innerWidth < 768) {
                    this.stopAutoplay();
                } else {
                    this.startAutoplay();
                }
            });
        }
    }" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-text-light dark:text-text-dark mb-4 leading-tight">
                        {{ __('Tourism.Title') }}
                    </h2>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-lg leading-relaxed">
                        {{ __('Tourism.Subtitle') }}
                    </p>
                </div>
                
                <!-- View All & Navigation Buttons -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- View All Button -->
                    <a href="{{ route('places.index') }}" 
                        class="text-primary font-bold hover:underline flex items-center gap-1">
                        {{ __('Tourism.Button.ViewAll') }} <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    
                    <!-- Navigation Buttons -->
                    <div class="hidden md:flex gap-2">
                        <button @click="scrollLeft()"
                            class="size-10 rounded-full border border-surface-light dark:border-white/10 flex items-center justify-center hover:bg-surface-light dark:hover:bg-white/5 text-text-light dark:text-text-dark transition-colors">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <button @click="scrollRight()"
                            class="size-10 rounded-full border border-surface-light dark:border-white/10 flex items-center justify-center hover:bg-surface-light dark:hover:bg-white/5 text-text-light dark:text-text-dark transition-colors">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Carousel Container -->
            <div class="relative w-full">
                <div class="flex gap-6 overflow-x-auto pb-8 snap-x snap-mandatory scrollbar-hide" x-ref="container">
                    
                    @foreach($places as $place)
                    @if(!$place->slug) @continue @endif
                    <!-- Gallery Item -->
                    <a href="{{ route('places.show', $place) }}" class="block min-w-[85%] sm:min-w-[calc(50%-12px)] lg:min-w-[calc(33.333%-16px)] snap-center group relative rounded-[2.5rem] overflow-hidden aspect-[4/5] shadow-lg cursor-pointer bg-surface-light dark:bg-surface-dark border border-surface-light dark:border-white/5">
                        <!-- Image -->
                        <div class="absolute inset-0 bg-gray-200">
                            @if($place->image_path)
                                <img src="{{ $place->image_path }}" alt="{{ $place->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-outlined text-4xl">image</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        </div>

                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-bold shadow-sm">
                                {{ $place->category->name ?? __('Tourism.Category.Default') }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-xl font-bold text-white mb-2 leading-tight drop-shadow-sm">{{ $place->name }}</h3>
                            <p class="text-white/80 text-sm line-clamp-2 mb-4 leading-relaxed">
                                {{ Str::limit($place->description, 80) }}
                            </p>
                            
                            <div class="flex items-center justify-between border-t border-white/20 pt-4">
                                <div class="flex items-center gap-2 text-white/90">
                                    <span class="material-symbols-outlined text-sm text-yellow-400">star</span>
                                    <span class="text-sm font-bold">{{ $place->rating }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach

                    @if($places->isEmpty())
                        <div class="w-full text-center py-10 text-gray-500">
                            {{ __('Tourism.Empty') }}
                        </div>
                    @endif

                </div>
                
                <!-- Dots Indicator -->
                @if($places->count() > 0)
                <div class="flex justify-center gap-2 mt-6">
                    @foreach($places as $index => $place)
                    <button 
                        @click="scrollToIndex({{ $index }})"
                        :class="currentIndex === {{ $index }} ? 'w-8 bg-primary' : 'w-2 bg-gray-300 dark:bg-gray-600 hover:bg-primary/50'"
                        class="h-2 rounded-full transition-all duration-300"
                        aria-label="Go to slide {{ $index + 1 }}">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    <!-- END SECTION: Tourism Potency -->
