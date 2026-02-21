    <!-- SECTION: Stats -->
    <div 
        class="stats-section relative w-full py-12 md:py-16 bg-gradient-to-b from-white to-slate-50 dark:from-slate-950 dark:to-slate-900 border-b border-slate-100 dark:border-white/5"
    >
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                
                <!-- Destinasi Wisata -->
                <div class="stat-card relative overflow-hidden rounded-[2rem] bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary rotate-12">photo_camera</span>
                    </div>
                    
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span class="stat-value" data-target="{{ $countDestinasi }}">0</span>+
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Stats.Destinations') }}</dt>
                        </div>
                    </div>
                </div>

                <!-- Kuliner Khas -->
                <div class="stat-card relative overflow-hidden rounded-[2rem] bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary -rotate-12">restaurant_menu</span>
                    </div>

                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">restaurant_menu</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span class="stat-value" data-target="{{ $countKuliner }}">0</span>+
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Stats.Culinary') }}</dt>
                        </div>
                    </div>
                </div>

                <!-- Agenda Event -->
                <div class="stat-card relative overflow-hidden rounded-[2rem] bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary rotate-12">event_available</span>
                    </div>

                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">event_available</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span class="stat-value" data-target="{{ $countEvent }}">0</span>
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Nav.Events') }}</dt>
                        </div>
                    </div>
                </div>

                <!-- Desa Wisata -->
                <div class="stat-card relative overflow-hidden rounded-[2rem] bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary -rotate-12">holiday_village</span>
                    </div>

                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">holiday_village</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span class="stat-value" data-target="{{ $countDesa }}">0</span>
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Stats.Villages') }}</dt>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <script>
        (function() {
            const initStats = () => {
                const statCards = document.querySelectorAll('.stat-card');
                const statsSection = document.querySelector('.stats-section');
                
                if (!statsSection) return;

                // Set initial state via GSAP
                gsap.set(statCards, { opacity: 0, y: 25 });
                
                ScrollTrigger.create({
                    trigger: statsSection,
                    start: "top bottom-=50",
                    onEnter: () => {
                        gsap.to(statCards, {
                            opacity: 1,
                            y: 0,
                            duration: 0.35,
                            stagger: 0.06,
                            ease: "power1.out",
                            onStart: () => {
                                // Counting Animation - faster
                                statCards.forEach(card => {
                                    const valueElement = card.querySelector('.stat-value');
                                    const target = parseInt(valueElement.getAttribute('data-target'));
                                    
                                    gsap.to(valueElement, {
                                        innerText: target,
                                        duration: 1,
                                        snap: { innerText: 1 },
                                        ease: "power1.out",
                                        onUpdate: function() {
                                            this.targets()[0].innerText = Math.ceil(this.targets()[0].innerText);
                                        }
                                    });
                                });
                            }
                        });
                    }
                });
            };

            // Run on initial load
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(initStats, 100);
            } else {
                document.addEventListener('DOMContentLoaded', initStats);
            }

            // Run on Livewire navigation
            document.addEventListener('livewire:navigated', initStats);
        })();
    </script>
    <!-- END SECTION: Stats -->
