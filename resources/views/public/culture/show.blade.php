<x-public-layout>
    @push('seo')
        <x-seo
            :title="$culture->name . ' - Budaya Jepara'"
            :description="Str::limit(strip_tags($culture->description), 150)"
            :image="$culture->image_url ? $culture->image_url : asset('images/logo-kura.png')"
            type="article"
        />
    @endpush

    @php
        $hideInfoGrid = in_array($culture->category, [
            'Kemahiran & Kerajinan Tradisional (Kriya)',
            'Seni Pertunjukan',
            'Kuliner Khas',
        ]);
        $youtubeId = null;
        if ($culture->youtube_url) {
            preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $culture->youtube_url, $m);
            $youtubeId = $m[1] ?? null;
        }
        $embedUrl = $youtubeId ? 'https://www.youtube.com/embed/' . $youtubeId . '?rel=0&modestbranding=1' : null;
    @endphp

    <div class="bg-white dark:bg-slate-950 min-h-screen font-sans -mt-20 pt-24 lg:pt-20">

        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <div class="flex flex-col lg:flex-row">

                {{-- ═══════════════════════════════════════
                     LEFT: Sticky Visual Panel (50%)
                ═══════════════════════════════════════ --}}
                @php
                    $galleryImages = collect([]);
                    if ($culture->image) {
                        $galleryImages->push(
                            file_exists(public_path('storage/' . $culture->image))
                                ? asset('storage/' . $culture->image)
                                : asset($culture->image)
                        );
                    }
                    foreach($culture->images as $img) {
                        $galleryImages->push(asset('storage/' . $img->image_path));
                    }
                    $galleryImages = $galleryImages->unique()->values();
                @endphp
                <div class="lg:w-1/2 lg:h-screen lg:sticky lg:top-0 relative bg-white dark:bg-slate-950 z-10 flex flex-col lg:overflow-hidden p-4 lg:pl-16 lg:pr-8 lg:pt-12"
                     x-data="{
                        activeImage: '{{ $galleryImages->first() ?? '' }}',
                        isFlipping: false,
                        lightboxOpen: false,
                        lightboxIndex: 0,
                        images: [
                            @foreach($galleryImages as $img)
                                '{{ $img }}',
                            @endforeach
                        ],
                        changeImage(url) {
                            if (this.activeImage === url) return;
                            this.isFlipping = true;
                            setTimeout(() => {
                                this.activeImage = url;
                                this.isFlipping = false;
                            }, 300);
                        },
                        openLightbox(url) {
                            this.lightboxIndex = this.images.indexOf(url);
                            if (this.lightboxIndex === -1) this.lightboxIndex = 0;
                            this.lightboxOpen = true;
                            document.body.style.overflow = 'hidden';
                        },
                        closeLightbox() {
                            this.lightboxOpen = false;
                            document.body.style.overflow = '';
                        },
                        lightboxPrev() {
                            this.lightboxIndex = (this.lightboxIndex - 1 + this.images.length) % this.images.length;
                        },
                        lightboxNext() {
                            this.lightboxIndex = (this.lightboxIndex + 1) % this.images.length;
                        }
                     }"
                     @keydown.escape.window="if (lightboxOpen) closeLightbox()"
                     @keydown.left.window="if (lightboxOpen) lightboxPrev()"
                     @keydown.right.window="if (lightboxOpen) lightboxNext()">

                    {{-- Breadcrumb --}}
                    <div class="mb-6">
                     <nav class="flex" aria-label="Breadcrumb">
                         <ol class="inline-flex items-center space-x-1 md:space-x-3">
                             <li class="inline-flex items-center">
                                 <a href="{{ route('welcome') }}" wire:navigate
                                    class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                     <span class="material-symbols-outlined text-lg mr-1">home</span>
                                     {{ __('Nav.Home') }}
                                 </a>
                             </li>
                             <li>
                                 <div class="flex items-center">
                                     <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                     <a href="{{ route('culture.index') }}" wire:navigate
                                        class="text-sm font-medium text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                         {{ __('Nav.Culture') }}
                                     </a>
                                 </div>
                             </li>
                             <li aria-current="page">
                                 <div class="flex items-center">
                                     <span class="material-symbols-outlined text-slate-400 mx-1">chevron_right</span>
                                     <span class="text-sm font-medium text-slate-900 dark:text-white line-clamp-1 max-w-[150px] md:max-w-xs">
                                         {{ $culture->name }}
                                     </span>
                                 </div>
                             </li>
                         </ol>
                     </nav>
                    </div>

                    {{-- Main Image --}}
                    <div class="relative w-full aspect-[4/3] lg:aspect-auto lg:h-[60vh] overflow-hidden perspective-[1000px]">
                        <div class="relative w-full h-full rounded-3xl overflow-hidden cursor-pointer text-transparent"
                             @click="openLightbox(activeImage)">
                            <template x-if="activeImage">
                                <img :src="activeImage"
                                     alt="{{ $culture->name }}"
                                     class="w-full h-full object-cover transition-all duration-500 ease-in-out transform origin-center"
                                     :class="isFlipping ? '[transform:rotateY(90deg)] opacity-75 scale-95' : '[transform:rotateY(0deg)] opacity-100 scale-100'">
                            </template>
                            <template x-if="!activeImage">
                                <div class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400">
                                    <span class="material-symbols-outlined text-6xl">image</span>
                                </div>
                            </template>
                            {{-- Zoom hint --}}
                            <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-colors flex items-center justify-center">
                                <div class="bg-white/90 backdrop-blur px-4 py-2 rounded-full font-bold text-sm text-slate-700 shadow-lg opacity-0 hover:opacity-100 transition-opacity flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">zoom_in</span>
                                    Perbesar
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnails --}}
                    @if($galleryImages->count() > 1)
                    <div class="w-full px-4 lg:px-6 pb-6 pt-3 flex items-center gap-3 overflow-x-auto scrollbar-hide scroll-smooth">
                        @foreach($galleryImages as $imgUrl)
                            <button @click="changeImage('{{ $imgUrl }}')"
                                    :class="activeImage === '{{ $imgUrl }}' ? 'ring-2 ring-primary scale-105' : 'opacity-70 hover:opacity-100'"
                                    class="relative w-20 h-14 lg:w-24 lg:h-16 flex-shrink-0 rounded-xl overflow-hidden transition-all duration-300">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                    @endif

                    {{-- Lightbox Modal --}}
                    <div x-show="lightboxOpen" x-cloak
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-[9999] flex items-center justify-center"
                         style="position: fixed;">
                        <div class="absolute inset-0 bg-black/95 backdrop-blur-sm" @click="closeLightbox()"></div>

                        {{-- Close --}}
                        <button @click="closeLightbox()"
                                class="absolute top-4 right-4 sm:top-6 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all hover:scale-110">
                            <span class="material-symbols-outlined text-2xl">close</span>
                        </button>

                        {{-- Counter --}}
                        <div class="absolute top-4 left-4 sm:top-6 sm:left-6 z-20 bg-white/10 backdrop-blur-sm text-white text-sm font-medium px-4 py-2 rounded-full">
                            <span x-text="(lightboxIndex + 1) + ' / ' + images.length"></span>
                        </div>

                        {{-- Prev --}}
                        <button x-show="images.length > 1" @click="lightboxPrev()"
                                class="absolute left-2 sm:left-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all hover:scale-110">
                            <span class="material-symbols-outlined text-2xl">chevron_left</span>
                        </button>

                        {{-- Next --}}
                        <button x-show="images.length > 1" @click="lightboxNext()"
                                class="absolute right-2 sm:right-6 z-20 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm flex items-center justify-center text-white transition-all hover:scale-110">
                            <span class="material-symbols-outlined text-2xl">chevron_right</span>
                        </button>

                        {{-- Lightbox Image --}}
                        <div class="relative z-10 w-full h-full max-w-[90vw] max-h-[80vh] flex items-center justify-center pointer-events-none">
                            <img :src="images[lightboxIndex]"
                                 :alt="'{{ $culture->name }} - Foto ' + (lightboxIndex + 1)"
                                 class="w-auto h-auto max-w-full max-h-full object-contain rounded-lg shadow-2xl select-auto pointer-events-auto">
                        </div>

                        {{-- Thumbnail Strip --}}
                        <div x-show="images.length > 1" class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2 bg-black/40 backdrop-blur-sm rounded-2xl p-2 max-w-[90vw] overflow-x-auto scrollbar-hide">
                            <template x-for="(img, idx) in images" :key="idx">
                                <button @click="lightboxIndex = idx"
                                        :class="lightboxIndex === idx ? 'ring-2 ring-white scale-110 opacity-100' : 'opacity-50 hover:opacity-80'"
                                        class="w-14 h-10 sm:w-16 sm:h-11 flex-shrink-0 rounded-lg overflow-hidden transition-all duration-200">
                                    <img :src="img" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>

                </div>{{-- /left --}}


                {{-- ═══════════════════════════════════════
                     RIGHT: Scrollable Content Panel (50%)
                ═══════════════════════════════════════ --}}
                <div class="lg:w-1/2 relative bg-white dark:bg-slate-950">
                    <main class="max-w-3xl mx-auto px-5 sm:px-8 py-10 md:py-16 lg:px-16 lg:pt-12 lg:pb-24">

                        {{-- Category Badge --}}
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            <span class="px-3 py-1 rounded-full bg-primary/5 dark:bg-primary/10 text-primary font-bold uppercase tracking-wider text-xs border border-primary/20">
                                {{ $culture->category }}
                            </span>
                        </div>

                        {{-- Title & Meta --}}
                        <div class="mb-10">
                            <h1 class="font-playfair text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 dark:text-white leading-[1.2] md:leading-tight mb-6">
                                {{ $culture->name }}
                            </h1>
                            @if(!$hideInfoGrid && ($culture->location || $culture->time))
                            <div class="flex flex-wrap gap-4">
                                @if($culture->location)
                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-base">
                                    <span class="material-symbols-outlined text-xl flex-shrink-0 text-primary">location_on</span>
                                    <span class="font-light">{{ $culture->location }}</span>
                                </div>
                                @endif
                                @if($culture->time)
                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-base">
                                    <span class="material-symbols-outlined text-xl flex-shrink-0 text-primary">event</span>
                                    <span class="font-light">{{ $culture->time }}</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        <hr class="border-slate-100 dark:border-slate-800 mb-10">

                        {{-- Content Body --}}
                        <div class="space-y-12">

                             {{-- Konten Lengkap --}}
                             @if($culture->content || $culture->description)
                             <section>
                                 <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-4 flex items-center gap-3">
                                     <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                                     Tentang Budaya Ini
                                 </h3>
                                 <div x-data="{ expanded: false }">
                                     <div class="prose prose-lg prose-slate dark:prose-invert font-light text-slate-600 dark:text-slate-300 leading-relaxed text-justify transition-all duration-300 overflow-hidden"
                                          :class="expanded ? '' : 'line-clamp-3 mask-image-b'">
                                         <div class="whitespace-pre-line">{{ trim($culture->content ?? $culture->description) }}</div>
                                     </div>
                                     @if(strlen($culture->content ?? $culture->description) > 150)
                                         <button @click="expanded = !expanded" 
                                                 class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-primary dark:text-blue-400 hover:text-primary-dark dark:hover:text-blue-300 transition-colors">
                                             <span x-text="expanded ? '{{ __('Culinary.Detail.Hide') }}' : '{{ __('News.Button.ReadMore') }}'"></span>
                                             <span class="material-symbols-outlined text-lg transition-transform duration-300" 
                                                   :class="expanded ? 'rotate-180' : ''">expand_more</span>
                                         </button>
                                     @endif
                                 </div>
                             </section>
                             @endif

                            {{-- Info Grid: Kategori, Waktu, Lokasi --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Kategori --}}
                                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">Kategori</div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary">
                                            <span class="material-symbols-outlined text-xl">category</span>
                                        </div>
                                        <span class="text-slate-900 dark:text-white font-semibold text-sm leading-snug">{{ $culture->category }}</span>
                                    </div>
                                </div>

                                @if(!$hideInfoGrid)
                                    @if($culture->time)
                                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">Waktu Pelaksanaan</div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary">
                                                <span class="material-symbols-outlined text-xl">event</span>
                                            </div>
                                            <span class="text-slate-900 dark:text-white font-semibold text-sm">{{ $culture->time }}</span>
                                        </div>
                                    </div>
                                    @endif
                                    @if($culture->location)
                                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors">
                                        <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-3">Lokasi</div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex-shrink-0 flex items-center justify-center text-primary">
                                                <span class="material-symbols-outlined text-xl">location_on</span>
                                            </div>
                                            <span class="text-slate-900 dark:text-white font-semibold text-sm">{{ $culture->location }}</span>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>

                            {{-- YouTube Video --}}
                            @if($embedUrl)
                            <section>
                                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-red-500 rounded-full"></span>
                                    Video
                                </h3>
                                <div class="relative w-full h-0 rounded-2xl overflow-hidden bg-black shadow-md" style="padding-bottom: 56.25%;">
                                    <iframe src="{{ $embedUrl }}"
                                            class="absolute inset-0 w-full h-full"
                                            frameborder="0" allowfullscreen
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                    </iframe>
                                </div>
                            </section>
                            @endif

                            {{-- Share --}}
                            <section class="p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-white dark:from-slate-900 dark:to-slate-950 border border-slate-200 dark:border-slate-800">
                                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3">Bagikan</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                                    Ceritakan warisan budaya <strong class="text-slate-700 dark:text-slate-200">{{ $culture->name }}</strong> kepada orang-orang di sekitar Anda.
                                </p>
                                <x-share-modal :url="request()->url()" :title="$culture->name" :text="Str::limit(strip_tags($culture->description), 100)">
                                    <button class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 active:scale-95 text-white font-bold py-3 px-6 rounded-xl shadow-md shadow-primary/20 transition-all text-sm">
                                        <i class="fa-solid fa-share-nodes"></i>
                                        Bagikan Sekarang
                                    </button>
                                </x-share-modal>
                            </section>

                        </div>

                        {{-- Footer --}}
                        <div class="mt-20 pt-10 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <a href="{{ route('culture.index') }}" wire:navigate
                               class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary dark:text-slate-400 dark:hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-base">arrow_back</span>
                                Kembali ke Daftar Budaya
                            </a>
                            <p class="text-slate-400 text-xs">© {{ date('Y') }} Jelajah Jepara</p>
                        </div>

                    </main>
                </div>{{-- /right --}}

            </div>
        </div>
    </div>

</x-public-layout>
