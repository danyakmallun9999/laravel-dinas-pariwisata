<x-public-layout>
<div class="min-h-screen bg-gradient-to-br from-red-50 to-orange-100 py-12 -mt-20 pt-32">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <!-- Failed Icon -->
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-times-circle text-red-600 text-4xl"></i>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-3">Pembayaran Gagal</h1>
            <p class="text-gray-600 mb-8">Maaf, pembayaran Anda tidak dapat diproses.</p>

            <!-- Order Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nomor Pesanan</p>
                        <p class="font-semibold text-gray-800">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tiket</p>
                        <p class="font-semibold text-gray-800">{{ $order->ticket->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total</p>
                        <p class="font-semibold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Reasons -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex items-start text-left">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-2">Kemungkinan Penyebab:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Pembayaran dibatalkan</li>
                            <li>Saldo tidak mencukupi</li>
                            <li>Waktu pembayaran habis</li>
                            <li>Terjadi kesalahan teknis</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('tickets.payment', $order->order_number) }}" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-redo mr-2"></i>Coba Bayar Lagi
                </a>
                
                <a href="{{ route('tickets.my') }}" 
                   class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition-colors duration-200">
                    Lihat Pesanan Saya
                </a>
                
                <a href="{{ route('tickets.index') }}" 
                   class="block w-full text-gray-600 hover:text-gray-800 font-medium py-2 transition-colors duration-200">
                    Kembali ke Daftar Tiket
                </a>
            </div>

            <!-- Help -->
            <div class="mt-8 pt-6 border-t">
                <p class="text-sm text-gray-600">
                    Butuh bantuan? Hubungi kami di <strong>support@jepara.go.id</strong>
                </p>
            </div>
        </div>
    </div>
</div>
</x-public-layout>
