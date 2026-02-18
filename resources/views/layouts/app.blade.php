<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="{{ asset('images/logo-kura.png') }}" type="image/png">

        <!-- Fonts -->

        
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <!-- TinyMCE (Official Cloud) -->
        <script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('styles')
        <style>
            :root { 
                --sidebar-width: 16rem; 
            }
            html.sidebar-minimized { 
                --sidebar-width: 5rem; 
            }
            .preload * { transition: none !important; }
            
            /* Sidebar Toggle Logic */
            @media (min-width: 768px) {
                .toggle-maximize { display: none !important; }
                .toggle-minimize { display: flex !important; }
                
                html.sidebar-minimized .toggle-maximize { display: flex !important; }
                html.sidebar-minimized .toggle-minimize { display: none !important; }
            }
            
            /* Ensure sidebar width is consistent */
            aside {
                width: var(--sidebar-width) !important;
                min-width: var(--sidebar-width) !important;
                max-width: var(--sidebar-width) !important;
            }
            
            /* Prevent layout shift during navigation */
            html.sidebar-minimized aside {
                width: 5rem !important;
                min-width: 5rem !important;
                max-width: 5rem !important;
            }
        </style>
        <script>
            // Initialize sidebar state immediately to prevent flicker (SSR-like behavior)
            (function() {
                const isMinimized = localStorage.getItem('sidebarMinimized') === 'true';
                if (isMinimized) {
                    document.documentElement.classList.add('sidebar-minimized');
                } else {
                    document.documentElement.classList.remove('sidebar-minimized');
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 preload" 
          x-data="{ 
              sidebarMinimized: localStorage.getItem('sidebarMinimized') === 'true',
              mobileSidebarOpen: false,
              screenWidth: window.innerWidth,
              get isDesktop() { return this.screenWidth >= 768; },
              get isSidebarMini() { return this.sidebarMinimized && this.isDesktop; },
              syncWithDom() {
                  // Ensure local state matches DOM/LocalStorage (crucial for SPA navigation)
                  this.sidebarMinimized = localStorage.getItem('sidebarMinimized') === 'true';
                  document.documentElement.classList.toggle('sidebar-minimized', this.sidebarMinimized);
              }
          }"
          x-init="syncWithDom();
                  window.addEventListener('resize', () => screenWidth = window.innerWidth);
                  $watch('sidebarMinimized', value => {
                      localStorage.setItem('sidebarMinimized', value);
                      document.documentElement.classList.toggle('sidebar-minimized', value);
                  }); 
                  window.addEventListener('load', () => document.body.classList.remove('preload'));
                  
                  // Sync on Livewire SPA navigation
                  document.addEventListener('livewire:navigated', () => {
                      syncWithDom();
                  });"
      :class="{'overflow-hidden': mobileSidebarOpen}">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="flex-shrink-0 z-50 transition-all duration-300 ease-in-out w-0 md:w-[var(--sidebar-width)]">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden relative">
                <!-- Top Header (Mobile Only) -->
                <header class="md:hidden bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sticky top-0 z-[40]">
                    <div class="flex items-center gap-3">
                        <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2 -ml-2 rounded-lg hover:bg-gray-50">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        <span class="font-bold text-gray-800 text-lg">Admin Panel</span>
                    </div>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
                         <img src="{{ asset('images/logo-kura.png') }}" alt="Logo" class="w-9 h-9 object-contain">
                    </a>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-white">
                    @isset($header)
                        <div x-data="{ mobileHeaderOpen: false }" class="sticky top-0 z-30 bg-white ">
                            <!-- Mobile Toggle Bar -->
                            <div class="md:hidden flex justify-between items-center px-4 py-2 bg-white border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors relative z-40"
                                 @click="mobileHeaderOpen = !mobileHeaderOpen">
                                <div class="flex items-center gap-2">
                                     <div class="w-1 h-4 bg-blue-500 rounded-full"></div>
                                     <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Halaman</span>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 transition-colors"
                                     :class="mobileHeaderOpen ? 'bg-blue-50 text-blue-600' : ''">
                                     <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="mobileHeaderOpen ? 'rotate-180' : ''"></i>
                                </div>
                            </div>

                            <!-- Header Content: Desktop (always visible) -->
                            <header class="hidden md:flex bg-white border-b border-gray-200 h-20 items-center">
                                <div class="w-full px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>

                            <!-- Header Content: Mobile (collapsible) -->
                            <div class="md:hidden">
                                <header x-show="mobileHeaderOpen"
                                        x-collapse
                                        style="display: none;"
                                        class="bg-white border-b border-gray-200">
                                    <div class="w-full px-4 py-4">
                                        {{ $header }}
                                    </div>
                                </header>
                            </div>
                        </div>
                    @endisset

                    @if(isset($slot) && $slot->isNotEmpty())
                        @if($fullWidth ?? false)
                            {{ $slot }}
                        @else
                            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
                                {{ $slot }}
                            </div>
                        @endif
                    @else
                        @if($fullWidth ?? false)
                            @yield('content')
                        @else
                            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
                                @yield('content')
                            </div>
                        @endif
                    @endif
                </main>
            </div>
        </div>
        @livewireScriptConfig
        @stack('scripts')
        
        <!-- Global Notification Toast (Alpine.js) -->
        <div x-data="{ 
            show: {{ session('success') || session('status') ? 'true' : 'false' }}, 
            message: '{{ session('success') ?? session('status') }}',
            type: 'success',
            init() {
                if (this.show) setTimeout(() => this.show = false, 3000);
                window.addEventListener('notify', (e) => {
                    this.message = e.detail.message;
                    this.type = e.detail.type || 'success';
                    this.show = true;
                    setTimeout(() => this.show = false, 3000);
                });
            }
        }" 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex items-center gap-4 px-5 py-4 rounded-2xl shadow-xl border bg-white"
        :class="{
            'border-green-100 dark:border-green-900/30': type === 'success',
            'border-red-100 dark:border-red-900/30': type === 'error'
        }" 
        style="display: none;">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                 :class="{
                     'bg-green-50 text-green-500': type === 'success',
                     'bg-red-50 text-red-500': type === 'error'
                 }">
                <i class="fa-solid text-lg" :class="type === 'success' ? 'fa-check' : 'fa-xmark'"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-sm" x-text="type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan!'"></h4>
                <p class="text-gray-500 text-sm" x-text="message"></p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Global Confirmation Modal (Alpine.js) -->
        <div x-data="{ 
            open: false, 
            title: 'Konfirmasi', 
            message: 'Apakah Anda yakin?', 
            confirmCallback: null,
            processing: false,
            init() {
                window.confirmAction = (title, message, callback) => {
                    this.title = title || 'Konfirmasi';
                    this.message = message || 'Apakah Anda yakin ingin melanjutkan?';
                    this.confirmCallback = callback;
                    this.open = true;
                }
            },
            confirm() {
                if (this.confirmCallback) {
                    this.processing = true;
                    this.confirmCallback();
                    setTimeout(() => { this.open = false; this.processing = false; }, 500); 
                }
            }
        }"
        x-show="open" 
        style="display: none;"
        class="fixed inset-0 z-[9999] flex items-center justify-center px-4"
        x-cloak>
            
            <!-- Backdrop -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"
                 @click="open = false"></div>

            <!-- Modal Card -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden p-6 mx-auto">
                 
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-5 text-red-500 animate-bounce-slow">
                        <i class="fa-solid fa-trash-can text-2xl"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-2" x-text="title"></h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-8" x-text="message"></p>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="open = false" 
                                class="w-full px-5 py-3 rounded-xl text-sm font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 transition-colors">
                            Batal
                        </button>
                        <button @click="confirm()" 
                                class="w-full px-5 py-3 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-600/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                            <span x-show="!processing">Ya, Hapus</span>
                            <i class="fa-solid fa-circle-notch fa-spin" x-show="processing"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Intercept Delete Forms
                document.body.addEventListener('submit', function(e) {
                    if (e.target.classList.contains('delete-form')) {
                        e.preventDefault();
                        const form = e.target;
                        window.confirmAction(
                            'Hapus Data?', 
                            'Data yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?', 
                            () => form.submit()
                        );
                    }
                });
            });
        </script>
    </body>
</html>
