<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-28 pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Index') }}</a>
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

                    <div class="space-y-3">
                        @foreach($orders as $order)
                            <div class="relative bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-colors overflow-hidden">
                                {{-- Status accent bar --}}
                                <div class="absolute top-0 left-0 w-1 h-full
                                    {{ $order->status == 'pending' ? 'bg-yellow-400' : '' }}
                                    {{ $order->status == 'paid' ? 'bg-emerald-500' : '' }}
                                    {{ $order->status == 'used' ? 'bg-blue-500' : '' }}
                                    {{ $order->status == 'cancelled' ? 'bg-red-400' : '' }}">
                                </div>
                                
                                <div class="p-4 md:p-5 pl-5 md:pl-6">
                                    {{-- Top row --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                        <div>
                                            <div class="text-[11px] text-slate-400 uppercase tracking-wider font-medium">{{ __('Tickets.My.OrderNumber') }}</div>
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</div>
                                        </div>
                                        <span class="inline-flex items-center self-start sm:self-auto gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                                            {{ $order->status == 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400' : '' }}
                                            {{ $order->status == 'paid' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : '' }}
                                            {{ $order->status == 'used' ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' : '' }}
                                            {{ $order->status == 'cancelled' ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400' : '' }}">
                                            @if($order->status == 'pending')
                                                <i class="fa-solid fa-clock text-[10px]"></i>
                                            @elseif($order->status == 'paid')
                                                <i class="fa-solid fa-check-circle text-[10px]"></i>
                                            @elseif($order->status == 'used')
                                                <i class="fa-solid fa-ticket text-[10px]"></i>
                                            @else
                                                <i class="fa-solid fa-times-circle text-[10px]"></i>
                                            @endif
                                            {{ $order->status_label }}
                                        </span>
                                    </div>

                                    {{-- Dashed separator --}}
                                    <div class="border-t border-dashed border-slate-100 dark:border-slate-700 my-3"></div>

                                    {{-- Ticket details --}}
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2.5 mb-4">
                                        <div>
                                            <div class="text-[11px] text-slate-400 font-medium uppercase tracking-wider mb-0.5">{{ __('Tickets.My.Ticket') }}</div>
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $order->ticket->name }}</div>
                                            <div class="text-xs text-primary font-medium">{{ $order->ticket->place->name }}</div>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-slate-400 font-medium uppercase tracking-wider mb-0.5">{{ __('Tickets.My.Date') }}</div>
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $order->visit_date->translatedFormat('d M Y') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-slate-400 font-medium uppercase tracking-wider mb-0.5">{{ __('Tickets.My.Quantity') }}</div>
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $order->quantity }} {{ __('Tickets.Card.Ticket') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-slate-400 font-medium uppercase tracking-wider mb-0.5">{{ __('Tickets.My.Total') }}</div>
                                            <div class="font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>

                                    {{-- Action buttons --}}
                                    <div class="flex gap-2">
                                        <a href="{{ route('tickets.confirmation', $order->order_number) }}" 
                                           class="flex-1 bg-primary hover:bg-primary/90 text-white text-center font-semibold py-2.5 rounded-xl transition-colors text-sm flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-eye text-xs"></i>{{ __('Tickets.My.ViewDetail') }}
                                        </a>
                                        <a href="{{ route('tickets.download', $order->order_number) }}" 
                                           class="flex-1 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-white text-center font-semibold py-2.5 rounded-xl transition-colors text-sm flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-600">
                                            <i class="fa-solid fa-download text-xs"></i>{{ __('Tickets.My.Download') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
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
