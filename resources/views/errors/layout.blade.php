<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title') - Jelajah Jepara</title>
    <link rel="icon" href="{{ asset('images/logo-kabupaten-jepara.png') }}" type="image/png">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-8px) rotate(1deg); }
            75% { transform: translateY(4px) rotate(-1deg); }
        }
        .animate-float { animation: float 5s ease-in-out infinite; }
        .animate-float-slow { animation: float 8s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out infinite 0.5s; }

        /* Gradient Text */
        .text-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Blob Animation */
        @keyframes blob {
            0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
        }
        .animate-blob { animation: blob 10s ease-in-out infinite; }
        .animate-blob-slow { animation: blob 15s ease-in-out infinite; }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-display antialiased h-screen overflow-hidden flex flex-col">

    <!-- Navbar -->
    <div class="flex-shrink-0 w-full bg-white/90 backdrop-blur-md border-b border-slate-200/50 z-50">
        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-12">
            <header class="flex items-center justify-between gap-8 h-14 md:h-16">
                <a class="flex items-center gap-2 group" href="{{ url('/') }}">
                    <div class="relative w-10 h-10 md:w-12 md:h-12 transition-transform duration-300 group-hover:scale-110">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain filter drop-shadow-md">
                    </div>
                    <div>
                        <h2 class="text-base md:text-lg font-bold leading-none tracking-tight text-slate-800 group-hover:text-primary transition-colors">
                            Jelajah Jepara
                        </h2>
                    </div>
                </a>

                <a href="{{ url('/') }}" class="px-4 py-2 md:px-5 md:py-2.5 bg-primary text-white text-xs md:text-sm font-bold rounded-full hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                    <i class="fa-solid fa-house text-xs md:text-sm mr-1.5"></i>
                    Beranda
                </a>
            </header>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 relative overflow-hidden">
        
        <!-- Decorative Blobs -->
        <div class="absolute top-10 -left-20 w-48 md:w-72 h-48 md:h-72 bg-blue-400/20 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute bottom-10 -right-20 w-64 md:w-96 h-64 md:h-96 bg-cyan-400/20 rounded-full blur-3xl animate-blob-slow"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] md:w-[400px] h-[300px] md:h-[400px] bg-gradient-to-br from-blue-100/50 to-cyan-100/50 rounded-full blur-3xl"></div>

        <!-- Error Content -->
        <div class="relative z-10 max-w-xl w-full text-center">
            
            <!-- Illustration Area -->
            <div class="relative w-32 h-32 md:w-44 md:h-44 mx-auto mb-4 md:mb-6">
                @yield('illustration')
            </div>

            <!-- Error Code -->
            <div class="inline-block mb-2 md:mb-3">
                <span class="text-5xl md:text-7xl font-extrabold text-gradient">@yield('code')</span>
            </div>
            
            <!-- Message -->
            <h1 class="text-xl md:text-3xl font-bold text-slate-800 mb-2 md:mb-3">
                @yield('message')
            </h1>
            
            <!-- Description -->
            <p class="text-sm md:text-base text-slate-500 max-w-sm mx-auto leading-relaxed mb-6 md:mb-8">
                @yield('description')
            </p>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url('/') }}" class="group inline-flex items-center justify-center gap-2 px-6 py-3 md:px-8 md:py-3.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-sm font-bold rounded-xl md:rounded-2xl shadow-xl shadow-blue-600/25 hover:shadow-2xl hover:shadow-blue-600/30 hover:-translate-y-0.5 transition-all duration-300">
                    <i class="fa-solid fa-house"></i>
                    Kembali ke Beranda
                </a>
                
                <button onclick="history.back()" class="inline-flex items-center justify-center gap-2 px-6 py-3 md:px-8 md:py-3.5 bg-white text-slate-700 text-sm font-bold rounded-xl md:rounded-2xl border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-all duration-300">
                    <i class="fa-solid fa-arrow-left"></i>
                    Halaman Sebelumnya
                </button>
            </div>

            @yield('actions')
        </div>
    </main>

    <!-- Minimal Footer -->
    <div class="flex-shrink-0 py-3 md:py-4 text-center border-t border-slate-100">
        <p class="text-xs text-slate-400">
            &copy; {{ date('Y') }} Dinas Pariwisata & Kebudayaan Kabupaten Jepara
        </p>
    </div>

</body>
</html>
