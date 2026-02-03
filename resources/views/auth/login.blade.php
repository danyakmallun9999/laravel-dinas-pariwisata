<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Sistem Informasi Geografis Desa Mayong Lor</title>
    
    <!-- Fonts -->

    



    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans text-text-light dark:text-text-dark bg-background-light dark:bg-background-dark h-screen overflow-hidden">
    <div class="h-full grid lg:grid-cols-2">
        <!-- Left Side: Image -->
        <div class="relative hidden lg:block h-full overflow-hidden group">
            <div class="absolute inset-0 bg-background-dark/40 group-hover:bg-background-dark/20 transition-colors duration-700 z-10"></div>
            <!-- Dynamic Image or Fallback -->
            <img src="/images/geografis.png" alt="Pesona Jepara" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
            
            <div class="absolute bottom-0 left-0 right-0 z-20 p-16 bg-gradient-to-t from-background-dark via-background-dark/80 to-transparent">
                <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="/images/logo-kabupaten-jepara.png" alt="Logo" class="h-12 w-auto drop-shadow-md">
                        <div class="h-8 w-[1px] bg-white/30"></div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/90 font-display">Official Admin Portal</p>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-white font-display leading-tight mb-4">
                        Dinas Pariwisata dan Kebudayaan<br>
                        <span class="text-primary">Kabupaten Jepara</span>
                    </h2>
                    <p class="text-lg text-white/70 max-w-lg font-medium leading-relaxed">
                        Kelola destinasi wisata, event budaya, dan informasi publik untuk memajukan pariwisata Jepara yang mendunia.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="flex items-center justify-center p-8 sm:p-12 lg:p-16 bg-white dark:bg-surface-dark relative">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 p-8 opacity-50 pointer-events-none">
                 <div class="w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
            </div>

            <div class="w-full max-w-[420px] space-y-10 relative z-10">
                <div class="space-y-2">                
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-text-light dark:text-text-dark font-display">Selamat Datang</h1>
                        <p class="mt-2 text-text-light/60 dark:text-text-dark/60">Silakan masuk untuk mengelola portal pariwisata.</p>
                    </div>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="rounded-xl bg-green-50 dark:bg-green-900/20 p-4 flex gap-3 border border-green-200 dark:border-green-800">
                        <span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>
                        <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-bold text-text-light dark:text-text-dark">Email Address</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-light/40 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                                class="block w-full rounded-xl border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-900/50 pl-11 pr-4 py-3.5 text-text-light dark:text-text-dark placeholder:text-text-light/30 focus:border-primary focus:bg-white dark:focus:bg-stone-900 focus:ring-4 focus:ring-primary/10 sm:text-sm transition-all shadow-sm group-hover:border-stone-300 dark:group-hover:border-stone-600"
                                placeholder="admin@jepara.go.id">
                        </div>
                        @error('email')
                            <p class="text-sm text-red-600 flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-base">error</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                         <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-bold text-text-light dark:text-text-dark">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-sm font-bold text-primary hover:text-primary-dark transition-colors" href="{{ route('password.request') }}">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-text-light/40 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="block w-full rounded-xl border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-900/50 pl-11 pr-4 py-3.5 text-text-light dark:text-text-dark placeholder:text-text-light/30 focus:border-primary focus:bg-white dark:focus:bg-stone-900 focus:ring-4 focus:ring-primary/10 sm:text-sm transition-all shadow-sm group-hover:border-stone-300 dark:group-hover:border-stone-600"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="text-sm text-red-600 flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-base">error</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer group">
                             <div class="relative">
                                <input id="remember_me" type="checkbox" name="remember" class="peer sr-only">
                                <div class="w-5 h-5 border-2 border-stone-300 dark:border-stone-600 rounded bg-white dark:bg-stone-800 peer-checked:bg-primary peer-checked:border-primary transition-all"></div>
                                <div class="absolute inset-0 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none">
                                    <span class="material-symbols-outlined text-sm font-bold">check</span>
                                </div>
                             </div>
                            <span class="ml-2 text-sm text-text-light/70 dark:text-text-dark/70 font-medium group-hover:text-text-light transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-primary text-white px-4 py-4 text-sm font-bold shadow-lg shadow-primary/25 hover:bg-primary-dark hover:shadow-primary/40 focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all transform active:scale-[0.98]">
                        Masuk ke Dashboard
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </form>

                <div class="pt-6 border-t border-stone-100 dark:border-stone-800 text-center">
                    <p class="text-xs text-text-light/40 dark:text-text-dark/40 font-medium">
                        &copy; {{ date('Y') }} Dinas Pariwisata dan Kebudayaan.<br>
                        Pemerintah Kabupaten Jepara.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
