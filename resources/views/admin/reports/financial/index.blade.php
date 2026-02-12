<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500 mb-0.5">Admin Panel</p>
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 leading-tight">
                    Laporan Keuangan
                </h2>
            </div>
            <div class="hidden md:flex items-center gap-2 text-sm text-gray-600 bg-white px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                <i class="fa-regular fa-calendar text-blue-500"></i>
                <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. Filter & Period Control --}}
            @include('admin.reports.financial.partials._header')

            {{-- 2. Summary Insight Cards --}}
            @include('admin.reports.financial.partials._summary-cards')

            {{-- 3. Revenue Trend Chart (Full Width) --}}
            <div class="h-auto xl:h-[450px]">
                @include('admin.reports.financial.partials._revenue-chart')
            </div>

            {{-- 4. Payment Distribution (Full Width Card) --}}
            <div>
                @include('admin.reports.financial.partials._payment-methods')
            </div>

            {{-- 4. Insight Highlights --}}
            @include('admin.reports.financial.partials._insights')

            {{-- 5. Detail Table --}}
            @include('admin.reports.financial.partials._ticket-sales-table')

        </div>
    </div>

    {{-- Chart.js Scripts --}}
    @include('admin.reports.financial.partials._charts-script')

</x-app-layout>
