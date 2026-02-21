<x-public-layout>
    @section('title', $agency->name . ' - Biro Wisata')

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 pt-24 pb-20">
    <div class="container mx-auto px-4">
        
        <!-- Header Section -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 mb-8 shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="w-24 h-24 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700">
                @if($agency->logo_path)
                    <img src="{{ asset($agency->logo_path) }}" class="w-full h-full object-cover">
                @else
                    <span class="material-symbols-outlined text-slate-400 text-4xl">store</span>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="font-playfair text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ $agency->name }}</h1>
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-[14px]">verified</span> Terverifikasi
                    </span>
                </div>
                <p class="text-slate-600 dark:text-slate-400 max-w-3xl leading-relaxed">
                    {{ $agency->description ?? 'Biro wisata bersertifikat yang melayani perjalanan wisata di Kabupaten Jepara.' }}
                </p>
            </div>
            <div class="flex flex-col gap-3 min-w-[200px]">
                @if($agency->contact_wa)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agency->contact_wa) }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-50 text-emerald-600 font-bold text-sm hover:bg-emerald-500 hover:text-white transition-colors border border-emerald-200 dark:border-emerald-800/50">
                        <i class="fa-brands fa-whatsapp text-lg"></i> Hubungi via WA
                    </a>
                @endif
                <div class="flex justify-between gap-2">
                    @if($agency->instagram)
                        <a href="{{ str_contains($agency->instagram, 'http') ? $agency->instagram : 'https://instagram.com/'.str_replace('@', '', $agency->instagram) }}" target="_blank" class="flex-1 flex items-center justify-center py-2 rounded-xl bg-pink-50 text-pink-600 hover:bg-pink-500 hover:text-white transition-colors border border-pink-200 dark:border-pink-800/50">
                            <i class="fa-brands fa-instagram text-lg"></i>
                        </a>
                    @endif
                    @if($agency->website)
                        <a href="{{ $agency->website }}" target="_blank" class="flex-1 flex items-center justify-center py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-500 hover:text-white transition-colors border border-slate-200 dark:border-slate-700">
                            <span class="material-symbols-outlined">language</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tour Packages Section -->
        <h2 class="font-bold text-2xl text-slate-900 dark:text-white mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">inventory_2</span> Paket Wisata yang Tersedia
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($agency->tourPackages as $pkg)
                <div x-data="{ showDetail: false }" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col h-full">
                    <!-- Package Card Header -->
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 cursor-pointer group" @click="showDetail = !showDetail">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2">{{ $pkg->name }}</h3>
                        </div>
                        <div class="text-2xl font-black text-primary mb-4">
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Harga Mulai</span>
                            Rp {{ number_format($pkg->price_start, 0, ',', '.') }}
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400 px-3 py-1.5 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                                <span class="material-symbols-outlined text-[16px]">schedule</span> 
                                <span class="font-medium">{{ $pkg->duration_days }} Hari {{ $pkg->duration_nights }} Malam</span>
                            </div>
                            <div class="flex items-center gap-1 text-xs font-bold text-slate-400 group-hover:text-primary transition-colors">
                                <span x-text="showDetail ? 'Tutup' : 'Detail'"></span>
                                <span class="material-symbols-outlined text-[16px] transition-transform duration-300" :class="showDetail ? 'rotate-90' : ''">chevron_right</span>
                            </div>
                        </div>
                    </div>

                    <!-- Package Details Expansion -->
                    <div x-show="showDetail" x-collapse class="bg-slate-50/50 dark:bg-slate-950/50">
                        <div class="p-6 space-y-6">
                            
                            <!-- Description -->
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-primary text-lg">info</span> Deskripsi Paket
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-line">{{ $pkg->description }}</p>
                            </div>

                            <!-- Inclusions -->
                            @if($pkg->inclusions && is_array($pkg->inclusions) && count($pkg->inclusions) > 0)
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                                        <span class="material-symbols-outlined text-emerald-500 text-lg">loyalty</span> Fasilitas Termasuk
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($pkg->inclusions as $inc)
                                            <div class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
                                                <span class="material-symbols-outlined text-emerald-500 text-[14px] mt-0.5">check_circle</span>
                                                <span class="leading-snug">{{ $inc }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Itinerary -->
                            @if($pkg->itinerary && is_array($pkg->itinerary) && count($pkg->itinerary) > 0)
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2 text-sm">
                                        <span class="material-symbols-outlined text-purple-500 text-lg">timeline</span> Itinerary Singkat
                                    </h4>
                                    <div class="relative border-l-2 border-slate-200 dark:border-slate-800 ml-2 space-y-4 pb-2">
                                        @foreach($pkg->itinerary as $item)
                                            <div class="relative pl-5">
                                                <div class="absolute w-3 h-3 bg-white dark:bg-slate-900 border-2 border-primary rounded-full left-[-7px] top-1"></div>
                                                <div class="mb-1 text-xs font-bold text-primary">HARI {{ $item['day'] ?? '' }} • {{ $item['time'] ?? '' }}</div>
                                                <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">{{ $item['activity'] ?? '' }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
                    <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-600 mb-3 block">search_off</span>
                    <h3 class="font-bold text-slate-700 dark:text-slate-300">Belum ada paket wisata</h3>
                    <p class="text-slate-500 text-sm">Biro ini belum mengunggah paket wisata apa pun.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
</div>
</x-public-layout>
