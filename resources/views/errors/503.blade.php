@extends('errors.layout')

@section('title', 'Sedang Pemeliharaan')
@section('code', '503')

@section('illustration')
    <!-- Under Construction Illustration -->
    <div class="relative w-full h-full">
        <!-- Main Icon Background -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-28 h-28 md:w-36 md:h-36 rounded-full bg-gradient-to-br from-amber-100 to-yellow-100 flex items-center justify-center animate-float-slow shadow-xl shadow-amber-200/50">
                <i class="fa-solid fa-screwdriver-wrench text-5xl md:text-6xl text-amber-400"></i>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="absolute -top-2 right-2 md:right-4 animate-float">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-orange-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-gear fa-spin text-lg md:text-xl text-orange-500" style="animation-duration: 3s;"></i>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 md:left-2 animate-float-delayed">
            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-green-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-clock text-sm md:text-base text-green-500"></i>
            </div>
        </div>
        <div class="absolute top-4 -left-2 md:left-0 animate-float-slow">
            <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-blue-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-hammer text-xs md:text-sm text-blue-400"></i>
            </div>
        </div>
    </div>
@endsection

@section('message', 'Lagi Dandan! ✨')
@section('description', 'Website lagi dipercantik supaya makin keren dan nyaman buat kamu. Sabar ya, sebentar lagi juga selesai!')

@section('actions')
    <div class="mt-4 md:mt-6 py-3 px-5 bg-white/80 backdrop-blur-sm rounded-xl border border-amber-100 inline-flex items-center gap-3">
        <i class="fa-solid fa-sync fa-spin text-lg text-amber-500"></i>
        <span class="text-sm font-medium text-amber-600">Estimasi selesai: Beberapa menit lagi</span>
    </div>
@endsection
