<x-public-layout>
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700;900&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .culture-card:hover .card-description {
            opacity: 1;
            transform: translateY(0);
        }
        .culture-card:hover .card-bg {
            transform: scale(1.05);
        }
        .culture-card .card-description {
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .culture-card .card-bg {
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    @endpush

    <div class="bg-background-light dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24 font-display">
        <div class="max-w-7xl mx-auto px-6 lg:px-20">
            
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2 font-sans">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors" wire:navigate>{{ __('Nav.Home') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Nav.Culture') }}</span>
            </nav>

            <!-- Hero Title Section -->
            <section class="mb-12">
                <div class="max-w-3xl">
                    <h1 class="text-5xl lg:text-7xl font-black mb-6 leading-[1.1] tracking-tight text-slate-900 dark:text-slate-100 font-display">
                        {{ __('Culture.Title') }} <span class="text-primary">{{ __('Nav.Culture') }}</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-600 dark:text-slate-400 leading-relaxed font-sans">
                        {{ __('Culture.Subtitle') }}
                    </p>
                </div>
            </section>

            <div x-data="{
                activeCategory: null,
                categories: [
                    {
                        id: 'Kemahiran & Kerajinan Tradisional (Kriya)',
                        title: 'Kemahiran & Kerajinan Tradisional (Kriya)', 
                        subtitle: 'Kriya',
                        description: 'Jepara is known as the World Carving Center, featuring exquisite wood carving, Troso weaving, and batik.',
                        image: '{{ asset("images/culture/ukir.jpg") }}'
                    },
                    {
                        id: 'Adat Istiadat, Ritus, & Perayaan Tradisional',
                        title: 'Adat Istiadat, Ritus, & Perayaan Tradisional',
                        subtitle: 'Tradisi',
                        description: 'Sacred traditions like Perang Obor and Pesta Lomban that celebrate the gratitude and history of Jepara.',
                        image: '{{ asset("images/culture/obor.png") }}'
                    },
                    {
                        id: 'Seni Pertunjukan',
                        title: 'Seni Pertunjukan',
                        subtitle: 'Seni',
                        description: 'Experience the rhythm of Wayang Kulit, Kridhajati Dance, and the graceful movements of local arts.',
                        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuByC0plW4kR_o3v4HYNa2r2JTW_CZ4SqENWKfWjQKnwCW8gPPQOpS2euCZuK2OeaH8SFfMje5m8x607ts6J8tZ42M2egKoBZTvB5clgNfHI5xXHqUtxtzoD10NZ3hyL9-pRo4f0VHA-HuDIJ4NhiN5nuu6Kw9KPyJTxnKYc4xGSBqWrEQtl9SMLfOGt81e8wCupxUP5mG3AHEQiOj0tgP8DQKYU30VyXmT50XUYr7I_IV3EzciVPLhNkG6oCYU44ENsU_B8-yM9MA'
                    },

                    {
                        id: 'Kuliner Khas',
                        title: 'Kuliner Khas Jepara',
                        subtitle: 'Kuliner',
                        description: 'A journey through spice and tradition, exploring the diverse flavors of Rendang, Satay, and the rich heritage of local ingredients.',
                        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAqBziuaRPIVdzVy6lqfQSsB1vBb-GOIriqfJv68H5uzzLAUP6poD5XP4FGglTwJaX3LPkeAVYOSVyEyjkH1Ci_b2WRORruNdhL1ugHYJ1HpMiTw2OjZYcC6UhsS1RyjaQLtpJOcndXvtAZiRea90NTMX6cNStTI40Wp2ql9UPdDTvP-MNpdm7kARbT4dh9eaLQM9DLE9TGujgtvbxjSnzbANWVaWMyVdOH60MHeE7J8OYDizNtb2aEGPvBqkX6FaHuR-28zuGNxA'
                    }
                ]
            }">
                
                <!-- Interactive Category Grid (The 3 Big Cards) -->
                <section class="grid grid-cols-2 gap-3 md:gap-6 lg:gap-8 min-h-[400px] mb-16" x-show="!activeCategory" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <template x-for="category in categories" :key="category.id">
                        <div class="culture-card relative group overflow-hidden rounded-xl bg-slate-200 cursor-pointer aspect-[3/4] md:h-[200px]" @click="activeCategory = category.id; window.scrollTo({top: 0, behavior: 'smooth'})">
                            <div class="card-bg absolute inset-0 bg-cover bg-center" :style="`background-image: url('${category.image}');`"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                            <div class="absolute inset-0 p-4 md:p-8 flex flex-col justify-end">
                                <h3 class="text-white text-xl md:text-3xl font-bold mb-2 md:mb-3 leading-tight" x-text="category.title"></h3>
                                <div class="flex items-center text-primary font-bold gap-2 text-[10px] md:text-sm uppercase tracking-wider group/link">
                                    <span>Explore Gallery</span>
                                    <span class="material-symbols-outlined transition-transform group-hover/link:translate-x-2 text-base md:text-2xl">arrow_forward</span>
                                </div>



                            </div>
                        </div>
                    </template>
                </section>

                <!-- Detail Grid (Shown when a category is selected) -->
                <section x-show="activeCategory" x-cloak class="min-h-[600px]">
                    <button @click="activeCategory = null" class="mb-8 flex items-center gap-2 text-slate-500 hover:text-primary transition-colors font-sans font-bold uppercase tracking-wider text-sm">
                        <span class="material-symbols-outlined">arrow_back</span> Back to Collections
                    </button>

                    <div class="space-y-16">
                        @foreach($categoryOrder as $categoryName)
                            <!-- Combine Adat & Kemahiran for the first block if needed, logic handled in controller/view mapping -->
                             @if(isset($groupedCultures[$categoryName]))
                                <div x-show="activeCategory === '{{ $categoryName }}'" 
                                     x-data="{ 
                                        currentPage: 1, 
                                        itemsPerPage: window.innerWidth < 768 ? 8 : 9, 
                                        init() {
                                            window.addEventListener('resize', () => {
                                                this.itemsPerPage = window.innerWidth < 768 ? 8 : 9;
                                            });
                                        },
                                        get totalPages() { return Math.ceil({{ count($groupedCultures[$categoryName]) }} / this.itemsPerPage) } 
                                     }"
                                     x-transition:enter="transition ease-out duration-500" 
                                     x-transition:enter-start="opacity-0 translate-y-8" 
                                     x-transition:enter-end="opacity-100 translate-y-0">
                                     
                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-6">
                                    
                                    @foreach($groupedCultures[$categoryName] as $culture)
                                        <div class="culture-card relative group overflow-hidden rounded-xl bg-slate-200 dark:bg-slate-800 cursor-pointer aspect-[3/4] md:aspect-auto h-[500px]" 
                                             x-show="{{ $loop->index }} >= (currentPage - 1) * itemsPerPage && {{ $loop->index }} < currentPage * itemsPerPage"
                                             onclick="window.location.href='{{ isset($culture->price) ? route('culinary.show', $culture->slug) : route('culture.show', $culture->slug) }}'">
                                            <!-- Background Image -->
                                            @if($culture->image)
                                                <div class="card-bg absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $culture->image_url }}');"></div>
                                            @else
                                                <div class="card-bg absolute inset-0 bg-slate-300 dark:bg-slate-700 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-6xl text-slate-400">theater_comedy</span>
                                                </div>
                                            @endif

                                            <!-- Gradient Overlay -->
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                                            <!-- Content -->
                                            <div class="absolute inset-0 p-8 flex flex-col justify-end">

                                                
                                                <h3 class="text-white text-3xl font-bold mb-3 leading-tight font-display">{{ $culture->name }}</h3>
                                                
                                                <p class="card-description text-slate-200 text-base leading-relaxed font-sans mb-6 line-clamp-3">
                                                    {{Str::limit($culture->description, 100)}}
                                                </p>
                                                
                                                <div class="flex items-center text-primary font-bold gap-2 text-sm uppercase tracking-wider group/link font-sans mt-auto">
                                                    <span>{{ __('Culture.Button.More') }}</span>
                                                    <span class="material-symbols-outlined transition-transform group-hover/link:translate-x-2 text-lg">arrow_forward</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination Controls -->
                                <div class="mt-8 flex justify-center items-center gap-4" x-show="totalPages > 1" x-cloak>
                                    <button 
                                        @click="currentPage > 1 ? currentPage-- : null" 
                                        :class="{'opacity-50 cursor-not-allowed': currentPage === 1, 'hover:text-primary': currentPage > 1}"
                                        class="flex items-center gap-1 font-bold text-slate-500 transition-colors uppercase text-sm"
                                        :disabled="currentPage === 1">
                                        <span class="material-symbols-outlined">chevron_left</span> Previous
                                    </button>
                                    
                                    <span class="text-slate-600 font-sans font-medium text-sm">
                                        Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                                    </span>
                                    
                                    <button 
                                        @click="currentPage < totalPages ? currentPage++ : null" 
                                        :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages, 'hover:text-primary': currentPage < totalPages}"
                                        class="flex items-center gap-1 font-bold text-slate-500 transition-colors uppercase text-sm"
                                        :disabled="currentPage === totalPages">
                                        Next <span class="material-symbols-outlined">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        
                        <!-- Special Handling for 'Traditional Architecture' mapping to generic Adat/Kemahiran if users click it -->
                         <div x-show="activeCategory === 'Adat Istiadat & Ritus'" class="mt-8">
                            <!-- Also show Kemahiran here if we want to merge them under one 'Architecture/Heritage' umbrella -->
                             @if(isset($groupedCultures['Kemahiran & Kerajinan Tradisional']))
                                <h3 class="text-2xl font-bold mb-6 text-slate-800 dark:text-white border-t border-slate-200 dark:border-slate-700 pt-8 mt-8">Crafts & Skills</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                                    @foreach($groupedCultures['Kemahiran & Kerajinan Tradisional'] as $culture)
                                         <div class="culture-card relative group overflow-hidden rounded-xl bg-slate-200 dark:bg-slate-800 cursor-pointer aspect-[3/4] md:aspect-auto h-[500px]" onclick="window.location.href='{{ route('culture.show', $culture->slug) }}'">
                                            @if($culture->image)
                                                <div class="card-bg absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $culture->image_url }}');"></div>
                                            @else
                                                 <div class="card-bg absolute inset-0 bg-slate-300 dark:bg-slate-700 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-6xl text-slate-400">handyman</span>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                                            <div class="absolute inset-0 p-8 flex flex-col justify-end">
                                                <h3 class="text-white text-2xl font-bold mb-2 font-display">{{ $culture->name }}</h3>
                                                <p class="text-slate-200 text-sm line-clamp-2">{{ $culture->description }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                             @endif
                         </div>
                    </div>
                </section>
            </div>


        </div>
    </div>
</x-public-layout>
