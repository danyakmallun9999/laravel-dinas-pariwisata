<x-public-layout>
    @push('styles')
        <!-- Fonts: Playfair Display for Headings, Inter for UI text -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
        <style>
            .font-inter { font-family: 'Inter', sans-serif; }
            h1, h2, h3, h4, .font-serif { font-family: 'Playfair Display', serif; }
            
            @media print {
                .no-print { display: none !important; }
                body { background: white; -webkit-print-color-adjust: exact; }
                .ticket-container { box-shadow: none; margin: 0; }
                nav, footer, .pt-20 { padding-top: 0 !important; }
            }
            
            /* Custom Pattern for Luxury Feel */
            .bg-luxury {
                background-color: #f5f5f4; /* stone-100 */
                background-image: radial-gradient(#e7e5e4 1px, transparent 1px);
                background-size: 24px 24px;
            }
        </style>
    @endpush

    <div class="bg-luxury min-h-[calc(100vh-5rem)] flex flex-col items-center justify-center p-6 text-stone-800 font-inter">
        <!-- Breadcrumb -->
        <nav class="w-full max-w-[400px] flex text-xs md:text-sm text-stone-500 mb-6 space-x-2 no-print">
            <a href="{{ route('welcome') }}" class="hover:text-stone-800 transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
            <span>/</span>
            <a href="{{ route('tickets.my') }}" class="hover:text-stone-800 transition-colors">{{ __('Tickets.Breadcrumb.MyTickets') }}</a>
            <span>/</span>
            <span class="text-stone-800 font-medium font-inter">{{ __('Tickets.My.Download') }}</span>
        </nav>

        <!-- Ticket Container -->
        <div class="max-w-[400px] w-full bg-white border border-stone-200 overflow-hidden relative ticket-card shadow-xl shadow-stone-200/50">
            
            <!-- Header / Brand Section -->
            <div class="bg-stone-900 text-white p-8 text-center relative overflow-hidden">
                <!-- Subtle Texture/Noise could go here -->
                <div class="relative z-10">
                    <p class="text-[10px] uppercase tracking-[0.3em] text-stone-400 mb-2">@lang('tickets.department')</p>
                    <h1 class="text-3xl font-serif italic tracking-wide text-stone-50">@lang('tickets.header')</h1>
                    <div class="w-16 h-px bg-stone-700 mx-auto mt-4"></div>
                </div>
                
                <!-- Abstract decorative circles for premium feel -->
                <div class="absolute top-0 left-0 w-64 h-64 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-stone-800/50 rounded-full translate-x-1/3 translate-y-1/3 blur-xl"></div>
            </div>

            <!-- Ticket Body -->
            <div class="p-8 relative">
                 <!-- Decorative connectors mimicking a physical ticket tear-off line -->
                 <div class="absolute top-0 left-0 w-4 h-8 bg-stone-100 rounded-r-full -mt-4 z-20"></div>
                 <div class="absolute top-0 right-0 w-4 h-8 bg-stone-100 rounded-l-full -mt-4 z-20"></div>
                 
                 <!-- Primary Info -->
                 <div class="text-center mb-8">
                     <p class="text-stone-500 text-xs uppercase tracking-widest mb-1">@lang('tickets.destination')</p>
                     <h2 class="text-2xl font-serif font-bold text-stone-900 leading-tight">
                         {{ $order->ticket->place->name }}
                     </h2>
                 </div>

                <!-- QR Code Section -->
                <div class="flex justify-center mb-8">
                    <div class="p-4 border border-stone-200 bg-stone-50">
                        <div id="qrcode" class="mix-blend-multiply opacity-90"></div>
                    </div>
                </div>
                <div class="text-center mb-8">
                    <p class="text-[10px] text-stone-400 tracking-widest uppercase mb-1">@lang('tickets.order_code')</p>
                    <p class="font-mono text-lg font-bold text-stone-700 tracking-wider">{{ $order->order_number }}</p>
                    <div class="mt-2 inline-block px-4 py-1 border border-stone-200 rounded-full">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-2 gap-y-6 gap-x-4 border-t border-stone-100 pt-6">
                    
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-stone-400 font-medium mb-1">@lang('tickets.visit_date')</p>
                        <p class="font-serif text-lg text-stone-800">{{ $order->visit_date->translatedFormat('d M Y') }}</p>
                    </div>

                    <div class="text-right">
                        <p class="text-[10px] uppercase tracking-wider text-stone-400 font-medium mb-1">@lang('tickets.visitors')</p>
                        <p class="font-serif text-lg text-stone-800">{{ $order->quantity }} @lang('tickets.people')</p>
                    </div>

                    <div class="col-span-2">
                         <p class="text-[10px] uppercase tracking-wider text-stone-400 font-medium mb-1">@lang('tickets.ticket_type')</p>
                         <div class="flex items-baseline gap-2">
                            <span class="font-serif text-lg text-stone-800">{{ $order->ticket->name }}</span>
                            <span class="text-xs text-stone-500 italic">({{ ucfirst($order->ticket->type) }})</span>
                         </div>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="mt-8 pt-6 border-t border-dashed border-stone-300">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-medium mb-1">@lang('tickets.customer')</p>
                            <p class="text-sm font-semibold text-stone-700">{{ $order->customer_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-medium mb-1">@lang('tickets.total_payment')</p>
                            <p class="font-serif text-2xl font-bold text-stone-900">RP {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer / Decorative Bottom -->
            <div class="bg-stone-50 p-4 text-center border-t border-stone-200">
                <p class="text-[10px] text-stone-400 italic font-serif">@lang('tickets.footer_thanks')</p>
            </div>
        </div>

        <!-- Actions Area -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 w-full max-w-[400px] no-print">
            <a href="{{ route('tickets.download-qr', $order->order_number) }}" 
               class="flex-1 px-6 py-3 bg-white border border-stone-300 text-stone-600 text-xs font-bold uppercase tracking-widest hover:bg-stone-50 transition-colors text-center">
                @lang('tickets.save_qr')
            </a>
            <button onclick="downloadTicketImage()" 
                    class="flex-1 px-6 py-3 bg-stone-900 text-white text-xs font-bold uppercase tracking-widest hover:bg-stone-800 transition-colors">
                @lang('tickets.download_pdf')
            </button>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script>
            // Generate QR Code
            new QRCode(document.getElementById("qrcode"), {
                text: "{{ $order->order_number }}",
                width: 120,
                height: 120,
                colorDark : "#292524", // stone-800
                colorLight : "#f5f5f4", // stone-100 bg matches container
                correctLevel : QRCode.CorrectLevel.H
            });

            function downloadTicketImage() {
                const ticketCard = document.querySelector('.ticket-card');
                
                html2canvas(ticketCard, {
                    scale: 3, // High resolution for print quality
                    useCORS: true,
                    backgroundColor: null
                }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = 'E-Tiket-{{ $order->order_number }}.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                });
            }
        </script>
    @endpush
</x-public-layout>

