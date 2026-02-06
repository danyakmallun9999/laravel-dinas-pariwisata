{{-- Desktop Sidebar Component - Enhanced with GSAP --}}
<aside id="desktop-sidebar" 
       class="fixed left-0 top-0 bottom-0 w-[420px] flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 z-20"
       style="transform: translateX(-100%); opacity: 0;"
       x-show="!isNavigating"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       x-init="$nextTick(() => animateSidebar())">
    
    {{-- Header Section --}}
    <div class="p-6 pb-4 border-b border-slate-100 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm sidebar-header" style="opacity: 0; transform: translateY(-20px);">
        <div class="flex items-center gap-3">
             <a href="{{ route('welcome') }}" class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-sky-500 hover:text-white dark:hover:bg-sky-500 transition group" title="Kembali ke Beranda">
                <span class="material-symbols-outlined text-lg group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
             </a>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-800 dark:text-white">Jelajahi Jepara</h1>
                <p class="text-xs text-slate-400">Kabupaten Jepara</p>
            </div>
        </div>
    </div>

    {{-- Search Section --}}
    <div class="px-6 py-4 sidebar-search relative z-[60]" style="opacity: 0; transform: translateY(10px);">
        <div class="flex items-center w-full h-12 rounded-xl bg-slate-100 dark:bg-slate-800 focus-within:ring-2 focus-within:ring-sky-500/50 transition-all border border-transparent focus-within:border-sky-500/30">
            <div class="grid place-items-center h-full w-12 text-slate-400 dark:text-slate-500">
                <span class="material-symbols-outlined">search</span>
            </div>
            <input class="peer h-full w-full bg-transparent border-none text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:ring-0 text-sm" 
                   placeholder="Cari destinasi wisata..." type="text"
                   x-model="searchQuery" @input.debounce.300ms="performSearch()">
        </div>
        
        {{-- Search Dropdown - Positioned relative to search section --}}
        <div x-show="searchResults.length > 0" @click.outside="searchResults = []"
             class="absolute top-full left-6 right-6 mt-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 max-h-60 overflow-y-auto z-[100] p-2" 
             x-cloak x-transition>
            <template x-for="result in searchResults" :key="result.id">
                <button @click="selectFeature(result); searchResults = []" class="w-full text-left px-3 py-2.5 hover:bg-sky-50 dark:hover:bg-sky-900/30 rounded-lg transition flex items-center gap-3">
                    {{-- Image / Icon --}}
                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 overflow-hidden flex-shrink-0 flex items-center justify-center">
                        <template x-if="result.image_url">
                            <img :src="result.image_url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!result.image_url">
                            <span class="material-symbols-outlined text-slate-400 text-lg">image</span>
                        </template>
                    </div>
                    
                    {{-- Text --}}
                    <div class="min-w-0">
                        <p class="font-bold text-sm text-slate-800 dark:text-white truncate" x-text="result.name"></p>
                        <p class="text-xs text-slate-400 truncate" x-text="result.category?.name || 'Destinasi'"></p>
                    </div>
                </button>
            </template>
        </div>
    </div>

    {{-- Category Filter --}}
    <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-800 sidebar-categories relative z-[50]" style="opacity: 0; transform: translateY(10px);">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Kategori</p>
        <div class="flex gap-2 flex-wrap">
            @foreach($categories as $index => $category)
            <button @click="toggleCategory({{ $category->id }})" 
                    :class="selectedCategories.includes({{ $category->id }}) ? 'bg-sky-500 text-white border-sky-500' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-sky-500'"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all border text-xs font-medium active:scale-95 category-btn"
                    style="opacity: 0; transform: scale(0.8);">
                <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }} text-sm"></i>
                <span>{{ $category->name }}</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Scrollable Content Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-6 py-4 sidebar-content" style="opacity: 0;">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
             <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-800 dark:text-white" x-text="visiblePlaces.length"></span>
                <span class="text-sm text-slate-400">Destinasi</span>
             </div>
             
             <button @click="toggleSortNearby()" 
                     :class="sortByDistance ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
                     class="text-xs font-bold px-4 py-2 rounded-full transition-all flex items-center gap-2 active:scale-95">
                 <span class="material-symbols-outlined text-base">near_me</span>
                 <span x-text="sortByDistance ? 'Terdekat' : 'Cari Terdekat'"></span>
             </button>
        </div>
        
         {{-- List of Places --}}
         <div class="space-y-3">
             <template x-if="visiblePlaces.length === 0">
                 <div class="text-center py-12">
                     <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                         <span class="material-symbols-outlined text-3xl text-slate-300">location_off</span>
                     </div>
                     <p class="text-slate-500 font-medium">Tidak ada destinasi</p>
                     <p class="text-xs text-slate-400 mt-1">Coba ubah filter kategori</p>
                 </div>
             </template>

             <template x-for="(place, index) in visiblePlaces" :key="place.id">
                <div @click="selectPlace(place)" 
                     :class="selectedFeature && selectedFeature.id === place.id ? 'ring-2 ring-sky-500 bg-sky-50 dark:bg-sky-900/20' : 'bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700'"
                     class="flex gap-4 p-4 rounded-2xl cursor-pointer transition-all group place-card"
                     :style="'animation-delay: ' + (index * 50) + 'ms'">
                    
                    {{-- Image --}}
                    <div class="w-20 h-20 rounded-xl bg-slate-200 dark:bg-slate-700 shrink-0 overflow-hidden">
                        <template x-if="place.image_url">
                            <img :src="place.image_url" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </template>
                        <template x-if="!place.image_url">
                            <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-500">
                                <i class="fa-solid fa-map-marker-alt text-2xl"></i>
                            </div>
                        </template>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex flex-col min-w-0 flex-1 py-0.5">
                        <h4 class="font-bold text-base text-slate-800 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-tight line-clamp-2" x-text="place.name"></h4>
                        
                        <div class="mt-auto flex items-center gap-3">
                            <span class="text-xs px-2 py-0.5 rounded-md bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300" x-text="place.category?.name"></span>
                            <template x-if="place.distance">
                                <span class="text-xs text-sky-500 font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">directions_walk</span>
                                    <span x-text="place.distance + ' km'"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                    
                    {{-- Arrow --}}
                    <div class="flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="material-symbols-outlined text-slate-300">chevron_right</span>
                    </div>
                </div>
             </template>
        </div>
    </div>
    
    {{-- Footer --}}
    <div class="p-4 border-t border-slate-100 dark:border-slate-800 sidebar-footer" style="opacity: 0;">
        <p class="text-xs text-slate-400 text-center">© {{ date('Y') }} Dinas Pariwisata Kab. Jepara</p>
    </div>
</aside>

<script>
function animateSidebar() {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    
    // Sidebar slide in
    tl.to('#desktop-sidebar', {
        x: 0,
        opacity: 1,
        duration: 0.6,
        ease: 'power4.out'
    });
    
    // Header
    tl.to('.sidebar-header', {
        opacity: 1,
        y: 0,
        duration: 0.4
    }, '-=0.3');
    
    // Search
    tl.to('.sidebar-search', {
        opacity: 1,
        y: 0,
        duration: 0.4
    }, '-=0.2');
    
    // Categories container
    tl.to('.sidebar-categories', {
        opacity: 1,
        y: 0,
        duration: 0.3
    }, '-=0.2');
    
    // Category buttons staggered
    tl.to('.category-btn', {
        opacity: 1,
        scale: 1,
        duration: 0.3,
        stagger: 0.05,
        ease: 'back.out(1.7)'
    }, '-=0.1');
    
    // Content
    tl.to('.sidebar-content', {
        opacity: 1,
        duration: 0.4
    }, '-=0.2');
    
    // Footer
    tl.to('.sidebar-footer', {
        opacity: 1,
        duration: 0.3
    }, '-=0.1');
}
</script>
