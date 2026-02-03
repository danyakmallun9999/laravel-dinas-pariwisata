<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen -mt-20 pt-24">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            
            <!-- Breadcrumb -->
            <nav class="flex justify-center text-xs md:text-sm text-gray-500 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('posts.index') }}" class="text-gray-400 hover:text-primary transition-colors">Berita</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 truncate max-w-[200px]">{{ $post->title }}</span>
            </nav>

            <!-- Header Section -->
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-black text-gray-900 dark:text-white leading-tight mb-6 tracking-tight font-serif">
                    {{ $post->title }}
                </h1>
                
                <div class="flex flex-col md:flex-row items-center justify-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8 mb-8">
                    <!-- Author Info -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" class="w-6 h-6 object-contain" alt="Admin">
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $post->author ?? 'Dinas Pariwisata dan Kebudayaan Jepara' }}</p>
                            <p class="text-xs text-gray-500">
                                Published {{ $post->published_at ? $post->published_at->format('M d, Y') : '-' }} • {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
                            </p>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="hidden md:block w-px h-8 bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Share Buttons -->
                    <!-- Share Buttons -->
                    <div class="flex items-center gap-3" x-data="{
                        share() {
                            if (navigator.share) {
                                navigator.share({
                                    title: '{{ $post->title }}',
                                    text: '{{ Str::limit(strip_tags($post->content), 100) }}',
                                    url: window.location.href,
                                })
                                .catch(console.error);
                            } else {
                                navigator.clipboard.writeText(window.location.href);
                                alert('Link telah disalin ke clipboard!');
                            }
                        }
                    }">
                        <span class="text-sm text-gray-500 mr-2 hidden md:inline">Share:</span>
                        <button @click="share()" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Bagikan artikel ini">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hero Image -->
            <div class="relative w-full aspect-video md:aspect-[21/9] rounded-2xl overflow-hidden mb-12 shadow-2xl">
                <img src="{{ $post->image_path }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent pointer-events-none"></div>
                <!-- Image Credit -->
                @if($post->image_credit)
                <div class="absolute bottom-4 right-4 px-3 py-1 bg-black/50 backdrop-blur-sm rounded-full text-[10px] text-white/80">
                    Photo: {{ $post->image_credit }}
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Main Content (Left) -->
                <div class="lg:col-span-8">
                    <!-- Intro Blockquote -->


                    <!-- Article Body -->
                    <article class="tinymce-content text-gray-700 dark:text-gray-300">
                        {!! $post->content !!}
                    </article>

                    <!-- Tags -->
                    <div class="mt-12 flex flex-wrap gap-2">
                        <span class="text-sm font-bold text-gray-400 mr-2">Tags:</span>
                        <a href="#" class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full hover:bg-primary hover:text-white transition-colors">#Jepara</a>
                        <a href="#" class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full hover:bg-primary hover:text-white transition-colors">#Pariwisata</a>
                        <a href="#" class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full hover:bg-primary hover:text-white transition-colors">#{{ $post->type }}</a>
                    </div>
                </div>

                <!-- Sidebar (Right) -->
                <div class="lg:col-span-4 space-y-12">
                    
                    <!-- Related News Widget -->
                    <div class="bg-white dark:bg-surface-dark rounded-2xl p-6 border border-gray-100 dark:border-gray-800 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">feed</span>
                            Related News
                        </h3>
                        <div class="space-y-6">
                            @foreach($relatedPosts as $related)
                            <a href="{{ route('posts.show', $related) }}" class="group flex gap-4 items-start">
                                <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                    <img src="{{ $related->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                </div>
                                <div>
                                    <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wider block mb-1">
                                        {{ $related->published_at ? $related->published_at->format('M d, Y') : '' }}
                                    </span>
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-primary transition-colors line-clamp-2">
                                        {{ $related->title }}
                                    </h4>
                                </div>
                            </a>
                            @endforeach
                            @if($relatedPosts->isEmpty())
                                <p class="text-sm text-gray-500 italic">Belum ada berita terkait lainnya.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Must Visit Widget -->
                    <div class="bg-blue-50 dark:bg-blue-900/10 rounded-2xl p-6 border border-blue-100 dark:border-blue-900/20">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">explore</span>
                            Must Visit
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($recommendedPlaces as $place)
                            @if(!$place->slug) @continue @endif
                            <div class="relative group rounded-xl overflow-hidden aspect-[16/9] shadow-md">
                                <img src="{{ $place->image_path }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <h4 class="text-white font-bold text-lg leading-tight mb-0.5">{{ $place->name }}</h4>
                                    <p class="text-white/80 text-xs">{{ $place->category?->name }}</p>
                                </div>
                                <a href="{{ route('places.show', $place) }}" class="absolute inset-0 z-10"></a>
                            </div>
                            @endforeach
                        </div>
                        
                        <a href="{{ route('explore.map') }}" class="mt-6 block w-full py-3 text-center text-sm font-bold text-blue-600 hover:text-blue-700 hover:bg-blue-100/50 rounded-xl transition-colors">
                            View Full Map
                        </a>
                    </div>

                    <!-- Newsletter / Ad Mockup -->
                    <div class="rounded-2xl overflow-hidden relative aspect-square bg-gray-900 flex items-center justify-center text-center p-6 group">
                         <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=600&q=80" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700">
                         <div class="relative z-10">
                             <h4 class="text-white font-serif text-2xl font-bold mb-2">Explore Jepara</h4>
                             <p class="text-white/80 text-sm mb-4">Discover hidden gems and local culture.</p>
                             <button class="px-6 py-2 bg-white text-gray-900 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-primary hover:text-white transition-colors">
                                 Start Journey
                             </button>
                         </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-public-layout>
