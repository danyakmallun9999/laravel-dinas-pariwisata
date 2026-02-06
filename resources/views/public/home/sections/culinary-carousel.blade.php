    <!-- SECTION: Culinary -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-10 lg:py-16 border-t border-surface-light dark:border-surface-dark" 
         x-data="{
            currentIndex: 0,
            autoplay: null,
            scrollFrame: null,
            originalsCount: 0,
            
            getGap() {
                const container = $refs.foodContainer;
                if (!container) return 32;
                const style = window.getComputedStyle(container);
                return parseInt(style.gap) || 0;
            },

            scrollLeft() { 
                const container = $refs.foodContainer;
                if (!container || !container.children.length) return;
                const itemWidth = container.children[0].offsetWidth; 
                const gap = this.getGap();
                container.scrollBy({ left: -(itemWidth + gap), behavior: 'smooth' });
                this.stopAutoplay();
                this.startAutoplay();
            },

            scrollRight() { 
                const container = $refs.foodContainer;
                if (!container || !container.children.length) return;
                const itemWidth = container.children[0].offsetWidth;
                const gap = this.getGap();
                container.scrollBy({ left: (itemWidth + gap), behavior: 'smooth' });
                this.stopAutoplay();
                this.startAutoplay();
            },

            scrollToIndex(index) {
                const container = $refs.foodContainer;
                if (!container || !container.children.length) return;
                const targetIndex = this.originalsCount + index;
                const targetElement = container.children[targetIndex];
                if (targetElement) {
                    const scrollPos = targetElement.offsetLeft - (container.clientWidth - targetElement.offsetWidth) / 2;
                    container.scrollTo({ left: scrollPos, behavior: 'smooth' });
                }
                this.stopAutoplay();
                this.startAutoplay();
            },

            updateActive() {
                const container = $refs.foodContainer;
                if (!container) return;
                const center = container.scrollLeft + (container.clientWidth / 2);
                Array.from(container.children).forEach(child => {
                    const childCenter = child.offsetLeft + (child.offsetWidth / 2);
                    const distance = Math.abs(center - childCenter);
                    child.setAttribute('data-snapped', distance < child.offsetWidth / 2 ? 'true' : 'false');
                });
            },

            updateCurrentIndex() {
                const container = $refs.foodContainer;
                if (!container || !container.children.length) return;
                const center = container.scrollLeft + (container.clientWidth / 2);
                let closestIndex = -1;
                let minDistance = Infinity;
                Array.from(container.children).forEach((child, idx) => {
                    const childCenter = child.offsetLeft + (child.offsetWidth / 2);
                    const distance = Math.abs(center - childCenter);
                    if (distance < minDistance) {
                        minDistance = distance;
                        closestIndex = idx;
                    }
                });
                if (closestIndex !== -1) {
                    const rawIndex = closestIndex - this.originalsCount;
                    this.currentIndex = ((rawIndex % this.originalsCount) + this.originalsCount) % this.originalsCount;
                }
            },

            startAutoplay() {
                if (window.innerWidth < 768) return;
                this.stopAutoplay();
                this.autoplay = setInterval(() => {
                    const container = $refs.foodContainer;
                    if (container && container.children.length) {
                        const itemWidth = container.children[0].offsetWidth;
                        const gap = this.getGap();
                        container.scrollBy({ left: (itemWidth + gap), behavior: 'smooth' });
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
                const container = $refs.foodContainer;
                if (!container) return;
                const originals = Array.from(container.children);
                this.originalsCount = originals.length;
                
                // Clone for infinite scroll
                originals.forEach(item => {
                    const clone = item.cloneNode(true);
                    clone.setAttribute('data-clone', 'true');
                    clone.setAttribute('aria-hidden', 'true');
                    container.appendChild(clone);
                });
                [...originals].reverse().forEach(item => {
                    const clone = item.cloneNode(true);
                    clone.setAttribute('data-clone', 'true');
                    clone.setAttribute('aria-hidden', 'true');
                    container.insertBefore(clone, container.firstChild);
                });
                
                this.$nextTick(() => {
                    const startItem = container.children[this.originalsCount];
                    if (startItem) {
                        container.scrollLeft = startItem.offsetLeft - (container.clientWidth - startItem.offsetWidth) / 2;
                    }
                    this.updateActive();
                    this.updateCurrentIndex();
                    this.startAutoplay();
                    
                    container.addEventListener('scroll', () => {
                        if (this.scrollFrame) cancelAnimationFrame(this.scrollFrame);
                        this.scrollFrame = requestAnimationFrame(() => {
                            this.updateActive();
                            this.updateCurrentIndex();
                            
                            if (container.children.length < this.originalsCount * 3) return;
                            const setB_StartElement = container.children[this.originalsCount];
                            const setC_StartElement = container.children[this.originalsCount * 2];
                            const setWidth = setC_StartElement.offsetLeft - setB_StartElement.offsetLeft;
                            const scrollLeft = container.scrollLeft;
                            
                            if (scrollLeft >= setC_StartElement.offsetLeft) {
                                container.scrollLeft -= setWidth;
                            } else if (scrollLeft < setB_StartElement.offsetLeft - setWidth) {
                                container.scrollLeft += setWidth;
                            }
                        });
                    });
                });
                
                window.addEventListener('resize', () => {
                    if (window.innerWidth < 768) {
                        this.stopAutoplay();
                    } else {
                        this.startAutoplay();
                    }
                });
            }
         }"
         @mouseenter="stopAutoplay()" 
         @mouseleave="startAutoplay()">
        
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-text-light dark:text-text-dark mb-4 leading-tight">
                        {{ __('Culinary.Title') }}
                    </h2>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-lg leading-relaxed">
                        {{ __('Culinary.Subtitle') }}
                    </p>
                </div>
                
                <!-- Navigation Buttons (Desktop Only) -->
                <div class="hidden md:flex gap-2 shrink-0">
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

            <!-- Carousel Container -->
            <div class="relative w-full group">
                <div class="flex gap-4 lg:gap-8 overflow-x-auto pb-12 pt-4 px-4 lg:px-0 snap-x snap-mandatory scrollbar-hide" 
                     x-ref="foodContainer">
                
                    @foreach($culinaries as $index => $culinary)
                    <!-- Culinary Card -->
                    <div class="min-w-[90%] sm:min-w-[70%] md:min-w-[60%] lg:min-w-[75%] snap-center group relative rounded-3xl overflow-hidden aspect-[3/4] sm:aspect-[4/5] md:aspect-[16/9] transition-all duration-500 scale-90 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10">
                        
                        <!-- Image -->
                        <img src="{{ asset($culinary->image) }}" 
                             alt="{{ $culinary->name }}" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        
                        <!-- Inactive Overlay -->
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-80 group-data-[snapped=true]:opacity-100 transition-opacity duration-500"></div>
                        
                        <!-- Content -->
                        <div class="absolute inset-0 flex flex-col justify-end p-6 lg:p-12">
                            <h3 class="text-white font-bold text-2xl sm:text-3xl lg:text-5xl mb-2 lg:mb-3 drop-shadow-2xl">
                                {{ $culinary->name }}
                            </h3>
                            <p class="text-white/95 text-sm sm:text-base lg:text-xl max-w-xl mb-4 lg:mb-6 leading-relaxed drop-shadow-lg line-clamp-3">
                                {{ $culinary->description }}
                            </p>
                            
                            <!-- Read More Button (Active State Only) -->
                            <div class="opacity-0 group-data-[snapped=true]:opacity-100 transition-opacity duration-500 delay-100">
                                <a href="{{ route('culinary.show', $culinary->slug) }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-white text-sm font-bold transition-all shadow-lg hover:translate-x-1">
                                    <span>{{ __('Culinary.Button.More') }}</span>
                                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
                
                <!-- Dots Indicator -->
                <div class="flex justify-center gap-2 mt-8">
                    @foreach($culinaries as $index => $culinary)
                    <button 
                        @click="scrollToIndex({{ $index }})"
                        :class="currentIndex === {{ $index }} ? 'w-8 bg-primary' : 'w-2 bg-gray-300 dark:bg-gray-600 hover:bg-primary/50'"
                        class="h-2 rounded-full transition-all duration-300"
                        aria-label="Go to slide {{ $index + 1 }}">
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION: Culinary -->
