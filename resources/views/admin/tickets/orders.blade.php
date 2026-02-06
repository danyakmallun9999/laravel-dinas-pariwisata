@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pesanan E-Tiket</h1>
        <a href="{{ route('admin.tickets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Tiket
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET" action="{{ route('admin.tickets.orders') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-lg">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Sudah Digunakan</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Pesanan, Nama, Email" class="w-full border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiket</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Kunjungan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $order->ticket->name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->ticket->place->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_email }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->visit_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->quantity }} tiket
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.tickets.orders.updateStatus', $order) }}" method="POST" class="inline">
                                @csrf
                                <select name="status" onchange="this.form.submit()" 
                                        class="text-xs rounded-full px-2 py-1 border-0 
                                        {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status == 'used' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $order->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Dibayar</option>
                                    <option value="used" {{ $order->status == 'used' ? 'selected' : '' }}>Digunakan</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <button onclick="showQR('{{ $order->order_number }}')" class="text-blue-600 hover:text-blue-900" title="Lihat QR Code">
                                <i class="fas fa-qrcode"></i>
                            </button>
                            <form action="{{ route('admin.tickets.orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pesanan {{ $order->order_number }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus Pesanan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Belum ada pesanan tiket
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">QR Code Tiket</h3>
            <button onclick="closeQR()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="qrContent" class="text-center">
            <!-- QR code will be displayed here -->
        </div>
        <div class="mt-4 text-center">
            <button onclick="downloadQR()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-download mr-2"></i>Download QR Code
            </button>
        </div>
    </div>
</div>

<!-- QRCode.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
let currentOrderNumber = '';

function showQR(orderNumber) {
    currentOrderNumber = orderNumber;
    document.getElementById('qrModal').classList.remove('hidden');
    
    // Clear previous QR code
    const qrContent = document.getElementById('qrContent');
    qrContent.innerHTML = `
        <div class="text-sm text-gray-600 mb-4">Order: ${orderNumber}</div>
        <div id="qrcode" class="flex justify-center mb-2"></div>
        <p class="text-xs text-gray-500 mt-2">Scan QR code ini untuk verifikasi tiket</p>
    `;
    
    // Generate QR code
    new QRCode(document.getElementById("qrcode"), {
        text: orderNumber,
        width: 256,
        height: 256,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
}

function closeQR() {
    document.getElementById('qrModal').classList.add('hidden');
}

function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        const url = canvas.toDataURL('image/png');
        const link = document.createElement('a');
        link.download = `ticket-qr-${currentOrderNumber}.png`;
        link.href = url;
        link.click();
    }
}
</script>
@endsection
