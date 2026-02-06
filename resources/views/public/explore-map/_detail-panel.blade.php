{{-- Detail Panel - Mobile: Bottom Sheet, Desktop: Slide-Over --}}
<template x-if="selectedFeature">
    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/40 z-[550]" @click="selectedFeature = null" x-transition.opacity></div>
        
        {{-- Panel Container --}}
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full lg:translate-y-0 lg:translate-x-full opacity-0"
             x-transition:enter-end="translate-y-0 lg:translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 lg:translate-x-0 opacity-100"
             x-transition:leave-end="translate-y-full lg:translate-y-0 lg:translate-x-full opacity-0"
             class="fixed z-[560]
                    bottom-0 left-0 right-0 max-h-[85vh] rounded-t-3xl
                    lg:top-4 lg:right-4 lg:bottom-auto lg:left-auto lg:w-96 lg:max-h-[calc(100vh-2rem)] lg:rounded-2xl
                    bg-white dark:bg-slate-800 flex flex-col overflow-hidden border-t lg:border border-slate-200 dark:border-slate-700">
            
            {{-- Mobile Drag Handle --}}
            <div class="lg:hidden flex justify-center pt-3 pb-2">
                <div class="w-12 h-1.5 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
            </div>

            {{-- Header Image --}}
            <div class="h-52 lg:h-44 bg-slate-100 dark:bg-slate-700 relative flex-shrink-0 overflow-hidden">
                <template x-if="selectedFeature?.image_url">
                    <img :src="selectedFeature.image_url" 
                         class="w-full h-full object-cover transition-transform duration-700"
                         :class="show ? 'scale-100' : 'scale-110'">
                </template>
                <template x-if="!selectedFeature?.image_url">
                    <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-500 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800">
                        <span class="material-symbols-outlined text-6xl transition-all duration-500" :class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-75'">landscape</span>
                    </div>
                </template>
                
                {{-- Close Button --}}
                <button @click="selectedFeature = null" 
                        class="absolute top-4 right-4 w-11 h-11 rounded-full bg-black/30 hover:bg-black/50 text-white backdrop-blur-sm flex items-center justify-center transition-all active:scale-95 duration-300"
                        :class="show ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-4'">
                    <span class="material-symbols-outlined">close</span>
                </button>
                
                {{-- Gradient Overlay with Title --}}
                <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                    <span class="inline-block text-xs font-bold px-3 py-1 rounded-full bg-sky-500/90 text-white uppercase tracking-wider mb-2 transition-all duration-500 delay-100"
                          :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'" 
                          x-text="selectedFeature?.category?.name || selectedFeature?.type || 'Lokasi'"></span>
                    <h3 class="text-white font-bold text-xl leading-tight transition-all duration-500 delay-150"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                        x-text="selectedFeature?.name"></h3>
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-5">
                {{-- Description --}}
                <div x-data="{ expanded: false }" 
                     class="transition-all duration-500 delay-200"
                     :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-sky-500">info</span>
                        Tentang
                    </h4>
                    <div class="relative overflow-hidden">
                        {{-- Collapsed Preview (3 lines) --}}
                        <div x-show="!expanded" x-collapse.min.72px>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-3"
                               x-text="selectedFeature?.description || 'Tidak ada deskripsi tersedia.'"></p>
                        </div>
                        
                        {{-- Expanded Full Text --}}
                        <div x-show="expanded" x-collapse>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed"
                               x-text="selectedFeature?.description || 'Tidak ada deskripsi tersedia.'"></p>
                        </div>
                        
                        {{-- Read More Button --}}
                        <template x-if="selectedFeature?.description && selectedFeature.description.length > 120">
                            <button @click="expanded = !expanded" 
                                    class="mt-3 text-sm font-semibold text-sky-500 hover:text-sky-600 flex items-center gap-1.5 transition-all duration-300 active:scale-95">
                                <span x-text="expanded ? 'Sembunyikan' : 'Baca selengkapnya'"></span>
                                <span class="material-symbols-outlined text-lg transition-transform duration-300" :class="expanded ? 'rotate-180' : ''">expand_more</span>
                            </button>
                        </template>
                    </div>
                </div>
                
                {{-- Quick Info Pills --}}
                <div class="flex flex-wrap gap-2 transition-all duration-500 delay-300"
                     :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                    <template x-if="selectedFeature?.distance">
                        <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-sky-50 to-cyan-50 dark:from-sky-900/30 dark:to-cyan-900/30 text-sky-600 dark:text-sky-400 rounded-xl text-sm font-medium border border-sky-100 dark:border-sky-800">
                            <span class="material-symbols-outlined text-lg">directions_walk</span>
                            <span x-text="selectedFeature.distance + ' km dari lokasi Anda'"></span>
                        </span>
                    </template>
                    <template x-if="selectedFeature?.area">
                        <span class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/30 dark:to-teal-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm font-medium border border-emerald-100 dark:border-emerald-800">
                            <span class="material-symbols-outlined text-lg">square_foot</span>
                            <span x-text="selectedFeature.area + ' hektar'"></span>
                        </span>
                    </template>
                </div>
            </div>

            {{-- Action Buttons (Sticky Bottom) --}}
            <div class="flex-shrink-0 p-5 pt-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 space-y-3 safe-bottom">
                {{-- Primary Actions --}}
                <div class="grid grid-cols-2 gap-3 transition-all duration-500 delay-400"
                     :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                    <button @click="startRouting(selectedFeature)" 
                            class="group h-14 bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-600 hover:to-cyan-600 text-white rounded-2xl font-bold text-base flex items-center justify-center gap-2 active:scale-[0.98] transition-all duration-300">
                        <span class="material-symbols-outlined text-xl group-hover:animate-bounce">directions</span>
                        Rute
                    </button>
                    <button @click="openGoogleMaps(selectedFeature)" 
                            class="group h-14 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-2xl font-bold text-base flex items-center justify-center gap-2 active:scale-[0.98] transition-all duration-300">
                        <i class="fa-brands fa-google text-lg text-red-500 group-hover:scale-110 transition-transform"></i>
                        Maps
                    </button>
                </div>
                
                {{-- Secondary Actions --}}
                <template x-if="routingControl">
                    <button @click="toggleNavigationInstructions()" 
                            class="w-full h-12 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 active:scale-[0.98] transition-all duration-300 delay-500"
                            :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                        <span class="material-symbols-outlined text-xl">list_alt</span>
                        Lihat Petunjuk Arah
                    </button>
                </template>
                
                <button @click="zoomToFeature(selectedFeature)" 
                        class="w-full h-11 text-slate-500 dark:text-slate-400 hover:text-sky-500 text-sm font-medium flex items-center justify-center gap-2 transition-all duration-300 delay-500"
                        :class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                    <span class="material-symbols-outlined text-lg hover:animate-pulse">my_location</span>
                    Fokus ke lokasi
                </button>
            </div>
        </div>
    </div>
</template>
