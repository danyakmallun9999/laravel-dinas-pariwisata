<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Index') }}</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ __('Tickets.Breadcrumb.MyTickets') }}</span>
            </nav>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-ticket text-primary text-2xl"></i>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ __('Tickets.My.Title') }}</h1>
                    <p class="text-slate-500 dark:text-slate-400">{{ __('Tickets.My.Subtitle') }}</p>
                </div>

                @if(!isset($orders))
                    <!-- Email Form -->
                    <div class="max-w-md mx-auto">
                        <p class="text-slate-600 dark:text-slate-400 mb-6 text-center">{{ __('Tickets.My.EmailPrompt') }}</p>
                        
                        <form action="{{ route('tickets.retrieve') }}" method="POST">
                            @csrf
                            <div class="mb-5">
                                <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    <i class="fa-solid fa-envelope mr-1 text-primary"></i> {{ __('Tickets.Form.Email') }}
                                </label>
                                <input type="email" name="email" id="email" required
                                       class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400"
                                       placeholder="{{ __('Tickets.Form.EmailPlaceholder') }}">
                            </div>
                            <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-search"></i>
                                {{ __('Tickets.My.SearchButton') }}
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Orders List -->
                    @if($orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl p-5 hover:shadow-md transition-all duration-300 border border-slate-100 dark:border-slate-700">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ __('Tickets.My.OrderNumber') }}</div>
                                            <div class="font-bold text-lg text-slate-900 dark:text-white">{{ $order->order_number }}</div>
                                        </div>
                                        <div class="mt-2 md:mt-0">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold
                                                {{ $order->status == 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' : '' }}
                                                {{ $order->status == 'paid' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}
                                                {{ $order->status == 'used' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}
                                                {{ $order->status == 'cancelled' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : '' }}">
                                                @if($order->status == 'pending')
                                                    <i class="fa-solid fa-clock"></i>
                                                @elseif($order->status == 'paid')
                                                    <i class="fa-solid fa-check-circle"></i>
                                                @elseif($order->status == 'used')
                                                    <i class="fa-solid fa-ticket"></i>
                                                @else
                                                    <i class="fa-solid fa-times-circle"></i>
                                                @endif
                                                {{ $order->status_label }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Tickets.My.Ticket') }}</div>
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $order->ticket->name }}</div>
                                            <div class="text-xs text-primary">{{ $order->ticket->place->name }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Tickets.My.Date') }}</div>
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $order->visit_date->translatedFormat('d M Y') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Tickets.My.Quantity') }}</div>
                                            <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $order->quantity }} {{ __('Tickets.Card.Ticket') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('Tickets.My.Total') }}</div>
                                            <div class="font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <a href="{{ route('tickets.confirmation', $order->order_number) }}" 
                                           class="flex-1 bg-primary hover:bg-primary/90 text-white text-center font-semibold py-2.5 rounded-xl transition-all duration-300 text-sm flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-eye"></i>{{ __('Tickets.My.ViewDetail') }}
                                        </a>
                                        <a href="{{ route('tickets.download', $order->order_number) }}" 
                                           class="flex-1 bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 dark:hover:bg-slate-500 text-slate-800 dark:text-white text-center font-semibold py-2.5 rounded-xl transition-all duration-300 text-sm flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-download"></i>{{ __('Tickets.My.Download') }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 text-center">
                            <a href="{{ route('tickets.my') }}" class="text-primary hover:text-primary/80 font-semibold inline-flex items-center gap-2">
                                <i class="fa-solid fa-search"></i>{{ __('Tickets.My.SearchOtherButton') }}
                            </a>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-ticket text-slate-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">{{ __('Tickets.My.NoTicketsTitle') }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 mb-6">{{ __('Tickets.My.NoTicketsSubtitle') }}</p>
                            <a href="{{ route('tickets.my') }}" class="text-primary hover:text-primary/80 font-semibold">
                                {{ __('Tickets.My.TryAgain') }}
                            </a>
                        </div>
                    @endif
                @endif

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 text-center">
                    <a href="{{ route('tickets.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-primary font-medium inline-flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>{{ __('Tickets.My.BackToKey') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
