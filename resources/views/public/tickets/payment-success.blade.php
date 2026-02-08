<x-public-layout :hideFooter="true">
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Index') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Tickets.Breadcrumb.Success') }}</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 text-center">
                <!-- Animated Success Icon -->
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

                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-3">{{ __('Tickets.Success.Title') }}</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-8">{{ __('Tickets.Success.Subtitle') }}</p>

                <!-- Order Info -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-8 text-left">
                    <!-- Status Badge at Top -->
                    <div class="text-center mb-4 pb-4 border-b border-slate-200 dark:border-slate-600">
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm font-bold rounded-xl">
                            <i class="fa-solid fa-check-circle"></i>
                            {{ $order->status_label }}
                        </span>
                    </div>
                    
                    <!-- Order Details - Stacked Layout -->
                    <div class="space-y-3 mb-4 pb-4 border-b border-slate-200 dark:border-slate-600">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Success.OrderCode') }}</p>
                            <p class="font-bold text-lg text-slate-900 dark:text-white">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Success.OrderCode') }}</p>
                            <p class="font-mono text-sm text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-600/50 px-3 py-2 rounded-lg">{{ $order->order_number }}</p>
                        </div>
                    </div>
                    
                    <!-- Ticket Info Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Payment.Ticket') }}</p>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $order->ticket->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">{{ __('Tickets.Payment.Quantity') }}</p>
                            <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $order->quantity }} {{ __('Tickets.Card.Ticket') }}</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="bg-gradient-to-br from-primary/5 to-indigo-500/5 border border-primary/10 rounded-2xl p-6 mb-8">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-qrcode text-primary"></i> {{ __('Tickets.Success.QRCodeTitle') }}
                    </h3>
                    <div id="qrcode" class="flex justify-center mb-3"></div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Tickets.Success.QRCodeSubtitle') }}</p>
                    <div class="mt-4">
                        <button onclick="downloadQR()" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all">
                            <i class="fa-solid fa-download"></i>{{ __('Tickets.Success.DownloadQR') }}
                        </button>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-5 mb-8 text-left">
                    <div class="flex items-start">
                        <i class="fa-solid fa-info-circle text-primary mt-0.5 mr-3"></i>
                        <div class="text-sm text-slate-700 dark:text-slate-300">
                            <p class="font-bold mb-2 text-slate-900 dark:text-white">{{ __('Tickets.Success.NextStepsTitle') }}</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>{{ __('Tickets.Success.NextSteps1') }} <strong>{{ $order->customer_email }}</strong></li>
                                <li>{{ __('Tickets.Success.NextSteps2') }}</li>
                                <li>{{ __('Tickets.Success.NextSteps3') }} {{ $order->ticket->place->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('tickets.download', $order->order_number) }}" 
                       class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i>{{ __('Tickets.Success.DownloadTicket') }}
                    </a>
                    
                    <a href="{{ route('tickets.index') }}" 
                       class="block w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300">
                        {{ __('Tickets.Success.BookMore') }}
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
