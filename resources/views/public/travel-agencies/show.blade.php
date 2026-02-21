<x-public-layout>
    @section('title', $agency->name . ' - Biro Wisata')

<div class="min-h-screen bg-slate-50 dark:bg-slate-950 pt-24 pb-20">
    <div class="container mx-auto px-4">
        
        <!-- Header Section -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 mb-6 shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="w-24 h-24 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700">
                @if($agency->logo_path)
                    <img src="{{ asset($agency->logo_path) }}" class="w-full h-full object-cover">
                @else
                    <span class="material-symbols-outlined text-slate-400 text-4xl">store</span>
                @endif
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <h1 class="font-playfair text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ $agency->name }}</h1>
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-[14px]">verified</span> Terverifikasi
                    </span>
                    @if($agency->business_type)
                        <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ $agency->business_type }}
                        </span>
                    @endif
                </div>
                @if($agency->address)
                    <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1 mb-2">
                        <span class="material-symbols-outlined text-[14px]">location_on</span> {{ $agency->address }}
                    </p>
                @endif
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

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Informasi Usaha -->
            @if($agency->owner_name || $agency->business_type || $agency->nib || $agency->address)
            <div class="md:col-span-1 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
                <h2 class="font-bold text-lg text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">badge</span> Informasi Usaha
                </h2>
                <div class="space-y-4">
                    @if($agency->owner_name)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-100 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[16px] text-slate-500">person</span>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-medium">Pemilik</p>
                            <p class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $agency->owner_name }}</p>
                        </div>
                    </div>
                    @endif
                    @if($agency->business_type)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-100 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[16px] text-slate-500">business</span>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-medium">Badan Usaha</p>
                            <p class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $agency->business_type }}</p>
                        </div>
                    </div>
                    @endif
                    @if($agency->nib)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-100 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[16px] text-slate-500">verified</span>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-medium">NIB</p>
                            <p class="text-sm text-slate-800 dark:text-slate-200 font-medium font-mono">{{ $agency->nib }}</p>
                        </div>
                    </div>
                    @endif
                    @if($agency->address)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0 border border-slate-100 dark:border-slate-700">
                            <span class="material-symbols-outlined text-[16px] text-slate-500">location_on</span>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-medium">Alamat</p>
                            <p class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $agency->address }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Hubungi Biro Section -->
            <div class="{{ ($agency->owner_name || $agency->business_type || $agency->nib || $agency->address) ? 'md:col-span-2' : 'md:col-span-3' }} bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-5xl text-primary mb-4 block">travel_explore</span>
                <h2 class="font-bold text-2xl text-slate-900 dark:text-white mb-3">Tertarik Berlibur?</h2>
                <p class="text-slate-600 dark:text-slate-400 max-w-xl mx-auto mb-6">Untuk informasi paket wisata, harga, dan jadwal keberangkatan, silakan hubungi biro ini langsung melalui WhatsApp, website, atau media sosial mereka.</p>

                <div class="flex flex-wrap justify-center gap-3">
                    @if($agency->contact_wa)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agency->contact_wa) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 transition-colors shadow-sm">
                            <i class="fa-brands fa-whatsapp text-lg"></i> Hubungi via WhatsApp
                        </a>
                    @endif
                    @if($agency->website)
                        <a href="{{ $agency->website }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold text-sm hover:bg-slate-200 transition-colors border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 dark:hover:bg-slate-700">
                            <span class="material-symbols-outlined text-lg">language</span> Kunjungi Website
                        </a>
                    @endif
                    @if($agency->instagram)
                        <a href="{{ str_contains($agency->instagram, 'http') ? $agency->instagram : 'https://instagram.com/'.str_replace('@', '', $agency->instagram) }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-pink-50 text-pink-600 font-bold text-sm hover:bg-pink-100 transition-colors border border-pink-200 dark:bg-pink-900/20 dark:text-pink-400 dark:border-pink-800/50 dark:hover:bg-pink-900/30">
                            <i class="fa-brands fa-instagram text-lg"></i> Instagram
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</x-public-layout>

