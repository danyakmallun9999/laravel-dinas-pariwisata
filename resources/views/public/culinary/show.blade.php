<x-public-layout>
    <div class="bg-background-light dark:bg-background-dark min-h-screen -mt-20">
        <!-- Hero Section -->
        <div class="relative h-[60vh] md:h-[70vh] w-full overflow-hidden">
            @if($place->image_path)
                <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-300 dark:bg-gray-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-6xl text-gray-400">restaurant_menu</span>
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-background-light dark:from-background-dark via-black/40 to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 lg:p-16">
                <div class="max-w-4xl mx-auto w-full text-center">
                    <span class="inline-block px-4 py-1.5 mb-6 rounded-full bg-orange-600/90 backdrop-blur text-white text-sm font-bold shadow-lg uppercase tracking-wider">
                        {{ $place->category->name ?? 'Kuliner Jepara' }}
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold text-white mb-6 drop-shadow-xl leading-tight">
                        {{ $place->name }}
                    </h1>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12 md:py-20 -mt-8 relative z-10">
            <div class="bg-white dark:bg-surface-dark rounded-3xl p-8 md:p-12 shadow-2xl border border-surface-light dark:border-white/5">
                <!-- Description -->
                <div class="prose prose-lg md:prose-xl dark:prose-invert max-w-none text-justify mx-auto leading-loose text-text-light/90 dark:text-text-dark/90 first-letter:text-5xl first-letter:font-bold first-letter:text-primary first-letter:mr-2">
                    <p class="whitespace-pre-line">
                        {{ $place->description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
