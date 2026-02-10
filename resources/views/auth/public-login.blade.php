<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-28 pb-24">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Main card --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 md:p-10">

                {{-- Header --}}
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                        Lihat Tiket Anda
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Masuk menggunakan akun Google untuk melihat detail tiket wisata yang telah Anda beli.
                    </p>
                </div>

                {{-- Steps indicator --}}
                <div class="flex items-center justify-center gap-0 mb-8">
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">1</div>
                        <span class="text-[11px] font-semibold text-primary">Masuk</span>
                    </div>
                    <div class="w-10 h-px bg-slate-200 dark:bg-slate-600 -mt-4"></div>
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 flex items-center justify-center text-xs font-bold">2</div>
                        <span class="text-[11px] font-medium text-slate-400">Cari Tiket</span>
                    </div>
                    <div class="w-10 h-px bg-slate-200 dark:bg-slate-600 -mt-4"></div>
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 flex items-center justify-center text-xs font-bold">3</div>
                        <span class="text-[11px] font-medium text-slate-400">Lihat Detail</span>
                    </div>
                </div>

                {{-- Error flash --}}
                @if(session('error'))
                    <div class="flex items-start gap-3 p-4 mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <p class="text-sm text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                {{-- Google login button --}}
                <a href="{{ route('auth.google') }}" 
                   class="group w-full flex items-center justify-center gap-3 px-6 py-3.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/50 hover:border-primary/50 dark:hover:border-primary/50 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold transition-all duration-200">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                    <i class="fa-solid fa-arrow-right text-sm text-slate-300 dark:text-slate-500 group-hover:text-primary group-hover:translate-x-0.5 transition-all"></i>
                </a>

                {{-- Divider --}}
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">atau</span>
                    <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700"></div>
                </div>

                {{-- Secondary action --}}
                <a href="{{ route('tickets.index') }}" 
                   class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700/50 text-slate-600 dark:text-slate-300 font-medium text-sm transition-colors">
                    <i class="fa-solid fa-store text-xs text-slate-400"></i>
                    Beli Tiket Baru
                </a>

                {{-- Info note --}}
                <div class="mt-6 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-700/20 border border-slate-100 dark:border-slate-700">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-shield-halved text-slate-400 text-xs mt-0.5"></i>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Login hanya untuk verifikasi identitas. Data Anda tidak dibagikan ke pihak manapun.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bottom links --}}
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="{{ route('welcome') }}" 
                   class="text-slate-400 hover:text-primary text-sm font-medium inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Beranda
                </a>
                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                <a href="{{ route('tickets.index') }}" 
                   class="text-slate-400 hover:text-primary text-sm font-medium inline-flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-ticket text-xs"></i>
                    E-Tiket
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
