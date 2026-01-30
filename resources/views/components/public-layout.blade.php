<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dinas Pariwisata dan Kebudayaan Jepara') }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Top Navigation -->
    <div
        class="fixed top-0 left-0 right-0 z-[99999] w-full bg-gradient-to-b from-white/30 via-white/15 to-transparent dark:from-black/25 dark:via-black/10 dark:to-transparent backdrop-blur-lg">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <header class="flex h-20 items-center justify-between gap-8" x-data="{ mobileMenuOpen: false }">
                <div class="flex items-center gap-8">
                    <a class="flex items-center gap-3 text-slate-900 group" href="{{ route('welcome') }}">
                        <img src="{{ asset('images/logo-kabupaten-jepara.png') }}" alt="Logo Kabupaten Jepara"
                            class="w-10 h-auto object-contain">
                        <h2 class="text-xl font-bold leading-tight tracking-tight">Blusukan Jepara</h2>
                    </a>
                    <nav class="hidden lg:flex items-center gap-8">
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors" href="{{ route('welcome') }}">Beranda</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors" href="{{ route('welcome') }}#gis-map">Peta GIS</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors" href="{{ route('welcome') }}#profile">Profil</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors" href="{{ route('welcome') }}#potency">Potensi</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors" href="{{ route('welcome') }}#news">Berita</a>
                    </nav>
                </div>

                <div class="flex flex-1 items-center justify-end gap-4">
                    <!-- Auth Buttons (Desktop) -->
                    <div class="hidden lg:flex">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="flex items-center justify-center rounded-full h-10 px-6 bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                    <span class="truncate">Login</span>
                                </a>
                            @endauth
                        @endif
                    </div>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden p-2 rounded-full hover:bg-surface-light dark:hover:bg-surface-dark">
                        <span class="material-symbols-outlined" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                    </button>
                </div>

                <!-- Mobile Menu Dropdown -->
                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="mobileMenuOpen = false" x-cloak
                    class="absolute top-20 left-0 w-full bg-background-light dark:bg-background-dark border-b border-surface-light dark:border-surface-dark shadow-xl lg:hidden z-50 p-4 flex flex-col gap-4">

                    <nav class="flex flex-col gap-4">
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors p-2"
                            href="{{ route('welcome') }}">Beranda</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors p-2" href="{{ route('welcome') }}#gis-map">Peta
                            GIS</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors p-2"
                            href="{{ route('welcome') }}#profile">Profil</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors p-2"
                            href="{{ route('welcome') }}#potency">Potensi</a>
                        <a class="text-sm font-medium text-slate-900 hover:text-primary transition-colors p-2"
                            href="{{ route('welcome') }}#news">Berita</a>
                    </nav>

                    <div class="border-t border-surface-light dark:border-surface-dark pt-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="flex items-center justify-center rounded-xl h-12 w-full bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="flex items-center justify-center rounded-xl h-12 w-full bg-primary hover:bg-primary-dark text-white dark:text-gray-900 text-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-95">
                                    Login
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </header>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow pt-20">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 pt-16 pb-8 border-t border-slate-800 mt-auto">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div class="space-y-4">
                    <h2 class="text-lg font-bold flex items-center gap-2 text-white">
                        <span class="material-symbols-outlined text-blue-500">terrain</span> Dinas Pariwisata dan Kebudayaan Jepara
                    </h2>
                    <p class="text-slate-400 text-sm leading-relaxed">Portal resmi informasi pariwisata, budaya, dan ekonomi kreatif Kabupaten Jepara.</p>
                </div>
                <!-- Quick Links -->
                <div>
                    <h3 class="font-bold text-white mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('welcome') }}" class="hover:text-blue-400 transition-colors">Beranda</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-blue-400 transition-colors">Login Admin</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Dinas Pariwisata dan Kebudayaan Kabupaten Jepara. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
