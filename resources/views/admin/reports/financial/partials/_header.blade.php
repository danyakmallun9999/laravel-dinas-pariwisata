{{-- Advanced Header: Quick Filters, Date Range, Grouping, Export --}}
<div x-data="{
    startDate: '{{ $startDate }}',
    endDate: '{{ $endDate }}',
    setRange(type) {
        const now = new Date();
        let start, end;
        switch(type) {
            case 'today':
                start = end = now; break;
            case 'week':
                start = new Date(now); start.setDate(now.getDate() - now.getDay() + 1);
                end = now; break;
            case 'month':
                start = new Date(now.getFullYear(), now.getMonth(), 1);
                end = now; break;
            case 'year':
                start = new Date(now.getFullYear(), 0, 1);
                end = now; break;
        }
        if (start && end) {
            this.startDate = start.toISOString().slice(0,10);
            this.endDate = end.toISOString().slice(0,10);
            this.$nextTick(() => this.$refs.filterForm.submit());
        }
    },
    activeQuick() {
        const now = new Date();
        const s = this.startDate, e = this.endDate;
        const fmt = d => d.toISOString().slice(0,10);
        if (s === fmt(now) && e === fmt(now)) return 'today';
        const mon = new Date(now); mon.setDate(now.getDate() - now.getDay() + 1);
        if (s === fmt(mon) && e === fmt(now)) return 'week';
        const m1 = new Date(now.getFullYear(), now.getMonth(), 1);
        if (s === fmt(m1) && e === fmt(now)) return 'month';
        const y1 = new Date(now.getFullYear(), 0, 1);
        if (s === fmt(y1) && e === fmt(now)) return 'year';
        return 'custom';
    }
}" class="bg-white rounded-[2rem] border border-gray-200 p-6">

    {{-- Quick Period Buttons --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mr-1">Periode:</span>
        <template x-for="[key, label] in [['today','Hari Ini'],['week','Minggu Ini'],['month','Bulan Ini'],['year','Tahun Ini']]">
            <button type="button" @click="setRange(key)"
                :class="activeQuick() === key
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-200'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                x-text="label">
            </button>
        </template>
        <span :class="activeQuick() === 'custom' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'bg-gray-50 text-gray-500'"
              class="px-3 py-1.5 rounded-lg text-xs font-semibold">
            <i class="fa-solid fa-sliders mr-1 text-[10px]"></i>Custom
        </span>
    </div>

    {{-- Date Range + Export --}}
    <form x-ref="filterForm" action="{{ route('admin.reports.financial.index') }}" method="GET"
          class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">

        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 flex-1 sm:flex-none">
            <i class="fa-regular fa-calendar text-blue-500 text-sm"></i>
            <input type="date" name="start_date" x-model="startDate"
                class="border-0 bg-transparent text-sm text-gray-700 focus:ring-0 p-0 w-[7.5rem]">
            <i class="fa-solid fa-arrow-right text-gray-300 text-[10px]"></i>
            <input type="date" name="end_date" x-model="endDate"
                class="border-0 bg-transparent text-sm text-gray-700 focus:ring-0 p-0 w-[7.5rem]">
        </div>

        <div class="flex items-center gap-2 sm:ml-auto">
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-semibold text-sm text-white hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-300 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                Terapkan
            </button>

            <a href="{{ route('admin.reports.financial.export', request()->all()) }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl font-semibold text-sm text-white hover:shadow-lg hover:shadow-emerald-500/25 transition-all duration-300 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-file-arrow-down text-xs"></i>
                Export
            </a>
        </div>
    </form>
</div>
