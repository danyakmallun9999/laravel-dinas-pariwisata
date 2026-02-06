{{-- Desktop Map Controls (Hidden on Mobile) --}}

{{-- Layer Toggle (Top Right) --}}
<div class="fixed top-4 right-4 z-[400]" style="left: 440px;">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="w-11 h-11 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-colors border border-slate-200 dark:border-slate-700">
            <span class="material-symbols-outlined">layers</span>
        </button>
        <div x-show="open" @click.outside="open = false" 
             class="absolute top-12 right-0 w-40 bg-white dark:bg-slate-800 p-2 rounded-xl border border-slate-200 dark:border-slate-700"
             x-transition x-cloak>
             <p class="text-[10px] font-bold uppercase text-slate-400 mb-2 px-2">Peta Dasar</p>
             <button @click="setBaseLayer('streets'); open = false" 
                     :class="currentBaseLayer === 'streets' ? 'bg-sky-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                     class="w-full py-2 text-sm font-medium rounded-lg mb-1 transition-colors">Jalan</button>
             <button @click="setBaseLayer('satellite'); open = false"
                     :class="currentBaseLayer === 'satellite' ? 'bg-sky-500 text-white' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                     class="w-full py-2 text-sm font-medium rounded-lg transition-colors">Satelit</button>
        </div>
    </div>
</div>

{{-- Zoom & Navigation Controls (Bottom Right) --}}
<div class="fixed bottom-6 right-6 flex flex-col gap-3 z-[400]">
    {{-- Zoom Controls --}}
    <div class="flex flex-col bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <button @click="map.zoomIn()" class="w-11 h-11 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-colors">
            <span class="material-symbols-outlined">add</span>
        </button>
        <div class="h-px bg-slate-200 dark:bg-slate-700"></div>
        <button @click="map.zoomOut()" class="w-11 h-11 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-500 transition-colors">
            <span class="material-symbols-outlined">remove</span>
        </button>
    </div>
    
    {{-- Navigation Toggle --}}
    <button @click="toggleLiveNavigation()" 
            :class="isNavigating ? 'bg-red-500 text-white animate-pulse' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-sky-500 border border-slate-200 dark:border-slate-700'"
            class="w-11 h-11 rounded-xl flex items-center justify-center hover:scale-105 transition-all">
        <span class="material-symbols-outlined" x-text="isNavigating ? 'navigation' : 'near_me'"></span>
    </button>
    
    {{-- My Location --}}
    <button @click="locateUser(null, true)" class="w-11 h-11 bg-gradient-to-r from-sky-500 to-cyan-500 text-white rounded-xl flex items-center justify-center hover:from-sky-600 hover:to-cyan-600 hover:scale-105 transition-all">
        <span class="material-symbols-outlined">my_location</span>
    </button>
</div>

{{-- Legend (Bottom Left) --}}
<div class="fixed bottom-6 z-[400]" style="left: 440px;">
    <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm p-3 rounded-xl border border-slate-200 dark:border-slate-700">
        <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Legenda</h5>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-emerald-500/30"></span>
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Batas Wilayah</span>
        </div>
    </div>
</div>
