<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-28 pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">E-Tickets</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Tickets.Breadcrumb.MyTickets') }}</span>
            </nav>

            {{-- User profile bar --}}
            @auth('web')
            <div class="mb-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 md:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @if(auth('web')->user()->avatar)
                            <img src="{{ auth('web')->user()->avatar }}" 
                                 alt="{{ auth('web')->user()->name }}" 
                                 class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-600">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr(auth('web')->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white text-sm leading-tight">{{ auth('web')->user()->name }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mt-0.5">
                                <svg class="w-3 h-3" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                {{ auth('web')->user()->email }}
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('auth.user.logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-3.5 py-2 bg-slate-50 dark:bg-slate-700/50 hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 font-medium rounded-xl transition-colors border border-slate-200 dark:border-slate-600 hover:border-red-200 dark:hover:border-red-800 flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
            @endauth

            {{-- Main card --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 md:p-8"
                 x-data="ticketList()" x-cloak>

                {{-- Page header --}}
                <div class="text-center mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-1">{{ __('Tickets.My.Title') }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Tickets.My.Subtitle') }}</p>
                </div>

                @if($orders->count() > 0)

                {{-- ════════════════════════════════════════ --}}
                {{-- TABS                                    --}}
                {{-- ════════════════════════════════════════ --}}
                <div class="mb-4 -mx-5 md:-mx-8 px-5 md:px-8 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex gap-1 overflow-x-auto scrollbar-hide -mb-px">
                        <template x-for="tab in tabs" :key="tab.key">
                            <button @click="activeTab = tab.key"
                                :class="activeTab === tab.key
                                    ? 'border-primary text-primary font-bold'
                                    : 'border-transparent text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300'"
                                class="relative flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium border-b-2 transition-all whitespace-nowrap shrink-0">
                                <i :class="tab.icon" class="text-[11px]"></i>
                                <span x-text="tab.label"></span>
                                <span x-show="tab.count > 0"
                                      x-text="tab.count"
                                      :class="activeTab === tab.key ? 'bg-primary/10 text-primary' : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500'"
                                      class="ml-0.5 text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center leading-none"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ════════════════════════════════════════ --}}
                {{-- SEARCH                                  --}}
                {{-- ════════════════════════════════════════ --}}
                <div class="mb-5">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 dark:text-slate-600 text-sm pointer-events-none"></i>
                        <input type="text" x-model.debounce.200ms="searchQuery"
                               placeholder="Cari order ID atau nama destinasi..."
                               class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-600 rounded-xl text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <button x-show="searchQuery.length > 0" x-cloak
                                @click="searchQuery = ''"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Results count --}}
                <div class="flex items-center justify-between mb-4 px-1">
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        <span x-text="visibleCount"></span> tiket ditampilkan
                    </p>
                </div>

                {{-- ═══════════════════════════════════════════════ --}}
                {{-- TICKET CARD CSS — Premium physical ticket feel --}}
                {{-- ═══════════════════════════════════════════════ --}}
                <style>
                    /* Perforated punch holes — semicircles at the tear line */
                    .tkt-tear {
                        position: relative;
                        height: 0;
                    }
                    .tkt-tear::before,
                    .tkt-tear::after {
                        content: '';
                        position: absolute;
                        width: 24px;
                        height: 24px;
                        border-radius: 50%;
                        top: 50%;
                        transform: translateY(-50%);
                        z-index: 10;
                        /* Match the outer container bg */
                        background: #ffffff;
                        box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
                    }
                    .dark .tkt-tear::before,
                    .dark .tkt-tear::after {
                        background: #1e293b;
                        box-shadow: inset 0 1px 3px rgba(0,0,0,0.25);
                    }
                    .tkt-tear::before { left: -12px; }
                    .tkt-tear::after  { right: -12px; }

                    /* Dashed perforation line between the punch holes */
                    .tkt-tear-line {
                        border-top: 2px dashed;
                        border-color: #e2e8f0;
                        margin: 0 20px;
                    }
                    .dark .tkt-tear-line {
                        border-color: #334155;
                    }

                    /* Left accent stripe — 4px colored bar */
                    .tkt-accent {
                        position: absolute;
                        left: 0;
                        top: 16px;
                        bottom: 16px;
                        width: 4px;
                        border-radius: 0 4px 4px 0;
                    }

                    /* Card outer shell */
                    .tkt-card {
                        position: relative;
                        border-radius: 20px;
                        overflow: hidden;
                        transition: box-shadow 0.2s ease, transform 0.15s ease;
                    }
                    .tkt-card:hover {
                        box-shadow: 0 8px 30px -8px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04);
                        transform: translateY(-1px);
                    }
                    .dark .tkt-card:hover {
                        box-shadow: 0 8px 30px -8px rgba(0,0,0,0.3), 0 2px 8px rgba(0,0,0,0.2);
                    }

                    /* Scrollbar hide for tabs */
                    .scrollbar-hide::-webkit-scrollbar { display: none; }
                    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

                    /* Countdown shimmer animation */
                    @keyframes countdown-pulse {
                        0%, 100% { opacity: 1; }
                        50% { opacity: 0.7; }
                    }
                    .countdown-pulse {
                        animation: countdown-pulse 2s ease-in-out infinite;
                    }
                </style>

                {{-- ════════════════════════════════════════ --}}
                {{-- TICKET CARDS                            --}}
                {{-- ════════════════════════════════════════ --}}
                <div class="space-y-5">
                    @foreach($orders as $order)
                        @php
                            $now = now();
                            $expiry = $order->expiry_time;
                            $isExpired = $order->status === 'pending' && $expiry && $now->greaterThan($expiry);
                            $remainingMs = $expiry && !$isExpired ? $now->diffInMilliseconds($expiry) : 0;
                            $cardStatus = $order->status;
                        @endphp

                        @if($order->status == 'pending')
                            {{-- ═══════════════════════════════════════ --}}
                            {{-- PENDING CARD                           --}}
                            {{-- ═══════════════════════════════════════ --}}
                            <div class="ticket-card tkt-card bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)]"
                                 data-status="{{ $cardStatus }}"
                                 data-order="{{ strtolower($order->order_number) }}"
                                 data-place="{{ strtolower($order->ticket->place->name ?? '') }}"
                                 x-show="isVisible('{{ $cardStatus }}', '{{ strtolower($order->order_number) }}', '{{ strtolower($order->ticket->place->name ?? '') }}')"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-data="{ 
                                    remaining: {{ $remainingMs }},
                                    expired: {{ $isExpired ? 'true' : 'false' }},
                                    showCancelConfirm: false,
                                    timer: null,
                                    format(ms) {
                                        if (ms <= 0) return '00:00:00';
                                        const h = Math.floor(ms / 3600000);
                                        const m = Math.floor((ms % 3600000) / 60000);
                                        const s = Math.floor((ms % 60000) / 1000);
                                        return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                                    },
                                    init() {
                                        if (this.remaining > 0 && !this.expired) {
                                            const endTime = new Date().getTime() + this.remaining;
                                            this.timer = setInterval(() => {
                                                const now = new Date().getTime();
                                                this.remaining = Math.max(0, endTime - now);
                                                if (this.remaining <= 0) {
                                                    clearInterval(this.timer);
                                                    this.expired = true;
                                                }
                                            }, 1000);
                                        }
                                    }
                                 }"
                                 x-init="init()">
                                
                                {{-- Left accent stripe — reactive color --}}
                                <div class="tkt-accent transition-colors duration-500"
                                     :class="expired ? 'bg-red-400' : 'bg-amber-400'"></div>

                                {{-- ▎HEADER — Order ID + Status + Timer --}}
                                <div class="px-6 pt-5 pb-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-mono text-[11px] text-slate-400 dark:text-slate-500 tracking-wider mb-1.5 uppercase">{{ $order->order_number }}</p>
                                            {{-- Active status badge --}}
                                            <span x-show="!expired"
                                                  x-transition:leave="transition ease-in duration-200"
                                                  x-transition:leave-start="opacity-100"
                                                  x-transition:leave-end="opacity-0"
                                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 ring-1 ring-amber-200/60 dark:ring-amber-800/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 countdown-pulse"></span>
                                                {{ $order->status_label }}
                                            </span>
                                            {{-- Expired status badge --}}
                                            <span x-show="expired" x-cloak
                                                  x-transition:enter="transition ease-out duration-300"
                                                  x-transition:enter-start="opacity-0 scale-90"
                                                  x-transition:enter-end="opacity-100 scale-100"
                                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 ring-1 ring-red-200/60 dark:ring-red-800/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Kadaluwarsa
                                            </span>
                                        </div>
                                        {{-- Countdown timer (right-aligned) --}}
                                        <div x-show="remaining > 0 && !expired" x-cloak 
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             class="flex flex-col items-end shrink-0">
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 mb-0.5 uppercase tracking-wider font-medium">Sisa Waktu</span>
                                            <span class="font-mono text-base font-extrabold text-amber-600 dark:text-amber-400 tabular-nums" x-text="format(remaining)"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- ▎BODY — Destination + Details --}}
                                <div class="px-6 pb-4">
                                    {{-- Destination — icon color transitions --}}
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5 transition-colors duration-500"
                                             :class="expired ? 'bg-red-50 dark:bg-red-900/20' : 'bg-amber-50 dark:bg-amber-900/20'">
                                            <i class="fa-solid fa-location-dot text-sm transition-colors duration-500"
                                               :class="expired ? 'text-red-400 dark:text-red-500' : 'text-amber-500 dark:text-amber-400'"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[15px] text-slate-900 dark:text-white leading-snug">{{ $order->ticket->place->name }}</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate">{{ $order->ticket->name }} · <span class="capitalize">{{ $order->ticket->type }}</span></p>
                                        </div>
                                    </div>

                                    {{-- Info chips row --}}
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <div class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-700/40 rounded-lg px-3 py-1.5">
                                            <i class="fa-regular fa-calendar text-[10px] text-slate-400 dark:text-slate-500"></i>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $order->visit_date->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-700/40 rounded-lg px-3 py-1.5">
                                            <i class="fa-solid fa-users text-[10px] text-slate-400 dark:text-slate-500"></i>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $order->quantity }} tiket</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 bg-primary/5 dark:bg-primary/10 rounded-lg px-3 py-1.5">
                                            <span class="text-xs font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    {{-- Payment Method & Inline Details — hides when expired --}}
                                    @if($order->payment_method_detail)
                                    <div x-show="!expired"
                                         x-transition:leave="transition ease-in duration-300"
                                         x-transition:leave-start="opacity-100 max-h-60"
                                         x-transition:leave-end="opacity-0 max-h-0"
                                         class="bg-slate-50/80 dark:bg-slate-700/20 rounded-xl p-3.5 border border-slate-100 dark:border-slate-700/60 overflow-hidden">
                                        <div class="flex items-center justify-between mb-2.5">
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">Metode Pembayaran</span>
                                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                                                @if($order->payment_method_detail === 'bank_transfer')
                                                    Bank {{ strtoupper($order->payment_channel) }}
                                                @else
                                                    {{ ucfirst($order->payment_method_detail) }}
                                                @endif
                                            </span>
                                        </div>

                                        @if(isset($order->payment_info['va_number']))
                                            <div class="bg-white dark:bg-slate-800 rounded-lg p-3 border border-slate-200/80 dark:border-slate-600/60 flex items-center justify-between gap-2">
                                                <div class="min-w-0">
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-0.5">Nomor Virtual Account</p>
                                                    <p class="font-mono text-[15px] font-bold text-slate-800 dark:text-white tracking-wider truncate">{{ $order->payment_info['va_number'] }}</p>
                                                </div>
                                                <button onclick="navigator.clipboard.writeText('{{ $order->payment_info['va_number'] }}'); this.querySelector('i').className='fa-solid fa-check text-emerald-500'; setTimeout(() => this.querySelector('i').className='fa-regular fa-copy', 1500)" 
                                                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 dark:bg-slate-700 hover:bg-primary/5 dark:hover:bg-primary/10 text-slate-400 hover:text-primary transition-colors shrink-0" title="Salin VA">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        @elseif(isset($order->payment_info['bill_key']))
                                            <div class="bg-white dark:bg-slate-800 rounded-lg p-3 border border-slate-200/80 dark:border-slate-600/60 space-y-2">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500">Bill Key</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-mono text-sm font-bold text-slate-800 dark:text-white">{{ $order->payment_info['bill_key'] }}</span>
                                                        <button onclick="navigator.clipboard.writeText('{{ $order->payment_info['bill_key'] }}')" class="text-slate-400 hover:text-primary text-xs transition-colors"><i class="fa-regular fa-copy"></i></button>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500">Biller Code</span>
                                                    <span class="font-mono text-sm font-bold text-slate-800 dark:text-white">{{ $order->payment_info['biller_code'] }}</span>
                                                </div>
                                            </div>
                                        @elseif(isset($order->payment_info['qr_url']))
                                            <div class="text-center py-1">
                                                <div class="inline-block bg-white rounded-xl p-3 border border-slate-200/80">
                                                    <img src="{{ $order->payment_info['qr_url'] }}" alt="QR Code" class="h-28 w-28 object-contain">
                                                </div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5">Scan QR untuk membayar</p>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                {{-- ▎TEAR LINE — Perforated separator --}}
                                <div class="tkt-tear">
                                    <div class="tkt-tear-line"></div>
                                </div>

                                {{-- ▎FOOTER — Action buttons (reactive) --}}
                                <div class="px-6 py-4">
                                    {{-- Active state buttons --}}
                                    <div x-show="!expired"
                                         x-transition:leave="transition ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0">
                                        <div class="flex gap-2.5 mb-2.5">
                                            <a href="{{ route('payment.status', $order->order_number) }}" 
                                               class="flex-1 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-center font-semibold py-3 rounded-[14px] transition-all text-sm flex items-center justify-center gap-2 shadow-md shadow-amber-500/15 active:scale-[0.98]">
                                                <i class="fa-solid fa-wallet text-xs"></i> Bayar Sekarang
                                            </a>
                                            <button @click="
                                                $el.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin text-xs\'></i>';
                                                $el.disabled = true;
                                                fetch('{{ route('payment.check', $order->order_number) }}')
                                                    .then(r => r.json())
                                                    .then(d => {
                                                        if(d.status === 'paid') { window.location.reload(); }
                                                        else { 
                                                            $el.innerHTML = '<i class=\'fa-solid fa-check text-emerald-500 text-xs\'></i>';
                                                            setTimeout(() => { $el.innerHTML = '<i class=\'fa-solid fa-arrows-rotate text-xs\'></i>'; $el.disabled = false; }, 2000);
                                                        }
                                                    })
                                                    .catch(() => { $el.innerHTML = '<i class=\'fa-solid fa-arrows-rotate text-xs\'></i>'; $el.disabled = false; });
                                            " class="w-12 h-12 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-500 dark:text-slate-400 rounded-[14px] transition-all flex items-center justify-center border border-slate-200 dark:border-slate-600 active:scale-95 shrink-0" title="Cek Status Pembayaran">
                                                <i class="fa-solid fa-arrows-rotate text-xs"></i>
                                            </button>
                                        </div>
                                        <div class="flex gap-2.5">
                                            <a href="{{ route('booking.confirmation', $order->order_number) }}" 
                                               class="flex-1 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 text-center font-semibold py-3 rounded-[14px] transition-all text-sm flex items-center justify-center gap-2 border border-slate-200/80 dark:border-slate-600 active:scale-[0.98]">
                                                <i class="fa-solid fa-receipt text-xs"></i> {{ __('Tickets.My.ViewDetail') }}
                                            </a>
                                            <button @click="showCancelConfirm = true"
                                                class="w-12 h-auto bg-red-50 dark:bg-red-900/15 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-500 dark:text-red-400 rounded-[14px] transition-all flex items-center justify-center border border-red-200/60 dark:border-red-800/40 active:scale-95 shrink-0">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Expired state buttons --}}
                                    <div x-show="expired" x-cloak
                                         x-transition:enter="transition ease-out duration-300 delay-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0">
                                        <div class="mb-2.5">
                                            <a href="{{ route('tickets.show', $order->ticket->id) }}" class="w-full bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 text-center font-semibold py-3 rounded-[14px] transition-all text-sm flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-600 active:scale-[0.98]">
                                                <i class="fa-solid fa-redo text-xs"></i> Pesan Ulang
                                            </a>
                                        </div>
                                        <div class="flex gap-2.5">
                                            <a href="{{ route('booking.confirmation', $order->order_number) }}" 
                                               class="flex-1 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 text-center font-semibold py-3 rounded-[14px] transition-all text-sm flex items-center justify-center gap-2 border border-slate-200/80 dark:border-slate-600 active:scale-[0.98]">
                                                <i class="fa-solid fa-receipt text-xs"></i> {{ __('Tickets.My.ViewDetail') }}
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Cancel Confirmation Modal --}}
                                    <div x-show="showCancelConfirm" x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0">
                                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center"
                                             @click.away="showCancelConfirm = false"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100">
                                            <div class="w-14 h-14 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                <i class="fa-solid fa-triangle-exclamation text-red-500 text-2xl"></i>
                                            </div>
                                            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Batalkan Pesanan?</h3>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Pesanan <strong class="font-mono text-slate-700 dark:text-slate-300">{{ $order->order_number }}</strong> akan dibatalkan secara permanen.</p>
                                            <div class="flex gap-3">
                                                <button @click="showCancelConfirm = false"
                                                    class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-semibold rounded-xl transition-colors text-sm">
                                                    Kembali
                                                </button>
                                                <form action="{{ route('payment.cancel', $order->order_number) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="w-full px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-colors text-sm">
                                                        Ya, Batalkan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @else
                            {{-- ═══════════════════════════════════════ --}}
                            {{-- PAID / USED / CANCELLED CARD           --}}
                            {{-- ═══════════════════════════════════════ --}}
                            <div class="ticket-card tkt-card bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-[0_1px_3px_rgba(0,0,0,0.04)]"
                                 data-status="{{ $cardStatus }}"
                                 data-order="{{ strtolower($order->order_number) }}"
                                 data-place="{{ strtolower($order->ticket->place->name ?? '') }}"
                                 x-show="isVisible('{{ $cardStatus }}', '{{ strtolower($order->order_number) }}', '{{ strtolower($order->ticket->place->name ?? '') }}')"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0">

                                {{-- Left accent stripe --}}
                                <div class="tkt-accent {{ $order->status == 'paid' ? 'bg-emerald-400' : ($order->status == 'used' ? 'bg-blue-400' : 'bg-red-400') }}"></div>

                                {{-- ▎HEADER — Order ID + Status --}}
                                <div class="px-6 pt-5 pb-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-mono text-[11px] text-slate-400 dark:text-slate-500 tracking-wider mb-1.5 uppercase">{{ $order->order_number }}</p>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-bold
                                                {{ $order->status == 'paid' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 ring-1 ring-emerald-200/60 dark:ring-emerald-800/40' : '' }}
                                                {{ $order->status == 'used' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 ring-1 ring-blue-200/60 dark:ring-blue-800/40' : '' }}
                                                {{ $order->status == 'cancelled' ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 ring-1 ring-red-200/60 dark:ring-red-800/40' : '' }}">
                                                @if($order->status == 'paid')
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                @elseif($order->status == 'used')
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                @endif
                                                {{ $order->status_label }}
                                            </span>
                                        </div>
                                        {{-- Ticket number badge (paid/used only) --}}
                                        @if($order->ticket_number && in_array($order->status, ['paid', 'used']))
                                        <div class="text-right shrink-0">
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-medium mb-0.5">No. Tiket</p>
                                            <p class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300 tracking-wider">{{ $order->ticket_number }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- ▎BODY — Destination + Details --}}
                                <div class="px-6 pb-4">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5
                                            {{ $order->status == 'paid' ? 'bg-emerald-50 dark:bg-emerald-900/20' : '' }}
                                            {{ $order->status == 'used' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}
                                            {{ $order->status == 'cancelled' ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                            <i class="fa-solid fa-location-dot text-sm
                                                {{ $order->status == 'paid' ? 'text-emerald-500 dark:text-emerald-400' : '' }}
                                                {{ $order->status == 'used' ? 'text-blue-500 dark:text-blue-400' : '' }}
                                                {{ $order->status == 'cancelled' ? 'text-red-400 dark:text-red-500' : '' }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[15px] text-slate-900 dark:text-white leading-snug">{{ $order->ticket->place->name }}</p>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 truncate">{{ $order->ticket->name }} · <span class="capitalize">{{ $order->ticket->type }}</span></p>
                                        </div>
                                    </div>

                                    {{-- Info chips --}}
                                    <div class="flex flex-wrap gap-2">
                                        <div class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-700/40 rounded-lg px-3 py-1.5">
                                            <i class="fa-regular fa-calendar text-[10px] text-slate-400 dark:text-slate-500"></i>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $order->visit_date->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-700/40 rounded-lg px-3 py-1.5">
                                            <i class="fa-solid fa-users text-[10px] text-slate-400 dark:text-slate-500"></i>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $order->quantity }} tiket</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 bg-primary/5 dark:bg-primary/10 rounded-lg px-3 py-1.5">
                                            <span class="text-xs font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- ▎TEAR LINE --}}
                                <div class="tkt-tear">
                                    <div class="tkt-tear-line"></div>
                                </div>

                                {{-- ▎FOOTER — Actions --}}
                                <div class="px-6 py-4 flex gap-2.5">
                                    <a href="{{ route('booking.confirmation', $order->order_number) }}" 
                                       class="flex-1 bg-gradient-to-r {{ $order->status == 'paid' ? 'from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 shadow-emerald-500/15' : ($order->status == 'used' ? 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-blue-500/15' : 'from-slate-500 to-slate-600 hover:from-slate-600 hover:to-slate-700 shadow-slate-500/10') }} text-white text-center font-semibold py-3 rounded-[14px] transition-all text-sm flex items-center justify-center gap-2 shadow-md active:scale-[0.98]">
                                        <i class="fa-solid fa-receipt text-xs"></i> Detail
                                    </a>
                                    @if(in_array($order->status, ['paid', 'used']))
                                    <a href="{{ route('tickets.download', $order->order_number) }}" 
                                       class="flex-1 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 text-center font-semibold py-3 rounded-[14px] transition-all text-sm flex items-center justify-center gap-2 border border-slate-200/80 dark:border-slate-600 active:scale-[0.98]">
                                        <i class="fa-solid fa-download text-xs"></i> Download
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- ════════════════════════════════════════ --}}
                {{-- EMPTY STATE (per tab / search)          --}}
                {{-- ════════════════════════════════════════ --}}
                <div x-show="visibleCount === 0" x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="text-center py-14">
                    
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
                         :class="emptyState.bgClass">
                        <i :class="emptyState.icon + ' text-2xl ' + emptyState.iconClass"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1.5" x-text="emptyState.title"></h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-sm mx-auto" x-text="emptyState.subtitle"></p>
                    <a href="{{ route('tickets.index') }}" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-xl transition-colors text-sm inline-flex items-center gap-2">
                        <i class="fa-solid fa-store text-xs"></i>
                        {{ __('Tickets.My.BuyNew') }}
                    </a>
                </div>

                @else
                    {{-- Global empty state (no orders at all) --}}
                    <div class="text-center py-14">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-ticket text-slate-300 dark:text-slate-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1.5">{{ __('Tickets.My.NoTicketsTitle') }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-sm mx-auto">{{ __('Tickets.My.NoTicketsSubtitle') }}</p>
                        <a href="{{ route('tickets.index') }}" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-xl transition-colors text-sm inline-flex items-center gap-2">
                            <i class="fa-solid fa-store text-xs"></i>
                            {{ __('Tickets.My.BuyNew') }}
                        </a>
                    </div>
                @endif

                {{-- Footer navigation --}}
                <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-700 flex items-center justify-center gap-4">
                    <a href="{{ route('tickets.index') }}" class="text-slate-400 hover:text-primary font-medium inline-flex items-center gap-1.5 text-sm transition-colors">
                        <i class="fa-solid fa-arrow-left text-xs"></i>{{ __('Tickets.My.BackToKey') }}
                    </a>
                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <a href="{{ route('welcome') }}" class="text-slate-400 hover:text-primary font-medium inline-flex items-center gap-1.5 text-sm transition-colors">
                        <i class="fa-solid fa-home text-xs"></i>{{ __('Tickets.My.Home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

@if($orders->count() > 0)
<script>
// Force reload when navigating back
window.addEventListener( "pageshow", function ( event ) {
  var historyTraversal = event.persisted || 
                         ( typeof window.performance != "undefined" && 
                              window.performance.navigation.type === 2 );
  if ( historyTraversal ) {
    // Handle page restore.
    window.location.reload();
  }
});

function ticketList() {
    const statusCounts = {
        all: {{ $orders->count() }},
        active: {{ $orders->whereIn('status', ['pending', 'paid'])->count() }},
        history: {{ $orders->where('status', 'used')->count() }},
        cancelled: {{ $orders->where('status', 'cancelled')->count() }},
    };

    return {
        activeTab: 'all',
        searchQuery: '',
        
        tabs: [
            { key: 'all', label: 'Semua', icon: 'fa-solid fa-layer-group', count: statusCounts.all },
            { key: 'active', label: 'Aktif', icon: 'fa-solid fa-bolt', count: statusCounts.active },
            { key: 'history', label: 'Riwayat', icon: 'fa-solid fa-clock-rotate-left', count: statusCounts.history },
            { key: 'cancelled', label: 'Dibatalkan', icon: 'fa-solid fa-ban', count: statusCounts.cancelled },
        ],

        isVisible(status, orderNum, placeName) {
            const tabMatch = this.activeTab === 'all' ||
                (this.activeTab === 'active' && (status === 'pending' || status === 'paid')) ||
                (this.activeTab === 'history' && status === 'used') ||
                (this.activeTab === 'cancelled' && status === 'cancelled');
            
            if (!tabMatch) return false;

            if (this.searchQuery.length > 0) {
                const q = this.searchQuery.toLowerCase();
                return orderNum.includes(q) || placeName.includes(q);
            }

            return true;
        },

        get visibleCount() {
            const cards = document.querySelectorAll('.ticket-card');
            let count = 0;
            cards.forEach(card => {
                const status = card.dataset.status;
                const order = card.dataset.order;
                const place = card.dataset.place;
                if (this.isVisible(status, order, place)) count++;
            });
            return count;
        },

        get emptyState() {
            if (this.searchQuery.length > 0) {
                return {
                    icon: 'fa-solid fa-magnifying-glass',
                    bgClass: 'bg-slate-100 dark:bg-slate-700',
                    iconClass: 'text-slate-300 dark:text-slate-500',
                    title: 'Tidak ditemukan',
                    subtitle: `Tidak ada tiket yang cocok dengan "${this.searchQuery}"`
                };
            }

            const states = {
                all: {
                    icon: 'fa-solid fa-ticket',
                    bgClass: 'bg-slate-100 dark:bg-slate-700',
                    iconClass: 'text-slate-300 dark:text-slate-500',
                    title: 'Belum ada transaksi',
                    subtitle: 'Anda belum melakukan pemesanan tiket'
                },
                active: {
                    icon: 'fa-solid fa-clock',
                    bgClass: 'bg-yellow-50 dark:bg-yellow-900/20',
                    iconClass: 'text-yellow-400 dark:text-yellow-600',
                    title: 'Belum ada tiket aktif',
                    subtitle: 'Tiket yang menunggu pembayaran atau sudah dibayar akan muncul di sini'
                },
                history: {
                    icon: 'fa-solid fa-clock-rotate-left',
                    bgClass: 'bg-blue-50 dark:bg-blue-900/20',
                    iconClass: 'text-blue-400 dark:text-blue-600',
                    title: 'Belum ada riwayat',
                    subtitle: 'Tiket yang sudah digunakan akan muncul di sini'
                },
                cancelled: {
                    icon: 'fa-solid fa-ban',
                    bgClass: 'bg-red-50 dark:bg-red-900/20',
                    iconClass: 'text-red-300 dark:text-red-600',
                    title: 'Tidak ada tiket dibatalkan',
                    subtitle: 'Tiket yang dibatalkan akan muncul di sini'
                }
            };

            return states[this.activeTab] || states.all;
        }
    };
}
</script>
@endif
</x-public-layout>
