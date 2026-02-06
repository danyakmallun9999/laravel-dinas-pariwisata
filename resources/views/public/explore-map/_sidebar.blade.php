{{-- Desktop Sidebar Component (Hidden on Mobile) --}}
<aside class="fixed left-0 top-0 bottom-0 w-[420px] flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 z-20">
    
    {{-- Header Section --}}
    <div class="p-6 pb-4 border-b border-slate-100 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm">
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
    <div class="px-6 py-4">
        <div class="relative flex items-center w-full h-12 rounded-xl bg-slate-100 dark:bg-slate-800 focus-within:ring-2 focus-within:ring-sky-500/50 transition-all border border-transparent focus-within:border-sky-500/30">
            <div class="grid place-items-center h-full w-12 text-slate-400 dark:text-slate-500">
                <span class="material-symbols-outlined">search</span>
            </div>
            <input class="peer h-full w-full bg-transparent border-none text-slate-800 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:ring-0 text-sm" 
                   placeholder="Cari destinasi wisata..." type="text"
                   x-model="searchQuery" @input.debounce.300ms="performSearch()">
                   
             {{-- Search Dropdown --}}
            <div x-show="searchResults.length > 0" @click.outside="searchResults = []"
                 class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 max-h-60 overflow-y-auto z-50 p-2" 
                 x-cloak x-transition>
                <template x-for="result in searchResults" :key="result.id">
                    <button @click="selectFeature(result); searchResults = []" class="w-full text-left px-3 py-2.5 hover:bg-sky-50 dark:hover:bg-sky-900/30 rounded-lg transition flex items-center gap-3">
                        <span class="material-symbols-outlined text-sky-500">location_on</span>
                        <div>
                            <p class="font-bold text-sm text-slate-800 dark:text-white" x-text="result.name"></p>
                            <p class="text-xs text-slate-400" x-text="result.category?.name || 'Destinasi'"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Category Filter --}}
    <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-800">
        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-3">Kategori</p>
        <div class="flex gap-2 flex-wrap">
            @foreach($categories as $category)
            <button @click="toggleCategory({{ $category->id }})" 
                    :class="selectedCategories.includes({{ $category->id }}) ? 'bg-sky-500 text-white border-sky-500' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-sky-500'"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all border text-xs font-medium active:scale-95">
                <i class="{{ $category->icon_class ?? 'fa-solid fa-map-marker-alt' }} text-sm"></i>
                <span>{{ $category->name }}</span>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Scrollable Content Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar px-6 py-4">
        
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

             <template x-for="place in visiblePlaces" :key="place.id">
                <div @click="selectPlace(place)" 
                     :class="selectedFeature && selectedFeature.id === place.id ? 'ring-2 ring-sky-500 bg-sky-50 dark:bg-sky-900/20' : 'bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700'"
                     class="flex gap-4 p-4 rounded-2xl cursor-pointer transition-all group">
                    
                    {{-- Image --}}
                    <div class="w-20 h-20 rounded-xl bg-slate-200 dark:bg-slate-700 shrink-0 overflow-hidden">
                        <template x-if="place.image_path">
                            <img :src="'{{ url('/') }}/' + place.image_path" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </template>
                        <template x-if="!place.image_path">
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
    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
        <p class="text-xs text-slate-400 text-center">© {{ date('Y') }} Dinas Pariwisata Kab. Jepara</p>
    </div>
</aside>
