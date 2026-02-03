<!DOCTYPE html>
<html class="light scroll-smooth overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Portal Wisata - {{ config('app.name', 'Jepara') }}</title>

    <!-- Fonts & Icons -->


    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark font-display antialiased transition-colors duration-200 overflow-x-hidden">

    <!-- Top Navigation -->
    @include('layouts.partials.navbar')

    <!-- Page Content -->
    <main class="pt-20">
        {{ $slot }}
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')
</body>
</html>
