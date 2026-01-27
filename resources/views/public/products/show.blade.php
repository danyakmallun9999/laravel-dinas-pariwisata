<x-public-layout>
    <div class="bg-gray-50 min-h-screen pb-12 pt-4 md:pt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('welcome') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            Beranda
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-gray-400 mx-2 text-sm">chevron_right</span>
                            <a href="{{ route('welcome') }}#potency" class="text-sm font-medium text-gray-700 hover:text-blue-600">Ekonomi Kreatif</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined text-gray-400 mx-2 text-sm">chevron_right</span>
                            <span class="text-sm font-medium text-gray-500">{{ $product->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-white rounded-3xl p-6 md:p-10 shadow-xl border border-gray-100">
                <!-- Image Section -->
                <div class="relative overflow-hidden rounded-2xl group h-[400px] lg:h-[500px]">
                    <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                </div>

                <!-- Info Section -->
                <div class="flex flex-col">
                    <div class="mb-6">
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2">{{ $product->name }}</h1>
                        <p class="text-blue-600 font-bold text-2xl">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>

                    <div class="prose prose-slate mb-8 text-gray-600 leading-relaxed">
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Deskripsi Produk</h3>
                        <p>{{ $product->description }}</p>
                    </div>

                    <!-- Seller Info Card -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mt-auto">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Penjual</h3>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                                <span class="material-symbols-outlined">storefront</span>
                            </div>
                            <div>
                                <p class="font-bold text-lg text-slate-800">{{ $product->seller_name }}</p>
                                <p class="text-sm text-slate-500">UMKM Jepara</p>
                            </div>
                        </div>

                        @if($product->seller_contact)
                            @php
                                $phone = $product->seller_contact;
                                // Basic formatting for WA link
                                if(Str::startsWith($phone, '0')) {
                                    $phone = '62' . substr($phone, 1);
                                }
                                $message = "Halo, saya tertarik dengan produk {$product->name} yang ada di Portal Pariwisata Jepara.";
                                $waLink = "https://wa.me/{$phone}?text=" . urlencode($message);
                            @endphp
                            <a href="{{ $waLink }}" target="_blank" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg hover:shadow-green-500/30">
                                <i class="fa-brands fa-whatsapp text-xl"></i>
                                Hubungi Penjual
                            </a>
                        @else
                            <button disabled class="w-full flex items-center justify-center gap-2 bg-gray-300 text-gray-500 font-bold py-3 px-6 rounded-xl cursor-not-allowed">
                                Kontak Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Products (Simple query for now) -->
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-slate-800 mb-6">Produk Lainnya</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach(\App\Models\Product::where('id', '!=', $product->id)->inRandomOrder()->take(4)->get() as $related)
                        <a href="{{ route('products.show', $related) }}" class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-gray-100">
                            <div class="h-40 overflow-hidden">
                                <img src="{{ $related->image_path }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-slate-800 truncate mb-1">{{ $related->name }}</h3>
                                <p class="text-blue-600 text-sm font-semibold">Rp {{ number_format($related->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-public-layout>
