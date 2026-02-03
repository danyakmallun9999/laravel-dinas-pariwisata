<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->

        
        <!-- FontAwesome -->

        
        <!-- TinyMCE (Official Cloud) -->
        <!-- TinyMCE (Official Cloud) -->
        <script src="https://cdn.tiny.cloud/1/4s9z14hu3s0t79sgv5en1l6dwen6jrdx5tkux16vps4qejt9/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <style>
            :root { --sidebar-width: 16rem; }
            .sidebar-minimized { --sidebar-width: 5rem; }
            .preload * { transition: none !important; }
            
            /* Sidebar Toggle Logic */
            .toggle-maximize { display: none !important; }
            .toggle-minimize { display: flex !important; }
            
            .sidebar-minimized .toggle-maximize { display: flex !important; }
            .sidebar-minimized .toggle-minimize { display: none !important; }
        </style>
        <script>
            if (localStorage.getItem('sidebarMinimized') === 'true') {
                document.documentElement.classList.add('sidebar-minimized');
            }
        </script>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 preload" 
          x-data="{ sidebarMinimized: localStorage.getItem('sidebarMinimized') === 'true' }" 
          x-init="$watch('sidebarMinimized', value => {
              localStorage.setItem('sidebarMinimized', value);
              document.documentElement.classList.toggle('sidebar-minimized', value);
          }); window.addEventListener('load', () => document.body.classList.remove('preload'))">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="flex-shrink-0 z-30 transition-all duration-300 ease-in-out w-[var(--sidebar-width)]">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden relative">
                <!-- Top Header (Optional, mostly for mobile toggle placeholder if needed, or breadcrumbs) -->
                <!-- For now, we keep it clean as sidebar handles nav -->

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                    @isset($header)
                        <header class="bg-white sticky top-0 z-20 h-20 flex items-center border-b border-gray-200">
                            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    @if(isset($attributes) && $attributes->get('full-width'))
                        {{ $slot }}
                    @else
                        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
                            {{ $slot }}
                        </div>
                    @endif
                </main>
            </div>
        </div>
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
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-4 px-5 py-4 rounded-2xl shadow-xl border bg-white"
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
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
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
