<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">E-Tiket</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">Pembayaran</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-credit-card text-primary text-2xl"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">Pembayaran Tiket</h1>
                    <p class="text-slate-500 dark:text-slate-400">Silakan lanjutkan pembayaran untuk menyelesaikan pesanan</p>
                </div>

                <!-- Order Summary -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-6">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-primary"></i> Ringkasan Pesanan
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Nomor Pesanan</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Tiket</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $order->ticket->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Destinasi</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $order->ticket->place->name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Tanggal Kunjungan</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $order->visit_date->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Jumlah Tiket</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $order->quantity }} tiket</span>
                        </div>
                        
                        <div class="border-t border-slate-200 dark:border-slate-600 pt-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-slate-700 dark:text-slate-300">Total Pembayaran</span>
                                <span class="text-2xl font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Button -->
                <div class="space-y-3">
                    <button id="pay-button" 
                            class="block w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-credit-card"></i>Bayar Sekarang
                    </button>
                    
                    <a href="{{ route('tickets.my') }}" 
                       class="block w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300 text-center">
                        Bayar Nanti
                    </a>
                </div>

                <!-- Customer Info -->
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400 text-center">
                        <i class="fa-solid fa-envelope mr-1 text-primary"></i>
                        Invoice pembayaran juga telah dikirim ke email <strong class="text-slate-700 dark:text-slate-300">{{ $order->customer_email }}</strong>
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
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Membuka halaman pembayaran...';
        this.disabled = true;
        
        // Re-enable button after 3 seconds
        setTimeout(() => {
            this.innerHTML = '<i class="fa-solid fa-credit-card mr-2"></i>Bayar Sekarang';
            this.disabled = false;
        }, 3000);
    });
</script>
</x-public-layout>
