@props(['transactions'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Transaksi Terkini</h3>
        <a href="{{ route('admin.tickets.orders') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-semibold uppercase text-xs">
                <tr>
                    <th class="px-6 py-3">Order ID</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Tiket</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs font-bold text-gray-800">{{ $trx->order_number }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $trx->customer_name }}</div>
                        <div class="text-xs text-gray-400">{{ $trx->customer_email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="block text-gray-800 font-medium">{{ $trx->ticket->name }}</span>
                        <span class="text-xs text-gray-500">{{ $trx->quantity }} items</span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs font-bold 
                            {{ $trx->status == 'paid' ? 'bg-green-100 text-green-700' : 
                               ($trx->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                               ($trx->status == 'used' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700')) }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">
                        {{ $trx->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">
                        Belum ada transaksi terbaru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
