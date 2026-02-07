    <!-- SECTION: News -->
    <div class="w-full bg-surface-light/30 dark:bg-surface-dark/20 py-10 lg:py-16 scroll-mt-20 border-t border-surface-light dark:border-surface-dark transition-colors duration-200"
        id="news">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-0 mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-text-light dark:text-text-dark">{{ __('News.Title') }}</h2>
                <a class="text-primary font-bold hover:underline flex items-center gap-1 self-start md:self-auto"
                    href="{{ route('posts.index') }}">
                    {{ __('View All') }} <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @foreach($posts as $post)
                <article class="news-card bg-background-light dark:bg-surface-dark rounded-[2rem] overflow-hidden shadow-sm hover:shadow-lg transition-all group flex flex-col h-full border border-surface-light dark:border-white/5">
                    <div class="h-48 overflow-hidden relative">
                        <div class="absolute top-3 left-3 {{ $post->type == 'event' ? 'bg-purple-600' : 'bg-blue-600' }} text-white text-xs font-bold px-3 py-1 rounded-full z-10 uppercase">
                            {{ $post->type == 'event' ? __('News.Type.Agenda') : __('News.Type.News') }}
                        </div>
                        <img alt="{{ $post->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            src="{{ $post->image_path }}" />
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 text-xs text-text-light/50 dark:text-text-dark/50 mb-2">
                            <span class="material-symbols-outlined text-sm">calendar_today</span>
                            <span>{{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-text-light dark:text-text-dark mb-2 leading-tight line-clamp-2">
                            {{ $post->title }}
                        </h3>
                        <p class="text-text-light/70 dark:text-text-dark/70 text-sm mb-4 line-clamp-2">
                            {{ Str::limit(strip_tags($post->content), 100) }}
                        </p>
                        <div class="mt-auto">
                            <a class="text-primary font-bold text-sm hover:underline" href="{{ route('posts.show', $post) }}">{{ __('News.Button.ReadMore') }}</a>
                        </div>
                    </div>
                </article>
                @endforeach
                
                @if($posts->isEmpty())
                    <div class="col-span-full text-center py-10 text-gray-500">
                        {{ __('News.Empty') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.registerPlugin(ScrollTrigger);

            const newsCards = document.querySelectorAll('.news-card');
            const newsSection = document.getElementById('news');
            
            // Set initial state via GSAP (faster than CSS)
            gsap.set(newsCards, { opacity: 0, y: 25 });

            // Trigger animation when section enters viewport
            ScrollTrigger.create({
                trigger: newsSection,
                start: "top bottom-=50",
                onEnter: () => {
                    gsap.to(newsCards, {
                        opacity: 1,
                        y: 0,
                        duration: 0.35,
                        stagger: 0.06,
                        ease: "power1.out"
                    });
                }
            });
        });
    </script>
    <!-- END SECTION: News -->
