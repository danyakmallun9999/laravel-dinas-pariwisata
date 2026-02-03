<x-public-layout>
    <div class="bg-white dark:bg-background-dark min-h-screen -mt-20 pt-36">
        <!-- Main Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            
            <!-- Header -->
            <div class="text-center mb-16">
                <span class="text-primary font-bold tracking-widest uppercase text-sm mb-2 block">{{ __('News.Header.Department') }}</span>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                    {{ __('News.Header.Title') }}
                </h1>
                <p class="text-gray-500 max-w-2xl mx-auto text-lg">
                    {{ __('News.Header.Subtitle') }}
                </p>
            </div>

            @if($featuredPost)
            <!-- Featured Post -->
            <div class="mb-16">
                <a href="{{ route('posts.show', $featuredPost) }}" class="group block relative rounded-3xl overflow-hidden shadow-2xl aspect-[21/9] md:aspect-[2/1]">
                    <img src="{{ $featuredPost->image_path }}" alt="{{ $featuredPost->title }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-8 md:p-12 lg:p-16">
                        <div class="max-w-4xl">
                            <span class="inline-block px-3 py-1 mb-4 text-xs font-bold text-white uppercase tracking-wider rounded-full bg-primary/90 backdrop-blur-sm">
                                {{ __('News.FeaturedBadge') }}
                            </span>
                            <h2 class="text-3xl md:text-5xl font-black text-white leading-tight mb-4 group-hover:text-primary transition-colors duration-300">
                                {{ $featuredPost->title }}
                            </h2>
                            <p class="text-gray-200 text-lg md:text-xl line-clamp-2 mb-6 max-w-2xl">
                                {{ Str::limit(strip_tags($featuredPost->content), 150) }}
                            </p>
                            
                            <div class="flex items-center gap-4 text-white/80 text-sm">
                                <span class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
                                        <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" class="w-4 h-4 object-contain">
                                    </div>
                                    {{ __('News.Author.Admin') }}
                                </span>
                                <span>•</span>
                                <span>{{ $featuredPost->published_at ? $featuredPost->published_at->format('M d, Y') : '' }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            <!-- Post Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                @foreach($posts as $post)
                <article class="group flex flex-col h-full">
                    <a href="{{ route('posts.show', $post) }}" class="block overflow-hidden rounded-2xl aspect-[4/3] mb-5 relative shadow-md">
                        <img src="{{ $post->image_path }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute bottom-3 right-3 px-2 py-1 bg-black/50 backdrop-blur-md rounded text-xs text-white">
                            {{ __('News.ReadTime') }}
                        </div>
                    </a>
                    
                    <div class="flex-1 flex flex-col">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ $post->type == 'event' ? __('News.Type.Agenda') : __('News.Type.News') }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium">
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight mb-3 group-hover:text-primary transition-colors">
                            <a href="{{ route('posts.show', $post) }}">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed line-clamp-3 mb-4 flex-1">
                            {{ Str::limit(strip_tags($post->content), 120) }}
                        </p>
                        
                        <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center text-sm font-bold text-primary group-hover:underline decoration-2 underline-offset-4">
                            {{ __('News.Button.ReadMore') }}
                            <span class="material-symbols-outlined text-base ml-1 transition-transform group-hover:translate-x-1">arrow_forward</span>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-16 border-t border-gray-100 dark:border-gray-800 pt-8">
                {{ $posts->links() }}
            </div>

        </div>
    </div>
</x-public-layout>
