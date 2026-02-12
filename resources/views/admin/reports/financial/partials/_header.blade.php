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
}" class="bg-white rounded-[2rem] border border-gray-200 p-4 md:p-6">

    {{-- Quick Period Buttons (Horizontal Scroll on Mobile) --}}
    <div class="mb-6">
        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Periode</label>
        <div class="flex items-center gap-2 overflow-x-auto pb-2 -mx-4 px-4 md:mx-0 md:px-0 md:pb-0 scrollbar-hide">
            <template x-for="[key, label] in [['today','Hari Ini'],['week','Minggu Ini'],['month','Bulan Ini'],['year','Tahun Ini']]">
                <button type="button" @click="setRange(key)"
                    :class="activeQuick() === key
                        ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 ring-2 ring-blue-600 ring-offset-1'
                        : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border border-gray-200'"
                    class="flex-none px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 whitespace-nowrap"
                    x-text="label">
                </button>
            </template>
            <span :class="activeQuick() === 'custom' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'bg-gray-50 text-gray-500 border border-gray-200'"
                  class="flex-none px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap flex items-center">
                <i class="fa-solid fa-sliders mr-1.5 text-[10px]"></i>Custom
            </span>
        </div>
    </div>

    {{-- Date Range + Export --}}
    <form x-ref="filterForm" action="{{ route('admin.reports.financial.index') }}" method="GET"
          class="space-y-4 lg:space-y-0 lg:flex lg:items-center lg:gap-4">

        <!-- Date Inputs -->
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-1.5 flex flex-col sm:flex-row items-center gap-0 sm:gap-2">
            <div class="relative w-full sm:w-auto flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                </div>
                <input type="date" name="start_date" x-model="startDate"
                    class="block w-full pl-9 pr-3 py-2 text-sm border-0 bg-transparent rounded-xl focus:ring-2 focus:ring-blue-500 transition-shadow">
            </div>
            
            <div class="hidden sm:block text-gray-300">
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </div>
            <div class="sm:hidden w-full h-px bg-gray-200 my-1"></div>

            <div class="relative w-full sm:w-auto flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                </div>
                <input type="date" name="end_date" x-model="endDate"
                    class="block w-full pl-9 pr-3 py-2 text-sm border-0 bg-transparent rounded-xl focus:ring-2 focus:ring-blue-500 transition-shadow">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-3 lg:flex lg:ml-auto">
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition-all shadow-sm shadow-blue-200">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Filter</span>
            </button>

            <a href="{{ route('admin.reports.financial.export', request()->all()) }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold text-sm hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200">
                <i class="fa-solid fa-file-arrow-down text-xs"></i>
                <span>Export</span>
            </a>
        </div>
    </form>
</div>
