@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')

@section('illustration')
    <!-- Fun Lost Traveler Illustration -->
    <div class="relative w-full h-full">
        <!-- Main Icon Background -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-28 h-28 md:w-36 md:h-36 rounded-full bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center animate-float-slow shadow-xl shadow-amber-200/50">
                <i class="fa-solid fa-compass text-5xl md:text-6xl text-amber-400"></i>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="absolute -top-2 right-2 md:right-4 animate-float">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-map-location-dot text-lg md:text-xl text-blue-500"></i>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 md:left-2 animate-float-delayed">
            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-rose-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-question text-sm md:text-base text-rose-400"></i>
            </div>
        </div>
        <div class="absolute top-4 -left-2 md:left-0 animate-float-slow">
            <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-cyan-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-route text-xs md:text-sm text-cyan-500"></i>
            </div>
        </div>
    </div>
@endsection

@section('message', 'Waduh, Nyasar Nih! 🗺️')
@section('description', 'Halaman yang kamu cari kayaknya udah pindah atau nggak ada. Tenang, banyak tempat seru lainnya yang bisa dijelajahi!')
