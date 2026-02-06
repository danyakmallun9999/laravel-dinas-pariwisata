<x-public-layout>
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-100 py-12 -mt-20 pt-32">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-3">Pembayaran Berhasil!</h1>
            <p class="text-gray-600 mb-8">Terima kasih, pembayaran Anda telah kami terima.</p>

            <!-- Order Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nomor Pesanan</p>
                        <p class="font-semibold text-gray-800">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tiket</p>
                        <p class="font-semibold text-gray-800">{{ $order->ticket->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Jumlah</p>
                        <p class="font-semibold text-gray-800">{{ $order->quantity }} tiket</p>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="bg-white border-2 border-gray-200 rounded-lg p-6 mb-8">
                <h3 class="font-semibold text-gray-800 mb-4 text-center">QR Code Tiket Anda</h3>
                <div id="qrcode" class="flex justify-center mb-3"></div>
                <p class="text-xs text-gray-500 text-center">Tunjukkan QR code ini saat berkunjung</p>
                <div class="mt-4 text-center">
                    <button onclick="downloadQR()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-download mr-2"></i>Download QR Code
                    </button>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <div class="flex items-start text-left">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-2">Langkah Selanjutnya:</p>
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
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>Download E-Tiket
                </a>
                
                <a href="{{ route('tickets.index') }}" 
                   class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg transition-colors duration-200">
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
