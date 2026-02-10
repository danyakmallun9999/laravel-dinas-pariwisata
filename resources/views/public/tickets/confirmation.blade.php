<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">E-Tiket</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">Konfirmasi</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8">
                <!-- Success Icon -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-4xl"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">Pesanan Berhasil!</h1>
                    <p class="text-slate-500 dark:text-slate-400">Terima kasih telah memesan tiket wisata</p>
                </div>

                <!-- Order Details -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">No. Pesanan</div>
                            <div class="font-bold text-xl text-slate-900 dark:text-white">{{ $order->order_number }}</div>
                        </div>
                        <div class="mt-2 md:mt-0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold
                                {{ $order->status == 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : '' }}
                                {{ $order->status == 'paid' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}">
                                @if($order->status == 'pending')
                                    <i class="fa-solid fa-clock"></i>
                                @else
                                    <i class="fa-solid fa-check-circle"></i>
                                @endif
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3 border-t border-slate-200 dark:border-slate-600 pt-4">
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
                            <span class="text-slate-600 dark:text-slate-400">Jumlah</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $order->quantity }} tiket</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200 dark:border-slate-600">
                            <span class="text-lg font-semibold text-slate-700 dark:text-slate-300">Total</span>
                            <span class="text-xl font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-6">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-user text-primary"></i> Informasi Pemesan
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-user w-5 text-slate-400"></i>
                            <span class="text-slate-700 dark:text-slate-300">{{ $order->customer_name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope w-5 text-slate-400"></i>
                            <span class="text-slate-700 dark:text-slate-300">{{ $order->customer_email }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-phone w-5 text-slate-400"></i>
                            <span class="text-slate-700 dark:text-slate-300">{{ $order->customer_phone }}</span>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="bg-gradient-to-br from-primary/5 to-indigo-500/5 rounded-2xl p-6 mb-6 text-center border border-primary/10">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-qrcode text-primary"></i> QR Code Tiket
                    </h3>
                    <div class="bg-white dark:bg-slate-800 inline-block p-4 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600">
        <div class="w-48 h-48 flex items-center justify-center bg-white p-2 rounded-xl">
                            <div id="qrcode"></div>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-4">Tunjukkan QR code ini saat berkunjung</p>
                </div>

                <!-- Payment Instructions -->
                @if($order->status === 'pending')
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-5 mb-6">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-yellow-600"></i> Instruksi Pembayaran
                        </h3>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mb-2">Metode: <strong>{{ ucfirst($order->payment_method) }}</strong></p>
                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            @if($order->payment_method === 'transfer')
                                <p>Silakan transfer ke rekening berikut:</p>
                                <p class="font-mono bg-white dark:bg-slate-800 p-3 rounded-xl mt-2 text-slate-900 dark:text-white">Bank BCA: 1234567890<br>a.n. Dinas Pariwisata Jepara</p>
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
                       class="flex-1 bg-primary hover:bg-primary/90 text-white text-center font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i>Download Tiket
                    </a>
                    <a href="{{ route('tickets.index') }}" 
                       class="flex-1 bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 dark:hover:bg-slate-500 text-slate-800 dark:text-white text-center font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-home"></i>Kembali ke Beranda
                    </a>
                </div>

                <!-- Email Notice -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-info-circle mr-1 text-primary"></i>
                        Konfirmasi pesanan telah dikirim ke email <strong class="text-slate-700 dark:text-slate-300">{{ $order->customer_email }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- QRCode.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ $order->order_number }}",
                width: 170,
                height: 170,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });
    </script>
</x-public-layout>
