<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">E-Tickets</a>
                <span>/</span>
                <a href="{{ route('tickets.my') }}" class="hover:text-primary transition-colors">Tiket Saya</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">Pembayaran Gagal</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 text-center">
                <!-- Failed Icon -->
                <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-times-circle text-red-600 dark:text-red-400 text-4xl"></i>
                </div>

                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-3">Pembayaran Gagal</h1>
                <p class="text-slate-500 dark:text-slate-400 mb-8">Maaf, pembayaran Anda tidak dapat diproses.</p>

                <!-- Order Info -->
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 mb-8 text-left">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Nomor Pesanan</p>
                            <p class="font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Status</p>
                            @php $config = $order->status_config; @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 {{ $config['bg'] }} {{ $config['iconColor'] }} text-xs font-bold rounded-xl">
                                <i class="fa-solid {{ $order->status === 'cancelled' ? 'fa-ban' : 'fa-clock' }}"></i>
                                {{ $order->status_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Tiket</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $order->ticket->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1">Total</p>
                            <p class="font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reasons -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-5 mb-8 text-left">
                    <div class="flex items-start">
                        <i class="fa-solid fa-exclamation-triangle text-yellow-600 mt-0.5 mr-3"></i>
                        <div class="text-sm text-yellow-800 dark:text-yellow-300">
                            <p class="font-bold mb-2">Kemungkinan Penyebab:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Pembayaran dibatalkan</li>
                                <li>Saldo tidak mencukupi</li>
                                <li>Waktu pembayaran habis</li>
                                <li>Terjadi kesalahan teknis</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('tickets.show', $order->ticket->slug ?? $order->ticket->id) }}" 
                       class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i> Pesan Tiket Baru
                    </a>
                    
                    <a href="{{ route('tickets.my') }}" 
                       class="block w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold py-3 rounded-2xl transition-all duration-300 text-center">
                        Lihat Pesanan Saya
                    </a>
                    
                    <a href="{{ route('tickets.index') }}" 
                       class="block w-full text-slate-600 dark:text-slate-400 hover:text-primary font-medium py-2 transition-colors text-center">
                        Kembali ke Daftar Tiket
                    </a>
                </div>

                <!-- Help -->
                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        <i class="fa-solid fa-headset mr-1 text-primary"></i>
                        Butuh bantuan? Hubungi kami di <strong class="text-slate-700 dark:text-slate-300">support@jepara.go.id</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
