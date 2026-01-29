@pushOnce('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endPushOnce

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Admin Panel</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    Dashboard
                </h2>
            </div>
            <!-- Date Display -->
            <div class="hidden md:flex items-center gap-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 md:p-10 text-white shadow-xl relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                    <p class="text-blue-100 text-lg max-w-2xl">Kelola destinasi wisata, berita, dan agenda kegiatan Kabupaten Jepara dalam satu panel terintegrasi.</p>
                </div>
                <!-- Decorative Circles -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl"></div>
                <div class="absolute bottom-0 right-20 -mb-16 w-48 h-48 rounded-full bg-white opacity-10 blur-2xl"></div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.places.create') }}" class="flex flex-col items-center justify-center gap-3 p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 hover:-translate-y-1 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <span class="font-semibold text-gray-700 group-hover:text-blue-600">Tambah Destinasi</span>
                </a>

                <a href="{{ route('admin.posts.create') }}" class="flex flex-col items-center justify-center gap-3 p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 hover:-translate-y-1 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <span class="font-semibold text-gray-700 group-hover:text-purple-600">Tulis Berita</span>
                </a>

                <a href="{{ route('admin.events.create') }}" class="flex flex-col items-center justify-center gap-3 p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-200 hover:-translate-y-1 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xl group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <span class="font-semibold text-gray-700 group-hover:text-orange-600">Agenda Baru</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center justify-center gap-3 p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 hover:-translate-y-1 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <span class="font-semibold text-gray-700 group-hover:text-green-600">Laporan</span>
                </a>
            </div>

            <!-- Main Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Places -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-map-marked-alt"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded bg-blue-50 text-blue-600">+{{ $stats['places_count'] > 0 ? 'Active' : '0' }}</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['places_count'] }}</h3>
                    <p class="text-sm text-gray-500">Destinasi Wisata</p>
                </div>

                <!-- Posts -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded bg-purple-50 text-purple-600">News</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['posts_count'] }}</h3>
                    <p class="text-sm text-gray-500">Berita & Artikel</p>
                </div>

                <!-- Events -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded bg-orange-50 text-orange-600">Events</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['events_count'] }}</h3>
                    <p class="text-sm text-gray-500">Agenda Kegiatan</p>
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded bg-pink-50 text-pink-600">Types</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-800">{{ \App\Models\Category::count() }}</h3>
                    <p class="text-sm text-gray-500">Kategori Wisata</p>
                </div>
            </div>

            <!-- Content Grid 1: Chart & Upcoming Events -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Statistics Chart -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Distribusi Kategori Wisata</h3>
                        <a href="{{ route('admin.categories.index') }}" class="text-sm text-blue-600 hover:underline">Kelola Kategori</a>
                    </div>
                    <div class="h-64 relative">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>

                <!-- Upcoming Events Widget -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Agenda Terdekat</h3>
                        <a href="{{ route('admin.events.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($stats['upcoming_events'] as $event)
                            <div class="flex gap-4 items-start group">
                                <div class="flex flex-col items-center bg-gray-50 rounded-lg p-2 min-w-[3.5rem] border border-gray-100 group-hover:border-blue-200 transition-colors">
                                    <span class="text-xs font-bold text-red-500 uppercase">{{ $event->start_date->isoFormat('MMM') }}</span>
                                    <span class="text-xl font-bold text-gray-800">{{ $event->start_date->isoFormat('DD') }}</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 line-clamp-1 group-hover:text-blue-600 transition-colors">{{ $event->title }}</h4>
                                    <p class="text-xs text-gray-500 mb-1 line-clamp-1">{{ $event->location }}</p>
                                    <span class="text-[0.65rem] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full inline-block">
                                        {{ $event->start_date->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <i class="fa-regular fa-calendar-xmark text-3xl mb-2"></i>
                                <p class="text-sm">Belum ada agenda terdekat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Content Grid 2: Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Places -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Destinasi Terbaru</h3>
                        <a href="{{ route('admin.places.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($stats['recent_places'] as $place)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            @if($place->image_path)
                                <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-12 h-12 rounded-lg object-cover shadow-sm group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-800 truncate">{{ $place->name }}</h4>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white" style="background-color: {{ $place->category->color }}">
                                        {{ $place->category->name }}
                                    </span>
                                    <span>• {{ $place->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.places.edit', $place) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </div>
                        @empty
                            <p class="text-gray-500 text-sm italic">Belum ada data destinasi.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Berita & Artikel Terbaru</h3>
                        <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($stats['recent_posts'] as $post)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600">
                                <i class="fa-solid {{ $post->type == 'news' ? 'fa-newspaper' : 'fa-bullhorn' }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-800 truncate">{{ $post->title }}</h4>
                                <p class="text-xs text-gray-500">
                                    {{ $post->published_at ? $post->published_at->format('d M Y') : 'Draft' }} • 
                                    <span class="{{ $post->is_published ? 'text-green-600' : 'text-amber-600' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </p>
                            </div>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </div>
                        @empty
                            <p class="text-gray-500 text-sm italic">Belum ada berita terbaru.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriesCtx = document.getElementById('categoriesChart');
            if (categoriesCtx) {
                new Chart(categoriesCtx, {
                    type: 'bar', // Changed to bar for better readability on distribution
                    data: {
                        labels: @json($stats['categories']->pluck('name')->values()),
                        datasets: [{
                            label: 'Jumlah Destinasi',
                            data: @json($stats['categories']->pluck('places_count')->values()),
                            backgroundColor: @json($stats['categories']->pluck('color')->values()),
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false 
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    drawBorder: false,
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
