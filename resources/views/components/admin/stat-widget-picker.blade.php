{{--
Stat Widget Picker Component
Usage: <x-admin.stat-widget-picker :value="$post->stat_widgets ?? []" />

Provides a UI for selecting which statistical widgets to embed in a blog post.
--}}

@props([
    'value' => [],
])

@php
    $widgets = \App\Services\PostStatService::availableWidgets();
    $periods = \App\Services\PostStatService::availablePeriods();
@endphp

<div x-data="statWidgetPicker(@js($value ?? []), @js($widgets), @js($periods))" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-indigo-500"></i>
                <h3 class="font-bold text-gray-900">Widget Statistik</h3>
            </div>
            <span x-show="selectedWidgets.length > 0" 
                  class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full"
                  x-text="selectedWidgets.length + ' dipilih'"></span>
        </div>
        <p class="text-xs text-gray-500 mt-1">Pilih data statistik yang ingin ditampilkan di artikel.</p>
    </div>

    <div class="p-5 space-y-3">
        <template x-for="(meta, key) in widgetDefs" :key="key">
            <div class="border rounded-xl transition-all duration-200"
                 :class="isSelected(key) ? 'border-indigo-200 bg-indigo-50/50 shadow-sm' : 'border-gray-100 hover:border-gray-200'">
                
                {{-- Widget Toggle Row --}}
                <label class="flex items-center gap-3 px-4 py-3 cursor-pointer select-none">
                    <input type="checkbox" 
                           :checked="isSelected(key)"
                           @change="toggleWidget(key)"
                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/20">
                    
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                         :class="{
                             'bg-emerald-100 text-emerald-600': meta.color === 'emerald',
                             'bg-blue-100 text-blue-600': meta.color === 'blue',
                             'bg-amber-100 text-amber-600': meta.color === 'amber',
                             'bg-violet-100 text-violet-600': meta.color === 'violet',
                             'bg-cyan-100 text-cyan-600': meta.color === 'cyan',
                             'bg-rose-100 text-rose-600': meta.color === 'rose',
                             'bg-indigo-100 text-indigo-600': meta.color === 'indigo',
                         }">
                        <i :class="meta.icon"></i>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900" x-text="meta.label"></p>
                        <p class="text-[11px] text-gray-500 truncate" x-text="meta.description"></p>
                    </div>
                </label>

                {{-- Period Selector (shown when selected) --}}
                <div x-show="isSelected(key)" x-collapse class="px-4 pb-3 pt-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 whitespace-nowrap">Rentang waktu:</span>
                        <select @change="updatePeriod(key, $event.target.value)"
                                :value="getWidgetPeriod(key)"
                                class="flex-1 text-xs border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:ring-1 focus:ring-indigo-500/20 focus:border-indigo-400">
                            <template x-for="(label, pKey) in periodDefs" :key="pKey">
                                <option :value="pKey" x-text="label" :selected="getWidgetPeriod(key) === pKey"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>
        </template>

        {{-- Selected Preview --}}
        <div x-show="selectedWidgets.length > 0" class="pt-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Akan ditampilkan:</p>
            <div class="flex flex-wrap gap-1.5">
                <template x-for="w in selectedWidgets" :key="w.type">
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-medium rounded-full"
                          :class="{
                              'bg-emerald-100 text-emerald-700': widgetDefs[w.type]?.color === 'emerald',
                              'bg-blue-100 text-blue-700': widgetDefs[w.type]?.color === 'blue',
                              'bg-amber-100 text-amber-700': widgetDefs[w.type]?.color === 'amber',
                              'bg-violet-100 text-violet-700': widgetDefs[w.type]?.color === 'violet',
                              'bg-cyan-100 text-cyan-700': widgetDefs[w.type]?.color === 'cyan',
                              'bg-rose-100 text-rose-700': widgetDefs[w.type]?.color === 'rose',
                              'bg-indigo-100 text-indigo-700': widgetDefs[w.type]?.color === 'indigo',
                          }">
                        <i :class="widgetDefs[w.type]?.icon" class="text-[8px]"></i>
                        <span x-text="widgetDefs[w.type]?.label"></span>
                    </span>
                </template>
            </div>
        </div>
    </div>

    {{-- Hidden JSON Input --}}
    <input type="hidden" name="stat_widgets" :value="JSON.stringify(selectedWidgets)">
</div>

<script>
function statWidgetPicker(initial, widgetDefs, periodDefs) {
    return {
        selectedWidgets: Array.isArray(initial) ? [...initial] : [],
        widgetDefs,
        periodDefs,

        isSelected(key) {
            return this.selectedWidgets.some(w => w.type === key);
        },

        toggleWidget(key) {
            if (this.isSelected(key)) {
                this.selectedWidgets = this.selectedWidgets.filter(w => w.type !== key);
            } else {
                this.selectedWidgets.push({
                    type: key,
                    period: '1_month',
                    title: null,
                });
            }
        },

        getWidgetPeriod(key) {
            const w = this.selectedWidgets.find(w => w.type === key);
            return w ? w.period : '1_month';
        },

        updatePeriod(key, period) {
            const w = this.selectedWidgets.find(w => w.type === key);
            if (w) w.period = period;
        },
    };
}
</script>
