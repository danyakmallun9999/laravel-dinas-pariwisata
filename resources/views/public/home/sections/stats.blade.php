    <!-- SECTION: Stats -->
    <div 
        x-data="{
            animateCount(el, target) {
                let start = 0;
                const duration = 2000;
                const stepTime = 20;
                const steps = duration / stepTime;
                const increment = target / steps;
                
                const timer = setInterval(() => {
                    start += increment;
                    if (start >= target) {
                        clearInterval(timer);
                        start = target;
                        el.innerText = target + '+';
                    } else {
                        el.innerText = Math.floor(start) + '+';
                    }
                }, stepTime);
            }
        }"
        class="relative w-full py-12 md:py-16 bg-gradient-to-b from-white to-slate-50 dark:from-slate-950 dark:to-slate-900 border-b border-slate-100 dark:border-white/5"
    >
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                
                <!-- Destinasi Wisata -->
                <div 
                    x-data="{ shown: false }"
                    x-init="
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting) {
                                shown = true;
                                animateCount($refs.counter, {{ $countDestinasi }});
                                observer.disconnect();
                            }
                        }, { threshold: 0.1 });
                        observer.observe($el);
                    "
                    class="relative overflow-hidden rounded-[2rem] bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 group"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                >
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary rotate-12">photo_camera</span>
                    </div>
                    
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span x-ref="counter">0</span>
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Stats.Destinations') }}</dt>
                        </div>
                    </div>
                </div>

                <!-- Kuliner Khas -->
                <div 
                    x-data="{ shown: false }"
                    x-init="
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting) {
                                setTimeout(() => {
                                    shown = true;
                                    animateCount($refs.counter, {{ $countKuliner }});
                                    observer.disconnect();
                                }, 100);
                            }
                        }, { threshold: 0.1 });
                        observer.observe($el);
                    "
                    class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 group"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                >
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary -rotate-12">restaurant_menu</span>
                    </div>

                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">restaurant_menu</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span x-ref="counter">0</span>
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Stats.Culinary') }}</dt>
                        </div>
                    </div>
                </div>

                <!-- Agenda Event -->
                <div 
                    x-data="{ shown: false }"
                    x-init="
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting) {
                                setTimeout(() => {
                                    shown = true;
                                    animateCount($refs.counter, {{ $countEvent }});
                                    observer.disconnect();
                                }, 200);
                            }
                        }, { threshold: 0.1 });
                        observer.observe($el);
                    "
                    class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 group"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                >
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary rotate-12">event_available</span>
                    </div>

                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">event_available</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span x-ref="counter">0</span>
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Nav.Events') }}</dt>
                        </div>
                    </div>
                </div>

                <!-- Desa Wisata -->
                <div 
                    x-data="{ shown: false }"
                    x-init="
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting) {
                                setTimeout(() => {
                                    shown = true;
                                    animateCount($refs.counter, {{ $countDesa }});
                                    observer.disconnect();
                                }, 300);
                            }
                        }, { threshold: 0.1 });
                        observer.observe($el);
                    "
                    class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border border-slate-200/60 dark:border-white/10 p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5 group"
                    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                >
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                        <span class="material-symbols-outlined text-6xl text-primary -rotate-12">holiday_village</span>
                    </div>

                    <div class="relative z-10 flex items-center gap-4">
                        <div class="flex items-center justify-center size-12 rounded-xl bg-primary/10 text-primary group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-500">
                            <span class="material-symbols-outlined text-2xl">holiday_village</span>
                        </div>
                        <div>
                            <dd class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none group-hover:text-primary transition-colors duration-300">
                                <span x-ref="counter">0</span>
                            </dd>
                            <dt class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ __('Stats.Villages') }}</dt>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END SECTION: Stats -->
