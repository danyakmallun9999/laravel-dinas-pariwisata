@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')

@section('illustration')
    <!-- Locked Door Illustration -->
    <div class="relative w-full h-full">
        <!-- Main Icon Background -->
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-28 h-28 md:w-36 md:h-36 rounded-full bg-gradient-to-br from-rose-100 to-pink-100 flex items-center justify-center animate-float-slow shadow-xl shadow-rose-200/50">
                <i class="fa-solid fa-shield-halved text-5xl md:text-6xl text-rose-400"></i>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="absolute -top-2 right-2 md:right-4 animate-float">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-amber-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-key text-lg md:text-xl text-amber-500"></i>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 md:left-2 animate-float-delayed">
            <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-purple-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-ban text-sm md:text-base text-purple-400"></i>
            </div>
        </div>
        <div class="absolute top-4 -left-2 md:left-0 animate-float-slow">
            <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-blue-100 flex items-center justify-center shadow-lg">
                <i class="fa-solid fa-lock text-xs md:text-sm text-blue-400"></i>
            </div>
        </div>
    </div>
@endsection

@section('message', 'Oops, Area Terbatas! 🔒')
@section('description', 'Kayaknya kamu nggak punya akses ke halaman ini. Mungkin perlu izin khusus atau login dulu?')
