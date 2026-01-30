<!DOCTYPE html>
<html class="light scroll-smooth overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Portal Wisata - {{ config('app.name', 'Jepara') }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-display antialiased transition-colors duration-200 overflow-x-hidden">

    <!-- Top Navigation -->
    <div class="fixed top-0 left-0 right-0 z-[10000] w-full border-b border-surface-light dark:border-surface-dark bg-background-light/95 dark:bg-background-dark/95 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <header class="flex h-20 items-center justify-between gap-8" x-data="{ mobileMenuOpen: false }">
                <div class="flex items-center gap-8">
                    <a class="flex items-center gap-3 text-text-light dark:text-text-dark group" href="{{ route('welcome') }}">
                        <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" alt="Logo Kabupaten Jepara" class="w-10 h-auto object-contain">
                        <h2 class="text-xl font-bold leading-tight tracking-tight">Pesona Jepara</h2>
                    </a>

                    <nav class="hidden lg:flex items-center gap-8">
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('welcome') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('welcome') }}">Beranda</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('explore.map') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('explore.map') }}">Peta GIS</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('places.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('places.index') }}">Destinasi</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('events.public.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('events.public.index') }}">Agenda</a>
                           
                        <a class="text-sm font-medium transition-colors {{ request()->routeIs('posts.*') ? 'text-primary font-bold' : 'text-text-light dark:text-text-dark hover:text-primary' }}" 
                           href="{{ route('posts.index') }}">Berita</a>
                    </nav>
                </div>

                <div class="flex flex-1 items-center justify-end gap-4">
                    <!-- Auth Buttons (Desktop) -->
                    <div class="hidden">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                    <span class="truncate">Login</span>
                                </a>
                            @endauth
                        @endif
                    </div>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-full hover:bg-surface-light dark:hover:bg-surface-dark">
                        <span class="material-symbols-outlined" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                    </button>
                </div>

                <!-- Mobile Menu Dropdown -->
                <div x-show="mobileMenuOpen" @click.outside="mobileMenuOpen = false" x-cloak
                     class="absolute top-20 left-0 w-full bg-background-light dark:bg-background-dark border-b border-surface-light dark:border-surface-dark shadow-xl lg:hidden z-50 p-4 flex flex-col gap-4">
                    <nav class="flex flex-col gap-4">
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="{{ route('welcome') }}">Beranda</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="{{ route('explore.map') }}">Peta GIS</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="{{ route('places.index') }}">Destinasi</a>
                         <a class="text-sm font-medium hover:text-primary transition-colors p-2 text-primary" href="{{ route('events.public.index') }}">Agenda</a>
                        <a class="text-sm font-medium hover:text-primary transition-colors p-2" href="{{ route('posts.index') }}">Berita</a>
                    </nav>
                </div>
            </header>
        </div>
    </div>

    <!-- Page Content -->
    <main class="pt-20">
        {{ $slot }}
    </main>


    <!-- Footer -->
    <!-- Footer Start -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700;900&display=swap');
    </style>
    <footer class="bg-gray-900/90 bg-cover bg-[60%_top] md:bg-[center_35%] bg-no-repeat bg-blend-multiply border-t border-white/10 pt-16 md:pt-32 pb-8 px-8 md:px-10" style="font-family: 'Poppins', sans-serif; background-image: url('{{ asset('images/footer/image.png') }}');">
        <div class="w-full mx-auto max-w-7xl">
            
            <!-- Baris 1: DINAS PARIWISATA DAN KEBUDAYAAN -->
            <div class="flex justify-between w-full text-center text-[3.8vw] font-[900] text-white drop-shadow-lg uppercase tracking-tighter mb-4 leading-[0.8] select-none">
                <span>D</span><span>I</span><span>N</span><span>A</span><span>S</span>
                <span class="invisible text-[1vw]">_</span>
                <span>P</span><span>A</span><span>R</span><span>I</span><span>W</span><span>I</span><span>S</span><span>A</span><span>T</span><span>A</span>
                <span class="invisible text-[1vw]">_</span>
                <span>D</span><span>A</span><span>N</span>
                <span class="invisible text-[1vw]">_</span>
                <span>K</span><span>E</span><span>B</span><span>U</span><span>D</span><span>A</span><span>Y</span><span>A</span><span>A</span><span>N</span>
            </div>

            <!-- Baris 2: KABUPATEN JEPARA -->
            <div class="flex justify-between w-full text-center text-[7.5vw] font-[900] text-white drop-shadow-lg uppercase tracking-tighter mb-10 leading-[0.8] select-none">
                <span>K</span><span>A</span><span>B</span><span>U</span><span>P</span><span>A</span><span>T</span><span>E</span><span>N</span>
                <span class="invisible inline-block w-[4vw] md:w-[1.5vw]">_</span>
                <span class="text-white">J</span><span class="text-white">E</span><span class="text-white">P</span><span class="text-white">A</span><span class="text-white">R</span><span class="text-white">A</span>
            </div>

            <!-- Links Section -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-10 mb-12 text-left">
                <div>
                    <h3 class="text-white text-lg font-bold mb-4 uppercase tracking-wider drop-shadow-md">Jelajahi</h3>
                    <ul class="space-y-2 text-white/80">
                        <li><a href="{{ route('welcome') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Beranda</a></li>
                        <li><a href="{{ route('places.index') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Destinasi</a></li>
                        <li><a href="{{ route('explore.map') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Peta Wisata</a></li>
                        <li><a href="{{ route('events.public.index') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Agenda</a></li>
                        <li><a href="{{ route('posts.index') }}" class="hover:text-white hover:translate-x-1 transition-all inline-block">Berita</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white text-lg font-bold mb-4 uppercase tracking-wider drop-shadow-md">Kategori</h3>
                     <ul class="space-y-2 text-white/80">
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all inline-block">Wisata Alam</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all inline-block">Wisata Budaya</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all inline-block">Wisata Kuliner</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 transition-all inline-block">Wisata Religi</a></li>
                    </ul>
                </div>
                <div class="col-span-2 md:col-span-1 text-center md:text-left">
                    <h3 class="text-white text-lg font-bold mb-4 uppercase tracking-wider drop-shadow-md">Hubungi Kami</h3>
                    <ul class="space-y-3 text-white/80">
                        <li class="flex justify-center md:justify-start items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 text-white"></i>
                            <span>Jl. Kartini No.1, Panggang I, Panggang, Kec. Jepara, Kabupaten Jepara, Jawa Tengah 59411</span>
                        </li>
                        <li class="flex justify-center md:justify-start items-center gap-3">
                            <i class="fa-solid fa-phone text-white"></i>
                            <span>(0291) 591148</span>
                        </li>
                        <li class="flex justify-center md:justify-start items-center gap-3">
                            <i class="fa-solid fa-envelope text-white"></i>
                            <span>disparbud@jepara.go.id</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <div class="w-full h-[1px] bg-white/20 mb-8"></div>

            <!-- Info Icons & Copyright -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                
                <div class="flex items-center gap-6 md:gap-10 opacity-80 hover:opacity-100 transition-opacity duration-300 -translate-y-4">
                    <div class="flex items-center drop-shadow-md">
                        <img src="{{ asset('images/footer/wndrfl-indonesia2.png') }}" alt="Wonderful Indonesia" class="h-16 w-auto object-contain contrast-150 drop-shadow-[0_0_1px_rgba(255,255,255,0.8)]">
                    </div>
                    
                    <div class="flex items-center drop-shadow-md">
                        <img src="{{ asset('images/footer/logo-jpr-psn.png') }}" alt="Jepara Mempesona" class="h-28 w-auto object-contain">
                    </div>
                </div>

                <div class="text-center md:text-right">
                    <!-- Social Media Icons -->
                    <div class="flex justify-center md:justify-end gap-6 mb-6">
                        <a href="#" aria-label="Facebook" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" aria-label="Instagram" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                        <a href="#" aria-label="YouTube" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-youtube text-xl"></i>
                        </a>
                        <a href="#" aria-label="Twitter" class="text-white/70 hover:text-white hover:scale-110 transition-all duration-300 drop-shadow-md">
                            <i class="fa-brands fa-twitter text-xl"></i>
                        </a>
                    </div>

                    <p class="text-xs text-white/70 font-bold uppercase tracking-widest mb-2 drop-shadow-md">Pemerintah Kabupaten Jepara</p>
                    <p class="text-sm text-white font-medium drop-shadow-md">© 2024 Dinas Pariwisata dan Kebudayaan. Seluruh hak cipta dilindungi undang-undang.</p>
                </div>

            </div>
        </div>
    </footer>
</body>
</html>
