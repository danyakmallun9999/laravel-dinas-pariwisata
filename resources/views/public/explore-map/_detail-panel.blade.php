{{-- Detail Panel - Mobile: Bottom Sheet, Desktop: Slide-Over --}}
{{-- Enhanced with GSAP Animations --}}
<template x-if="selectedFeature">
    <div x-data="{ panelReady: false }" 
         x-init="$nextTick(() => { panelReady = true; animateDetailPanel(); })">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/40 z-[550] backdrop-blur-sm" 
             @click="selectedFeature = null" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>
        
        {{-- Panel Container --}}
        <div id="detail-panel"
             class="fixed z-[560]
                    bottom-0 left-0 right-0 max-h-[85vh] rounded-t-3xl
                    lg:top-4 lg:right-4 lg:bottom-auto lg:left-auto lg:w-96 lg:max-h-[calc(100vh-2rem)] lg:rounded-2xl
                    bg-white dark:bg-slate-800 flex flex-col overflow-hidden border-t lg:border border-slate-200 dark:border-slate-700"
             style="transform: translateY(100%); opacity: 0;">
            
            {{-- Mobile Drag Handle --}}
            <div class="lg:hidden flex justify-center pt-3 pb-2 touch-none"
                 @touchstart.passive="handleDetailTouchStart($event)"
                 @touchend="handleDetailTouchEnd($event)">
                <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full drag-handle"></div>
            </div>

            {{-- Header Image with Minimalist Controls --}}
            <div class="h-56 lg:h-48 bg-slate-100 dark:bg-slate-700 relative flex-shrink-0 overflow-hidden group mx-4 rounded-xl mt-2 lg:mx-0 lg:mt-0 lg:rounded-none">
                <template x-if="selectedFeature?.image_url">
                    <img :src="selectedFeature.image_url" 
                         class="w-full h-full object-cover detail-image transition-transform duration-700"
                         style="transform: scale(1.1);">
                </template>
                <template x-if="!selectedFeature?.image_url">
                    <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-500 bg-slate-100 dark:bg-slate-800">
                        <span class="material-symbols-outlined text-5xl detail-placeholder-icon opacity-50">landscape</span>
                    </div>
                </template>
                
                {{-- Minimalist Close Button --}}
                <button @click="selectedFeature = null" 
                        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur-md flex items-center justify-center transition-all active:scale-95 detail-close-btn opacity-0 translate-x-4 z-20">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
                
                {{-- Clean Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                {{-- Title Area --}}
                <div class="absolute bottom-4 left-5 right-5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-white/20 backdrop-blur-md border border-white/10 text-[10px] font-medium text-white uppercase tracking-wider mb-2 detail-category opacity-0 translate-y-2"
                          x-text="selectedFeature?.category?.name || selectedFeature?.type || 'Lokasi'"></span>
                    <h3 class="text-white font-bold text-xl leading-tight detail-title opacity-0 translate-y-2 drop-shadow-sm"
                        x-text="selectedFeature?.name"></h3>
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5 custom-scrollbar">
                {{-- Quick Stats Row --}}
                <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-700/50 pb-4 detail-pills opacity-0 translate-y-4">
                    <template x-if="selectedFeature?.distance">
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                            <span class="material-symbols-outlined text-lg text-sky-500">near_me</span>
                            <span class="text-xs font-semibold" x-text="selectedFeature.distance + ' km'"></span>
                        </div>
                    </template>
                    <template x-if="selectedFeature?.area">
                         <div class="w-px h-4 bg-slate-200 dark:bg-slate-700"></div>
                         <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                            <span class="material-symbols-outlined text-lg text-emerald-500">square_foot</span>
                            <span class="text-xs font-semibold" x-text="selectedFeature.area + ' ha'"></span>
                        </div>
                    </template>
                </div>

                {{-- Description --}}
                <div x-data="{ expanded: false }" class="detail-description opacity-0 translate-y-4">
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed"
                       :class="!expanded ? 'line-clamp-3' : ''"
                       x-text="selectedFeature?.description || 'Tidak ada deskripsi tersedia.'"></p>
                    
                    <template x-if="selectedFeature?.description && selectedFeature.description.length > 120">
                        <button @click="expanded = !expanded" 
                                class="mt-2 text-xs font-medium text-sky-600 dark:text-sky-400 hover:underline flex items-center gap-1">
                            <span x-text="expanded ? 'Sembunyikan' : 'Baca selengkapnya'"></span>
                        </button>
                    </template>
                </div>
                
                {{-- Operating Hours --}}
                <template x-if="selectedFeature?.opening_hours">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 detail-info opacity-0 translate-y-4">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl mt-0.5">schedule</span>
                        <div>
                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 uppercase tracking-wide">Jam Operasional</span>
                            <p class="text-sm text-slate-700 dark:text-slate-200 mt-0.5" x-text="selectedFeature.opening_hours"></p>
                        </div>
                    </div>
                </template>
                
                {{-- Photo Gallery --}}
                <template x-if="selectedFeature?.images && selectedFeature.images.length > 0">
                    <div x-data="{ galleryIndex: 0 }" class="detail-gallery opacity-0 translate-y-4">
                        <h4 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Galeri Foto</h4>
                        <div class="relative">
                            {{-- Main Image --}}
                            <div class="relative aspect-video rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700">
                                <img :src="selectedFeature.images[galleryIndex]" 
                                     class="w-full h-full object-cover"
                                     alt="Gallery image">
                                {{-- Navigation Arrows --}}
                                <template x-if="selectedFeature.images.length > 1">
                                    <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-2">
                                        <button @click="galleryIndex = galleryIndex > 0 ? galleryIndex - 1 : selectedFeature.images.length - 1" 
                                                class="w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center backdrop-blur-sm transition-all">
                                            <span class="material-symbols-outlined text-lg">chevron_left</span>
                                        </button>
                                        <button @click="galleryIndex = galleryIndex < selectedFeature.images.length - 1 ? galleryIndex + 1 : 0" 
                                                class="w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center backdrop-blur-sm transition-all">
                                            <span class="material-symbols-outlined text-lg">chevron_right</span>
                                        </button>
                                    </div>
                                </template>
                                {{-- Counter --}}
                                <div class="absolute bottom-2 right-2 px-2 py-1 rounded-md bg-black/50 backdrop-blur-sm text-white text-xs font-medium">
                                    <span x-text="(galleryIndex + 1) + '/' + selectedFeature.images.length"></span>
                                </div>
                            </div>
                            {{-- Thumbnails --}}
                            <template x-if="selectedFeature.images.length > 1">
                                <div class="flex gap-2 mt-2 overflow-x-auto pb-1 scrollbar-hide">
                                    <template x-for="(img, idx) in selectedFeature.images" :key="idx">
                                        <button @click="galleryIndex = idx" 
                                                class="w-14 h-10 rounded-lg overflow-hidden flex-shrink-0 ring-2 transition-all"
                                                :class="galleryIndex === idx ? 'ring-sky-500' : 'ring-transparent opacity-60 hover:opacity-100'">
                                            <img :src="img" class="w-full h-full object-cover" alt="Thumbnail">
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Minimalist Action Bar --}}
            <div class="flex-shrink-0 p-5 pt-3 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 safe-bottom">
                <div class="grid grid-cols-[1fr,auto,auto,auto] gap-2 detail-actions opacity-0 translate-y-4">
                    {{-- Primary Route Button --}}
                    <button @click="startRouting(selectedFeature); selectedFeature = null" 
                            class="h-12 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-2xl font-bold text-sm flex items-center justify-center gap-2 active:scale-[0.98] transition-all shadow-lg shadow-sky-500/30">
                        <span class="material-symbols-outlined text-[20px]">directions</span>
                        <span>Rute Sekarang</span>
                    </button>

                    {{-- Share Button with Dropdown --}}
                    <div x-data="{ shareOpen: false }" class="relative">
                        <button @click="shareOpen = !shareOpen" 
                                class="w-12 h-12 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-2xl flex items-center justify-center active:scale-[0.98] transition-all"
                                title="Bagikan">
                            <span class="material-symbols-outlined text-xl">share</span>
                        </button>
                        
                        {{-- Share Dropdown --}}
                        <div x-show="shareOpen" 
                             @click.outside="shareOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute bottom-14 right-0 w-48 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden z-10"
                             x-cloak>
                            <button @click="shareToWhatsApp(selectedFeature); shareOpen = false" 
                                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-left">
                                <i class="fa-brands fa-whatsapp text-lg text-green-500"></i>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">WhatsApp</span>
                            </button>
                            <div class="h-px bg-slate-100 dark:bg-slate-700"></div>
                            <button @click="copyShareLink(selectedFeature); shareOpen = false" 
                                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-left">
                                <span class="material-symbols-outlined text-lg text-slate-500">content_copy</span>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Salin Link</span>
                            </button>
                        </div>
                    </div>

                    {{-- Icon-only Google Maps Button --}}
                    <button @click="openGoogleMaps(selectedFeature)" 
                            class="w-12 h-12 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-2xl flex items-center justify-center active:scale-[0.98] transition-all"
                            title="Buka di Google Maps">
                        <i class="fa-brands fa-google text-lg"></i>
                    </button>
                    
                    {{-- Street View Button --}}
                    <button @click="openStreetView(selectedFeature)" 
                            class="w-12 h-12 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-2xl flex items-center justify-center active:scale-[0.98] transition-all"
                            title="Street View">
                        <span class="material-symbols-outlined text-xl">streetview</span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</template>

<script>
function animateDetailPanel() {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    
    // Panel slide up
    tl.to('#detail-panel', {
        y: 0,
        opacity: 1,
        duration: 0.5,
        ease: 'power4.out'
    });
    
    // Image zoom effect
    tl.to('.detail-image', {
        scale: 1,
        duration: 0.8,
        ease: 'power2.out'
    }, '-=0.3');
    
    // Placeholder icon (only if exists)
    if (document.querySelector('.detail-placeholder-icon')) {
        tl.to('.detail-placeholder-icon', {
            opacity: 0.5,
            scale: 1,
            duration: 0.4,
            ease: 'back.out(1.7)'
        }, '-=0.6');
    }
    
    // Close button
    tl.to('.detail-close-btn', {
        opacity: 1,
        x: 0,
        duration: 0.3,
        ease: 'power2.out'
    }, '-=0.5');
    
    // Category badge
    tl.to('.detail-category', {
        opacity: 1,
        y: 0,
        duration: 0.3
    }, '-=0.3');
    
    // Title
    tl.to('.detail-title', {
        opacity: 1,
        y: 0,
        duration: 0.4
    }, '-=0.2');
    
    // Content sections staggered
    tl.to('.detail-description', {
        opacity: 1,
        y: 0,
        duration: 0.4
    }, '-=0.1');
    
    // Operating hours info
    tl.to('.detail-info', {
        opacity: 1,
        y: 0,
        duration: 0.4
    }, '-=0.1');
    
    // Photo gallery
    tl.to('.detail-gallery', {
        opacity: 1,
        y: 0,
        duration: 0.4
    }, '-=0.1');
    
    tl.to('.detail-pills', {
        opacity: 1,
        y: 0,
        duration: 0.4
    }, '-=0.2');
    
    // Action buttons
    tl.to('.detail-actions', {
        opacity: 1,
        y: 0,
        duration: 0.4,
        ease: 'back.out(1.2)'
    }, '-=0.2');
    

    
    // Drag handle pulse
    tl.fromTo('.drag-handle', 
        { scaleX: 0.5 },
        { scaleX: 1, duration: 0.5, ease: 'elastic.out(1, 0.5)' },
        '-=0.3'
    );
}
</script>
