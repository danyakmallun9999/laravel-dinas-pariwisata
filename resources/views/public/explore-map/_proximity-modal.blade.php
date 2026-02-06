{{-- Proximity Alert Modal - Enhanced with GSAP --}}
<div x-show="nearbyAlert" 
     x-init="$watch('nearbyAlert', val => { if(val) animateProximityModal(); })"
     class="fixed inset-0 z-[1000] flex items-end sm:items-center justify-center p-4"
     x-cloak>
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm proximity-backdrop" 
         @click="nearbyAlert = null"
         style="opacity: 0;"></div>

    {{-- Modal Content --}}
    <div id="proximity-modal" 
         class="relative bg-white dark:bg-slate-800 rounded-3xl max-w-sm w-full overflow-hidden border border-slate-200 dark:border-slate-700"
         style="opacity: 0; transform: translateY(50px) scale(0.9);">
        
        {{-- Image Header --}}
        <div class="h-36 bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
            <template x-if="nearbyAlert?.image_url">
                <img :src="nearbyAlert.image_url" class="w-full h-full object-cover proximity-image" style="transform: scale(1.2);">
            </template>
            <template x-if="!nearbyAlert?.image_url">
                <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-500">
                    <span class="material-symbols-outlined text-5xl">landscape</span>
                </div>
            </template>
            
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/30 to-transparent flex items-end p-5">
                <div>
                    <div class="flex items-center gap-2 mb-2 proximity-badge" style="opacity: 0; transform: translateX(-20px);">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Di Sekitar Anda</span>
                    </div>
                    <h3 class="text-white font-bold text-xl leading-tight proximity-title" style="opacity: 0; transform: translateY(10px);" x-text="nearbyAlert?.name"></h3>
                </div>
            </div>
        </div>
        
        {{-- Content --}}
        <div class="p-5">
            <p class="text-slate-600 dark:text-slate-300 text-sm mb-6 proximity-text" style="opacity: 0; transform: translateY(15px);">
                Anda berada dalam jarak <span class="font-bold text-sky-500">500m</span> dari destinasi ini. Ingin melihat detailnya?
            </p>
            
            {{-- Actions --}}
            <div class="flex gap-3 proximity-buttons" style="opacity: 0; transform: translateY(20px);">
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
        
        {{-- Confetti Effect --}}
        <div class="absolute top-0 left-0 right-0 h-1 proximity-glow"></div>
    </div>
</div>

<script>
function animateProximityModal() {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    
    // Backdrop fade
    tl.to('.proximity-backdrop', {
        opacity: 1,
        duration: 0.3
    });
    
    // Modal bounce in
    tl.to('#proximity-modal', {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 0.5,
        ease: 'back.out(1.7)'
    }, '-=0.1');
    
    // Image zoom
    tl.to('.proximity-image', {
        scale: 1,
        duration: 0.6,
        ease: 'power2.out'
    }, '-=0.3');
    
    // Badge slide in
    tl.to('.proximity-badge', {
        opacity: 1,
        x: 0,
        duration: 0.4,
        ease: 'power2.out'
    }, '-=0.4');
    
    // Title
    tl.to('.proximity-title', {
        opacity: 1,
        y: 0,
        duration: 0.3
    }, '-=0.2');
    
    // Text
    tl.to('.proximity-text', {
        opacity: 1,
        y: 0,
        duration: 0.3
    }, '-=0.1');
    
    // Buttons with spring
    tl.to('.proximity-buttons', {
        opacity: 1,
        y: 0,
        duration: 0.4,
        ease: 'back.out(1.5)'
    }, '-=0.1');
    
    // Glow effect
    tl.fromTo('.proximity-glow', 
        { background: 'linear-gradient(90deg, transparent, #0ea5e9, transparent)', scaleX: 0 },
        { scaleX: 1, duration: 0.8, ease: 'power2.out' },
        '-=0.5'
    );
}
</script>
