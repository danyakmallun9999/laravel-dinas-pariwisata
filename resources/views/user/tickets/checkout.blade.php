<x-public-layout :hideFooter="true">
    {{-- Scrollbar-hide utility --}}
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-24 sm:pt-32 pb-32 sm:pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-[11px] sm:text-xs md:text-sm text-gray-400/80 sm:text-gray-400 mb-3 sm:mb-6 space-x-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">Checkout</span>
            </nav>

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 sm:px-6 py-3 sm:py-4 rounded-2xl mb-4 sm:mb-6 flex items-center gap-3 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-4 sm:p-6 md:p-8">
                <div class="text-center mb-5 sm:mb-8">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-primary/10 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <i class="fa-solid fa-cart-shopping text-primary text-xl sm:text-2xl"></i>
                    </div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-1 sm:mb-2">Ringkasan Pesanan</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm hidden sm:block">Periksa kembali pesanan Anda sebelum melakukan pembayaran</p>
                </div>

                {{-- Order Summary --}}
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-xl sm:rounded-2xl p-4 sm:p-5 mb-5 sm:mb-8">
                    <div class="flex justify-between items-center sm:pb-4 sm:border-b sm:border-slate-200 sm:dark:border-slate-600">
                        <div>
                            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-0.5">Item</p>
                            <p class="font-bold text-slate-900 dark:text-white font-mono text-xs sm:text-sm">{{ $ticket->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-0.5">Total</p>
                            <p class="text-xl sm:text-lg font-bold text-primary">Rp {{ number_format($booking['total_price'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="hidden sm:flex pt-3 items-center gap-3">
                        <i class="fa-solid fa-location-dot text-primary"></i>
                        <div>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $ticket->place->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $booking['quantity'] }}x tiket · {{ \Carbon\Carbon::parse($booking['visit_date'])->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <div class="flex sm:hidden mt-3 pt-3 border-t border-slate-200 dark:border-slate-600 text-xs text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-ticket text-primary mr-2 mt-0.5"></i>
                        <span>{{ $booking['quantity'] }}x {{ $ticket->name }} · {{ \Carbon\Carbon::parse($booking['visit_date'])->translatedFormat('d M Y') }}</span>
                    </div>
                </div>

                {{-- Payment Method Selection Form --}}
                <form action="{{ route('tickets.process-checkout') }}" method="POST" id="payment-form" x-data="paymentSelector()" @submit="isSubmitting = true">
                    @csrf
                    <input type="hidden" name="payment_type" x-model="paymentType">
                    <input type="hidden" name="bank" x-model="bank">

                    <div class="mb-5 sm:mb-8">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 sm:mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-primary"></i>
                            E-Wallet & QRIS
                        </h3>
                        <div class="flex sm:grid sm:grid-cols-3 gap-3 sm:gap-4 overflow-x-auto sm:overflow-visible snap-x snap-mandatory pb-2 sm:pb-0 -mx-1 px-1 scrollbar-hide">
                            {{-- QRIS --}}
                            <button type="button" @click="selectMethod('qris')"
                                :class="paymentType === 'qris' 
                                    ? 'border-gray-800 bg-gray-50 dark:bg-gray-800/50 ring-0 shadow-md sm:shadow-none' 
                                    : 'border-slate-200 dark:border-slate-700 hover:border-gray-400 bg-white dark:bg-slate-800'"
                                class="relative flex flex-col items-center justify-between gap-2 sm:gap-4 p-4 sm:p-6 rounded-2xl border transition-all duration-200 cursor-pointer group h-full overflow-hidden min-w-[130px] sm:min-w-0 snap-start flex-shrink-0 sm:flex-shrink">
                                <div class="flex-1 flex items-center justify-center w-full py-1 sm:py-2 z-10">
                                    <div class="w-14 h-14 sm:w-20 sm:h-20 relative transition-opacity duration-200 group-hover:opacity-80">
                                        <img src="{{ asset('images/payment/qris.png') }}" alt="QRIS" class="w-full h-full object-contain transition-all duration-300">
                                    </div>
                                </div>
                                <div class="text-center w-full z-10">
                                    <p class="font-medium text-sm text-slate-900 dark:text-white mb-0.5 sm:mb-1">QRIS</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wide">Scan e-wallet</p>
                                </div>
                                <div class="absolute top-3 right-3 sm:top-4 sm:right-4 transition-all duration-200" :class="paymentType === 'qris' ? 'opacity-100' : 'opacity-0'">
                                     <div class="w-5 h-5 bg-gray-900 text-white rounded-full flex items-center justify-center"><i class="fa-solid fa-check text-[10px]"></i></div>
                                </div>
                            </button>

                            {{-- GoPay --}}
                            <button type="button" @click="selectMethod('gopay')"
                                :class="paymentType === 'gopay' 
                                    ? 'border-[#00AED6] bg-[#00AED6]/5 ring-0 shadow-md sm:shadow-none' 
                                    : 'border-slate-200 dark:border-slate-700 hover:border-[#00AED6]/50 bg-white dark:bg-slate-800'"
                                class="relative flex flex-col items-center justify-between gap-2 sm:gap-4 p-4 sm:p-6 rounded-2xl border transition-all duration-200 cursor-pointer group h-full overflow-hidden min-w-[130px] sm:min-w-0 snap-start flex-shrink-0 sm:flex-shrink">
                                <div class="flex-1 flex items-center justify-center w-full py-1 sm:py-2 z-10">
                                    <div class="w-14 h-14 sm:w-20 sm:h-20 relative transition-opacity duration-200 group-hover:opacity-80">
                                        <img src="{{ asset('images/payment/gopay.png') }}" alt="GoPay" class="w-full h-full object-contain transition-all duration-300">
                                    </div>
                                </div>
                                <div class="text-center w-full z-10">
                                    <p class="font-medium text-sm text-slate-900 dark:text-white mb-0.5 sm:mb-1 transition-colors" :class="paymentType === 'gopay' ? 'text-[#00AED6]' : ''">GoPay</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wide">Instant Checkout</p>
                                </div>
                                <div class="absolute top-3 right-3 sm:top-4 sm:right-4 transition-all duration-200" :class="paymentType === 'gopay' ? 'opacity-100' : 'opacity-0'">
                                     <div class="w-5 h-5 bg-[#00AED6] text-white rounded-full flex items-center justify-center"><i class="fa-solid fa-check text-[10px]"></i></div>
                                </div>
                            </button>

                            {{-- ShopeePay --}}
                            <button type="button" @click="selectMethod('shopeepay')"
                                :class="paymentType === 'shopeepay' 
                                    ? 'border-[#EE4D2D] bg-[#EE4D2D]/5 ring-0 shadow-md sm:shadow-none' 
                                    : 'border-slate-200 dark:border-slate-700 hover:border-[#EE4D2D]/50 bg-white dark:bg-slate-800'"
                                class="relative flex flex-col items-center justify-between gap-2 sm:gap-4 p-4 sm:p-6 rounded-2xl border transition-all duration-200 cursor-pointer group h-full overflow-hidden min-w-[130px] sm:min-w-0 snap-start flex-shrink-0 sm:flex-shrink">
                                <div class="flex-1 flex items-center justify-center w-full py-1 sm:py-2 z-10">
                                    <div class="w-14 h-14 sm:w-20 sm:h-20 relative transition-opacity duration-200 group-hover:opacity-80">
                                        <img src="{{ asset('images/payment/shopeepay.png') }}" alt="ShopeePay" class="w-full h-full object-contain transition-all duration-300">
                                    </div>
                                </div>
                                <div class="text-center w-full z-10">
                                    <p class="font-medium text-sm text-slate-900 dark:text-white mb-0.5 sm:mb-1 transition-colors" :class="paymentType === 'shopeepay' ? 'text-[#EE4D2D]' : ''">ShopeePay</p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wide">Instant Checkout</p>
                                </div>
                                <div class="absolute top-3 right-3 sm:top-4 sm:right-4 transition-all duration-200" :class="paymentType === 'shopeepay' ? 'opacity-100' : 'opacity-0'">
                                     <div class="w-5 h-5 bg-[#EE4D2D] text-white rounded-full flex items-center justify-center"><i class="fa-solid fa-check text-[10px]"></i></div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="mb-5 sm:mb-8 relative">
                        <div class="mb-3">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-building-columns text-primary"></i>
                                <span class="hidden sm:inline">Virtual Account (Transfer Bank)</span>
                                <span class="sm:hidden">VA (Transfer Bank)</span>
                                <span class="hidden sm:inline-flex bg-yellow-100 text-yellow-700 text-[10px] py-0.5 px-2 rounded-full font-bold border border-yellow-200 items-center"><i class="fa-solid fa-clock mr-1"></i> Segera Hadir</span>
                            </h3>
                            <span class="sm:hidden inline-flex mt-1.5 ml-5 bg-yellow-100 text-yellow-700 text-[9px] py-0.5 px-2 rounded-full font-bold border border-yellow-200 items-center"><i class="fa-solid fa-clock mr-1"></i> Segera Hadir</span>
                        </div>
                        <div class="flex sm:grid sm:grid-cols-4 gap-3 sm:gap-4 overflow-x-auto sm:overflow-visible pb-2 sm:pb-0 -mx-1 px-1 scrollbar-hide opacity-60 grayscale select-none pointer-events-none filter">
                            {{-- Disabled Bank Buttons (Same as payment.blade.php) --}}
                            <button type="button" disabled class="relative flex flex-col items-center justify-center gap-3 p-3 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed h-20 sm:h-24 min-w-[100px]"><img src="{{ asset('images/payment/bca.png') }}" class="h-6 sm:h-8 w-auto object-contain"></button>
                            <button type="button" disabled class="relative flex flex-col items-center justify-center gap-3 p-3 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed h-20 sm:h-24 min-w-[100px]"><img src="{{ asset('images/payment/bni.png') }}" class="h-6 sm:h-8 w-auto object-contain"></button>
                            <button type="button" disabled class="relative flex flex-col items-center justify-center gap-3 p-3 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed h-20 sm:h-24 min-w-[100px]"><img src="{{ asset('images/payment/bri.png') }}" class="h-6 sm:h-8 w-auto object-contain"></button>
                            <button type="button" disabled class="relative flex flex-col items-center justify-center gap-3 p-3 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 cursor-not-allowed h-20 sm:h-24 min-w-[100px]"><img src="{{ asset('images/payment/mandiri.png') }}" class="h-6 sm:h-8 w-auto object-contain"></button>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-2 mb-4 sm:mb-6 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        <i class="fa-solid fa-shield-halved text-green-500"></i>
                        <span>Pembayaran aman diproses oleh Midtrans</span>
                    </div>

                    <div class="hidden sm:block">
                        <button type="submit" id="pay-button-desktop" :disabled="!paymentType || isSubmitting"
                            :class="paymentType && !isSubmitting ? 'bg-primary hover:bg-primary/90 shadow-lg shadow-primary/25 hover:shadow-xl' : 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed'"
                            class="w-full text-white font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                            <template x-if="isSubmitting"><span><i class="fa-solid fa-spinner fa-spin mr-2"></i>Memproses pesanan...</span></template>
                            <template x-if="!isSubmitting && paymentType"><span>Lanjutkan Pembayaran <i class="fa-solid fa-arrow-right ml-1"></i></span></template>
                            <template x-if="!isSubmitting && !paymentType"><span>Pilih metode pembayaran</span></template>
                        </button>
                    </div>

                    <div class="fixed sm:hidden bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-slate-800/95 backdrop-blur-lg p-4 border-t border-slate-100 dark:border-slate-700 shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
                        <button type="submit" id="pay-button-mobile" :disabled="!paymentType || isSubmitting"
                            :class="paymentType && !isSubmitting ? 'bg-primary hover:bg-primary/90 shadow-lg shadow-primary/25' : 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed'"
                            class="w-full text-white font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2 text-[15px]">
                            <template x-if="isSubmitting"><span><i class="fa-solid fa-spinner fa-spin mr-2"></i>Memproses...</span></template>
                            <template x-if="!isSubmitting && paymentType"><span>Lanjutkan <i class="fa-solid fa-arrow-right ml-1"></i></span></template>
                            <template x-if="!isSubmitting && !paymentType"><span>Pilih Pembayaran</span></template>
                        </button>
                    </div>
                </form>
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
