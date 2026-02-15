<x-public-layout :hideFooter="true">
    @push('styles')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            .font-inter { font-family: 'Inter', sans-serif; }
            h1, h2, h3, h4, .font-serif { font-family: 'Poppins', sans-serif; }

            @media print {
                .no-print { display: none !important; }
                body { background: white; -webkit-print-color-adjust: exact; }
                .ticket-container { box-shadow: none; margin: 0; }
                nav, footer, .pt-20 { padding-top: 0 !important; }
            }

            .bg-luxury {
                background-color: #eff6ff;
                background-image: radial-gradient(#dbeafe 1px, transparent 1px);
                background-size: 24px 24px;
            }
        </style>
    @endpush

    @php $config = $order->status_config; @endphp

    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24 font-inter">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2 no-print">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('tickets.my') }}" class="hover:text-primary transition-colors">Tiket Saya</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">Detail Pesanan</span>
            </nav>

            {{-- ═══════════════════════════════════════════════════════
                 STATUS HEADER — Dynamic per status
                 ═══════════════════════════════════════════════════════ --}}
            <div class="mb-6">
                <x-ticket-status-header :order="$order" />
            </div>

            {{-- ═══════════════════════════════════════════════════════
                 ORDER INFO CARD
                 ═══════════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 mb-6"
                 x-data="{ show: false }" x-init="setTimeout(() => show = true, 150)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-400 delay-100"
                 x-transition:enter-start="opacity-0 transform translate-y-3"
                 x-transition:enter-end="opacity-100 transform translate-y-0">

                {{-- Order Number --}}
                <div class="space-y-3 mb-5 pb-5 border-b border-slate-200 dark:border-slate-600">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">No. Pesanan</p>
                        <p class="font-mono text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-600/50 px-3 py-2 rounded-lg">{{ $order->order_number }}</p>
                    </div>
                    @if($order->ticket_number)
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">No. Tiket</p>
                        <p class="font-mono text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-600/50 px-3 py-2 rounded-lg">{{ $order->ticket_number }}</p>
                    </div>
                    @endif
                </div>

                {{-- Ticket Info Grid --}}
                <div class="grid grid-cols-2 gap-4 mb-5 pb-5 border-b border-slate-200 dark:border-slate-600">
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

                {{-- Total --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Pembayaran</span>
                    <span class="text-xl font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════
                 CUSTOMER INFO
                 ═══════════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 mb-6"
                 x-data="{ show: false }" x-init="setTimeout(() => show = true, 250)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-400 delay-200"
                 x-transition:enter-start="opacity-0 transform translate-y-3"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                <h3 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    Informasi Pemesan
                </h3>
                <div class="space-y-2.5">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-600/50 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $order->customer_name }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-600/50 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $order->customer_email }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-600/50 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        </span>
                        <span class="text-sm text-slate-700 dark:text-slate-300">{{ $order->customer_phone }}</span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════
                 QR CODE SECTION — Only for paid / used
                 ═══════════════════════════════════════════════════════ --}}
            @if($config['showQr'] && $order->ticket_number)
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 mb-6 text-center"
                 x-data="{ show: false }" x-init="setTimeout(() => show = true, 350)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-400 delay-300"
                 x-transition:enter-start="opacity-0 transform translate-y-3"
                 x-transition:enter-end="opacity-100 transform translate-y-0">

                <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75H16.5v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75H16.5v-.75z" /></svg>
                    QR Code Tiket
                </h3>

                <div class="relative inline-block">
                    <div id="qrcode" class="flex justify-center mb-3 mx-auto" style="width: 200px; height: 200px; overflow: hidden;"></div>

                    {{-- USED overlay badge --}}
                    @if($order->status === 'used')
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="bg-blue-600/90 text-white text-xs font-black uppercase tracking-[0.2em] px-5 py-1.5 rounded-lg rotate-[-15deg] shadow-lg">
                            Sudah Digunakan
                        </span>
                    </div>
                    @endif
                </div>

                @if($order->status === 'paid')
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Tunjukkan QR code ini saat berkunjung</p>
                <button onclick="downloadQR()" class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Download QR
                </button>
                @endif
            </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════
                 CONTEXTUAL INFO BOXES — Per status
                 ═══════════════════════════════════════════════════════ --}}
            @if($order->status === 'paid')
                {{-- Next Steps --}}
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-5 mb-6 text-left">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
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
            @elseif($order->status === 'pending')
                {{-- Payment Instructions --}}
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-5 mb-6 text-left">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" /></svg>
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
            @elseif($order->status === 'cancelled')
                {{-- Cancellation Notice --}}
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-5 mb-6 text-left">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                        <div class="text-sm text-slate-700 dark:text-slate-300">
                            <p class="font-bold mb-2 text-slate-900 dark:text-white">Pesanan Dibatalkan</p>
                            <p>Pesanan ini telah dibatalkan dan tidak dapat digunakan. Silakan buat pesanan baru jika Anda ingin berkunjung.</p>
                        </div>
                    </div>
                </div>
            @elseif($order->status === 'used')
                {{-- Usage Details --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-5 mb-6 text-left">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                        <div class="text-sm text-slate-700 dark:text-slate-300">
                            <p class="font-bold mb-2 text-slate-900 dark:text-white">Detail Penggunaan</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Destinasi: <strong>{{ $order->ticket->place->name }}</strong></li>
                                @if($order->check_in_time)
                                <li>Check-in: <strong>{{ $order->check_in_time->translatedFormat('d F Y, H:i') }}</strong></li>
                                @endif
                                <li>Tiket ini telah digunakan dan tidak dapat digunakan kembali</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════
                 ACTION BUTTONS — Per status
                 ═══════════════════════════════════════════════════════ --}}
            <div class="space-y-3 no-print" x-data="{ showCancelConfirm: false }">
                @if($order->status === 'pending')
                    <a href="{{ route('tickets.payment', $order->order_number) }}"
                       class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                        Bayar Sekarang
                    </a>
                    <div class="flex gap-3">
                        <button onclick="
                            this.innerHTML = '<svg class=\'w-4 h-4 animate-spin\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z\'></path></svg> Mengecek...';
                            this.disabled = true;
                            const btn = this;
                            fetch('{{ route('tickets.check-status', $order->order_number) }}')
                                .then(r => r.json())
                                .then(d => {
                                    if(d.status === 'paid') { window.location.reload(); }
                                    else {
                                        btn.innerHTML = d.message;
                                        setTimeout(() => { btn.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182\' /></svg> Cek Status'; btn.disabled = false; }, 3000);
                                    }
                                })
                                .catch(() => { btn.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'1.5\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182\' /></svg> Cek Status'; btn.disabled = false; });
                        " class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" /></svg>
                            Cek Status
                        </button>
                        <button @click="showCancelConfirm = true"
                            class="flex-1 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 font-semibold py-3 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2 text-sm border border-red-200 dark:border-red-800">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            Batalkan
                        </button>
                    </div>

                    {{-- Cancel Confirmation Modal --}}
                    <div x-show="showCancelConfirm" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center"
                             @click.away="showCancelConfirm = false">
                            <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                            </div>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Batalkan Pesanan?</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Pesanan <strong class="font-mono">{{ $order->order_number }}</strong> akan dibatalkan secara permanen.</p>
                            <div class="flex gap-3">
                                <button @click="showCancelConfirm = false"
                                    class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-xl transition-colors text-sm">
                                    Kembali
                                </button>
                                <form action="{{ route('tickets.cancel', $order->order_number) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-colors text-sm">
                                        Ya, Batalkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                @elseif($order->status === 'paid')
                    <button onclick="downloadTicketImage()" id="downloadBtn"
                       class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Download Tiket
                    </button>

                @elseif($order->status === 'cancelled')
                    <a href="{{ route('tickets.index') }}"
                       class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>
                        Pesan Tiket Baru
                    </a>
                @endif

                {{-- Back button — always visible --}}
                <a href="{{ route('tickets.my') }}"
                   class="w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                    Kembali ke Tiket Saya
                </a>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         HIDDEN TICKET CARD — For PNG download (off-screen)
         ═══════════════════════════════════════════════════════ --}}
    @if(in_array($order->status, ['paid', 'used']) && $order->ticket_number)
    <div style="position: fixed; left: -9999px; top: 0; z-index: -1;" class="font-inter text-slate-800">
        <div id="ticket-card" class="max-w-[400px] w-[400px] bg-white border border-blue-100 overflow-hidden relative ticket-card shadow-xl shadow-blue-100/50">

            {{-- Header --}}
            <div class="bg-blue-600 text-white p-8 text-center relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[10px] uppercase tracking-[0.3em] text-blue-100 mb-2">@lang('tickets.department')</p>
                    <h1 class="text-3xl font-serif italic tracking-wide text-white">@lang('tickets.header')</h1>
                    <div class="w-16 h-px bg-blue-400 mx-auto mt-4"></div>
                </div>
                <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-blue-800/20 rounded-full translate-x-1/3 translate-y-1/3 blur-xl"></div>
            </div>

            {{-- Body --}}
            <div class="p-8 relative">
                <div class="absolute top-0 left-0 w-4 h-8 bg-blue-50 rounded-r-full -mt-4 z-20"></div>
                <div class="absolute top-0 right-0 w-4 h-8 bg-blue-50 rounded-l-full -mt-4 z-20"></div>

                <div class="text-center mb-8">
                    <p class="text-slate-500 text-xs uppercase tracking-widest mb-1">@lang('tickets.destination')</p>
                    <h2 class="text-2xl font-serif font-bold text-slate-900 leading-tight">{{ $order->ticket->place->name }}</h2>
                </div>

                <div class="flex justify-center mb-8">
                    <div class="p-4 border border-blue-100 bg-blue-50/50 rounded-xl">
                        <div id="ticket-qrcode" class="mix-blend-multiply opacity-90"></div>
                    </div>
                </div>
                <div class="text-center mb-8">
                    <p class="text-[10px] text-slate-400 tracking-widest uppercase mb-1">@lang('tickets.ticket_no')</p>
                    <p class="font-mono text-lg font-bold text-slate-700 tracking-wider">{{ $order->ticket_number }}</p>
                    <div class="mt-2 inline-block px-4 py-1 border border-blue-100 bg-blue-50 rounded-full">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">{{ $order->status_label }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-6 gap-x-4 border-t border-slate-100 pt-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium mb-1">@lang('tickets.visit_date')</p>
                        <p class="font-serif text-lg text-slate-800">{{ $order->visit_date->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium mb-1">@lang('tickets.visitors')</p>
                        <p class="font-serif text-lg text-slate-800">{{ $order->quantity }} @lang('tickets.people')</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium mb-1">@lang('tickets.ticket_type')</p>
                        <div class="flex items-baseline gap-2">
                            <span class="font-serif text-lg text-slate-800">{{ $order->ticket->name }}</span>
                            <span class="text-xs text-slate-500 italic">({{ ucfirst($order->ticket->type) }})</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-dashed border-slate-300">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium mb-1">@lang('tickets.customer')</p>
                            <p class="text-sm font-semibold text-slate-700">{{ $order->customer_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium mb-1">@lang('tickets.total_payment')</p>
                            <p class="font-serif text-2xl font-bold text-blue-600">RP {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-slate-50 p-4 text-center border-t border-slate-100">
                <p class="text-[10px] text-slate-400 italic font-serif">@lang('tickets.footer_thanks')</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($config['showQr'] && $order->ticket_number)
        // QR code for the visible confirmation page
        const qrcodeElement = document.getElementById("qrcode");
        if (qrcodeElement) {
            new QRCode(qrcodeElement, {
                text: "{{ $order->ticket_number }}",
                width: 800,
                height: 800,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // QR code for hidden ticket card (PNG download)
        const ticketQrcodeElement = document.getElementById("ticket-qrcode");
        if (ticketQrcodeElement) {
            new QRCode(ticketQrcodeElement, {
                text: "{{ $order->ticket_number }}",
                width: 120,
                height: 120,
                colorDark: "#1e40af",
                colorLight: "#eff6ff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }

        // Scale down visible QR via CSS
        const styleQR = () => {
            const qrc = document.getElementById("qrcode");
            if (!qrc) return;
            const canvas = qrc.querySelector('canvas');
            const img = qrc.querySelector('img');
            if(canvas) { canvas.style.width = '100%'; canvas.style.height = '100%'; }
            if(img) { img.style.width = '100%'; img.style.height = '100%'; img.style.display = 'block'; }
        };
        styleQR();
        setTimeout(styleQR, 0);
        @endif
    });

    @if($order->status === 'paid' && $order->ticket_number)
    function downloadQR() {
        const sourceCanvas = document.querySelector('#qrcode canvas');
        if (!sourceCanvas) return;

        const padding = 100;
        const size = sourceCanvas.width;
        const newSize = size + (padding * 2);

        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = newSize;
        finalCanvas.height = newSize;
        const ctx = finalCanvas.getContext('2d');

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, newSize, newSize);
        ctx.drawImage(sourceCanvas, padding, padding);

        const url = finalCanvas.toDataURL('image/jpeg', 1.0);
        const link = document.createElement('a');
        link.download = 'ticket-qr-{{ $order->ticket_number }}.jpg';
        link.href = url;
        link.click();
    }

    function downloadTicketImage() {
        const btn = document.getElementById('downloadBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Generating...';
        btn.disabled = true;

        const ticketCard = document.getElementById('ticket-card');

        html2canvas(ticketCard, {
            scale: 3,
            useCORS: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'E-Tiket-{{ $order->ticket_number }}.png';
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
    @endif
    </script>
</x-public-layout>
