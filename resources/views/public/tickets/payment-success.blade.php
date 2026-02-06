<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">E-Tiket</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">Pembayaran Berhasil</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 text-center">
                <!-- Success Icon -->
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400 text-4xl"></i>
                </div>

                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-3">Pembayaran Berhasil!</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Terima kasih, pembayaran Anda telah kami terima.</p>

                <!-- Order Info -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-8 text-left">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Nomor Pesanan</p>
                            <p class="font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Status</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded-xl">
                                <i class="fa-solid fa-check-circle"></i>
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Tiket</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $order->ticket->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Jumlah</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $order->quantity }} tiket</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="bg-gradient-to-br from-primary/5 to-indigo-500/5 border border-primary/10 rounded-2xl p-6 mb-8">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-qrcode text-primary"></i> QR Code Tiket Anda
                    </h3>
                    <div id="qrcode" class="flex justify-center mb-3"></div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tunjukkan QR code ini saat berkunjung</p>
                    <div class="mt-4">
                        <button onclick="downloadQR()" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all">
                            <i class="fa-solid fa-download"></i>Download QR Code
                        </button>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-5 mb-8 text-left">
                    <div class="flex items-start">
                        <i class="fa-solid fa-info-circle text-primary mt-0.5 mr-3"></i>
                        <div class="text-sm text-slate-700 dark:text-slate-300">
                            <p class="font-bold mb-2 text-slate-900 dark:text-white">Langkah Selanjutnya:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>E-Tiket telah dikirim ke email <strong>{{ $order->customer_email }}</strong></li>
                                <li>Simpan atau cetak e-tiket Anda</li>
                                <li>Tunjukkan e-tiket saat berkunjung ke {{ $order->ticket->place->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('tickets.download', $order->order_number) }}" 
                       class="block w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i>Download E-Tiket
                    </a>
                    
                    <a href="{{ route('tickets.index') }}" 
                       class="block w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300">
                        Pesan Tiket Lainnya
                    </a>
                </div>
            </div>
        </div>
    </div>

<!-- QRCode.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
// Generate QR code on page load
document.addEventListener('DOMContentLoaded', function() {
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $order->order_number }}",
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
});

function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = 'ticket-qr-{{ $order->order_number }}.png';
        link.href = url;
        link.click();
    }
}
</script>
</x-public-layout>
