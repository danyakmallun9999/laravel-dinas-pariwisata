<x-public-layout>
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">E-Tiket</a>
                <span>/</span>
                <span class="text-gray-800 dark:text-gray-200 font-medium">{{ $ticket->name }}</span>
            </nav>

            @php
                $imagePath = $ticket->place->image_path ?? '';
                $imageUrl = '';
                if ($imagePath) {
                    if (str_starts_with($imagePath, 'http')) {
                        $imageUrl = $imagePath;
                    } elseif (str_starts_with($imagePath, 'images/')) {
                        $imageUrl = asset($imagePath);
                    } else {
                        $imageUrl = asset('storage/' . $imagePath);
                    }
                }
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left: Ticket Details (3 cols) -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Hero Image Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-sm border border-slate-100 dark:border-slate-700">
                        @if($imageUrl)
                            <div class="relative h-72 md:h-96 overflow-hidden">
                                <img src="{{ $imageUrl }}" alt="{{ $ticket->place->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent to-transparent"></div>
                                
                                <!-- Price Badge -->
                                <div class="absolute bottom-6 left-6">
                                    <span class="px-5 py-2.5 rounded-2xl bg-primary text-white font-bold text-2xl shadow-xl">
                                        Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <!-- Valid Days Badge -->
                                <div class="absolute top-6 right-6 px-4 py-2 rounded-xl bg-white/95 dark:bg-slate-900/95 backdrop-blur text-sm font-bold text-slate-700 dark:text-white shadow-lg border border-white/20">
                                    <i class="fa-solid fa-calendar-check mr-2 text-primary"></i>
                                    Berlaku {{ $ticket->valid_days }} hari
                                </div>
                            </div>
                        @else
                            <div class="h-72 md:h-96 bg-gradient-to-br from-primary/20 to-indigo-500/20 flex items-center justify-center">
                                <i class="fa-solid fa-ticket text-6xl text-primary opacity-50"></i>
                            </div>
                        @endif

                        <div class="p-6 md:p-8">
                            <!-- Place Name -->
                            <div class="text-xs font-bold uppercase tracking-wider text-primary mb-2">{{ $ticket->place->name }}</div>
                            
                            <!-- Ticket Name -->
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-4">{{ $ticket->name }}</h1>

                            <!-- Description -->
                            @if($ticket->description)
                                <div class="prose prose-slate dark:prose-invert max-w-none mb-6">
                                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $ticket->description }}</p>
                                </div>
                            @endif

                            <!-- Info Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 text-center">
                                    <i class="fa-solid fa-money-bill-wave text-2xl text-primary mb-2"></i>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Harga</div>
                                    <div class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($ticket->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 text-center">
                                    <i class="fa-solid fa-calendar-days text-2xl text-primary mb-2"></i>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Masa Berlaku</div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $ticket->valid_days }} Hari</div>
                                </div>
                                @if($ticket->quota)
                                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 text-center">
                                        <i class="fa-solid fa-users text-2xl text-primary mb-2"></i>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kuota</div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ number_format($ticket->quota) }}/hari</div>
                                    </div>
                                @else
                                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 text-center">
                                        <i class="fa-solid fa-infinity text-2xl text-green-500 mb-2"></i>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kuota</div>
                                        <div class="font-bold text-green-600">Unlimited</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Terms & Conditions -->
                            @if($ticket->terms_conditions)
                                <div class="border-t border-slate-100 dark:border-slate-700 pt-6">
                                    <h4 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-file-contract text-primary"></i>
                                        Syarat & Ketentuan
                                    </h4>
                                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-line bg-slate-50 dark:bg-slate-700/30 rounded-xl p-4">{{ $ticket->terms_conditions }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Form (2 cols) -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 sticky top-28">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-list text-primary"></i>
                            Form Pemesanan
                        </h2>

                        @if($errors->any())
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl mb-6">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('tickets.book') }}" method="POST" id="bookingForm" x-data="{ quantity: {{ old('quantity', 1) }} }">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                            <div class="space-y-5">
                                <!-- Customer Name -->
                                <div>
                                    <label for="customer_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-user mr-1 text-primary"></i> Nama Lengkap
                                    </label>
                                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400"
                                           placeholder="Masukkan nama lengkap">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="customer_email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-envelope mr-1 text-primary"></i> Email
                                    </label>
                                    <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}" required
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400"
                                           placeholder="email@example.com">
                                    <p class="text-xs text-slate-500 mt-1.5"><i class="fa-solid fa-info-circle mr-1"></i>Tiket akan dikirim ke email ini</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="customer_phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-phone mr-1 text-primary"></i> No. Telepon
                                    </label>
                                    <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400"
                                           placeholder="08xxxxxxxxxx">
                                </div>

                                <!-- Visit Date (Custom Dropdown) -->
                                <div x-data="{ 
                                    open: false, 
                                    selectedDate: '{{ old('visit_date', '') }}',
                                    selectedLabel: '',
                                    dates: [],
                                    init() {
                                        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                        for (let i = 0; i < 30; i++) {
                                            const d = new Date();
                                            d.setDate(d.getDate() + i);
                                            const value = d.toISOString().split('T')[0];
                                            const dayName = days[d.getDay()];
                                            const date = d.getDate();
                                            const month = months[d.getMonth()];
                                            const year = d.getFullYear();
                                            const label = `${dayName}, ${date} ${month} ${year}`;
                                            const shortLabel = `${date} ${month} ${year}`;
                                            this.dates.push({ value, label, shortLabel, isWeekend: d.getDay() === 0 || d.getDay() === 6 });
                                            if (this.selectedDate === value) {
                                                this.selectedLabel = shortLabel;
                                            }
                                        }
                                        if (!this.selectedLabel && this.dates.length > 0) {
                                            this.selectedLabel = '';
                                        }
                                    },
                                    selectDate(date) {
                                        this.selectedDate = date.value;
                                        this.selectedLabel = date.shortLabel;
                                        this.open = false;
                                    }
                                }" class="relative">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-calendar mr-1 text-primary"></i> Tanggal Kunjungan
                                    </label>
                                    <input type="hidden" name="visit_date" :value="selectedDate" required>
                                    
                                    <button 
                                        type="button"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        class="w-full px-4 py-3 text-left rounded-xl bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all flex items-center justify-between"
                                    >
                                        <span x-text="selectedLabel || 'Pilih tanggal kunjungan'" :class="selectedLabel ? '' : 'text-slate-400'"></span>
                                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>

                                    <div 
                                        x-show="open" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="transform opacity-0 translate-y-2"
                                        x-transition:enter-end="transform opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="transform opacity-100 translate-y-0"
                                        x-transition:leave-end="transform opacity-0 translate-y-2"
                                        class="absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 max-h-72 overflow-y-auto no-scrollbar p-1.5"
                                        style="display: none;"
                                    >
                                        <template x-for="date in dates" :key="date.value">
                                            <button 
                                                type="button"
                                                @click="selectDate(date)"
                                                class="w-full text-left px-4 py-3 rounded-xl text-sm transition-all flex items-center justify-between group"
                                                :class="selectedDate === date.value ? 'bg-primary/10 text-primary font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5'"
                                            >
                                                <span class="flex items-center gap-3">
                                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold"
                                                          :class="date.isWeekend ? 'bg-red-100 dark:bg-red-900/30 text-red-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'"
                                                          x-text="date.label.split(',')[0].substring(0, 3)"></span>
                                                    <span x-text="date.label.split(', ')[1]"></span>
                                                </span>
                                                <i class="fa-solid fa-check text-primary" x-show="selectedDate === date.value"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label for="quantity" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-hashtag mr-1 text-primary"></i> Jumlah Tiket
                                    </label>
                                    <input type="number" name="quantity" id="quantity" x-model="quantity" required
                                           min="1" max="10"
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all">
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-sticky-note mr-1 text-primary"></i> Catatan <span class="text-slate-400 font-normal">(Opsional)</span>
                                    </label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400 resize-none"
                                              placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                                </div>

                                <!-- Total Price -->
                                <div class="bg-gradient-to-r from-primary/10 to-indigo-500/10 rounded-2xl p-5 border border-primary/20">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-slate-700 dark:text-slate-300">Total Pembayaran</span>
                                        <span class="text-2xl font-bold text-primary" x-text="'Rp ' + ({{ $ticket->price }} * quantity).toLocaleString('id-ID')">
                                            Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-check-circle"></i>
                                    Pesan Tiket Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
