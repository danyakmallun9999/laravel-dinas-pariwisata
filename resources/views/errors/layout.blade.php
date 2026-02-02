<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Dinas Pariwisata & Kebudayaan Jepara</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-serif-display { font-family: 'Playfair Display', serif; }
        .text-outline {
            text-shadow: -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000, 1px 1px 0 #000;
        }
        .text-outline-sm {
            text-shadow: -0.5px -0.5px 0 #000, 0.5px -0.5px 0 #000, -0.5px 0.5px 0 #000, 0.5px 0.5px 0 #000;
        }
        @keyframes slow-pan {
            0%, 100% { transform: scale(1.05) translate(0, 0); }
            50% { transform: scale(1.1) translate(-0.5%, -0.5%); }
        }
    </style>
</head>
<body class="antialiased font-sans text-white bg-[#1a1c23] overflow-hidden selection:bg-blue-500/30 selection:text-blue-200 flex flex-col min-h-screen">
    
    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col items-center justify-center p-6 relative z-10 py-20">
        
        <!-- Background Overlay for Content -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
             <!-- Deep Dark Color -->
            <div class="absolute inset-0 bg-[#1a1c23]"></div>
            
            <!-- Subtle Image -->
            <img src="{{ asset('images/culture/barikan-kubro.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-10 scale-110 blur-sm mix-blend-overlay">
            
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-[#1a1c23]/90 to-[#1a1c23]/50"></div>
            
            <!-- Animated Orbs -->
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-500/5 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-blue-600/5 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <!-- Content -->
        <div class="max-w-3xl w-full text-center relative z-10">
            <!-- Icon & Message -->

            <h1 class="text-[5rem] md:text-[10rem] font-bold tracking-tighter leading-none text-white/5 font-serif-display select-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 blur-sm scale-150">
                @yield('code')
            </h1>

            <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight mb-4 relative z-10">
                @yield('message')
            </h1>
            
            <p class="text-lg md:text-xl text-white/60 max-w-lg mx-auto leading-relaxed mb-10 relative z-10">
                @yield('description')
            </p>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center relative z-10">
                <a href="{{ url('/') }}" class="group relative inline-flex items-center justify-center px-8 py-3.5 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-full overflow-hidden transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_0_20px_rgba(37,99,235,0.5)]">
                    <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></span>
                    <i class="fa-solid fa-house mr-2 text-sm"></i>
                    <span class="relative font-bold text-sm tracking-wide uppercase text-white">Kembali ke Beranda</span>
                </a>
                
                @yield('actions')
            </div>
        </div>
    </div>

    <!-- Footer (Matched with Welcome Page) -->
    <footer class="relative bg-[#1a1c23] text-white pt-16 md:pt-24 pb-8 md:pb-12 overflow-hidden border-t border-white/5">
        <!-- Dynamic Photo Collage Background -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 h-full w-full gap-0.5 md:gap-2 p-0.5 md:p-2 transform scale-105 motion-safe:animate-[slow-pan_20s_ease-in-out_infinite] opacity-30 md:opacity-40">
                <!-- Item 1 (Large Square) -->
                <div class="relative overflow-hidden rounded-lg col-span-2 row-span-2 bg-gray-800">
                    <img src="{{ asset('images/culture/barikan-kubro.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <!-- Item 2 (Small) -->
                <div class="relative overflow-hidden rounded-lg bg-gray-800">
                    <img src="{{ asset('images/culture/festival-kupat-lepet.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <!-- Item 3 (Tall) -->
                <div class="relative overflow-hidden rounded-lg row-span-2 bg-gray-800">
                    <img src="{{ asset('images/culture/jondang-kawak.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <!-- Item 4 (Wide - Original Footer Image) -->
                <div class="relative overflow-hidden rounded-lg col-span-2 bg-gray-800">
                    <img src="{{ asset('images/footer/image.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <!-- Item 5 (Small) -->
                <div class="relative overflow-hidden rounded-lg bg-gray-800">
                    <img src="{{ asset('images/culture/kirab-buka-luwur.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <!-- Item 6 (Wide) -->
                <div class="relative overflow-hidden rounded-lg col-span-2 bg-gray-800">
                    <img src="{{ asset('images/culture/lomban.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <!-- Item 7 (Small) -->
                <div class="relative overflow-hidden rounded-lg bg-gray-800">
                    <img src="{{ asset('images/culture/obor.png') }}" alt="" class="w-full h-full object-cover">
                </div>
            </div>
            
            <!-- Deep Dark Overlays -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a1c23] via-[#1a1c23]/70 to-[#1a1c23] z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#1a1c23] via-transparent to-[#1a1c23] z-10"></div>
        </div>

        <!-- Background Decorative Elements -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-px bg-white/5 z-10"></div>
        
        <!-- Animated Background Orbs -->
        <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none z-10"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[30%] bg-blue-500/5 rounded-full blur-[100px] pointer-events-none z-10"></div>

        <div class="relative w-full mx-auto max-w-7xl px-6 md:px-10 z-20">
            <!-- Branding Section -->
            <div class="mb-12 md:mb-16 text-center">
                <div class="inline-flex flex-col mb-4 md:mb-6 w-full items-center">
                    
                    <!-- Main Branding -->
                    <h2 class="text-3xl md:text-7xl font-bold tracking-tight leading-[0.9] md:leading-[0.8] uppercase mb-6 text-outline-sm md:text-outline drop-shadow-2xl">
                        Pemerintah <br class="hidden md:block">
                        Kabupaten <span class="text-blue-500">Jepara</span>
                    </h2>

                    <!-- Department Subtitle (Centered) -->
                    <div class="flex flex-col items-center justify-center relative">
                        <div class="text-center">
                            <span class="block text-white/90 text-sm md:text-xl font-bold tracking-tight leading-tight uppercase font-heading">
                                Dinas Pariwisata & Kebudayaan
                            </span>
                            <span class="block text-white/40 text-[10px] md:text-sm font-medium tracking-wide mt-1">
                                Tourism & Culture Office of Jepara
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-white/40 text-xs md:text-sm text-center md:text-left order-2 md:order-1">
                    &copy; {{ date('Y') }} Dinas Pariwisata & Kebudayaan Kabupaten Jepara.
                </p>
                <div class="flex items-center gap-1 order-1 md:order-2 opacity-80 grayscale hover:grayscale-0 transition-all duration-500">
                    <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" alt="Logo Jepara" class="h-8 w-auto">
                    <span class="text-white/30 text-[10px] uppercase tracking-widest ml-2 font-bold hidden md:block">Official Government Site</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
