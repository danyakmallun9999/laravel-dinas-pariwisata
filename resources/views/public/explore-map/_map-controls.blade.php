{{-- Desktop Map Controls - Enhanced with GSAP --}}

{{-- Layer Toggle (Top Left of Map Area) --}}
<div id="layer-toggle" class="fixed top-4 z-[400]" style="left: 440px; opacity: 0; transform: translateY(-20px);"
     x-init="$nextTick(() => animateMapControls())">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="w-11 h-11 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-all border border-slate-200 dark:border-slate-700 active:scale-95">
            <span class="material-symbols-outlined">layers</span>
        </button>
        <div x-show="open" @click.outside="open = false" 
             class="absolute top-12 left-0 w-40 bg-white dark:bg-slate-800 p-2 rounded-xl border border-slate-200 dark:border-slate-700"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak>
             <p class="text-[10px] font-bold uppercase text-slate-400 mb-2 px-2">Peta Dasar</p>
             <button @click="setBaseLayer('streets'); open = false" 
                     :class="currentBaseLayer === 'streets' ? 'bg-sky-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                     class="w-full py-2 text-sm font-medium rounded-lg mb-1 transition-all active:scale-95">Jalan</button>
             <button @click="setBaseLayer('satellite'); open = false"
                     :class="currentBaseLayer === 'satellite' ? 'bg-sky-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                     class="w-full py-2 text-sm font-medium rounded-lg transition-all active:scale-95">Satelit</button>
        </div>
    </div>
</div>

{{-- Zoom & Navigation Controls (Bottom Right) --}}
<div id="zoom-controls" class="fixed bottom-6 right-6 flex flex-col gap-3 z-[400]" style="opacity: 0; transform: translateX(30px);">
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
    
    {{-- Navigation Toggle --}}
    <button @click="toggleLiveNavigation()" 
            :class="isNavigating ? 'bg-red-500 text-white animate-pulse' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-sky-500 border border-slate-200 dark:border-slate-700'"
            class="w-11 h-11 rounded-xl flex items-center justify-center hover:scale-105 transition-all active:scale-95">
        <span class="material-symbols-outlined" x-text="isNavigating ? 'navigation' : 'near_me'"></span>
    </button>
    
    {{-- My Location --}}
    <button @click="locateUser(null, true)" class="w-11 h-11 bg-gradient-to-r from-sky-500 to-cyan-500 text-white rounded-xl flex items-center justify-center hover:from-sky-600 hover:to-cyan-600 hover:scale-105 transition-all active:scale-95 location-btn">
        <span class="material-symbols-outlined">my_location</span>
    </button>
</div>

<script>
function animateMapControls() {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' }, delay: 0.5 });
    
    // Layer toggle from top
    tl.to('#layer-toggle', {
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
