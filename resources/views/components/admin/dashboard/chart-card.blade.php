@props(['title', 'canvasId', 'actionText' => null, 'actionLink' => null])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
        @if($actionText && $actionLink)
            <a href="{{ $actionLink }}" class="text-sm text-blue-600 hover:underline">{{ $actionText }}</a>
        @endif
    </div>
    <div class="relative w-full h-64">
        <canvas id="{{ $canvasId }}"></canvas>
    </div>
    {{ $slot }}
</div>
