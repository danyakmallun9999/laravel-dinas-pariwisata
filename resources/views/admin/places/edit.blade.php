<x-app-layout :full-width="true">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500">Admin Panel · Lokasi</p>
                <h2 class="font-semibold text-xl md:text-2xl text-gray-800 leading-tight">
                    Edit Lokasi
                </h2>
            </div>
            <a href="{{ isset($isSinglePlaceManager) && $isSinglePlaceManager ? route('admin.dashboard') : route('admin.places.index') }}" class="inline-flex items-center gap-2 px-3 py-2 md:px-4 md:py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
                <i class="fa-solid fa-arrow-left text-xs md:text-sm"></i>
                <span class="hidden md:inline">{{ isset($isSinglePlaceManager) && $isSinglePlaceManager ? 'Kembali ke Dashboard' : 'Kembali' }}</span>
            </a>
        </div>
    </x-slot>

    @include('admin.places.partials.form', [
        'action' => route('admin.places.update', $place),
        'method' => 'PUT',
        'submitLabel' => 'Perbarui Lokasi'
    ])
</x-app-layout>

