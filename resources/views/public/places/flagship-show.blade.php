<x-public-layout>
    @push('seo')
        <x-seo 
            :title="$place->translated_name . ' - Destinasi Unggulan Jepara'"
            :description="$place->translated_description ? Str::limit(strip_tags($place->translated_description), 150) : 'Jelajahi ' . $place->translated_name . ' di Jepara.'"
            :image="$place->image_path ? asset($place->image_path) : asset('images/logo-kura.png')"
            type="article"
        />
    @endpush

    @push('styles')
        <style>
            /* Hide scrollbar for horizontal scroll areas */
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

            /* Package card stagger entrance */
            @keyframes slide-up-fade {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-slide-up { animation: slide-up-fade 0.4s ease-out forwards; opacity: 0; }

            /* GPU-accelerated hero background */
            .hero-bg { will-change: transform; transform: translateZ(0); }

            /* Isolate card repaints from affecting the rest of the page */
            .agency-card { contain: layout style paint; }
        </style>
    @endpush

    <div class="bg-slate-50 dark:bg-slate-950 font-sans -mt-20">
        
        {{-- ============================================================ --}}
        {{-- 1. HERO SECTION --}}
        {{-- ============================================================ --}}
        <section class="relative w-full h-[80vh] min-h-[600px] flex items-center justify-center overflow-hidden">
            {{-- Background Image (GPU-composited) --}}
            <div class="absolute inset-0 z-0 hero-bg"
                 style="background-image: url('{{ $place->image_path ? asset($place->image_path) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80' }}'); background-position: center; background-size: cover;">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-900/90"></div>
            </div>

            {{-- Hero Content --}}
            <div class="relative z-10 text-center px-4 max-w-5xl mx-auto mt-20">
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-white/15 border border-white/20 text-white text-sm font-semibold tracking-wide uppercase mb-6 animate-fade-in-up">
                    <span class="material-symbols-outlined text-amber-400 text-sm">workspace_premium</span>
                    Destinasi Unggulan
                </span>
                
                <h1 class="font-playfair text-5xl md:text-7xl lg:text-8xl font-bold text-white leading-tight mb-6 animate-fade-in-up drop-shadow-lg" style="animation-delay: 200ms;">
                    {{ $place->translated_name }}
                </h1>
                
                <p class="text-lg md:text-xl text-slate-100/90 font-light max-w-3xl mx-auto leading-relaxed mb-10 animate-fade-in-up drop-shadow" style="animation-delay: 400ms;">
                    {{ Str::limit(strip_tags($place->translated_description), 150) }}
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up" style="animation-delay: 600ms;">
                    <a href="#biro" class="px-8 py-4 rounded-full bg-primary text-white font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/30 flex items-center gap-2">
                        <span class="material-symbols-outlined">sailing</span>
                        Lihat Trip & Biro Wisata
                    </a>
                    <a href="#tentang" class="px-8 py-4 rounded-full bg-white/15 border border-white/30 text-white font-bold hover:bg-white/25 transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined">info</span>
                        Tentang Destinasi
                    </a>
                </div>
            </div>

            {{-- Scroll indicator --}}
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 animate-bounce">
                <span class="material-symbols-outlined text-white text-3xl opacity-70">keyboard_double_arrow_down</span>
            </div>
        </section>

        {{-- ============================================================ --}}
        {{-- MAIN CONTENT CONTAINER --}}
        {{-- ============================================================ --}}
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 -mt-10 relative z-20">
            
            {{-- ======================================================== --}}
            {{-- 2. QUICK STATS CARDS --}}
            {{-- ======================================================== --}}
            @php
                $stats = [
                    ['icon' => 'verified',  'color' => 'blue',    'value' => $agencies->total(), 'label' => 'Biro Terdaftar'],
                    ['icon' => 'tour',      'color' => 'emerald', 'value' => count($place->rides ?? []), 'label' => 'Spot Wisata'],
                    ['icon' => 'favorite',  'color' => 'rose',    'value' => '4.8',              'label' => 'Rating Rata-rata'],
                    ['icon' => 'sailing',   'color' => 'purple',  'value' => 'Setiap Hari',      'label' => 'Penyeberangan'],
                ];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-20">
                @foreach($stats as $stat)
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-lg shadow-slate-200/10 dark:shadow-none border border-slate-100 dark:border-slate-800 text-center flex flex-col items-center justify-center group hover:-translate-y-1 transition-transform duration-200">
                    <div class="w-12 h-12 rounded-full bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-900/30 text-{{ $stat['color'] }}-500 mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">{{ $stat['icon'] }}</span>
                    </div>
                    <h3 class="font-bold text-2xl text-slate-900 dark:text-white">{{ $stat['value'] }}</h3>
                    <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- ======================================================== --}}
            {{-- 3. ABOUT DESTINATION --}}
            {{-- ======================================================== --}}
            <section id="tentang" class="mb-24 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center scroll-mt-24">
                <div class="relative rounded-3xl overflow-hidden aspect-square lg:aspect-auto lg:h-[600px] shadow-xl">
                    <img src="{{ $place->image_path ? asset($place->image_path) : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?ixlib=rb-4.0.3' }}" class="w-full h-full object-cover" loading="lazy" alt="{{ $place->translated_name }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent flex items-end p-8">
                        <div>
                            <span class="text-white/80 text-sm font-semibold tracking-wider uppercase mb-2 block">Tentang Destinasi</span>
                            <h3 class="text-2xl font-bold text-white leading-tight">Keindahan Tersembunyi di Utara Jawa</h3>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="font-playfair text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-6 leading-tight">
                        Mengapa Harus ke <br><span class="text-primary">{{ $place->translated_name }}?</span>
                    </h2>
                    <div class="prose prose-lg prose-slate dark:prose-invert text-slate-600 dark:text-slate-300 mb-8">
                        {!! nl2br(e($place->translated_description)) !!}
                    </div>
                    
                    @if($place->facilities)
                    <div class="grid grid-cols-2 gap-6">
                        @foreach(array_slice($place->facilities, 0, 4) as $facility)
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 p-2 rounded-lg">check</span>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 mt-1">{{ $facility }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </section>

            {{-- ======================================================== --}}
            {{-- 4. TIMELINE ROUTE SECTION --}}
            {{-- ======================================================== --}}
            @php
                $timelineSteps = [
                    ['emoji' => '🏙️', 'title' => 'Kota Jepara',       'desc' => 'Berangkat dari Pelabuhan Kartini', 'badge' => 'Start',  'badgeBg' => 'bg-[#e0f1f1] text-[#2c787b] dark:bg-teal-900/80 dark:text-teal-300',                                  'hoverBorder' => 'group-hover:border-teal-400'],
                    ['emoji' => '⚓',    'title' => 'Boarding Kapal',    'desc' => 'Check-in Express/Ferry',           'badge' => '06:00',  'badgeBg' => 'bg-white dark:bg-slate-800 text-[#0277bd] dark:text-sky-400 border border-sky-100 dark:border-sky-800', 'hoverBorder' => 'group-hover:border-sky-400'],
                    ['emoji' => '🚢',    'title' => 'Mengarungi Laut',   'desc' => 'Menikmati Laut Jawa',              'badge' => '2-3 jam','badgeBg' => 'bg-[#e0f7fa] text-[#0277bd] dark:bg-sky-900/80 dark:text-sky-300',                                      'hoverBorder' => 'group-hover:border-blue-400'],
                    ['emoji' => '🏝️',   'title' => 'Pulau Utama',       'desc' => 'Tiba di pelabuhan tujuan',          'badge' => '09:00',  'badgeBg' => 'bg-white dark:bg-slate-800 text-[#0277bd] dark:text-sky-400 border border-sky-100 dark:border-sky-800', 'hoverBorder' => 'group-hover:border-emerald-400'],
                    ['emoji' => '🏨',    'title' => 'Check-in Hotel',    'desc' => 'Istirahat & Persiapan',             'badge' => '10:00',  'badgeBg' => 'bg-white dark:bg-slate-800 text-[#0277bd] dark:text-sky-400 border border-sky-100 dark:border-sky-800', 'hoverBorder' => 'group-hover:border-purple-400'],
                    ['emoji' => '🎉',    'title' => 'Mulai Eksplorasi',  'desc' => 'Jelajahi surga tersembunyi!',       'badge' => 'Next',   'badgeBg' => 'bg-[#e0f1f1] text-[#2c787b] dark:bg-teal-900/80 dark:text-teal-300',                                  'hoverBorder' => 'group-hover:bg-teal-50 dark:group-hover:bg-teal-900/20'],
                ];
            @endphp
            <section class="mb-24 relative bg-[#ebf6f6] dark:bg-slate-900/80 overflow-hidden py-20 px-4 sm:px-6 lg:px-8 rounded-[2.5rem] border border-teal-50 dark:border-slate-800">
                {{-- Decorative Horizontal Wavy Line Background --}}
                <svg class="absolute w-[200%] sm:w-[150%] lg:w-full h-[60%] lg:h-[80%] top-[20%] lg:top-[10%] left-1/2 -translate-x-1/2 opacity-30 dark:opacity-15 pointer-events-none" viewBox="0 0 1000 200" preserveAspectRatio="none">
                    <path d="M -250,100 Q -125,160 0,100 T 250,100 T 500,100 T 750,100 T 1000,100 T 1250,100" stroke="#0ea5e9" stroke-width="1.5" stroke-dasharray="6 8" fill="none" />
                    <path d="M -250,100 Q -125,40 0,100 T 250,100 T 500,100 T 750,100 T 1000,100 T 1250,100" stroke="#10b981" stroke-width="1" stroke-dasharray="4 6" fill="none" opacity="0.6"/>
                </svg>

                <div class="text-center w-full mx-auto mb-16 relative z-10 overflow-hidden px-2">
                    <h2 class="font-bold text-[28px] sm:text-3xl md:text-4xl text-[#1a3641] dark:text-white mb-4 flex flex-row items-center justify-center gap-2 sm:gap-3 font-display flex-nowrap whitespace-nowrap">
                        Jepara <span class="material-symbols-outlined text-teal-600/60 font-light text-3xl sm:text-4xl">east</span> Karimunjawa
                    </h2>
                    <p class="text-teal-900/60 dark:text-slate-400 font-medium text-sm sm:text-base">Perjalanan menuju surga tropis hanya 2-3 jam dari Pelabuhan Kartini, Jepara</p>
                </div>

                <div class="relative w-full max-w-6xl mx-auto pb-6 px-4">
                    {{-- Desktop Horizontal Line --}}
                    <div class="hidden lg:block absolute left-[8%] right-[8%] top-6 h-[2px] bg-teal-600/30 dark:bg-teal-800/50 rounded-full z-0"></div>
                    {{-- Mobile Vertical Line --}}
                    <div class="lg:hidden absolute left-[39px] top-6 bottom-6 w-[2px] bg-teal-600/30 dark:bg-teal-800/50 rounded-full z-0"></div>
                    
                    <div class="flex flex-col lg:flex-row justify-between relative z-10 gap-8 lg:gap-4">
                        @foreach($timelineSteps as $i => $step)
                        <div class="flex flex-row lg:flex-col items-start lg:items-center relative group w-full lg:w-1/6">
                            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-white dark:bg-slate-800 ring-[4px] ring-[#ebf6f6] dark:ring-slate-900 shadow-sm flex items-center justify-center shrink-0 z-20 mb-0 lg:mb-4 relative border-2 {{ $i === count($timelineSteps) - 1 ? 'border-teal-400 dark:border-teal-600' : 'border-teal-100 dark:border-teal-900/50' }} group-hover:scale-110 {{ $step['hoverBorder'] }} transition-all cursor-default">
                                <span class="text-xl lg:text-2xl">{{ $step['emoji'] }}</span>
                                <span class="absolute -top-3 -right-2 lg:-top-3 lg:-right-4 px-2 py-0.5 {{ $step['badgeBg'] }} text-[10px] font-bold rounded-full shadow-sm">{{ $step['badge'] }}</span>
                            </div>
                            <div class="ml-4 lg:ml-0 text-left lg:text-center pt-2 lg:pt-0">
                                <h4 class="font-bold text-[#1a3641] dark:text-white text-sm lg:text-[15px] mb-1 leading-tight">{{ $step['title'] }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-snug">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ======================================================== --}}
            {{-- 5. TRAVEL AGENCIES DIRECTORY --}}
            {{-- ======================================================== --}}
            <section id="biro" class="mb-24 scroll-mt-24" x-data="{
                showModal: false,
                activeAgency: null,
                agenciesData: {{ Js::from($agencies->items()) }},
                openModal(id) {
                    this.activeAgency = this.agenciesData.find(a => a.id === id);
                    if (this.activeAgency) {
                        this.showModal = true;
                        document.body.style.overflow = 'hidden';
                    }
                },
                closeModal() {
                    this.showModal = false;
                    setTimeout(() => this.activeAgency = null, 300);
                    document.body.style.overflow = '';
                }
            }" @keydown.escape.window="if(showModal) closeModal()">

                {{-- Section Header --}}
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-primary font-bold tracking-wider uppercase text-sm mb-2 block">Rencanakan Liburan Anda</span>
                    <h2 class="font-playfair text-3xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4">Direktori Biro Wisata</h2>
                    <p class="text-slate-500 dark:text-slate-400">Pilih dari puluhan operator wisata bersertifikat yang siap mengantarkan Anda menjelajah surga tersembunyi dengan aman dan nyaman.</p>
                </div>

                {{-- Search Form --}}
                <form action="{{ route('places.show', $place->slug) }}#biro" method="GET" class="mb-10 max-w-xl mx-auto">
                    <div class="relative" x-data="{ query: '{{ $search ?? '' }}' }">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
                        <input type="text" name="search" x-model="query" value="{{ $search ?? '' }}" placeholder="Cari nama atau deskripsi biro wisata..." class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-full py-4 pl-14 pr-24 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary shadow-sm transition-shadow focus:shadow-md" autocomplete="off" @keydown.escape="query = ''; $el.value = ''">
                        {{-- Clear button --}}
                        <button type="button" x-show="query.length > 0" x-cloak @click="query = ''; $refs.searchInput?.focus(); window.location.href='{{ route('places.show', $place->slug) }}#biro'" class="absolute right-14 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-slate-600 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-2 bg-primary text-white rounded-full hover:bg-primary/90 transition-colors text-sm font-semibold flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">search</span>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                </form>

                {{-- Active Search Indicator --}}
                @if($search)
                <div class="mb-6 text-center">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-medium">
                        <span class="material-symbols-outlined text-sm">filter_alt</span>
                        Hasil pencarian: "{{ $search }}"
                        <a href="{{ route('places.show', $place->slug) }}#biro" class="ml-1 hover:text-primary/70 transition-colors">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </a>
                    </span>
                </div>
                @endif

                {{-- Agency Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($agencies as $agency)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-lg hover:border-primary/30 transition-[box-shadow,border-color] duration-200 group flex flex-col h-full agency-card">
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0 relative">
                                        @if($agency->logo_path)
                                            <img src="{{ asset($agency->logo_path) }}" class="w-full h-full object-cover" loading="lazy" alt="{{ $agency->name }}">
                                        @else
                                            <span class="material-symbols-outlined text-slate-400 text-3xl">store</span>
                                        @endif

                                    </div>
                                    <div class="min-w-0">
                                        <button type="button" @click="openModal({{ $agency->id }})" class="text-left w-full">
                                            <h3 class="font-bold text-lg text-slate-900 dark:text-white hover:text-primary transition-colors line-clamp-1">{{ $agency->name }}</h3>
                                        </button>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                <span class="material-symbols-outlined text-[10px]">verified</span> Terverifikasi
                                            </span>
                                            @if($agency->business_type)
                                                <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                    {{ $agency->business_type }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($agency->address)
                                            <p class="text-xs text-slate-500 dark:text-slate-500 mt-1.5 flex items-center gap-1 line-clamp-1">
                                                <span class="material-symbols-outlined text-[12px]">location_on</span> {{ $agency->address }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3 flex-1">
                                    {{ $agency->description ?? 'Biro wisata yang siap melayani perjalanan Anda.' }}
                                </p>
                                
                                <div class="flex flex-wrap gap-2 mt-auto">
                                    @if($agency->contact_wa)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agency->contact_wa) }}" target="_blank" class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-colors" title="WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    @if($agency->instagram)
                                        <a href="{{ str_contains($agency->instagram, 'http') ? $agency->instagram : 'https://instagram.com/'.str_replace('@', '', $agency->instagram) }}" target="_blank" class="w-8 h-8 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center hover:bg-pink-500 hover:text-white transition-colors" title="Instagram">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                    @endif
                                    @if($agency->website)
                                        <a href="{{ $agency->website }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-500 hover:text-white transition-colors" title="Website">
                                            <span class="material-symbols-outlined text-sm">language</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- View Details Button --}}
                            <div class="border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 mt-auto">
                                <button type="button" @click="openModal({{ $agency->id }})" class="w-full flex items-center justify-between p-4 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-primary transition-colors group/btn">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                        Lihat Detail Biro
                                    </div>
                                    <span class="material-symbols-outlined transition-transform duration-300 group-hover/btn:translate-x-1">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-slate-50 dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
                            <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-3 block">store_off</span>
                            <h3 class="font-bold text-slate-700 dark:text-slate-300">{{ $search ? 'Tidak ada hasil untuk "'.$search.'"' : 'Belum ada biro wisata' }}</h3>
                            <p class="text-slate-500 text-sm mt-1">{{ $search ? 'Coba kata kunci yang berbeda.' : 'Data biro wisata belum tersedia untuk destinasi ini.' }}</p>
                            @if($search)
                                <a href="{{ route('places.show', $place->slug) }}#biro" class="inline-flex items-center gap-1 mt-4 text-sm text-primary font-semibold hover:underline">
                                    <span class="material-symbols-outlined text-sm">arrow_back</span> Tampilkan Semua Biro
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($agencies->hasPages())
                    <div class="mt-12">
                        {{ $agencies->links() }}
                    </div>
                @endif

                {{-- ================================================== --}}
                {{-- AGENCY DETAIL MODAL (teleported to body) --}}
                {{-- ================================================== --}}
                <template x-teleport="body">
                <div x-show="showModal" 
                     class="relative z-[9999]" 
                     aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                     
                    {{-- Backdrop --}}
                    <div x-show="showModal" 
                         x-transition:enter="ease-out duration-300" 
                         x-transition:enter-start="opacity-0" 
                         x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-slate-900/80 transition-opacity" 
                         @click="closeModal()"></div>

                    {{-- Scrollable Wrapper --}}
                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            
                            {{-- Panel --}}
                            <div x-show="showModal" 
                                 @click.away="closeModal()"
                                 x-transition:enter="ease-out duration-300" 
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave="ease-in duration-200" 
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all w-full sm:my-8 sm:max-w-5xl border border-slate-100 dark:border-slate-800">
                                
                                {{-- Header --}}
                                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900 z-20">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden shrink-0">
                                            <template x-if="activeAgency?.logo_path">
                                                <img :src="activeAgency.logo_path.startsWith('http') ? activeAgency.logo_path : ('/' + activeAgency.logo_path)" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!activeAgency?.logo_path">
                                                <span class="material-symbols-outlined text-sm text-slate-400">store</span>
                                            </template>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-xl font-bold text-slate-900 dark:text-white truncate" id="modal-title" x-text="activeAgency?.name"></h3>
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                                <span class="material-symbols-outlined text-[10px]">verified</span> Terverifikasi
                                            </span>
                                        </div>
                                    </div>
                                    <button @click="closeModal()" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none rounded-xl p-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shrink-0 ml-4" title="Tutup (Esc)">
                                        <span class="material-symbols-outlined">close</span>
                                    </button>
                                </div>
                                
                                {{-- Body --}}
                                <div class="px-6 py-6">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                        {{-- Sidebar --}}
                                        <div class="md:col-span-1 space-y-4">
                                            <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-xl border border-slate-100 dark:border-slate-700">
                                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[18px]">info</span> Tentang Biro
                                                </h4>
                                                <p class="text-slate-600 dark:text-slate-400 text-sm whitespace-pre-line leading-relaxed" x-text="activeAgency?.description || 'Tidak ada deskripsi.'"></p>
                                            </div>

                                            {{-- Informasi Usaha --}}
                                            <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-xl border border-slate-100 dark:border-slate-700">
                                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[18px]">badge</span> Informasi Usaha
                                                </h4>
                                                <div class="space-y-3">
                                                    <template x-if="activeAgency?.owner_name">
                                                        <div class="flex items-start gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0">
                                                                <span class="material-symbols-outlined text-[16px] text-slate-500">person</span>
                                                            </div>
                                                            <div>
                                                                <p class="text-[11px] uppercase tracking-wider text-slate-400 dark:text-slate-500 font-medium">Pemilik</p>
                                                                <p class="text-sm text-slate-700 dark:text-slate-300 font-medium" x-text="activeAgency.owner_name"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="activeAgency?.business_type">
                                                        <div class="flex items-start gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0">
                                                                <span class="material-symbols-outlined text-[16px] text-slate-500">business</span>
                                                            </div>
                                                            <div>
                                                                <p class="text-[11px] uppercase tracking-wider text-slate-400 dark:text-slate-500 font-medium">Badan Usaha</p>
                                                                <p class="text-sm text-slate-700 dark:text-slate-300 font-medium" x-text="activeAgency.business_type"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="activeAgency?.nib">
                                                        <div class="flex items-start gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0">
                                                                <span class="material-symbols-outlined text-[16px] text-slate-500">verified</span>
                                                            </div>
                                                            <div>
                                                                <p class="text-[11px] uppercase tracking-wider text-slate-400 dark:text-slate-500 font-medium">NIB</p>
                                                                <p class="text-sm text-slate-700 dark:text-slate-300 font-medium" x-text="activeAgency.nib"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="activeAgency?.address">
                                                        <div class="flex items-start gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm shrink-0">
                                                                <span class="material-symbols-outlined text-[16px] text-slate-500">location_on</span>
                                                            </div>
                                                            <div>
                                                                <p class="text-[11px] uppercase tracking-wider text-slate-400 dark:text-slate-500 font-medium">Alamat</p>
                                                                <p class="text-sm text-slate-700 dark:text-slate-300 font-medium" x-text="activeAgency.address"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            
                                            <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-xl border border-slate-100 dark:border-slate-700">
                                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[18px]">contact_support</span> Kontak
                                                </h4>
                                                <div class="space-y-4">
                                                    <template x-if="activeAgency?.contact_wa">
                                                        <a :href="'https://wa.me/' + activeAgency.contact_wa.replace(/\D/g,'')" target="_blank" class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400 hover:text-emerald-500 transition-colors group">
                                                            <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/30">
                                                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                                            </div>
                                                            <span x-text="activeAgency.contact_wa"></span>
                                                        </a>
                                                    </template>
                                                    <template x-if="activeAgency?.instagram">
                                                        <a :href="activeAgency.instagram.includes('http') ? activeAgency.instagram : 'https://instagram.com/' + activeAgency.instagram.replace('@','')" target="_blank" class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400 hover:text-pink-500 transition-colors group">
                                                            <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm group-hover:bg-pink-100 dark:group-hover:bg-pink-900/30">
                                                                <i class="fa-brands fa-instagram text-pink-500"></i>
                                                            </div>
                                                            <span x-text="activeAgency.instagram"></span>
                                                        </a>
                                                    </template>
                                                    <template x-if="activeAgency?.website">
                                                        <a :href="activeAgency.website" target="_blank" class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-500 transition-colors group">
                                                            <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center shadow-sm group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30">
                                                                <span class="material-symbols-outlined text-[16px] text-blue-500">language</span>
                                                            </div>
                                                            <span class="line-clamp-1" x-text="new URL(activeAgency.website).hostname.replace('www.', '')"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- CTA: Hubungi Biro --}}
                                        <div class="md:col-span-2">
                                            <div class="text-center py-12 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                                                <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                                    <span class="material-symbols-outlined text-3xl text-primary">travel_explore</span>
                                                </div>
                                                <h5 class="text-slate-900 dark:text-white font-bold text-lg mb-2">Tertarik Berlibur?</h5>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-6">Untuk info paket wisata, harga, dan jadwal, hubungi biro ini langsung.</p>
                                                <div class="flex flex-wrap justify-center gap-3">
                                                    <template x-if="activeAgency?.contact_wa">
                                                        <a :href="'https://wa.me/' + activeAgency.contact_wa.replace(/\D/g,'')" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                                                            <i class="fa-brands fa-whatsapp text-lg"></i> WhatsApp
                                                        </a>
                                                    </template>
                                                    <template x-if="activeAgency?.website">
                                                        <a :href="activeAgency.website" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition-colors border border-slate-200 dark:border-slate-600">
                                                            <span class="material-symbols-outlined text-sm">language</span> Website
                                                        </a>
                                                    </template>
                                                    <template x-if="activeAgency?.instagram">
                                                        <a :href="activeAgency.instagram.includes('http') ? activeAgency.instagram : 'https://instagram.com/' + activeAgency.instagram.replace('@','')" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-pink-50 hover:bg-pink-100 dark:bg-pink-900/20 dark:hover:bg-pink-900/30 text-pink-600 dark:text-pink-400 text-sm font-bold rounded-xl transition-colors border border-pink-200 dark:border-pink-800/50">
                                                            <i class="fa-brands fa-instagram text-lg"></i> Instagram
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </template>
            </section>
        </main>
    </div>
</x-public-layout>
