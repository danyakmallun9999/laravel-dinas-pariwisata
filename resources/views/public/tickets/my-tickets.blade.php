<x-public-layout>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 -mt-20 pt-32">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Tiket Saya</h1>

            @if(!isset($orders))
                <!-- Email Form -->
                <div class="max-w-md mx-auto">
                    <p class="text-gray-600 mb-6 text-center">Masukkan email yang Anda gunakan saat memesan tiket</p>
                    
                    <form action="{{ route('tickets.retrieve') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="email@example.com">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Cari Tiket Saya
                        </button>
                    </form>
                </div>
            @else
                <!-- Orders List -->
                @if($orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div class="border rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                    <div>
                                        <div class="text-sm text-gray-500">No. Pesanan</div>
                                        <div class="font-bold text-lg text-gray-800">{{ $order->order_number }}</div>
                                    </div>
                                    <div class="mt-2 md:mt-0">
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status == 'used' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <div class="text-sm text-gray-500">Tiket</div>
                                        <div class="font-semibold text-gray-800">{{ $order->ticket->name }}</div>
                                        <div class="text-sm text-gray-600">{{ $order->ticket->place->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Tanggal Kunjungan</div>
                                        <div class="font-semibold text-gray-800">{{ $order->visit_date->format('d F Y') }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Jumlah</div>
                                        <div class="font-semibold text-gray-800">{{ $order->quantity }} tiket</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Total</div>
                                        <div class="font-semibold text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('tickets.confirmation', $order->order_number) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-2 rounded-lg transition-colors duration-200 text-sm">
                                        <i class="fas fa-eye mr-1"></i>Lihat Detail
                                    </a>
                                    <a href="{{ route('tickets.download', $order->order_number) }}" 
                                       class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-center font-semibold py-2 rounded-lg transition-colors duration-200 text-sm">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('tickets.my') }}" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-search mr-1"></i>Cari dengan email lain
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-ticket-alt text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-500 text-lg mb-4">Tidak ada tiket ditemukan untuk email ini</p>
                        <a href="{{ route('tickets.my') }}" class="text-blue-600 hover:text-blue-700">
                            Coba lagi
                        </a>
                    </div>
                @endif
            @endif

            <div class="mt-8 text-center">
                <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke Daftar Tiket
                </a>
            </div>
        </div>
    </div>
</div>
</x-public-layout>
