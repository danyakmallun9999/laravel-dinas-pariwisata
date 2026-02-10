<x-public-layout :hideFooter="true">
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('tickets.my') }}" class="hover:text-primary transition-colors">Tiket Saya</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $order->status == 'pending' ? 'Detail Pesanan' : 'Konfirmasi' }}</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 text-center">
                @if($order->status == 'pending')
                    <!-- Pending: Clock Icon -->
                    <div class="w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-clock text-yellow-500 dark:text-yellow-400 text-4xl"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-3">Menunggu Pembayaran</h1>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">Selesaikan pembayaran untuk mengaktifkan tiket Anda</p>
                @else
                    <!-- Paid: Animated Success Icon -->
                    <div class="success-checkmark mx-auto mb-6">
                        <div class="check-icon">
                            <span class="icon-line line-tip"></span>
                            <span class="icon-line line-long"></span>
                            <div class="icon-circle"></div>
                            <div class="icon-fix"></div>
                        </div>
                    </div>
                    
                    <style>
                        .success-checkmark {
                            width: 80px;
                            height: 80px;
                        }
                        .check-icon {
                            width: 80px;
                            height: 80px;
                            position: relative;
                            border-radius: 50%;
                            box-sizing: content-box;
                            border: 4px solid #22c55e;
                        }
                        .check-icon::before {
                            top: 3px;
                            left: -2px;
                            width: 30px;
                            transform-origin: 100% 50%;
                            border-radius: 100px 0 0 100px;
                        }
                        .check-icon::after {
                            top: 0;
                            left: 30px;
                            width: 60px;
                            transform-origin: 0 50%;
                            border-radius: 0 100px 100px 0;
                            animation: rotate-circle 4.25s ease-in;
                        }
                        .check-icon::before, .check-icon::after {
                            content: '';
                            height: 100px;
                            position: absolute;
                            background: #fff;
                            transform: rotate(-45deg);
                        }
                        .dark .check-icon::before, .dark .check-icon::after {
                            background: #1e293b;
                        }
                        .icon-line {
                            height: 5px;
                            background-color: #22c55e;
                            display: block;
                            border-radius: 2px;
                            position: absolute;
                            z-index: 10;
                        }
                        .icon-line.line-tip {
                            top: 46px;
                            left: 14px;
                            width: 25px;
                            transform: rotate(45deg);
                            animation: icon-line-tip 0.75s;
                        }
                        .icon-line.line-long {
                            top: 38px;
                            right: 8px;
                            width: 47px;
                            transform: rotate(-45deg);
                            animation: icon-line-long 0.75s;
                        }
                        .icon-circle {
                            top: -4px;
                            left: -4px;
                            z-index: 10;
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            position: absolute;
                            box-sizing: content-box;
                            border: 4px solid rgba(34, 197, 94, 0.5);
                            animation: icon-circle-pulse 1s ease-out;
                        }
                        .icon-fix {
                            top: 8px;
                            width: 5px;
                            left: 26px;
                            z-index: 1;
                            height: 85px;
                            position: absolute;
                            transform: rotate(-45deg);
                            background-color: #fff;
                        }
                        .dark .icon-fix {
                            background-color: #1e293b;
                        }
                        @keyframes rotate-circle {
                            0% { transform: rotate(-45deg); }
                            5% { transform: rotate(-45deg); }
                            12% { transform: rotate(-405deg); }
                            100% { transform: rotate(-405deg); }
                        }
                        @keyframes icon-line-tip {
                            0% { width: 0; left: 1px; top: 19px; }
                            54% { width: 0; left: 1px; top: 19px; }
                            70% { width: 50px; left: -8px; top: 37px; }
                            84% { width: 17px; left: 21px; top: 48px; }
                            100% { width: 25px; left: 14px; top: 46px; }
                        }
                        @keyframes icon-line-long {
                            0% { width: 0; right: 46px; top: 54px; }
                            65% { width: 0; right: 46px; top: 54px; }
                            84% { width: 55px; right: 0px; top: 35px; }
                            100% { width: 47px; right: 8px; top: 38px; }
                        }
                        @keyframes icon-circle-pulse {
                            0% { transform: scale(0.8); opacity: 0; }
                            50% { transform: scale(1.2); opacity: 0.5; }
                            100% { transform: scale(1); opacity: 1; }
                        }
                    </style>

                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-3">Pesanan Berhasil!</h1>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">Terima kasih telah memesan tiket wisata</p>
                @endif

                <!-- Order Info -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-6 text-left">
                    <!-- Status Badge at Top -->
                    <div class="text-center mb-4 pb-4 border-b border-slate-200 dark:border-slate-600">
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-bold rounded-xl
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
                    
                    <!-- Order Number -->
                    <div class="space-y-3 mb-4 pb-4 border-b border-slate-200 dark:border-slate-600">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">No. Pesanan</p>
                            <p class="font-mono text-sm text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-600/50 px-3 py-2 rounded-lg">{{ $order->order_number }}</p>
                        </div>
                    </div>
                    
                    <!-- Ticket Info Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-4 pb-4 border-b border-slate-200 dark:border-slate-600">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Tiket</p>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $order->ticket->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Destinasi</p>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $order->ticket->place->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Tanggal Kunjungan</p>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $order->visit_date->translatedFormat('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Jumlah</p>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $order->quantity }} tiket</p>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Pembayaran</span>
                        <span class="text-xl font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-6 text-left">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-user text-primary"></i> Informasi Pemesan
                    </h3>
                    <div class="space-y-2.5">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-600/50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-user text-xs text-slate-400"></i>
                            </span>
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $order->customer_name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-600/50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-envelope text-xs text-slate-400"></i>
                            </span>
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $order->customer_email }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-600/50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-phone text-xs text-slate-400"></i>
                            </span>
                            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $order->customer_phone }}</span>
                        </div>
                    </div>
                </div>

                @if($order->status !== 'pending')
                    <!-- QR Code Section (only for paid orders) -->
                    <div class="bg-gradient-to-br from-primary/5 to-indigo-500/5 border border-primary/10 rounded-2xl p-6 mb-6">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-qrcode text-primary"></i> QR Code Tiket
                        </h3>
                        <div id="qrcode" class="flex justify-center mb-3"></div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Tunjukkan QR code ini saat berkunjung</p>
                        <div class="mt-4">
                            <button onclick="downloadQR()" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all">
                                <i class="fa-solid fa-download"></i> Download QR
                            </button>
                        </div>
                    </div>

                    <!-- Next Steps Info (only for paid orders) -->
                    <div class="bg-primary/5 border border-primary/10 rounded-2xl p-5 mb-8 text-left">
                        <div class="flex items-start">
                            <i class="fa-solid fa-info-circle text-primary mt-0.5 mr-3"></i>
                            <div class="text-sm text-slate-700 dark:text-slate-300">
                                <p class="font-bold mb-2 text-slate-900 dark:text-white">Langkah Selanjutnya</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Konfirmasi pesanan dikirim ke <strong>{{ $order->customer_email }}</strong></li>
                                    <li>Simpan QR code atau download tiket sebagai bukti</li>
                                    <li>Tunjukkan tiket saat berkunjung ke {{ $order->ticket->place->name }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Payment Instructions (only for pending orders) -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-5 mb-8 text-left">
                        <div class="flex items-start">
                            <i class="fa-solid fa-wallet text-yellow-600 mt-0.5 mr-3"></i>
                            <div class="text-sm text-slate-700 dark:text-slate-300">
                                <p class="font-bold mb-2 text-slate-900 dark:text-white">Instruksi Pembayaran</p>
                                <p class="mb-2">Metode: <strong>{{ ucfirst($order->payment_method) }}</strong></p>
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
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-3">
                    @if($order->status == 'pending')
                        <a href="{{ route('tickets.payment', $order->order_number) }}" 
                           class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                        </a>
                        <a href="{{ route('tickets.my') }}" 
                           class="w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Tiket Saya
                        </a>
                    @else
                        <button onclick="downloadTicketImage()" 
                           class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2"
                           id="downloadBtn">
                            <i class="fa-solid fa-download"></i> Download Tiket
                        </button>
                        <a href="{{ route('tickets.my') }}" 
                           class="w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Tiket Saya
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Ticket Card for PNG Generation (off-screen) -->
    <div style="position: fixed; left: -9999px; top: 0; z-index: -1;">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
        <div id="ticket-card" style="width: 400px; font-family: 'Inter', sans-serif; background: #fff; border: 1px solid #e7e5e4; overflow: hidden;">
            <!-- Header -->
            <div style="background: #1c1917; color: #fff; padding: 32px; text-align: center; position: relative; overflow: hidden;">
                <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.3em; color: #a8a29e; margin: 0 0 8px 0;">@lang('tickets.department')</p>
                <h1 style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 28px; margin: 0; color: #fafaf9; letter-spacing: 0.05em;">@lang('tickets.header')</h1>
                <div style="width: 64px; height: 1px; background: #44403c; margin: 16px auto 0;"></div>
            </div>

            <!-- Ticket Body -->
            <div style="padding: 32px; position: relative;">
                <!-- Tear-off connectors -->
                <div style="position: absolute; top: -16px; left: 0; width: 16px; height: 32px; background: #fff; border-radius: 0 9999px 9999px 0;"></div>
                <div style="position: absolute; top: -16px; right: 0; width: 16px; height: 32px; background: #fff; border-radius: 9999px 0 0 9999px;"></div>

                <!-- Destination -->
                <div style="text-align: center; margin-bottom: 32px;">
                    <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; color: #78716c; margin: 0 0 4px 0;">@lang('tickets.destination')</p>
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: bold; color: #1c1917; margin: 0;">{{ $order->ticket->place->name }}</h2>
                </div>

                <!-- QR Code -->
                <div style="display: flex; justify-content: center; margin-bottom: 32px;">
                    <div style="padding: 16px; border: 1px solid #e7e5e4; background: #fafaf9;">
                        <div id="ticket-qrcode"></div>
                    </div>
                </div>
                <div style="text-align: center; margin-bottom: 32px;">
                    <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.15em; color: #a8a29e; margin: 0 0 4px 0;">@lang('tickets.order_code')</p>
                    <p style="font-family: monospace; font-size: 18px; font-weight: bold; color: #44403c; letter-spacing: 0.1em; margin: 0 0 8px 0;">{{ $order->order_number }}</p>
                    <span style="display: inline-block; padding: 4px 16px; border: 1px solid #e7e5e4; border-radius: 9999px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.1em; color: #78716c;">{{ $order->status_label }}</span>
                </div>

                <!-- Details Grid -->
                <div style="border-top: 1px solid #f5f5f4; padding-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px 16px;">
                    <div>
                        <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #a8a29e; margin: 0 0 4px 0;">@lang('tickets.visit_date')</p>
                        <p style="font-family: 'Playfair Display', serif; font-size: 18px; color: #292524; margin: 0;">{{ $order->visit_date->translatedFormat('d M Y') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #a8a29e; margin: 0 0 4px 0;">@lang('tickets.visitors')</p>
                        <p style="font-family: 'Playfair Display', serif; font-size: 18px; color: #292524; margin: 0;">{{ $order->quantity }} @lang('tickets.people')</p>
                    </div>
                    <div style="grid-column: span 2;">
                        <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #a8a29e; margin: 0 0 4px 0;">@lang('tickets.ticket_type')</p>
                        <p style="font-family: 'Playfair Display', serif; font-size: 18px; color: #292524; margin: 0; display: inline;">{{ $order->ticket->name }}</p>
                        <span style="font-size: 12px; color: #78716c; font-style: italic; margin-left: 8px;">({{ ucfirst($order->ticket->type) }})</span>
                    </div>
                </div>

                <!-- Total -->
                <div style="margin-top: 32px; padding-top: 24px; border-top: 2px dashed #d6d3d1; display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #a8a29e; margin: 0 0 4px 0;">@lang('tickets.customer')</p>
                        <p style="font-size: 14px; font-weight: 600; color: #44403c; margin: 0;">{{ $order->customer_name }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: #a8a29e; margin: 0 0 4px 0;">@lang('tickets.total_payment')</p>
                        <p style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: bold; color: #1c1917; margin: 0;">RP {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div style="background: #fafaf9; padding: 16px; text-align: center; border-top: 1px solid #e7e5e4;">
                <p style="font-size: 10px; color: #a8a29e; font-style: italic; font-family: 'Playfair Display', serif; margin: 0;">@lang('tickets.footer_thanks')</p>
            </div>
        </div>
    </div>

<!-- QRCode.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // QR code for the visible confirmation page
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $order->order_number }}",
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // QR code for the hidden ticket card (for PNG download)
    new QRCode(document.getElementById("ticket-qrcode"), {
        text: "{{ $order->order_number }}",
        width: 120,
        height: 120,
        colorDark : "#292524",
        colorLight : "#f5f5f4",
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

function downloadTicketImage() {
    const btn = document.getElementById('downloadBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...';
    btn.disabled = true;

    const ticketCard = document.getElementById('ticket-card');
    
    html2canvas(ticketCard, {
        scale: 3,
        useCORS: true,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'E-Tiket-{{ $order->order_number }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();

        btn.innerHTML = originalText;
        btn.disabled = false;
    }).catch(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Gagal generate tiket. Silakan coba lagi.');
    });
}
</script>
</x-public-layout>

