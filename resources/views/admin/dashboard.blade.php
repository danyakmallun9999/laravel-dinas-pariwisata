@pushOnce('styles')


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
            <div class="hidden md:flex items-center gap-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner -->
            <!-- Welcome Banner (Compact & Elegant) -->
            <!-- Welcome Banner (Compact & Elegant) -->
            <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            Selamat Datang, <span class="text-blue-600">{{ Auth::user()->name }}</span>! 👋
                        </h1>
                        <p class="text-gray-500 text-sm mt-1">
                            Siap untuk mengelola pariwisata Jepara hari ini?
                        </p>
                    </div>
                    <div class="hidden md:block">
                         <a href="{{ route('welcome') }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-2xl text-sm font-bold transition-all border border-gray-200 hover:border-blue-200">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            Lihat Website
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.places.create') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-blue-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-blue-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 border border-blue-100">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-blue-600 transition-colors">Tambah Destinasi</span>
                    </div>
                </a>

                <a href="{{ route('admin.posts.create') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-purple-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-purple-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 border border-purple-100">
                            <i class="fa-solid fa-pen-nib"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-purple-600 transition-colors">Tulis Berita</span>
                    </div>
                </a>

                <a href="{{ route('admin.events.create') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-orange-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-orange-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl group-hover:bg-orange-600 group-hover:text-white transition-all duration-300 border border-orange-100">
                            <i class="fa-solid fa-calendar-plus"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-orange-600 transition-colors">Agenda Baru</span>
                    </div>
                </a>

                <!-- Check-in Scanner Shortcut -->
                <a href="{{ route('admin.scan.index') }}" class="group bg-white p-1 rounded-[2.5rem] border border-gray-200 hover:border-emerald-300 transition-colors">
                    <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full group-hover:bg-emerald-50/30 transition-colors">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 border border-emerald-100">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <span class="font-bold text-gray-700 group-hover:text-emerald-600 transition-colors">Check-in Tiket</span>
                    </div>
                </a>
            </div>

            <!-- Main Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Places -->
                <!-- Places -->
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl border border-blue-200">
                                <i class="fa-solid fa-map-marked-alt"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 border border-blue-100">+{{ $stats['places_count'] > 0 ? 'Active' : '0' }}</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['places_count'] }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Destinasi Wisata</p>
                    </div>
                </div>

                <!-- Posts -->
                <!-- Posts -->
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl border border-purple-200">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 border border-purple-100">News</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['posts_count'] }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Berita & Artikel</p>
                    </div>
                </div>

                <!-- Events -->
                <!-- Events -->
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl border border-orange-200">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-orange-50 text-orange-600 border border-orange-100">Events</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $stats['events_count'] }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Agenda Kegiatan</p>
                    </div>
                </div>

                <!-- Categories -->
                <!-- Categories -->
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center text-xl border border-pink-200">
                                <i class="fa-solid fa-tags"></i>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-pink-50 text-pink-600 border border-pink-100">Types</span>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ \App\Models\Category::count() }}</h3>
                        <p class="text-sm text-gray-500 font-medium">Kategori Wisata</p>
                    </div>
                </div>
            </div>

            <!-- Content Grid 1: Chart & Upcoming Events -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Statistics Chart -->
                <!-- Statistics Chart -->
                <div class="lg:col-span-2 bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Distribusi Kategori Wisata</h3>
                            <a href="{{ route('admin.categories.index') }}" class="text-sm text-blue-600 hover:underline">Kelola Kategori</a>
                        </div>
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <!-- Chart -->
                            <div class="relative w-48 h-48 md:w-56 md:h-56 flex-shrink-0">
                                <canvas id="categoriesChart"></canvas>
                                <!-- Center Text -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-3xl font-bold text-gray-800">{{ $stats['places_count'] }}</span>
                                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Total</span>
                                </div>
                            </div>

                            <!-- Custom Legend -->
                            <div class="flex-1 w-full grid grid-cols-2 gap-3">
                                @foreach($stats['categories'] as $category)
                                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white transition-colors border border-transparent hover:border-gray-100">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $category->color }}"></span>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700 truncate">{{ $category->name }}</span>
                                            <span class="text-xs font-bold text-gray-500">{{ $category->places_count }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200/50 rounded-full h-1 mt-1">
                                            <div class="bg-gray-300 h-1 rounded-full" style="width: {{ $stats['places_count'] > 0 ? ($category->places_count / $stats['places_count']) * 100 : 0 }}%; background-color: {{ $category->color }}"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events Widget -->
                <!-- Upcoming Events Widget -->
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Agenda Terdekat</h3>
                            <a href="{{ route('admin.events.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($stats['upcoming_events'] as $event)
                                <div class="flex gap-4 items-start group">
                                    <div class="flex flex-col items-center bg-white rounded-2xl p-2 min-w-[3.5rem] border border-gray-100 group-hover:border-blue-200 transition-colors">
                                        <span class="text-xs font-bold text-red-500 uppercase">{{ $event->start_date->isoFormat('MMM') }}</span>
                                        <span class="text-xl font-bold text-gray-800">{{ $event->start_date->isoFormat('DD') }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 line-clamp-1 group-hover:text-blue-600 transition-colors">{{ $event->title }}</h4>
                                        <p class="text-xs text-gray-500 mb-1 line-clamp-1">{{ $event->location }}</p>
                                        <span class="text-[0.65rem] bg-white border border-gray-100 text-gray-500 px-2 py-0.5 rounded-full inline-block">
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
            </div>

            <!-- Content Grid 2: Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Places -->
                <!-- Recent Places -->
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Destinasi Terbaru</h3>
                            <a href="{{ route('admin.places.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($stats['recent_places'] as $place)
                            <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white transition-colors group border border-transparent hover:border-gray-100">
                                @if($place->image_path)
                                    <img src="{{ asset($place->image_path) }}" alt="{{ $place->name }}" class="w-12 h-12 rounded-xl object-cover group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 truncate">{{ $place->name }}</h4>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white border border-white/20" style="background-color: {{ $place->category->color }}">
                                            {{ $place->category->name }}
                                        </span>
                                        <span>• {{ $place->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.places.edit', $place) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors bg-white rounded-lg border border-gray-100 opacity-0 group-hover:opacity-100">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                            @empty
                                <p class="text-gray-500 text-sm italic">Belum ada data destinasi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Posts -->
                <!-- Recent Posts -->
                <div class="bg-white p-1 rounded-[2.5rem] border border-gray-200">
                    <div class="p-6 rounded-[2rem] border border-gray-100 bg-gray-50/30 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Berita & Artikel Terbaru</h3>
                            <a href="{{ route('admin.posts.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($stats['recent_posts'] as $post)
                            <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-white transition-colors group border border-transparent hover:border-gray-100">
                                <div class="flex flex-col items-center justify-center w-12 h-12 rounded-xl bg-white text-indigo-600 border border-gray-100">
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
                                <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors bg-white rounded-lg border border-gray-100 opacity-0 group-hover:opacity-100">
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
    </div>

    <!-- Chart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriesCtx = document.getElementById('categoriesChart');
            if (categoriesCtx) {
                new Chart(categoriesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($stats['categories']->pluck('name')->values()),
                        datasets: [{
                            data: @json($stats['categories']->pluck('places_count')->values()),
                            backgroundColor: @json($stats['categories']->pluck('color')->values()),
                            borderWidth: 0,
                            hoverOffset: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%', 
                        plugins: {
                            legend: {
                                display: false // We use custom legend
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.parsed || 0;
                                        let total = context.chart._metasets[context.datasetIndex].total;
                                        let percentage = ((value / total) * 100).toFixed(1) + '%';
                                        return label + ': ' + value + ' (' + percentage + ')';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
