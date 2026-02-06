<x-public-layout>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 -mt-20 pt-32">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">E-Tiket Wisata Jepara</h1>
            <p class="text-lg text-gray-600">Pesan tiket wisata secara online dengan mudah dan cepat</p>
        </div>

        <!-- Tickets Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tickets as $ticket)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Destination Image -->
                    @if($ticket->place->image_path)
                        <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $ticket->place->image_path) }}')"></div>
                    @else
                        <div class="h-48 bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-white text-6xl"></i>
                        </div>
                    @endif

                    <div class="p-6">
                        <!-- Place Name -->
                        <div class="text-sm text-blue-600 font-semibold mb-1">{{ $ticket->place->name }}</div>
                        
                        <!-- Ticket Name -->
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $ticket->name }}</h3>
                        
                        <!-- Description -->
                        @if($ticket->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $ticket->description }}</p>
                        @endif

                        <!-- Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar-check w-5"></i>
                                <span>Berlaku {{ $ticket->valid_days }} hari</span>
                            </div>
                            @if($ticket->quota)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-users w-5"></i>
                                    <span>Kuota: {{ number_format($ticket->quota) }}/hari</span>
                                </div>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="text-sm text-gray-500">Harga</div>
                                <div class="text-2xl font-bold text-blue-600">
                                    Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Book Button -->
                        <a href="{{ route('tickets.show', $ticket) }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-3 rounded-lg transition-colors duration-200">
                            <i class="fas fa-shopping-cart mr-2"></i>Pesan Sekarang
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-ticket-alt text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Belum ada tiket yang tersedia saat ini</p>
                </div>
            @endforelse
        </div>

        <!-- My Tickets Link -->
        <div class="mt-12 text-center">
            <a href="{{ route('tickets.my') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold">
                <i class="fas fa-ticket-alt mr-2"></i>
                Lihat Tiket Saya
            </a>
        </div>
    </div>
</div>
</x-public-layout>
