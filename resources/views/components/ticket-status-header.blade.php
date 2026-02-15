@props(['order'])

@php
    $config = $order->status_config;
    $status = $order->status;
@endphp

<div x-data="{ shown: false }" x-init="$nextTick(() => shown = true)"
     class="rounded-2xl border p-6 md:p-8 text-center {{ $config['bg'] }} {{ $config['border'] }} transition-all duration-500"
     x-show="shown"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 transform translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0">

    {{-- Animated Icon --}}
    <div class="flex justify-center mb-5">
        <div class="w-20 h-20 rounded-full flex items-center justify-center {{ $config['iconBg'] }}
            @if($config['animation'] === 'pulse') animate-pulse @endif"
            @if($config['animation'] === 'scale')
                x-data="{ pop: false }"
                x-init="setTimeout(() => pop = true, 200)"
                :class="pop ? 'scale-100' : 'scale-0'"
                style="transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)"
            @endif>

            @if($status === 'paid')
                {{-- Check Circle --}}
                <svg class="w-10 h-10 {{ $config['iconColor'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($status === 'pending')
                {{-- Clock --}}
                <svg class="w-10 h-10 {{ $config['iconColor'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($status === 'cancelled' || $status === 'expired')
                {{-- X Circle --}}
                <svg class="w-10 h-10 {{ $config['iconColor'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($status === 'used')
                {{-- Ticket Check --}}
                <svg class="w-10 h-10 {{ $config['iconColor'] }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                </svg>
            @endif
        </div>
    </div>

    {{-- Title --}}
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">
        {{ $config['title'] }}
    </h1>

    {{-- Subtitle --}}
    <p class="text-sm text-slate-500 dark:text-slate-400">
        {{ $config['subtitle'] }}
    </p>

    {{-- Status Badge --}}
    <div class="mt-4">
        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-full {{ $config['iconBg'] }} {{ $config['iconColor'] }}">
            {{ $order->status_label }}
        </span>
    </div>
</div>
