<x-public-layout>
    @push('seo')
        <x-seo 
            :title="$culinary->name . ' - Kuliner Jepara'"
            :description="Str::limit(strip_tags($culinary->full_description ?? $culinary->description), 150)"
            :image="$culinary->image_url ? $culinary->image_url : asset('images/logo-kura.png')"
            type="article"
        />
    @endpush

    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans -mt-20 pt-24 lg:pt-20">
        
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row">
            
            <!-- Left Side: Sticky Visuals (50%) -->
            <div class="lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative bg-white dark:bg-slate-950 z-10 p-4 lg:pl-16 lg:pr-8 lg:pt-12 flex flex-col justify-start">
                 
                 <!-- Breadcrumbs -->
                 <div class="mb-6">
                     <nav class="flex" aria-label="Breadcrumb">
                         <ol class="inline-flex items-center space-x-1 md:space-x-3">
                             <li class="inline-flex items-center">
                                 <a href="{{ route('welcome') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                     <span class="material-symbols-outlined text-lg mr-1">home</span>
                                     {{ __('Nav.Home') }}
                                 </a>
                             </li>
                             <li>
                                 <div class="flex items-center">
                                     <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                     <a href="{{ route('culture.index') }}" wire:navigate class="text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                         {{ __('Nav.Culture') }}
                                     </a>
                                 </div>
                             </li>
                             <li aria-current="page">
                                 <div class="flex items-center">
                                     <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                     <span class="text-sm font-medium text-slate-900 dark:text-white line-clamp-1 max-w-[150px] md:max-w-xs">
                                         {{ $culinary->name }}
                                     </span>
                                 </div>
                             </li>
                         </ol>
                     </nav>
                 </div>

                 <!-- Image Card Wrapper -->
                 <div class="relative w-full max-w-2xl mx-auto aspect-[4/3] rounded-[2.5rem] overflow-hidden group">
                    <!-- Main Image with "Ken Burns" Effect -->
                    <img src="{{ $culinary->image_url }}" alt="{{ $culinary->name }}" class="w-full h-full object-cover transform scale-100 group-hover:scale-110 transition-transform duration-[20s] ease-in-out will-change-transform">
                    
                    <!-- Cinematic Overlays -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/10 opacity-60 transition-opacity duration-700"></div>
                 </div>
            </div>

            <!-- Right Side: Scrollable Content (50%) -->
            <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                <main class="max-w-2xl mx-auto px-5 sm:px-8 py-10 md:py-20 lg:px-16 lg:pt-12 lg:pb-24 stagger-entry">
                    
                    <!-- Header Section -->
                    <div class="mb-10">
                        <!-- Breadcrumbs / Badges -->
                        <div class="flex items-center gap-3 mb-4 text-sm">
                            <span class="px-3 py-1 rounded-full bg-primary/10 dark:bg-blue-900/30 text-primary dark:text-blue-400 font-bold uppercase tracking-wider text-xs border border-primary/20">
                                {{ __('Culinary.Detail.Badge') }}
                            </span>
                        </div>

                        <h1 class="font-playfair text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-[1.2] md:leading-tight mb-6">
                            {{ $culinary->name }}
                        </h1>
                        
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-lg">
                            <span class="material-symbols-outlined text-xl text-primary">restaurant_menu</span>
                            <span>{{ __('Culinary.Detail.Subtitle') }}</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="border-slate-100 dark:border-slate-800 mb-10">

                    <!-- Content Body -->
                    <div class="space-y-12">
                        
                         <!-- Description -->
                         <section>
                             <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                 <span class="w-1 h-6 bg-primary rounded-full"></span>
                                 {{ __('Culinary.Detail.About') }}
                             </h3>
                             <div x-data="{ expanded: false }">
                                 <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed text-justify transition-all duration-300 overflow-hidden"
                                      :class="expanded ? '' : 'line-clamp-3 mask-image-b'">
                                     <div class="whitespace-pre-line">{{ trim($culinary->content ?? $culinary->description) }}</div>
                                 </div>
                                 @if(strlen($culinary->content ?? $culinary->description) > 150)
                                     <button @click="expanded = !expanded" 
                                             class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-primary dark:text-blue-400 hover:text-primary-dark dark:hover:text-blue-300 transition-colors">
                                         <span x-text="expanded ? '{{ __('Culinary.Detail.Hide') }}' : '{{ __('News.Button.ReadMore') }}'"></span>
                                         <span class="material-symbols-outlined text-lg transition-transform duration-300" 
                                               :class="expanded ? 'rotate-180' : ''">expand_more</span>
                                     </button>
                                 @endif
                             </div>
                         </section>

                        <!-- Highlights / Quick Info Grid -->
                        <div class="grid grid-cols-1 gap-4">
                            <div class="p-6 rounded-2xl bg-primary/5 dark:bg-blue-900/10 border border-primary/20 dark:border-blue-800/30">
                                <div class="text-primary text-xs font-bold uppercase tracking-wider mb-2">{{ __('Culinary.Detail.Recommendation') }}</div>
                                <p class="text-slate-800 dark:text-blue-100 font-medium text-sm italic">
                                    "{{ $culinary->description }}"
                                </p>
                            </div>
                        </div>

                        <!-- Location / Map Section -->
                        <section>
                             <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-5 border border-slate-100 dark:border-slate-800">
                                 <div class="flex items-center gap-3 mb-4">
                                     <div class="w-10 h-10 rounded-full bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400">
                                         <span class="material-symbols-outlined text-xl">storefront</span>
                                     </div>
                                     <div>
                                         <h3 class="font-bold text-slate-900 dark:text-white">{{ __('Culinary.Detail.WantToTry') }}</h3>
                                         <p class="text-slate-500 text-xs mt-0.5">{{ __('Culinary.Detail.FindNearby', ['name' => $culinary->name]) }}</p>
                                     </div>
                                 </div>

                                 @if($culinary->locations->isNotEmpty())
                                     <!-- Recommended Locations List -->
                                     <div class="space-y-3 mb-6">
                                         @foreach($culinary->locations as $location)
                                             <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm hover:border-primary/30 transition-colors group">
                                                 <div class="flex items-start gap-3">
                                                     <div class="mt-1 w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-500">
                                                         <span class="material-symbols-outlined text-sm">location_on</span>
                                                     </div>
                                                     <div>
                                                         <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $location->name }}</h4>
                                                         @if($location->address)
                                                             <p class="text-slate-500 text-[11px] leading-tight">{{ $location->address }}</p>
                                                         @endif
                                                     </div>
                                                 </div>
                                                 @if($location->google_maps_url)
                                                     <a href="{{ $location->google_maps_url }}" target="_blank" 
                                                        class="flex items-center gap-1 px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg text-xs font-bold transition-all">
                                                         {{ __('Events.Detail.MapsLink') }}
                                                         <span class="material-symbols-outlined text-xs">open_in_new</span>
                                                     </a>
                                                 @endif
                                             </div>
                                         @endforeach
                                     </div>
                                 @endif
                             </div>
                        </section>
                    </div>

                    <!-- Footer Area -->
                    <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-slate-400 text-sm font-serif italic">{{ __('Culinary.Detail.ShareLabel') }}</span>
                        <x-share-modal :url="request()->url()" :title="$culinary->name" :text="Str::limit(strip_tags($culinary->description), 100)">
                            <button class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all duration-300" title="{{ __('Culinary.Detail.ShareButton') }}">
                                <i class="fa-solid fa-share-nodes text-sm"></i>
                            </button>
                        </x-share-modal>
                    </div>

                </main>
            </div>

            </div>
        </div>
    </div>
</x-public-layout>
