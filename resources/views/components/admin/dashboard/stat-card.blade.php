@props(['title', 'value', 'icon', 'color' => 'blue', 'subtext' => null, 'trend' => null])

<div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 h-full">
    <div class="flex items-center justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center text-xl">
            <i class="{{ $icon }}"></i>
        </div>
        @if($trend)
            <span class="text-xs font-bold px-2 py-1 rounded {{ $trend > 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
            </span>
        @endif
    </div>
    <h3 class="text-3xl font-bold text-gray-800">{{ $value }}</h3>
    <p class="text-sm text-gray-500 font-medium">{{ $title }}</p>
    @if($subtext)
        <p class="text-xs text-gray-400 mt-2">{{ $subtext }}</p>
    @endif
</div>
