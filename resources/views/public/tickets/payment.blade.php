<x-public-layout :hideFooter="true">
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Index') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Tickets.Breadcrumb.Payment') }}</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-credit-card text-primary text-2xl"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ __('Tickets.Payment.Title') }}</h1>
                    <p class="text-slate-500 dark:text-slate-400">{{ __('Tickets.Payment.Subtitle') }}</p>
                </div>

                <!-- Order Summary -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-6 mb-8">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary"></i>
                        {{ __('Tickets.Payment.SummaryTitle') }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-start pb-4 border-b border-slate-200 dark:border-slate-600">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Payment.OrderNumber') }}</p>
                                <p class="font-bold text-slate-900 dark:text-white font-mono">{{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Payment.Date') }}</p>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $order->visit_date->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Payment.Ticket') }}</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $order->ticket->name }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $order->ticket->place->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Payment.Quantity') }}</p>
                                <p class="font-bold text-slate-900 dark:text-white">{{ $order->quantity }} x Rp {{ number_format($order->total_price / $order->quantity, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-600 flex justify-between items-center">
                            <span class="font-bold text-slate-900 dark:text-white">{{ __('Tickets.Payment.Total') }}</span>
                            <span class="text-xl font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Button -->
                <button id="pay-button" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 flex items-center justify-center gap-2 group">
                    <span>{{ __('Tickets.Payment.PayNowButton') }}</span>
                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>

                <div class="mt-4 text-center">
                    <a href="{{ route('tickets.my') }}" class="text-slate-500 hover:text-primary text-sm font-medium transition-colors">
                        {{ __('Tickets.Payment.PayLaterButton') }}
                    </a>
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
