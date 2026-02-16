<x-public-layout :hideFooter="true">
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">E-Tickets</a>
                <span>/</span>
                <a href="{{ route('tickets.my') }}" class="hover:text-primary transition-colors">Tiket Saya</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">Menunggu Pembayaran</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8" x-data="paymentStatusChecker()">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-clock text-yellow-500 dark:text-yellow-400 text-2xl animate-pulse"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">Menunggu Pembayaran</h1>
                    <p class="text-slate-500 dark:text-slate-400" x-text="statusMessage">Selesaikan pembayaran Anda sebelum waktu habis</p>
                </div>

                <!-- Countdown Timer -->
                @if(!empty($paymentData['expiry_time']))
                <div class="text-center mb-6">
                    <div class="inline-flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-5 py-2.5 rounded-2xl">
                        <i class="fa-solid fa-hourglass-half text-red-500 text-sm"></i>
                        <span class="text-sm font-semibold text-red-700 dark:text-red-400" x-text="countdownText">Menghitung...</span>
                    </div>
                </div>
                @endif

                <!-- Order Summary Compact -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-4 mb-6 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pesanan</p>
                        <p class="font-mono text-sm font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total</p>
                        <p class="text-lg font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- ================================ --}}
                {{-- QRIS PAYMENT --}}
                {{-- ================================ --}}
                @if($paymentData['payment_type'] === 'qris' && !empty($paymentData['qr_url']))
                <div class="text-center mb-6">
                    <div class="bg-white dark:bg-slate-700/50 border-2 border-dashed border-slate-300 dark:border-slate-500 rounded-2xl p-6 inline-block">
                        <img src="{{ $paymentData['qr_url'] }}" alt="QR Code" class="w-56 h-56 mx-auto" id="qris-img">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Scan menggunakan e-wallet (GoPay, OVO, DANA, dll)</p>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-4 mb-6">
                    <h4 class="font-bold text-sm text-blue-900 dark:text-blue-300 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i> Cara Pembayaran QRIS
                    </h4>
                    <ol class="text-xs text-blue-700 dark:text-blue-400 space-y-1.5 list-decimal list-inside">
                        <li>Buka aplikasi e-wallet Anda (GoPay, OVO, DANA, ShopeePay, LinkAja, dll)</li>
                        <li>Pilih menu <strong>Scan QR</strong> atau <strong>Bayar</strong></li>
                        <li>Arahkan kamera ke QR code di atas</li>
                        <li>Konfirmasi pembayaran di aplikasi Anda</li>
                    </ol>
                </div>

                {{-- ================================ --}}
                {{-- GOPAY PAYMENT --}}
                {{-- ================================ --}}
                @elseif($paymentData['payment_type'] === 'gopay')
                <div class="text-center mb-6">
                    @if(!empty($paymentData['qr_url']))
                    <div class="bg-white dark:bg-slate-700/50 border-2 border-dashed border-slate-300 dark:border-slate-500 rounded-2xl p-6 inline-block mb-4">
                        <img src="{{ $paymentData['qr_url'] }}" alt="GoPay QR" class="w-48 h-48 mx-auto">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Scan dengan GoPay atau e-wallet lainnya</p>
                    </div>
                    @endif

                    @if(!empty($paymentData['deeplink']))
                    <div class="mt-4">
                        <a href="{{ $paymentData['deeplink'] }}"
                           class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-3.5 rounded-2xl transition-all shadow-lg shadow-emerald-500/25">
                            <i class="fa-solid fa-external-link-alt"></i>
                            Buka Aplikasi GoPay
                        </a>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Atau scan QR code di atas</p>
                    </div>
                    @endif
                </div>

                {{-- ================================ --}}
                {{-- SHOPEEPAY PAYMENT --}}
                {{-- ================================ --}}
                @elseif($paymentData['payment_type'] === 'shopeepay')
                <div class="text-center mb-6">
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/10 dark:to-red-900/10 border border-orange-200 dark:border-orange-800/30 rounded-2xl p-8">
                        <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-orange-500/25">
                            <i class="fa-solid fa-shop text-white text-3xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Bayar dengan ShopeePay</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Klik tombol di bawah untuk membuka aplikasi Shopee</p>

                        @if(!empty($paymentData['deeplink']))
                        <a href="{{ $paymentData['deeplink'] }}"
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold px-8 py-3.5 rounded-2xl transition-all shadow-lg shadow-orange-500/25">
                            <i class="fa-solid fa-external-link-alt"></i>
                            Buka Aplikasi Shopee
                        </a>
                        @endif
                    </div>
                </div>

                {{-- ================================ --}}
                {{-- BANK TRANSFER VA --}}
                {{-- ================================ --}}
                @elseif($paymentData['payment_type'] === 'bank_transfer' && !empty($paymentData['va_number']))
                @php
                    $bankNames = ['bca' => 'BCA', 'bni' => 'BNI', 'bri' => 'BRI', 'permata' => 'Permata'];
                    $bankColors = ['bca' => 'from-blue-700 to-blue-900', 'bni' => 'from-orange-500 to-orange-700', 'bri' => 'from-blue-500 to-indigo-700', 'permata' => 'from-green-600 to-green-800'];
                    $bankName = $bankNames[$paymentData['bank']] ?? strtoupper($paymentData['bank']);
                    $bankColor = $bankColors[$paymentData['bank']] ?? 'from-slate-600 to-slate-800';
                @endphp
                <div class="mb-6">
                    <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-6 text-center">
                        <!-- Bank Badge -->
                        <div class="w-16 h-16 bg-gradient-to-br {{ $bankColor }} rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <span class="text-white font-bold text-sm">{{ $bankName }}</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Nomor Virtual Account {{ $bankName }}</p>

                        <!-- VA Number -->
                        <div class="bg-white dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-500 rounded-xl px-3 md:px-6 py-4 inline-flex items-center gap-2 md:gap-3 mb-3 max-w-full overflow-hidden">
                            <span class="font-mono text-lg md:text-2xl font-bold text-slate-900 dark:text-white tracking-wide md:tracking-widest truncate" id="va-number">{{ $paymentData['va_number'] }}</span>
                            <button onclick="copyVA()" class="w-8 h-8 md:w-10 md:h-10 bg-primary/10 hover:bg-primary/20 rounded-xl flex items-center justify-center transition-colors flex-shrink-0" title="Salin">
                                <i class="fa-solid fa-copy text-primary text-xs md:text-base" id="copy-icon"></i>
                            </button>
                        </div>
                        <p class="text-xs text-green-600 dark:text-green-400 hidden" id="copy-success">
                            <i class="fa-solid fa-check"></i> Nomor VA disalin!
                        </p>
                    </div>
                </div>

                <!-- Transfer Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-4 mb-6">
                    <h4 class="font-bold text-sm text-blue-900 dark:text-blue-300 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i> Cara Transfer via {{ $bankName }}
                    </h4>
                    <ol class="text-xs text-blue-700 dark:text-blue-400 space-y-1.5 list-decimal list-inside">
                        <li>Buka aplikasi m-Banking atau ATM <strong>{{ $bankName }}</strong></li>
                        <li>Pilih menu <strong>Transfer</strong> → <strong>Virtual Account</strong></li>
                        <li>Masukkan nomor VA: <strong class="font-mono">{{ $paymentData['va_number'] }}</strong></li>
                        <li>Pastikan nominal sesuai: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></li>
                        <li>Konfirmasi dan selesaikan pembayaran</li>
                    </ol>
                </div>

                {{-- ================================ --}}
                {{-- MANDIRI BILL (ECHANNEL) --}}
                {{-- ================================ --}}
                @elseif($paymentData['payment_type'] === 'echannel')
                <div class="mb-6">
                    <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-6 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <span class="text-white font-bold text-[10px] leading-tight text-center">Man<br>diri</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-4">Mandiri Bill Payment</p>

                        <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl p-4">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Biller Code</p>
                                <p class="font-mono text-lg font-bold text-slate-900 dark:text-white">{{ $paymentData['biller_code'] ?? '-' }}</p>
                            </div>
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl p-4">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Bill Key</p>
                                <p class="font-mono text-lg font-bold text-slate-900 dark:text-white">{{ $paymentData['bill_key'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-4 mb-6">
                    <h4 class="font-bold text-sm text-blue-900 dark:text-blue-300 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i> Cara Bayar via Mandiri
                    </h4>
                    <ol class="text-xs text-blue-700 dark:text-blue-400 space-y-1.5 list-decimal list-inside">
                        <li>Buka Mandiri Online atau ATM Mandiri</li>
                        <li>Pilih menu <strong>Bayar</strong> → <strong>Multipayment</strong></li>
                        <li>Masukkan Biller Code: <strong class="font-mono">{{ $paymentData['biller_code'] ?? '-' }}</strong></li>
                        <li>Masukkan Bill Key: <strong class="font-mono">{{ $paymentData['bill_key'] ?? '-' }}</strong></li>
                        <li>Konfirmasi dan selesaikan pembayaran</li>
                    </ol>
                </div>
                @endif

                <!-- Status Check Indicator -->
                <div class="flex items-center justify-center gap-2 mb-6 text-sm" :class="isPaid ? 'text-green-600' : 'text-slate-500 dark:text-slate-400'">
                    <template x-if="isChecking && !isPaid">
                        <span><i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengecek status pembayaran...</span>
                    </template>
                    <template x-if="!isChecking && !isPaid">
                        <span><i class="fa-solid fa-rotate mr-1"></i> Auto-check setiap 5 detik</span>
                    </template>
                    <template x-if="isPaid">
                        <span><i class="fa-solid fa-circle-check mr-1"></i> Pembayaran dikonfirmasi! Mengalihkan...</span>
                    </template>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button @click="manualCheck()" :disabled="isChecking"
                        class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrows-rotate" :class="isChecking ? 'fa-spin' : ''"></i>
                        <span x-text="isChecking ? 'Mengecek...' : 'Cek Status Pembayaran'"></span>
                    </button>

                    <a href="{{ route('payment.show', $order->order_number) }}"
                       class="w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Ganti Metode Pembayaran
                    </a>
                </div>
            </div>
        </div>
    </div>

<script>
function copyVA() {
    const vaNumber = document.getElementById('va-number')?.textContent;
    if (vaNumber) {
        navigator.clipboard.writeText(vaNumber.trim()).then(() => {
            const icon = document.getElementById('copy-icon');
            const success = document.getElementById('copy-success');
            icon.className = 'fa-solid fa-check text-green-500';
            success.classList.remove('hidden');
            setTimeout(() => {
                icon.className = 'fa-solid fa-copy text-primary';
                success.classList.add('hidden');
            }, 2000);
        });
    }
}

function paymentStatusChecker() {
    return {
        isChecking: false,
        isPaid: false,
        statusMessage: 'Selesaikan pembayaran Anda sebelum waktu habis',
        countdownText: 'Menghitung...',
        interval: null,
        countdownInterval: null,

        init() {
            // Start auto-checking every 5 seconds
            this.startAutoCheck();

            // Start countdown timer
            @if(!empty($paymentData['expiry_time']))
            this.startCountdown('{{ $paymentData["expiry_time"] }}');
            @endif
        },

        startAutoCheck() {
            this.interval = setInterval(() => {
                if (!this.isPaid) {
                    this.checkStatus();
                }
            }, 5000);
        },

        startCountdown(expiryTime) {
            const expiry = new Date(expiryTime).getTime();

            const update = () => {
                const now = new Date().getTime();
                const diff = expiry - now;

                if (diff <= 0) {
                    this.countdownText = 'Waktu habis!';
                    this.statusMessage = 'Waktu pembayaran telah habis. Mengalihkan...';
                    clearInterval(this.countdownInterval);
                    clearInterval(this.interval);

                    // Force redirect to failed page
                    setTimeout(() => {
                        window.location.href = '{{ route("payment.failed", $order->order_number) }}';
                    }, 1500);
                    return;
                }

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (hours > 0) {
                    this.countdownText = `Sisa waktu: ${hours}j ${minutes}m ${seconds}d`;
                } else {
                    this.countdownText = `Sisa waktu: ${minutes}m ${seconds}d`;
                }
            };

            update();
            this.countdownInterval = setInterval(update, 1000);
        },

        async checkStatus() {
            if (this.isChecking || this.isPaid) return;
            this.isChecking = true;

            try {
                const response = await fetch('{{ route("payment.check", $order->order_number) }}');
                const data = await response.json();

                if (data.status === 'paid') {
                    this.isPaid = true;
                    this.statusMessage = 'Pembayaran berhasil! Mengalihkan...';
                    clearInterval(this.interval);
                    clearInterval(this.countdownInterval);

                    setTimeout(() => {
                        window.location.href = '{{ route("payment.success", $order->order_number) }}';
                    }, 1500);
                } else if (data.status === 'cancelled' || data.status === 'expire' || data.status === 'deny') {
                    clearInterval(this.interval);
                    clearInterval(this.countdownInterval);
                    this.statusMessage = data.message || 'Pembayaran dibatalkan';
                    setTimeout(() => {
                        window.location.href = '{{ route("payment.failed", $order->order_number) }}';
                    }, 1500);
                }
            } catch (e) {
                console.error('Status check failed', e);
            } finally {
                this.isChecking = false;
            }
        },

        manualCheck() {
            this.checkStatus();
        },

        destroy() {
            if (this.interval) clearInterval(this.interval);
            if (this.countdownInterval) clearInterval(this.countdownInterval);
        }
    }
}
</script>
</x-public-layout>
