<x-public-layout>
    @section('title', $place->name)
    @section('meta_description', Str::limit(strip_tags($place->description), 160))

    <div class="relative min-h-screen bg-white dark:bg-gray-900 pt-20">
        
        <!-- Hero Header -->
        <div class="relative h-[60vh] overflow-hidden">
            <div class="absolute inset-0">
                <img src="{{ asset($place->image_path) }}" 
                     alt="{{ $place->name }}" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
            </div>
            
            <div class="absolute bottom-0 left-0 w-full p-8 md:p-16">
                <div class="max-w-7xl mx-auto">
                    <span class="inline-block px-4 py-1 mb-4 rounded-full bg-orange-600/90 backdrop-blur text-white text-sm font-bold shadow-lg">
                        {{ $place->category->name }}
                    </span>
                    <h1 class="text-4xl md:text-6xl font-black text-white mb-4 leading-tight">
                        {{ $place->name }}
                    </h1>
                    <div class="flex items-center gap-6 text-white/80 text-sm md:text-base">
                        @if($place->address)
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-400">location_on</span>
                            <span>{{ $place->address }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="prose prose-lg prose-orange dark:prose-invert max-w-none">
                <p class="text-xl leading-relaxed text-gray-600 dark:text-gray-300 font-medium mb-8">
                    {{ $place->description }}
                </p>
                
                <!-- Additional Details if any (e.g. Schedule for events) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
                    <div class="p-6 rounded-2xl bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-500/20">
                        <h3 class="text-lg font-bold text-orange-900 dark:text-orange-100 mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined">calendar_month</span> Jadwal / Waktu
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ $place->opening_hours ?? 'Hubungi panitia setempat.' }}
                        </p>
                    </div>
                    
                    <div class="p-6 rounded-2xl bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-500/20">
                        <h3 class="text-lg font-bold text-orange-900 dark:text-orange-100 mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined">confirmation_number</span> Tiket & Akses
                        </h3>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ $place->ticket_price ?? 'Gratis / Umum' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Back Button -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-orange-600 font-medium transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
