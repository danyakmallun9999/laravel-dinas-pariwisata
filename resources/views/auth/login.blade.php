<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Sistem Informasi Geografis Desa Mayong Lor</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans text-slate-800 bg-white">
    <div class="min-h-screen grid lg:grid-cols-2">
        <!-- Left Side: Image -->
        <div class="relative hidden lg:block h-full overflow-hidden">
            <div class="absolute inset-0 bg-slate-900/60 z-10"></div>
            <img src="/images/balaidesa.jpeg" alt="Balai Desa Mayong Lor" class="w-full h-full object-cover">
            <div class="absolute bottom-0 left-0 right-0 z-20 p-12 bg-gradient-to-t from-slate-900/90 to-transparent">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-300 mb-2">Sistem Informasi Geografis</p>
                <h2 class="text-3xl font-bold text-white">Desa Mayong Lor</h2>
                <p class="mt-2 text-slate-300">Kelola data spasial, potensi desa, dan layanan publik dalam satu dashboard terintegrasi.</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="flex items-center justify-center p-8 sm:p-12 lg:p-16 bg-white">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center lg:text-left">
                    <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-500 transition mb-8">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900">Selamat Datang Kembali</h1>
                    <p class="mt-2 text-slate-600">Silakan masuk untuk mengakses dashboard admin.</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="rounded-xl bg-green-50 p-4 text-sm font-medium text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                        <div class="mt-1 relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 sm:text-sm transition shadow-sm"
                                placeholder="admin@mayonglor.id">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        <div class="mt-1 relative">
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 sm:text-sm transition shadow-sm"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember" 
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <label for="remember_me" class="ml-2 block text-sm text-slate-600">Ingat saya</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 hover:shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition transform active:scale-[0.98]">
                        Masuk ke Dashboard
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-slate-500">
                        &copy; {{ date('Y') }} Pemerintah Desa Mayong Lor. <br>Sistem Informasi Geografis.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
