<x-public-layout>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 -mt-20 pt-32">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left: Ticket Details -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-700 mb-4 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Tiket
                </a>

                <h2 class="text-2xl font-bold text-gray-800 mb-4">Detail Tiket</h2>

                <!-- Destination Info -->
                <div class="mb-6">
                    <div class="text-sm text-blue-600 font-semibold mb-1">{{ $ticket->place->name }}</div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $ticket->name }}</h3>
                </div>

                @if($ticket->place->image_path)
                    <img src="{{ asset('storage/' . $ticket->place->image_path) }}" alt="{{ $ticket->place->name }}" class="w-full h-64 object-cover rounded-lg mb-6">
                @endif

                <!-- Description -->
                @if($ticket->description)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-800 mb-2">Deskripsi</h4>
                        <p class="text-gray-600">{{ $ticket->description }}</p>
                    </div>
                @endif

                <!-- Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-money-bill-wave w-6 text-blue-600"></i>
                        <span class="font-semibold mr-2">Harga:</span>
                        <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center text-gray-700">
                        <i class="fas fa-calendar-check w-6 text-blue-600"></i>
                        <span class="font-semibold mr-2">Masa Berlaku:</span>
                        <span>{{ $ticket->valid_days }} hari sejak tanggal kunjungan</span>
                    </div>
                    @if($ticket->quota)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-users w-6 text-blue-600"></i>
                            <span class="font-semibold mr-2">Kuota:</span>
                            <span>{{ number_format($ticket->quota) }} tiket per hari</span>
                        </div>
                    @endif
                </div>

                <!-- Terms & Conditions -->
                @if($ticket->terms_conditions)
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Syarat & Ketentuan</h4>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $ticket->terms_conditions }}</p>
                    </div>
                @endif
            </div>

            <!-- Right: Booking Form -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Form Pemesanan</h2>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('tickets.book') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                    <div class="space-y-4">
                        <!-- Customer Name -->
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="Masukkan nama lengkap">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="email@example.com">
                            <p class="text-xs text-gray-500 mt-1">Tiket akan dikirim ke email ini</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon *</label>
                            <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   placeholder="08xxxxxxxxxx">
                        </div>

                        <!-- Visit Date -->
                        <div>
                            <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan *</label>
                            <input type="date" name="visit_date" id="visit_date" value="{{ old('visit_date') }}" required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tiket *</label>
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" required
                                   min="1" max="10"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                   oninput="calculateTotal()">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                      placeholder="Catatan tambahan">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Total Price -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-700">Total Pembayaran:</span>
                                <span id="totalPrice" class="text-2xl font-bold text-blue-600">
                                    Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200">
                            <i class="fas fa-check-circle mr-2"></i>Pesan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const ticketPrice = {{ $ticket->price }};

function calculateTotal() {
    const quantity = document.getElementById('quantity').value || 1;
    const total = ticketPrice * quantity;
    document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
</x-public-layout>
