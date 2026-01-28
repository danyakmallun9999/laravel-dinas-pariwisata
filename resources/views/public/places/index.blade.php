<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen -mt-20 pt-36">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12" x-data="{
            activeCategory: 'Semua',
            places: {{ Js::from($places) }},
            get filteredPlaces() {
                if (this.activeCategory === 'Semua') return this.places;
                return this.places.filter(place => place.category.name === this.activeCategory);
            }
        }">
            
            <!-- Header -->
            <div class="text-center mb-12">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-2 block">Jelajahi Jepara</span>
                <h1 class="text-4xl md:text-5xl font-black text-text-light dark:text-text-dark mb-4">
                    Destinasi Wisata Unggulan
                </h1>
                <p class="text-gray-500 max-w-2xl mx-auto text-lg">
                    Temukan keindahan alam, kekayaan sejarah, dan pesona budaya yang membuat Jepara begitu istimewa.
                </p>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <button @click="activeCategory = 'Semua'" 
                        :class="activeCategory === 'Semua' ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-105' : 'bg-gray-100 dark:bg-surface-dark text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10'"
                        class="px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                    Semua
                </button>
                @foreach($categories as $category)
                <button @click="activeCategory = '{{ $category->name }}'" 
                        :class="activeCategory === '{{ $category->name }}' ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-105' : 'bg-gray-100 dark:bg-surface-dark text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10'"
                        class="px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-300">
                    {{ $category->name }}
                </button>
                @endforeach
            </div>

            <!-- Places Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 min-h-[50vh]">
                <template x-for="place in filteredPlaces" :key="place.id">
                    <a :href="`/destinasi/${place.slug}`" class="group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-lg cursor-pointer bg-gray-100 dark:bg-surface-dark block">
                        <!-- Image -->
                        <div class="absolute inset-0">
                            <template x-if="place.image_path">
                                <img :src="place.image_path" :alt="place.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </template>
                            <template x-if="!place.image_path">
                                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-200 dark:bg-gray-800">
                                    <span class="material-symbols-outlined text-4xl">image</span>
                                </div>
                            </template>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        </div>

                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-white text-xs font-bold shadow-sm" x-text="place.category ? place.category.name : 'Wisata'">
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="absolute bottom-0 left-0 right-0 p-6 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                            <h3 class="text-xl font-bold text-white mb-2 leading-tight drop-shadow-sm" x-text="place.name"></h3>
                            <p class="text-white/80 text-sm line-clamp-2 mb-4 leading-relaxed" x-text="place.description ? place.description.substring(0, 80) + '...' : ''">
                            </p>
                            
                            <div class="flex items-center justify-between border-t border-white/20 pt-4">
                                <div class="flex items-center gap-2 text-white/90">
                                    <span class="material-symbols-outlined text-sm text-yellow-400">star</span>
                                    <span class="text-sm font-bold" x-text="place.rating"></span>
                                </div>
                                <span class="text-white/90 text-sm font-medium" x-text="place.ticket_price === 'Gratis' ? 'Gratis' : place.ticket_price">
                                </span>
                            </div>
                        </div>
                    </a>
                </template>
                
                <!-- Empty State -->
                <div x-show="filteredPlaces.length === 0" class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-20 text-gray-500">
                    <span class="material-symbols-outlined text-6xl mb-4 text-gray-300">search_off</span>
                    <p class="text-lg">Tidak ada destinasi ditemukan untuk kategori ini.</p>
                </div>
            </div>

        </div>
    </div>
</x-public-layout>
