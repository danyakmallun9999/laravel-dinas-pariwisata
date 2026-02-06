<x-public-layout>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 -mt-20 pt-32">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Pembayaran Tiket</h1>
                <p class="text-gray-600">Silakan lanjutkan pembayaran untuk menyelesaikan pesanan Anda</p>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nomor Pesanan</span>
                        <span class="font-semibold text-gray-800">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tiket</span>
                        <span class="font-semibold text-gray-800">{{ $order->ticket->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Destinasi</span>
                        <span class="font-semibold text-gray-800">{{ $order->ticket->place->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Kunjungan</span>
                        <span class="font-semibold text-gray-800">{{ $order->visit_date->format('d F Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Tiket</span>
                        <span class="font-semibold text-gray-800">{{ $order->quantity }} tiket</span>
                    </div>
                    
                    <div class="border-t pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Button -->
            <div class="space-y-3">
                <button id="pay-button" 
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-lg transition-colors duration-200 text-center">
                    <i class="fas fa-credit-card mr-2"></i>Bayar Sekarang
                </button>
                
                <a href="{{ route('tickets.my') }}" 
                   class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition-colors duration-200 text-center">
                    Bayar Nanti
                </a>
            </div>

            <!-- Customer Info -->
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm text-gray-600 text-center">
                    Invoice pembayaran juga telah dikirim ke email <strong>{{ $order->customer_email }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Xendit Snap.js Script -->
<script src="https://js.xendit.co/v1/xendit.min.js"></script>
<script>
    // Initialize Xendit with your public key
    Xendit.setPublishableKey('{{ config('services.xendit.public_key') }}');

    document.getElementById('pay-button').addEventListener('click', function() {
        // Open Xendit checkout
        window.open('{{ $order->xendit_invoice_url }}', '_blank');
        
        // Optional: Show loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membuka halaman pembayaran...';
        this.disabled = true;
        
        // Re-enable button after 3 seconds
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Bayar Sekarang';
            this.disabled = false;
        }, 3000);
    });
</script>
</x-public-layout>
