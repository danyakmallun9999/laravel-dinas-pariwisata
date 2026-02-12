<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 h-full">
    {{-- Revenue Chart --}}
    <div class="bg-white rounded-[2rem] border border-gray-200 p-6 flex flex-col justify-between h-full">
        <div>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center border-4 border-blue-50">
                        <i class="fa-solid fa-chart-line text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Pendapatan</h3>
                        <p class="text-xs text-gray-500" id="revenuePeriodLabel">30 hari terakhir</p>
                    </div>
                </div>
                <div x-data="{ active: '1B' }" class="flex bg-gray-100 rounded-lg p-0.5">
                    <template x-for="p in ['1H', '1M', '1B', '1T']" :key="p">
                        <button @click="active = p; window.filterFinancialRevenue(p)"
                                :class="active === p ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                class="px-2.5 py-1.5 text-[11px] font-semibold rounded-md transition-all duration-200"
                                x-text="p">
                        </button>
                    </template>
                </div>
            </div>
            <div id="revenueChart" class="min-h-[200px]"></div>
        </div>
        
        {{-- Revenue Stats --}}
        <div class="grid grid-cols-4 gap-2 mt-4 pt-4 border-t border-gray-100" id="revenueStats">
            <div class="text-center">
                <div class="text-xs font-bold text-gray-800 truncate" id="revTotal">-</div>
                <div class="text-[9px] text-gray-500">Total</div>
            </div>
            <div class="text-center">
                <div class="text-xs font-bold text-gray-800 truncate" id="revAvg">-</div>
                <div class="text-[9px] text-gray-500">Avg/Hari</div>
            </div>
            <div class="text-center">
                <div class="text-xs font-bold text-gray-800 truncate" id="revTx">-</div>
                <div class="text-[9px] text-gray-500">Transaksi</div>
            </div>
            <div class="text-center">
                <div class="text-xs font-bold text-emerald-600 truncate" id="revMax">-</div>
                <div class="text-[9px] text-gray-500">Tertinggi</div>
            </div>
        </div>
    </div>

    {{-- Transaction Chart --}}
    <div class="bg-white rounded-[2rem] border border-gray-200 p-6 flex flex-col justify-between h-full">
        <div>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center border-4 border-emerald-50">
                        <i class="fa-solid fa-ticket text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Transaksi</h3>
                        <p class="text-xs text-gray-500" id="ticketPeriodLabel">30 hari terakhir</p>
                    </div>
                </div>
                <div x-data="{ active: '1B' }" class="flex bg-gray-100 rounded-lg p-0.5">
                    <template x-for="p in ['1H', '1M', '1B', '1T']" :key="p">
                        <button @click="active = p; window.filterFinancialTickets(p)"
                                :class="active === p ? 'bg-white shadow-sm text-emerald-600' : 'text-gray-500 hover:text-gray-700'"
                                class="px-2.5 py-1.5 text-[11px] font-semibold rounded-md transition-all duration-200"
                                x-text="p">
                        </button>
                    </template>
                </div>
            </div>
            <div id="ticketChart" class="min-h-[200px]"></div>
        </div>

        {{-- Ticket Stats --}}
        <div class="grid grid-cols-4 gap-2 mt-4 pt-4 border-t border-gray-100" id="ticketStats">
            <div class="text-center">
                <div class="text-xs font-bold text-gray-800 truncate" id="txTotal">-</div>
                <div class="text-[9px] text-gray-500">Total Tiket</div>
            </div>
            <div class="text-center">
                <div class="text-xs font-bold text-gray-800 truncate" id="txAvg">-</div>
                <div class="text-[9px] text-gray-500">Avg/Hari</div>
            </div>
            <div class="text-center">
                <div class="text-xs font-bold text-gray-800 truncate" id="txDays">-</div>
                <div class="text-[9px] text-gray-500">Hari Aktif</div>
            </div>
            <div class="text-center">
                <div class="text-xs font-bold text-emerald-600 truncate" id="txMax">-</div>
                <div class="text-[9px] text-gray-500">Tertinggi</div>
            </div>
        </div>
    </div>
</div>
