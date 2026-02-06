{{-- Proximity Alert Modal --}}
<div x-show="nearbyAlert" 
     class="fixed inset-0 z-[1000] flex items-end sm:items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak>
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="nearbyAlert = null"></div>

    {{-- Modal Content --}}
    <div class="relative bg-white dark:bg-slate-800 rounded-3xl max-w-sm w-full overflow-hidden border border-slate-200 dark:border-slate-700"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-8 scale-95"
         x-transition:enter-end="translate-y-0 scale-100">
        
        {{-- Image Header --}}
        <div class="h-36 bg-slate-100 dark:bg-slate-700 relative">
            <template x-if="nearbyAlert?.image_url">
                <img :src="nearbyAlert.image_url" class="w-full h-full object-cover">
            </template>
            <template x-if="!nearbyAlert?.image_url">
                <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-500">
                    <span class="material-symbols-outlined text-5xl">landscape</span>
                </div>
            </template>
            
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/30 to-transparent flex items-end p-5">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Di Sekitar Anda</span>
                    </div>
                    <h3 class="text-white font-bold text-xl leading-tight" x-text="nearbyAlert?.name"></h3>
                </div>
            </div>
        </div>
        
        {{-- Content --}}
        <div class="p-5">
            <p class="text-slate-600 dark:text-slate-300 text-sm mb-6">
                Anda berada dalam jarak <span class="font-bold text-sky-500">500m</span> dari destinasi ini. Ingin melihat detailnya?
            </p>
            
            {{-- Actions --}}
            <div class="flex gap-3">
                <button @click="nearbyAlert = null" 
                        class="flex-1 h-12 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition active:scale-[0.98]">
                    Tutup
                </button>
                <button @click="selectPlace({ ...nearbyAlert, latitude: nearbyAlert.latitude, longitude: nearbyAlert.longitude }); nearbyAlert = null;" 
                        class="flex-1 h-12 rounded-xl bg-gradient-to-r from-sky-500 to-cyan-500 text-white font-bold hover:from-sky-600 hover:to-cyan-600 transition active:scale-[0.98]">
                    Lihat Detail
                </button>
            </div>
        </div>
    </div>
</div>
