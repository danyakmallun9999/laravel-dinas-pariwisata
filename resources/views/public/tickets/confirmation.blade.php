<x-public-layout>
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-100 py-12 -mt-20 pt-32">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Success Icon -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-check-circle text-green-600 text-5xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Pesanan Berhasil!</h1>
                <p class="text-gray-600">Terima kasih telah memesan tiket wisata</p>
            </div>

            <!-- Order Details -->
            <div class="border-t border-b py-6 mb-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-sm text-gray-500">No. Pesanan</div>
                        <div class="font-bold text-lg text-gray-800">{{ $order->order_number }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Status</div>
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tiket:</span>
                        <span class="font-semibold text-gray-800">{{ $order->ticket->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Destinasi:</span>
                        <span class="font-semibold text-gray-800">{{ $order->ticket->place->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Kunjungan:</span>
                        <span class="font-semibold text-gray-800">{{ $order->visit_date->format('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah:</span>
                        <span class="font-semibold text-gray-800">{{ $order->quantity }} tiket</span>
                    </div>
                    <div class="flex justify-between text-lg">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Informasi Pemesan</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex">
                        <i class="fas fa-user w-6 text-gray-400"></i>
                        <span class="text-gray-700">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex">
                        <i class="fas fa-envelope w-6 text-gray-400"></i>
                        <span class="text-gray-700">{{ $order->customer_email }}</span>
                    </div>
                    <div class="flex">
                        <i class="fas fa-phone w-6 text-gray-400"></i>
                        <span class="text-gray-700">{{ $order->customer_phone }}</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Placeholder -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-center">
                <h3 class="font-semibold text-gray-800 mb-3">QR Code Tiket</h3>
                <div class="bg-white inline-block p-4 rounded-lg border-2 border-dashed border-gray-300">
                    <div class="w-48 h-48 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-qrcode text-gray-400 text-6xl mb-2"></i>
                            <p class="text-xs text-gray-500">QR Code untuk<br>{{ $order->order_number }}</p>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-3">Tunjukkan QR code ini saat berkunjung</p>
            </div>

            <!-- Payment Instructions -->
            @if($order->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Instruksi Pembayaran</h3>
                    <p class="text-sm text-gray-700 mb-2">Metode: <strong>{{ ucfirst($order->payment_method) }}</strong></p>
                    <div class="text-sm text-gray-600">
                        @if($order->payment_method === 'transfer')
                            <p>Silakan transfer ke rekening berikut:</p>
                            <p class="font-mono bg-white p-2 rounded mt-2">Bank BCA: 1234567890<br>a.n. Dinas Pariwisata Jepara</p>
                        @elseif($order->payment_method === 'cash')
                            <p>Pembayaran dapat dilakukan di lokasi wisata saat kunjungan.</p>
                        @else
                            <p>Instruksi pembayaran akan dikirim ke email Anda.</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('tickets.download', $order->order_number) }}" 
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>Download Tiket
                </a>
                <a href="{{ route('tickets.index') }}" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-center font-semibold py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
            </div>

            <!-- Email Notice -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Konfirmasi pesanan telah dikirim ke email <strong>{{ $order->customer_email }}</strong>
            </div>
        </div>
    </div>
</div>
</x-public-layout>
