<x-public-layout :hideFooter="true">
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <a href="{{ route('tickets.my') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.MyTickets') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Tickets.Breadcrumb.Payment') }}</span>
            </nav>

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-credit-card text-primary text-2xl"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">Pilih Metode Pembayaran</h1>
                    <p class="text-slate-500 dark:text-slate-400">Pilih metode pembayaran yang paling nyaman untuk Anda</p>
                </div>

                <!-- Order Summary -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-8">
                    <div class="flex justify-between items-center pb-4 border-b border-slate-200 dark:border-slate-600">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-0.5">Pesanan</p>
                            <p class="font-bold text-slate-900 dark:text-white font-mono text-sm">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-0.5">Total</p>
                            <p class="text-lg font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="pt-3 flex items-center gap-3">
                        <i class="fa-solid fa-ticket text-primary"></i>
                        <div>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $order->ticket->name }} — {{ $order->ticket->place->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $order->quantity }}x tiket · {{ $order->visit_date->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection Form -->
                <form action="{{ route('tickets.process-payment', $order->order_number) }}" method="POST" id="payment-form" x-data="paymentSelector()" @submit="isSubmitting = true">
                    @csrf
                    <input type="hidden" name="payment_type" x-model="paymentType">
                    <input type="hidden" name="bank" x-model="bank">

                    <!-- E-Wallet & QRIS -->
                    <div class="mb-8">
                        <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-primary"></i>
                            E-Wallet & QRIS
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- QRIS -->
                            <button type="button" @click="selectMethod('qris')"
                                :class="paymentType === 'qris' 
                                    ? 'border-gray-800 bg-gray-50 dark:bg-gray-800/50 ring-0' 
                                    : 'border-slate-200 dark:border-slate-700 hover:border-gray-400 bg-white dark:bg-slate-800'"
                                class="relative flex flex-col items-center justify-between gap-4 p-6 rounded-2xl border transition-all duration-200 cursor-pointer group h-full overflow-hidden">
                                
                                <div class="flex-1 flex items-center justify-center w-full py-2 z-10">
                                    <div class="w-20 h-20 relative transition-opacity duration-200 group-hover:opacity-80">
                                        <img src="{{ asset('images/payment/qris.png') }}" alt="QRIS" class="w-full h-full object-contain transition-all duration-300">
                                    </div>
                                </div>
                                
                                <div class="text-center w-full z-10">
                                    <p class="font-medium text-sm text-slate-900 dark:text-white mb-1 transition-colors group-hover:text-gray-800 dark:group-hover:text-gray-200">QRIS</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wide">Scan e-wallet</p>
                                </div>

                                <!-- Minimalist Selection Indicator -->
                                <div class="absolute top-4 right-4 transition-all duration-200" 
                                     :class="paymentType === 'qris' ? 'opacity-100' : 'opacity-0'">
                                     <div class="w-5 h-5 bg-gray-900 text-white rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                     </div>
                                </div>
                            </button>

                            <!-- GoPay -->
                            <button type="button" @click="selectMethod('gopay')"
                                :class="paymentType === 'gopay' 
                                    ? 'border-[#00AED6] bg-[#00AED6]/5 ring-0' 
                                    : 'border-slate-200 dark:border-slate-700 hover:border-[#00AED6]/50 bg-white dark:bg-slate-800'"
                                class="relative flex flex-col items-center justify-between gap-4 p-6 rounded-2xl border transition-all duration-200 cursor-pointer group h-full overflow-hidden">
                                
                                <div class="flex-1 flex items-center justify-center w-full py-2 z-10">
                                    <div class="w-20 h-20 relative transition-opacity duration-200 group-hover:opacity-80">
                                        <img src="{{ asset('images/payment/gopay.png') }}" alt="GoPay" class="w-full h-full object-contain transition-all duration-300">
                                    </div>
                                </div>
                                
                                <div class="text-center w-full z-10">
                                    <p class="font-medium text-sm text-slate-900 dark:text-white mb-1 transition-colors group-hover:text-[#00AED6]" :class="paymentType === 'gopay' ? 'text-[#00AED6]' : ''">GoPay</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wide">Instant Checkout</p>
                                </div>

                                <div class="absolute top-4 right-4 transition-all duration-200" 
                                     :class="paymentType === 'gopay' ? 'opacity-100' : 'opacity-0'">
                                     <div class="w-5 h-5 bg-[#00AED6] text-white rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                     </div>
                                </div>
                            </button>

                            <!-- ShopeePay -->
                            <button type="button" @click="selectMethod('shopeepay')"
                                :class="paymentType === 'shopeepay' 
                                    ? 'border-[#EE4D2D] bg-[#EE4D2D]/5 ring-0' 
                                    : 'border-slate-200 dark:border-slate-700 hover:border-[#EE4D2D]/50 bg-white dark:bg-slate-800'"
                                class="relative flex flex-col items-center justify-between gap-4 p-6 rounded-2xl border transition-all duration-200 cursor-pointer group h-full overflow-hidden">
                                
                                <div class="flex-1 flex items-center justify-center w-full py-2 z-10">
                                    <div class="w-20 h-20 relative transition-opacity duration-200 group-hover:opacity-80">
                                        <img src="{{ asset('images/payment/shopeepay.png') }}" alt="ShopeePay" class="w-full h-full object-contain transition-all duration-300">
                                    </div>
                                </div>
                                
                                <div class="text-center w-full z-10">
                                    <p class="font-medium text-sm text-slate-900 dark:text-white mb-1 transition-colors group-hover:text-[#EE4D2D]" :class="paymentType === 'shopeepay' ? 'text-[#EE4D2D]' : ''">ShopeePay</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wide">Instant Checkout</p>
                                </div>

                                <div class="absolute top-4 right-4 transition-all duration-200" 
                                     :class="paymentType === 'shopeepay' ? 'opacity-100' : 'opacity-0'">
                                     <div class="w-5 h-5 bg-[#EE4D2D] text-white rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                     </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Virtual Account -->
                    <div class="mb-8 relative">
                        <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-building-columns text-primary"></i>
                            Virtual Account (Transfer Bank)
                            <span class="bg-yellow-100 text-yellow-700 text-[10px] py-0.5 px-2 rounded-full font-bold border border-yellow-200">
                                <i class="fa-solid fa-clock mr-1"></i> Segera Hadir
                            </span>
                        </h3>
                        
                        <!-- Overlay for disabled state -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 opacity-60 grayscale select-none pointer-events-none filter">
                            <!-- BCA -->
                            <button type="button" disabled
                                class="relative flex flex-col items-center justify-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed group h-24">
                                <img src="{{ asset('images/payment/bca.png') }}" alt="BCA" class="h-8 w-auto object-contain">
                            </button>

                            <!-- BNI -->
                            <button type="button" disabled
                                class="relative flex flex-col items-center justify-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed group h-24">
                                <img src="{{ asset('images/payment/bni.png') }}" alt="BNI" class="h-8 w-auto object-contain">
                            </button>

                            <!-- BRI -->
                            <button type="button" disabled
                                class="relative flex flex-col items-center justify-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed group h-24">
                                <img src="{{ asset('images/payment/bri.png') }}" alt="BRI" class="h-8 w-auto object-contain">
                            </button>

                            <!-- Mandiri -->
                            <button type="button" disabled
                                class="relative flex flex-col items-center justify-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed group h-24">
                                <img src="{{ asset('images/payment/mandiri.png') }}" alt="Mandiri" class="h-8 w-auto object-contain">
                            </button>
                        </div>
                        
                        <p class="text-xs text-slate-400 mt-2 italic text-center sm:text-left">
                            * Metode pembayaran ini sedang dalam pengembangan dan akan tersedia segera.
                        </p>
                    </div>

                    <!-- Secure Payment Badge -->
                    <div class="flex items-center justify-center gap-2 mb-6 text-sm text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-shield-halved text-green-500"></i>
                        <span>Pembayaran aman diproses oleh Midtrans</span>
                    </div>

                    <!-- Pay Button -->
                    <button type="submit" id="pay-button"
                        :disabled="!paymentType || isSubmitting"
                        :class="paymentType && !isSubmitting ? 'bg-primary hover:bg-primary/90 shadow-lg shadow-primary/25 hover:shadow-xl' : 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed'"
                        class="w-full text-white font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                        <template x-if="isSubmitting">
                            <span><i class="fa-solid fa-spinner fa-spin mr-2"></i>Memproses pembayaran...</span>
                        </template>
                        <template x-if="!isSubmitting && paymentType">
                            <span>
                                Bayar Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                <i class="fa-solid fa-arrow-right ml-1"></i>
                            </span>
                        </template>
                        <template x-if="!isSubmitting && !paymentType">
                            <span>Pilih metode pembayaran</span>
                        </template>
                    </button>

                    <div class="mt-4 text-center">
                        <a href="{{ route('tickets.my') }}" class="text-slate-500 hover:text-primary text-sm font-medium transition-colors">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>

            <!-- QRIS Info -->
            <div class="mt-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-4 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                <p class="text-xs text-blue-700 dark:text-blue-400">
                    <strong>Tip:</strong> Pilih <strong>QRIS</strong> untuk membayar dengan semua e-wallet (GoPay, OVO, DANA, ShopeePay, LinkAja, dll). Cukup scan QR code dengan aplikasi favorit Anda.
                </p>
            </div>
        </div>
    </div>

<script>
function paymentSelector() {
    return {
        paymentType: '',
        bank: '',
        isSubmitting: false,
        selectMethod(type, bankName = '') {
            this.paymentType = type;
            this.bank = bankName;
        }
    }
}
</script>
</x-public-layout>
