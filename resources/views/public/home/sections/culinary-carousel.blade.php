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
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
                this.stopAutoplay();
                this.startAutoplay();
            },

            updateActive() {
                const container = $refs.foodContainer;
                if (!container) return;
                const containerCenter = container.scrollLeft + (container.clientWidth / 2);
                
                Array.from(container.children).forEach(child => {
                    const childCenter = child.offsetLeft + (child.offsetWidth / 2);
                    const distance = Math.abs(childCenter - containerCenter);
                    const isSnapped = distance < (child.offsetWidth / 2); 
                    child.setAttribute('data-snapped', isSnapped ? 'true' : 'false');
                });
            },

            updateCurrentIndex() {
                const container = $refs.foodContainer;
                if (!container || !container.children.length) return;
                
                const containerCenter = container.scrollLeft + (container.clientWidth / 2);
                let closestIndex = -1;
                let minDistance = Infinity;
                
                Array.from(container.children).forEach((child, idx) => {
                    const childCenter = child.offsetLeft + (child.offsetWidth / 2);
                    const distance = Math.abs(childCenter - containerCenter);
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
                        container.scrollLeft = startItem.offsetLeft;
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
                <div class="max-w-2xl culinary-header">
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
                <div class="flex gap-4 md:gap-8 overflow-x-auto pb-12 pt-4 px-[5%] md:px-[20%] snap-x snap-mandatory scrollbar-hide scroll-smooth items-center" 
                     style="scroll-snap-type: x mandatory;"
                     x-ref="foodContainer">
                
                    @foreach($culinaries as $index => $culinary)
                    <!-- Culinary Card -->
                    <div class="culinary-card shrink-0 w-[90vw] md:w-[60vw] lg:w-[50vw] snap-center group relative rounded-[2.5rem] overflow-hidden aspect-[16/9] transition-all duration-500 scale-95 data-[snapped=true]:scale-100 data-[snapped=true]:shadow-xl data-[snapped=true]:hover:shadow-2xl data-[snapped=true]:border data-[snapped=true]:border-white/10"
                         style="scroll-snap-align: center; scroll-snap-stop: always;">
                        
                        <!-- Image -->
                        <img src="{{ asset($culinary->image) }}" 
                             alt="{{ $culinary->name }}" 
                             class="w-full h-full object-cover transform transition-transform duration-700 [.group[data-snapped='true']:hover_&]:scale-110 contrast-110 saturate-110 brightness-105">
                        
                        <!-- Inactive Overlay -->
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-500 group-data-[snapped=true]:opacity-0"></div>
                        
                        <!-- Gradient Removed as requested -->
                        
                        <!-- Gradient Overlay (Better for Mobile Legibility) -->
                        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-90 transition-opacity duration-500 pointer-events-none"></div>

                        <!-- Content -->
                        <div class="absolute inset-0 flex flex-col justify-end p-4 md:p-8 lg:p-12">
                            <div class="transform transition-transform duration-500 group-data-[snapped=true]:translate-y-0 translate-y-4 opacity-0 group-data-[snapped=true]:opacity-100">
                                <h3 class="text-white font-bold text-xl sm:text-2xl md:text-3xl lg:text-4xl mb-1 md:mb-2 drop-shadow-lg">
                                    {{ $culinary->name }}
                                </h3>
                                <p class="text-white/90 text-xs sm:text-sm md:text-base lg:text-lg mb-3 md:mb-5 leading-relaxed line-clamp-2 md:line-clamp-3 font-medium drop-shadow-md">
                                    {{ $culinary->description }}
                                </p>
                                
                                <!-- Read More Button -->
                                <div>
                                    <a href="{{ route('culinary.show', $culinary->slug) }}" 
                                       class="inline-flex items-center gap-1.5 md:gap-2 px-4 py-2 md:px-5 md:py-2.5 rounded-full bg-white text-primary text-xs md:text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:scale-105">
                                        <span>{{ __('Culinary.Button.More') }}</span>
                                        <span class="material-symbols-outlined text-base md:text-lg">arrow_forward</span>
                                    </a>
                                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.registerPlugin(ScrollTrigger);
            
            // Immediately set initial state via GSAP (faster than CSS)
            gsap.set(".culinary-header", { opacity: 0, y: 20 });
            gsap.set(".culinary-card", { opacity: 0, y: 30 });
            
            // Header Animation - Super fast trigger
            ScrollTrigger.create({
                trigger: ".culinary-header",
                start: "top bottom-=50",
                onEnter: () => {
                    gsap.to(".culinary-header", {
                        opacity: 1,
                        y: 0,
                        duration: 0.4,
                        ease: "power1.out"
                    });
                }
            });

            // Cards Animation - Instant trigger when section visible
            const culinaryCards = document.querySelectorAll('.culinary-card');
            const section = document.querySelector('.culinary-header')?.closest('[class*="bg-surface"]');
            
            ScrollTrigger.create({
                trigger: section || ".culinary-header",
                start: "top bottom-=20",
                onEnter: () => {
                    gsap.to(culinaryCards, {
                        opacity: 1,
                        y: 0,
                        duration: 0.35,
                        stagger: 0.05,
                        ease: "power1.out"
                    });
                }
            });
        });
    </script>
    <!-- END SECTION: Culinary -->
