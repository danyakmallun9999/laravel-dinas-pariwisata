<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="hidden md:block text-sm text-gray-500">E-Tiket</p>
                <h2 class="font-semibold text-xl md:text-2xl text-gray-800 leading-tight">
                    Riwayat Penjualan
                </h2>
            </div>
            <a href="{{ route('admin.tickets.orders') }}" class="inline-flex items-center gap-2 px-3 py-2 md:px-4 md:py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-sm font-medium text-sm">
                <i class="fa-solid fa-inbox text-xs md:text-sm"></i>
                <span class="hidden md:inline">Pesanan Masuk</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalTransactions) }}</p>
                            <p class="text-xs text-gray-500 font-medium">Total Transaksi</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-ticket-alt"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalQty) }}</p>
                            <p class="text-xs text-gray-500 font-medium">Total Tiket Terjual</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue / 1000, 0) }}k</p>
                            <p class="text-xs text-gray-500 font-medium">Revenue (Total)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('admin.tickets.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                        <select name="status" class="w-full border-gray-200 rounded-xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Semua Status (Paid/Used)</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                            <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Sudah Digunakan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Kunjungan</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full border-gray-200 rounded-xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Pesanan, Nama, Email" class="w-full border-gray-200 rounded-xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">&nbsp;</label>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-colors">
                                <i class="fa-solid fa-search mr-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.tickets.history') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-medium text-sm transition-colors" title="Reset Filter">
                                <i class="fa-solid fa-sync"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- History Table -->
            <div class="bg-white rounded-[2.5rem] border border-gray-200 p-1">
                <div class="rounded-[2rem] border border-gray-100 overflow-hidden bg-white">
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50/50 text-gray-500 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">No. Pesanan</th>
                                    <th class="px-6 py-4">Tiket</th>
                                    <th class="px-6 py-4">Pelanggan</th>
                                    <th class="px-6 py-4">Asal Pengunjung</th>
                                    <th class="px-6 py-4">Tgl Kunjungan</th>
                                    <th class="px-6 py-4">Jumlah</th>
                                    <th class="px-6 py-4">Total</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-blue-50/30 transition-colors group {{ $loop->even ? 'bg-gray-50/30' : 'bg-white' }}">
                                        <td class="px-6 py-4">
                                            <div class="font-mono font-bold text-gray-800 text-xs">{{ $order->order_number }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d M Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $order->ticket->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->ticket->place->name ?? 'Lokasi via Relasi' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->customer_email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ $order->customer_city ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">
                                                @if($order->customer_province)
                                                    {{ $order->customer_province }}
                                                @endif
                                                @if($order->customer_country)
                                                    <span class="text-gray-400">({{ $order->customer_country }})</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ $order->visit_date->format('d M Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 font-medium">
                                            {{ $order->quantity }} <span class="text-gray-400">tiket</span>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-800">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs rounded-lg px-3 py-1.5 font-bold inline-block border
                                                {{ $order->status == 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                                {{ $order->status == 'used' ? 'bg-blue-50 text-blue-700 border-blue-100' : '' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button onclick="showQR('{{ $order->order_number }}')" class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Lihat QR Code">
                                                <i class="fa-solid fa-qrcode"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                                    <i class="fa-solid fa-history text-2xl text-gray-400"></i>
                                                </div>
                                                <p class="text-gray-500 mb-1 font-medium">Belum ada riwayat penjualan</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4 p-4">
                        @forelse ($orders as $order)
                            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                                <div class="flex justify-between items-start mb-3 border-b border-gray-50 pb-3">
                                    <div>
                                        <div class="font-mono font-bold text-gray-800 text-xs bg-gray-50 px-2 py-1 rounded-lg inline-block mb-1">{{ $order->order_number }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                    <span class="text-[10px] rounded-lg px-2 py-1 font-bold inline-block border
                                        {{ $order->status == 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : '' }}
                                        {{ $order->status == 'used' ? 'bg-blue-50 text-blue-700 border-blue-100' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                
                                <div class="flex gap-4 mb-3">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                                        <i class="fa-solid fa-ticket"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-gray-900 text-sm line-clamp-1">{{ $order->ticket->name }}</h3>
                                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                            <i class="fa-regular fa-calendar text-gray-400"></i>
                                            <span>{{ $order->visit_date->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-3 mb-3">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs text-gray-500">Pemesan</span>
                                        <span class="text-xs font-bold text-gray-900 text-right">{{ $order->customer_name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs text-gray-500">Asal</span>
                                        <span class="text-xs font-bold text-gray-900 text-right">
                                            {{ $order->customer_city ?? '-' }}
                                            @if($order->customer_province)
                                                , {{ $order->customer_province }}
                                            @endif
                                            @if($order->customer_country)
                                                <br><span class="text-gray-500 font-normal">({{ $order->customer_country }})</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500">Total</span>
                                        <span class="text-sm font-bold text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end pt-2 border-t border-gray-50">
                                    <button onclick="showQR('{{ $order->order_number }}')" class="w-full flex items-center justify-center gap-2 py-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors font-bold text-xs" title="Lihat QR Code">
                                        <i class="fa-solid fa-qrcode"></i> Lihat QR Code
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-gray-500 text-sm">Belum ada riwayat penjualan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($orders->hasPages())
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Reuse QR Modal from Orders Page -->
    <div id="qrModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">QR Code Tiket</h3>
                <button onclick="closeQR()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div id="qrContent" class="text-center"></div>
            <div class="mt-4 text-center">
                <button onclick="downloadQR()" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-colors">
                    <i class="fa-solid fa-download mr-2"></i>Download QR Code
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
    let currentOrderNumber = '';

    function showQR(orderNumber) {
        currentOrderNumber = orderNumber;
        document.getElementById('qrModal').classList.remove('hidden');
        
        const qrContent = document.getElementById('qrContent');
        qrContent.innerHTML = `
            <div class="text-sm text-gray-500 mb-4 font-mono">Order: ${orderNumber}</div>
            <div id="qrcode" class="flex justify-center mb-2 mx-auto" style="width: 200px; height: 200px; overflow: hidden;"></div>
        `;
        
        const qrContainer = document.getElementById("qrcode");
        
        new QRCode(qrContainer, {
            text: orderNumber,
            width: 800,
            height: 800, 
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        const styleQR = () => {
            const canvas = qrContainer.querySelector('canvas');
            const img = qrContainer.querySelector('img');
            if(canvas) {
                canvas.style.width = '100%';
                canvas.style.height = '100%';
            }
            if(img) {
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.display = 'block';
            }
        };

        styleQR();
        setTimeout(styleQR, 0);
    }

    function closeQR() {
        document.getElementById('qrModal').classList.add('hidden');
    }

    function downloadQR() {
        const sourceCanvas = document.querySelector('#qrcode canvas');
        if (!sourceCanvas) return;

        const padding = 100;
        const size = sourceCanvas.width;
        const newSize = size + (padding * 2);
        
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = newSize;
        finalCanvas.height = newSize;
        const ctx = finalCanvas.getContext('2d');

        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, newSize, newSize);
        ctx.drawImage(sourceCanvas, padding, padding);

        const url = finalCanvas.toDataURL('image/jpeg', 1.0);
        const link = document.createElement('a');
        link.download = `ticket-qr-${currentOrderNumber}.jpg`;
        link.href = url;
        link.click();
    }
    </script>
</x-app-layout>
