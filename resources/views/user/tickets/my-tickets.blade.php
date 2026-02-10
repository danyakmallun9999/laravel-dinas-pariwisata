<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-28 pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
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
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 md:p-8">

                {{-- Page header --}}
                <div class="text-center mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ __('Tickets.My.Title') }}</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Tickets.My.Subtitle') }}</p>
                </div>

                @if($orders->count() > 0)
                    {{-- Results count --}}
                    <div class="flex items-center justify-between mb-4 px-1">
                        <p class="text-sm text-slate-400">
                            {!! __('Tickets.My.ShowingOrders', ['count' => $orders->count()]) !!}
                        </p>
                    </div>

                    {{-- Ticket-style CSS --}}
                    <style>
                        .ticket-punch {
                            position: relative;
                        }
                        .ticket-punch::before,
                        .ticket-punch::after {
                            content: '';
                            position: absolute;
                            width: 20px;
                            height: 20px;
                            background: #f9fafb;
                            border-radius: 50%;
                            top: 50%;
                            transform: translateY(-50%);
                            z-index: 10;
                        }
                        .dark .ticket-punch::before,
                        .dark .ticket-punch::after {
                            background: var(--color-background-dark, #0f172a);
                        }
                        .ticket-punch::before { left: -10px; }
                        .ticket-punch::after { right: -10px; }
                    </style>

                    <div class="space-y-5">
                        @foreach($orders as $order)
                            @if($order->status == 'pending')
                                {{-- ═══════════════════════════════════════ --}}
                                {{-- PENDING CARD: Minimalist ticket design --}}
                                {{-- ═══════════════════════════════════════ --}}
                                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                    {{-- TOP: Main ticket section --}}
                                    <div class="p-5 pb-4">
                                        {{-- Status badge on its own line --}}
                                        <div class="mb-3">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                                <i class="fa-solid fa-clock text-[9px]"></i>
                                                {{ $order->status_label }}
                                            </span>
                                        </div>

                                        {{-- Destination name --}}
                                        <p class="font-bold text-base text-slate-900 dark:text-white leading-snug">{{ $order->ticket->place->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4 truncate">{{ $order->ticket->name }} · <span class="capitalize">{{ $order->ticket->type }}</span></p>

                                        {{-- Details 2x2 grid --}}
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.Date') }}</p>
                                                <p class="font-semibold text-sm text-slate-800 dark:text-white mt-0.5">{{ $order->visit_date->translatedFormat('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.Quantity') }}</p>
                                                <p class="font-semibold text-sm text-slate-800 dark:text-white mt-0.5">{{ $order->quantity }} {{ __('Tickets.Card.Ticket') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.OrderNumber') }}</p>
                                                <p class="font-mono text-xs text-slate-800 dark:text-white mt-0.5 tracking-wide">{{ $order->order_number }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.Total') }}</p>
                                                <p class="font-bold text-sm text-primary mt-0.5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Perforated tear line with punch holes --}}
                                    <div class="ticket-punch">
                                        <div class="border-t-2 border-dashed border-slate-200 dark:border-slate-700 mx-5"></div>
                                    </div>

                                    {{-- BOTTOM: Actions stub --}}
                                    <div class="px-5 py-3 flex gap-2">
                                        <a href="{{ route('tickets.payment', $order->order_number) }}" 
                                           class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white text-center font-semibold py-2.5 rounded-xl transition-all text-sm flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-credit-card text-xs"></i> Bayar
                                        </a>
                                        <a href="{{ route('tickets.confirmation', $order->order_number) }}" 
                                           class="flex-1 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 text-center font-semibold py-2.5 rounded-xl transition-all text-sm flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-600">
                                            <i class="fa-solid fa-eye text-xs"></i> {{ __('Tickets.My.ViewDetail') }}
                                        </a>
                                    </div>
                                </div>
                            @else
                                {{-- ═══════════════════════════════════════ --}}
                                {{-- PAID/USED CARD: Physical ticket design --}}
                                {{-- ═══════════════════════════════════════ --}}
                                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                    {{-- TOP: Main ticket section --}}
                                    <div class="p-5 pb-4">
                                        {{-- Destination & Status row --}}
                                        <div class="flex items-start justify-between gap-3 mb-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="font-bold text-base text-slate-900 dark:text-white leading-snug">{{ $order->ticket->place->name }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $order->ticket->name }} · <span class="capitalize">{{ $order->ticket->type }}</span></p>
                                            </div>
                                            <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold whitespace-nowrap
                                                {{ $order->status == 'paid' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : '' }}
                                                {{ $order->status == 'used' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                                                {{ $order->status == 'cancelled' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}">
                                                @if($order->status == 'paid')
                                                    <i class="fa-solid fa-check-circle text-[9px]"></i>
                                                @elseif($order->status == 'used')
                                                    <i class="fa-solid fa-ticket text-[9px]"></i>
                                                @else
                                                    <i class="fa-solid fa-times-circle text-[9px]"></i>
                                                @endif
                                                {{ $order->status_label }}
                                            </span>
                                        </div>

                                        {{-- Details 2x2 grid --}}
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.Date') }}</p>
                                                <p class="font-semibold text-sm text-slate-800 dark:text-white mt-0.5">{{ $order->visit_date->translatedFormat('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.Quantity') }}</p>
                                                <p class="font-semibold text-sm text-slate-800 dark:text-white mt-0.5">{{ $order->quantity }} {{ __('Tickets.Card.Ticket') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.OrderNumber') }}</p>
                                                <p class="font-mono text-sm text-slate-800 dark:text-white mt-0.5 tracking-wide">{{ $order->order_number }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-wider font-semibold">{{ __('Tickets.My.Total') }}</p>
                                                <p class="font-bold text-sm text-primary mt-0.5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Perforated tear line with punch holes --}}
                                    <div class="ticket-punch">
                                        <div class="border-t-2 border-dashed border-slate-200 dark:border-slate-700 mx-5"></div>
                                    </div>

                                    {{-- BOTTOM: Actions stub --}}
                                    <div class="px-5 py-3 flex gap-2">
                                        <a href="{{ route('tickets.confirmation', $order->order_number) }}" 
                                           class="flex-1 bg-primary hover:bg-primary/90 text-white text-center font-semibold py-2.5 rounded-xl transition-all text-sm flex items-center justify-center gap-1.5 shadow-sm shadow-primary/20">
                                            <i class="fa-solid fa-eye text-xs"></i> {{ __('Tickets.My.ViewDetail') }}
                                        </a>
                                        <a href="{{ route('tickets.download', $order->order_number) }}" 
                                           class="flex-1 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 text-center font-semibold py-2.5 rounded-xl transition-all text-sm flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-600">
                                            <i class="fa-solid fa-download text-xs"></i> {{ __('Tickets.My.Download') }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    {{-- Empty state --}}
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
</x-public-layout>
