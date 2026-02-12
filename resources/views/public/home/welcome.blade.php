<!DOCTYPE html>
<html class="light scroll-smooth overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <x-seo 
        title="Jelajah Jepara - Portal Wisata Resmi" 
        description="Temukan keindahan Jepara, dari wisata alam memukau di Karimunjawa hingga kekayaan budaya ukir yang mendunia. Panduan lengkap liburanmu ada di sini!" 
        image="{{ asset('images/logo-kura.png') }}"
    />
    <link rel="icon" href="{{ asset('images/logo-kura.png') }}" type="image/png">

    {{-- Leaflet & Icon --}}
    {{-- Local assets handled by Vite --}}

    {{-- Fonts & Icons --}}

    {{-- Scripts & Styles --}}
    @vite(['resources/css/app.css', 'resources/css/pages/welcome.css', 'resources/js/pages/welcome.js', 'resources/js/app.js'])
</head>

<body
    class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-display antialiased transition-colors duration-200 overflow-x-hidden pt-20"
    x-data="mapComponent({
        routes: {
            places: '{{ route('places.geojson') }}',
            boundaries: '{{ route('boundaries.geojson') }}',
            infrastructures: '{{ route('infrastructures.geojson') }}',
            landUses: '{{ route('land_uses.geojson') }}',
            search: '/search/places'
        },
        categories: {{ Js::from($categories) }}
    })">

    {{-- Top Navigation --}}
    @include('layouts.partials.navbar')

    {{-- Hero Section --}}
    @include('public.home.sections.hero')

    {{-- Stats Section --}}
    @include('public.home.sections.stats')

    {{-- Profile Section --}}
    @include('public.home.sections.profile')

    {{-- History & Legends Section --}}
    @include('public.home.sections.history-legends')

    {{-- Culinary Carousel Section --}}
    @include('public.home.sections.culinary-carousel')

    {{-- Culture Section --}}
    @include('public.home.sections.culture')

    {{-- Upcoming Event Section --}}
    @include('public.home.sections.upcoming-event')

    {{-- Tourism Carousel Section --}}
    @include('public.home.sections.tourism-carousel')



    {{-- News Section --}}
    @include('public.home.sections.news')

    {{-- Footer --}}
    @include('layouts.partials.footer')

</body>

</html>
