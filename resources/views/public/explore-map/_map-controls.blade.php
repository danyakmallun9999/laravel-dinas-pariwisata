{{-- Desktop Map Controls - Enhanced with GSAP --}}

{{-- Layer Toggle (Top Left of Map Area) --}}
<div id="layer-toggle-desktop" 
     class="fixed z-[400] transition-all duration-500 ease-in-out hidden lg:block"
     :class="isNavigating ? 'lg:bottom-80 lg:right-6 lg:top-auto lg:left-auto' : 'lg:top-4 lg:left-[440px] lg:right-auto'"
     style="opacity: 0; transform: translateY(-20px);"
     x-init="$nextTick(() => animateMapControls())">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="w-11 h-11 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-all border border-slate-200 dark:border-slate-700 active:scale-95">
            <span class="material-symbols-outlined">layers</span>
        </button>
        <div x-show="open" @click.outside="open = false" 
             class="absolute w-auto whitespace-nowrap bg-transparent p-0"
             :class="isNavigating ? 'right-14 bottom-0 origin-bottom-right' : 'lg:top-12 lg:left-0 lg:origin-top-left'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak>
             <div class="flex flex-row gap-2 bg-white dark:bg-slate-800 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg">
                 <button @click="setBaseLayer('streets'); open = false" 
                         :class="currentBaseLayer === 'streets' ? 'bg-sky-500 text-white border-transparent' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-transparent'"
                         class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all active:scale-95">Jalan</button>
                 <button @click="setBaseLayer('satellite'); open = false"
                         :class="currentBaseLayer === 'satellite' ? 'bg-sky-500 text-white border-transparent' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-transparent'"
                         class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all active:scale-95">Satelit</button>
             </div>
        </div>
    </div>
</div>

{{-- Zoom & Navigation Controls (Bottom Right) --}}
<div id="zoom-controls" 
     class="fixed flex flex-col gap-3 z-[400] right-4 lg:right-6 lg:!bottom-6 transition-all duration-300 lg:!flex"
     :style="{ bottom: bottomSheetState === 'collapsed' ? '90px' : (bottomSheetState === 'half' ? 'calc(45% + 70px)' : '20px') }"
     x-show="!isNavigating && !hasActiveRoute && bottomSheetState !== 'full'"
     style="opacity: 0; transform: translateX(30px);">
    {{-- Zoom Controls --}}
    <div class="flex flex-col bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <button @click="map.zoomIn()" class="w-11 h-11 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-all active:scale-90">
            <span class="material-symbols-outlined">add</span>
        </button>
        <div class="h-px bg-slate-200 dark:bg-slate-700"></div>
        <button @click="map.zoomOut()" class="w-11 h-11 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-all active:scale-90">
            <span class="material-symbols-outlined">remove</span>
        </button>
    </div>
    
    {{-- Navigation Toggle (Only visible when route is active) --}}
    <button @click="toggleLiveNavigation()" 
            x-show="hasActiveRoute"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-75"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-75"
            :class="isNavigating ? 'bg-red-500 text-white animate-pulse' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-sky-500 border border-slate-200 dark:border-slate-700'"
            class="w-11 h-11 rounded-xl items-center justify-center hover:scale-105 transition-all active:scale-95 hidden lg:flex">
        <span class="material-symbols-outlined" x-text="isNavigating ? 'navigation' : 'near_me'"></span>
    </button>
    
    {{-- My Location --}}
    <button @click="locateUser(null, true)" class="w-11 h-11 bg-gradient-to-r from-sky-500 to-cyan-500 text-white rounded-xl flex items-center justify-center hover:from-sky-600 hover:to-cyan-600 hover:scale-105 transition-all active:scale-95 location-btn">
        <span class="material-symbols-outlined">my_location</span>
    </button>

    {{-- Mobile Layer Toggle (Bottom of Stack) --}}
    <div x-data="{ open: false }" class="relative lg:hidden">
        <button @click="open = !open" class="w-11 h-11 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-all border border-slate-200 dark:border-slate-700 active:scale-95">
            <span class="material-symbols-outlined">layers</span>
        </button>
        <div x-show="open" @click.outside="open = false" 
             class="absolute right-14 bottom-0 origin-bottom-right w-auto whitespace-nowrap bg-transparent p-0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak>
             <div class="flex flex-row gap-2 bg-white dark:bg-slate-800 p-1.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg">
                 <button @click="setBaseLayer('streets'); open = false" 
                         :class="currentBaseLayer === 'streets' ? 'bg-sky-500 text-white border-transparent' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-transparent'"
                         class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all active:scale-95">Jalan</button>
                 <button @click="setBaseLayer('satellite'); open = false"
                         :class="currentBaseLayer === 'satellite' ? 'bg-sky-500 text-white border-transparent' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border-transparent'"
                         class="px-3 py-1.5 text-xs font-bold rounded-lg border transition-all active:scale-95">Satelit</button>
             </div>
        </div>
    </div>
</div>

<script>
function animateMapControls() {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' }, delay: 0.5 });
    
    // Layer toggle from top
    tl.to('#layer-toggle-desktop', {
        opacity: 1,
        y: 0,
        duration: 0.4,
        ease: 'back.out(1.5)'
    });
    
    // Zoom controls from right
    tl.to('#zoom-controls', {
        opacity: 1,
        x: 0,
        duration: 0.5,
        ease: 'power4.out'
    }, '-=0.2');
    
    // Location button pulse
    tl.fromTo('.location-btn', 
        { scale: 1 },
        { scale: 1.1, duration: 0.2, yoyo: true, repeat: 1, ease: 'power2.inOut' },
        '-=0.1'
    );
}
</script>
