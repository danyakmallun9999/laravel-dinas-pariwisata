    <!-- SECTION: Culinary -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-6 lg:py-16 border-t border-surface-light dark:border-surface-dark"
         x-data="{
            currentIndex: 0,
            total: {{ count($culinaries) }},
            autoplayInterval: null,
            transitioning: false,
            touchStartX: 0,
            touchEndX: 0,
            isDragging: false,
            dragTriggered: false,

            prev() {
                if (this.transitioning) return;
                this.transitioning = true;
                this.currentIndex = (this.currentIndex - 1 + this.total) % this.total;
                setTimeout(() => this.transitioning = false, 600);
            },

            next() {
                if (this.transitioning) return;
                this.transitioning = true;
                this.currentIndex = (this.currentIndex + 1) % this.total;
                setTimeout(() => this.transitioning = false, 600);
            },

            goTo(index) {
                if (this.transitioning || index === this.currentIndex) return;
                this.transitioning = true;
                this.currentIndex = index;
                setTimeout(() => this.transitioning = false, 600);
            },

            prevIndex() {
                return (this.currentIndex - 1 + this.total) % this.total;
            },

            nextIndex() {
                return (this.currentIndex + 1) % this.total;
            },

            handleSwipeStart(e) {
                this.touchStartX = e.type.includes('mouse') ? e.screenX : e.changedTouches[0].screenX;
                this.isDragging = true;
                this.dragTriggered = false;
                this.stopAutoplay();
            },

            handleSwipeMove(e) {
                if (!this.isDragging) return;
                const currentX = e.type.includes('mouse') ? e.screenX : e.changedTouches[0].screenX;
                if (Math.abs(currentX - this.touchStartX) > 10) {
                    this.dragTriggered = true;
                }
            },

            handleSwipeEnd(e) {
                if (!this.isDragging) return;
                this.isDragging = false;
                const endX = e.type.includes('mouse') ? e.screenX : e.changedTouches[0].screenX;
                const diff = this.touchStartX - endX;
                
                if (Math.abs(diff) > 50) {
                    diff > 0 ? this.next() : this.prev();
                    this.dragTriggered = true; // Confirm drag for click prevention
                }
                this.startAutoplay();
            },

            handleClick(url) {
                if (!this.dragTriggered) {
                    window.location.href = url;
                }
            },

            startAutoplay() {
                this.stopAutoplay();
                this.autoplayInterval = setInterval(() => this.next(), 5000);
            },

            stopAutoplay() {
                if (this.autoplayInterval) {
                    clearInterval(this.autoplayInterval);
                    this.autoplayInterval = null;
                }
            },

            getItemClass(index) {
                if (this.currentIndex === index) {
                    return 'z-50 opacity-100'; 
                } else if (this.prevIndex() === index || this.nextIndex() === index) {
                    return 'z-30 opacity-100';
                } else {
                    return 'z-0 opacity-0 pointer-events-none';
                }
            },

            getItemStyle(index) {
                const isCurrent = this.currentIndex === index;
                const isPrev = this.prevIndex() === index;
                const isNext = this.nextIndex() === index;

                let transform = 'translateX(0) scale(0.5)';
                let filter = 'blur(6px)';
                let zIndexDelay = '0s';

                if (isCurrent) {
                    transform = 'translateX(0) scale(1)';
                    filter = 'none';
                    zIndexDelay = '0s'; 
                } else if (isPrev) {
                    transform = 'translateX(-30%) scale(0.55)';
                    filter = 'blur(4px)';
                    zIndexDelay = '0s'; 
                } else if (isNext) {
                    transform = 'translateX(30%) scale(0.55)';
                    filter = 'blur(4px)';
                    zIndexDelay = '0s'; 
                }

                return `transform: ${transform}; filter: ${filter}; transition-delay: 0s, 0s, 0s, ${zIndexDelay};`;
            },

            init() {
                this.startAutoplay();
            }
         }">

        <style>
            .carousel-transition {
                transition-property: transform, filter, opacity, z-index;
                transition-duration: 700ms, 700ms, 700ms, 0s;
                transition-timing-function: cubic-bezier(0.25, 0.1, 0.25, 1), cubic-bezier(0.25, 0.1, 0.25, 1), cubic-bezier(0.25, 0.1, 0.25, 1), linear;
            }
        </style>

        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row items-end justify-between mb-6 md:mb-12 gap-4 md:gap-6 px-2 sm:px-0">
                <div class="max-w-2xl culinary-header">
                    <h2 class="text-3xl md:text-4xl font-poppins font-bold text-text-light dark:text-text-dark mb-4 leading-tight">
                        {{ __('Culinary.Title') }}
                    </h2>
                    <p class="text-text-light/70 dark:text-text-dark/70 text-lg leading-relaxed">
                        {{ __('Culinary.Subtitle') }}
                    </p>
                </div>
            </div>

            <!-- Carousel Container -->
            <div class="relative w-full overflow-hidden rounded-3xl py-4 md:py-8 cursor-grab active:cursor-grabbing"
                 @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()"
                 @touchstart="handleSwipeStart($event)"
                 @touchmove="handleSwipeMove($event)"
                 @touchend="handleSwipeEnd($event)"
                 @mousedown="handleSwipeStart($event)"
                 @mousemove="handleSwipeMove($event)"
                 @mouseup="handleSwipeEnd($event)">

                <div class="relative mx-auto flex max-w-6xl items-center justify-center" style="min-height: 420px;">
                    
                    @foreach($culinaries as $index => $culinary)
                    <div class="absolute w-full rounded-3xl overflow-hidden carousel-transition"
                         :class="getItemClass({{ $index }})"
                         :style="getItemStyle({{ $index }})">
                        <div class="mx-auto" :class="currentIndex === {{ $index }} ? 'max-w-4xl px-4 md:px-0' : 'max-w-3xl'">
                            <div class="relative overflow-hidden rounded-3xl transition-shadow duration-700"
                                 :class="currentIndex === {{ $index }} ? 'shadow-2xl cursor-pointer' : 'shadow-lg pointer-events-none'"
                                 @click="currentIndex === {{ $index }} && handleClick('{{ route('culinary.show', $culinary->slug) }}')">
                                <img src="{{ asset($culinary->image) }}"
                                     alt="{{ $culinary->name }}"
                                     class="h-[280px] md:h-[420px] w-full object-cover rounded-3xl transition-transform duration-700"
                                     :class="currentIndex === {{ $index }} ? 'group-hover:scale-105' : ''">

                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent transition-opacity duration-500"
                                     :class="currentIndex === {{ $index }} ? 'opacity-100' : 'opacity-60'"></div>

                                <!-- Text Content (only visible on active slide) -->
                                <div class="absolute bottom-6 left-6 right-6 md:bottom-8 md:left-8 md:right-8 text-white transition-all duration-500"
                                     :class="currentIndex === {{ $index }} ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                                    <h3 class="text-2xl md:text-3xl lg:text-4xl font-bold leading-tight drop-shadow-lg">
                                        {{ $culinary->name }}
                                    </h3>
                                    <p class="mt-2 max-w-xl text-white/90 text-sm md:text-base line-clamp-2 leading-relaxed">
                                        {{ $culinary->description }}
                                    </p>
                                    <a href="{{ route('culinary.show', $culinary->slug) }}"
                                       class="mt-4 inline-flex items-center gap-2 rounded-full bg-sky-600 px-5 md:px-6 py-2 md:py-2.5 text-sm font-medium text-white hover:bg-sky-700 transition-all shadow-lg hover:shadow-xl hover:scale-105"
                                       @click.stop.prevent="handleClick('{{ route('culinary.show', $culinary->slug) }}')">
                                        {{ __('Culinary.Button.More') }} →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Navigation Arrows -->
                <button @click="prev()"
                        class="hidden md:block absolute left-2 md:left-6 top-1/2 -translate-y-1/2 z-[60] rounded-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm p-2.5 md:p-3 shadow-lg hover:bg-white dark:hover:bg-slate-700 hover:scale-110 transition-all">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-slate-700 dark:text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="next()"
                        class="hidden md:block absolute right-2 md:right-6 top-1/2 -translate-y-1/2 z-[60] rounded-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm p-2.5 md:p-3 shadow-lg hover:bg-white dark:hover:bg-slate-700 hover:scale-110 transition-all">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-slate-700 dark:text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Dots Indicator -->
            <div class="flex justify-center gap-2 mt-4 md:mt-6">
                @foreach($culinaries as $index => $culinary)
                <button 
                    @click="goTo({{ $index }})"
                    :class="currentIndex === {{ $index }} ? 'w-6 bg-sky-600' : 'w-2 bg-slate-300 dark:bg-slate-600 hover:bg-sky-400'"
                    class="h-2 rounded-full transition-all duration-300"
                    aria-label="Go to slide {{ $index + 1 }}">
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        (function() {
            const initCulinary = () => {
                if (typeof gsap === 'undefined') return;
                gsap.registerPlugin(ScrollTrigger);
                
                gsap.set(".culinary-header", { opacity: 0, y: 20 });

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
            };

            // Run on initial load
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(initCulinary, 100);
            } else {
                document.addEventListener('DOMContentLoaded', initCulinary);
            }

            // Run on Livewire navigation
            document.addEventListener('livewire:navigated', initCulinary);
        })();
    </script>
    <!-- END SECTION: Culinary -->
