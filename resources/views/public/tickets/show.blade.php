<x-public-layout :hideFooter="true">
    <div class="bg-gray-50 dark:bg-background-dark min-h-screen -mt-20 pt-32 pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex text-xs md:text-sm text-gray-400 mb-6 space-x-2">
                <a href="{{ route('welcome') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Home') }}</a>
                <span>/</span>
                <a href="{{ route('tickets.index') }}" class="hover:text-primary transition-colors">{{ __('Tickets.Breadcrumb.Index') }}</a>
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
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="px-5 py-2.5 rounded-2xl bg-primary text-white font-bold text-2xl shadow-xl">
                                            Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                        </span>
                                        @if($ticket->price_weekend)
                                            <span class="px-3 py-1 rounded-lg bg-rose-500 text-white text-xs font-bold shadow-lg">
                                                {{ __('Tickets.Show.WeekendPrice') }}: Rp {{ number_format($ticket->price_weekend, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Valid Days Badge -->
                                <div class="absolute top-6 right-6 px-4 py-2 rounded-xl bg-white/95 dark:bg-slate-900/95 backdrop-blur text-sm font-bold text-slate-700 dark:text-white shadow-lg border border-white/20">
                                    <i class="fa-solid fa-calendar-check mr-2 text-primary"></i>
                                    {{ __('Tickets.Show.ValidDays', ['days' => $ticket->valid_days]) }}
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
                                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ __('Tickets.Show.Info.Price') }}</div>
                                    <div class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($ticket->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 text-center">
                                    <i class="fa-solid fa-calendar-days text-2xl text-primary mb-2"></i>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ __('Tickets.Show.Info.ValidPeriod') }}</div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $ticket->valid_days }} {{ __('Tickets.Card.Day') }}</div>
                                </div>
                                @if($ticket->quota)
                                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 text-center">
                                        <i class="fa-solid fa-users text-2xl text-primary mb-2"></i>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ __('Tickets.Show.Info.Quota') }}</div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ number_format($ticket->quota) }}/{{ __('Tickets.Card.Day') }}</div>
                                    </div>
                                @else
                                    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-4 text-center">
                                        <i class="fa-solid fa-infinity text-2xl text-green-500 mb-2"></i>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ __('Tickets.Show.Info.Quota') }}</div>
                                        <div class="font-bold text-green-600">{{ __('Tickets.Card.Unlimited') }}</div>
                                    </div>
                                @endif
                            </div>

                            <!-- Terms & Conditions -->
                            @if($ticket->terms_conditions)
                                <div class="border-t border-slate-100 dark:border-slate-700 pt-6">
                                    <h4 class="font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-file-contract text-primary"></i>
                                        {{ __('Tickets.Show.Terms') }}
                                    </h4>
                                    <div class="text-sm text-slate-600 dark:text-slate-400 whitespace-pre-line bg-slate-50 dark:bg-slate-700/30 rounded-xl p-4">{{ $ticket->terms_conditions }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Form (2 cols) -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 md:p-8 sticky top-28 overflow-hidden">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-list text-primary"></i>
                            {{ __('Tickets.Form.Title') }}
                        </h2>

                        @guest('web')
                        {{-- Login overlay for unauthenticated users --}}
                        <div class="absolute inset-0 z-20 flex items-center justify-center" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(2px);">
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 mx-4 max-w-sm w-full text-center">
                                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-lock text-primary text-xl"></i>
                                </div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-lg mb-2">Login Diperlukan</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                                    Masuk dengan akun Google untuk memesan tiket dan mengakses e-tiket Anda.
                                </p>
                                <a href="{{ route('auth.google') }}"
                                   onclick="sessionStorage.setItem('intended_url', window.location.href)"
                                   class="w-full flex items-center justify-center gap-3 px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/50 hover:border-primary/50 hover:bg-slate-50 text-slate-700 dark:text-slate-200 font-semibold transition-all duration-200">
                                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    Masuk dengan Google
                                </a>
                                <p class="text-[11px] text-slate-400 mt-3">
                                    <i class="fa-solid fa-shield-halved mr-1"></i>
                                    Data Anda aman & tidak dibagikan
                                </p>
                            </div>
                        </div>
                        @endguest

                        @if($errors->any())
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl mb-6">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('tickets.book') }}" method="POST" id="bookingForm" 
                            x-data="{ 
                                quantity: {{ old('quantity', 1) }},
                                open: false, 
                                selectedDate: '{{ old('visit_date', '') }}',
                                selectedLabel: '',
                                dates: [],
                                priceWeekday: {{ $ticket->price }},
                                priceWeekend: {{ $ticket->price_weekend ?? $ticket->price }},
                                currentPrice: {{ $ticket->price }},
                                isWeekendSelected: false,
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
                                            this.isWeekendSelected = (d.getDay() === 0 || d.getDay() === 6);
                                            this.currentPrice = this.isWeekendSelected ? this.priceWeekend : this.priceWeekday;
                                        }
                                    }
                                },
                                selectDate(date) {
                                    this.selectedDate = date.value;
                                    this.selectedLabel = date.shortLabel;
                                    this.isWeekendSelected = date.isWeekend;
                                    this.currentPrice = date.isWeekend ? this.priceWeekend : this.priceWeekday;
                                    this.open = false;
                                }
                            }">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                            <div class="space-y-5">
                                <!-- Customer Name -->
                                <div>
                                    <label for="customer_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-user mr-1 text-primary"></i> {{ __('Tickets.Form.Name') }}
                                    </label>
                                    <input type="text" name="customer_name" id="customer_name" 
                                           value="{{ old('customer_name', auth('web')->check() ? auth('web')->user()->name : '') }}" 
                                           required
                                           @guest('web') disabled @endguest
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400 disabled:opacity-50"
                                           placeholder="{{ __('Tickets.Form.NamePlaceholder') }}">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="customer_email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-envelope mr-1 text-primary"></i> {{ __('Tickets.Form.Email') }}
                                    </label>
                                    <input type="email" name="customer_email" id="customer_email" 
                                           value="{{ old('customer_email', auth('web')->check() ? auth('web')->user()->email : '') }}" 
                                           required
                                           @auth('web') readonly @endauth
                                           @guest('web') disabled @endguest
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400 disabled:opacity-50 read-only:bg-slate-100 dark:read-only:bg-slate-700 read-only:cursor-not-allowed"
                                           placeholder="{{ __('Tickets.Form.EmailPlaceholder') }}">
                                    @auth('web')
                                    <p class="text-xs text-slate-500 mt-1.5"><i class="fa-solid fa-check-circle mr-1 text-emerald-500"></i>Email terverifikasi dari akun Google Anda</p>
                                    @else
                                    <p class="text-xs text-slate-500 mt-1.5"><i class="fa-solid fa-info-circle mr-1"></i>{{ __('Tickets.Form.EmailInfo') }}</p>
                                    @endauth
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="customer_phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-phone mr-1 text-primary"></i> {{ __('Tickets.Form.Phone') }}
                                    </label>
                                    <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                                           @guest('web') disabled @endguest
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400 disabled:opacity-50"
                                           placeholder="{{ __('Tickets.Form.PhonePlaceholder') }}">
                                </div>

                                <!-- Origin Fields -->
                                <div class="space-y-4" x-data="{
                                    country: '{{ old('customer_country', 'Indonesia') }}',
                                    province: '{{ old('customer_province', '') }}',
                                    city: '{{ old('customer_city', '') }}',
                                    otherCity: '',
                                    countries: ['Indonesia', 'Malaysia', 'Singapura', 'Thailand', 'Filipina', 'Australia', 'Amerika Serikat', 'Inggris', 'Jepang', 'Korea Selatan', 'China', 'Lainnya'],
                                    provinces: [],
                                    cities: [],
                                    isLoadingProvinces: false,
                                    isLoadingCities: false,
                                    
                                    async init() {
                                        if (this.country === 'Indonesia') {
                                            await this.fetchProvinces();
                                            if (this.province) {
                                                await this.fetchCities(this.province);
                                            }
                                        }
                                    },
                                    
                                    async fetchProvinces() {
                                        this.isLoadingProvinces = true;
                                        try {
                                            const response = await fetch('/api/locations/provinces');
                                            this.provinces = await response.json();
                                        } catch (e) {
                                            console.error('Gagal mengambil data provinsi:', e);
                                        } finally {
                                            this.isLoadingProvinces = false;
                                        }
                                    },
                                    
                                    async fetchCities(provinceId) {
                                        if (!provinceId) {
                                            this.cities = [];
                                            return;
                                        }
                                        this.isLoadingCities = true;
                                        try {
                                            const response = await fetch(`/api/locations/cities?province_id=${provinceId}`);
                                            this.cities = await response.json();
                                        } catch (e) {
                                            console.error('Gagal mengambil data kota:', e);
                                        } finally {
                                            this.isLoadingCities = false;
                                        }
                                    },
                                    
                                    handleCountryChange() {
                                        if (this.country === 'Indonesia') {
                                            this.fetchProvinces();
                                        } else {
                                            this.province = '';
                                            this.city = '';
                                        }
                                    },
                                    
                                    handleProvinceChange() {
                                        this.city = '';
                                        this.fetchCities(this.province);
                                    }
                                }">
                                    <div>
                                        <label for="customer_country" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                            <i class="fa-solid fa-globe mr-1 text-primary"></i> Negara Asal
                                        </label>
                                        <select name="customer_country" id="customer_country" x-model="country" @change="handleCountryChange" required
                                                @guest('web') disabled @endguest
                                                class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all disabled:opacity-50">
                                            <template x-for="c in countries" :key="c">
                                                <option :value="c" x-text="c" :selected="country === c"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="country === 'Indonesia'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2">
                                        <div>
                                            <label for="customer_province" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                                <i class="fa-solid fa-map mr-1 text-primary"></i> Provinsi
                                                <i class="fa-solid fa-circle-notch fa-spin ml-2 text-primary" x-show="isLoadingProvinces" style="display: none;"></i>
                                            </label>
                                            <select id="customer_province" x-model="province" @change="handleProvinceChange"
                                                    :required="country === 'Indonesia'"
                                                    @guest('web') disabled @endguest
                                                    class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all disabled:opacity-50">
                                                <option value="">Pilih Provinsi</option>
                                                <template x-for="p in provinces" :key="p.id">
                                                    <option :value="p.id" x-text="p.name" :selected="province == p.id"></option>
                                                </template>
                                            </select>
                                            <!-- Send Name instead of ID -->
                                            <input type="hidden" name="customer_province" :value="province ? (provinces.find(p => p.id == province)?.name || '') : ''">
                                        </div>
                                        <div>
                                            <label for="customer_city_select" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                                <i class="fa-solid fa-city mr-1 text-primary"></i> Kabupaten/Kota
                                                <i class="fa-solid fa-circle-notch fa-spin ml-2 text-primary" x-show="isLoadingCities" style="display: none;"></i>
                                            </label>
                                            <div class="space-y-2">
                                                <select id="customer_city_select" x-model="city"
                                                        :required="country === 'Indonesia'"
                                                        @guest('web') disabled @endguest
                                                        class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all disabled:opacity-50">
                                                    <option value="">Pilih Kota</option>
                                                    <template x-for="ct in cities" :key="ct.id">
                                                        <option :value="ct.name" x-text="ct.name" :selected="city === ct.name"></option>
                                                    </template>
                                                    <option value="Lainnya">Lainnya...</option>
                                                </select>
                                                
                                                <!-- Value sent to server -->
                                                <input type="hidden" name="customer_city" :value="city === 'Lainnya' ? otherCity : city">
                                                
                                                <input type="text" x-show="city === 'Lainnya'" x-model="otherCity"
                                                       placeholder="Masukkan nama kota"
                                                       class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400 mt-2">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Visit Date (Custom Dropdown) -->
                                <div class="relative">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-calendar mr-1 text-primary"></i> {{ __('Tickets.Form.Date') }}
                                    </label>
                                    <input type="hidden" name="visit_date" :value="selectedDate" required>
                                    
                                    <button 
                                        type="button"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        @guest('web') disabled @endguest
                                        class="w-full px-4 py-3 text-left rounded-xl bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all flex items-center justify-between disabled:opacity-50"
                                    >
                                        <span x-text="selectedLabel || '{{ __('Tickets.Form.SelectDate') }}'" :class="selectedLabel ? '' : 'text-slate-400'"></span>
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
                                        <i class="fa-solid fa-hashtag mr-1 text-primary"></i> {{ __('Tickets.Form.Quantity') }}
                                    </label>
                                    <input type="number" name="quantity" id="quantity" x-model="quantity" required
                                           min="1" max="10"
                                           @guest('web') disabled @endguest
                                           class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all disabled:opacity-50">
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        <i class="fa-solid fa-sticky-note mr-1 text-primary"></i> {{ __('Tickets.Form.Notes') }} <span class="text-slate-400 font-normal">{{ __('Tickets.Form.NotesOptional') }}</span>
                                    </label>
                                    <textarea name="notes" id="notes" rows="3"
                                              @guest('web') disabled @endguest
                                              class="w-full px-4 py-3 rounded-xl border-none bg-slate-50 dark:bg-slate-700/50 ring-1 ring-slate-200 dark:ring-slate-600 focus:ring-2 focus:ring-primary text-slate-900 dark:text-white font-medium transition-all placeholder:text-slate-400 resize-none disabled:opacity-50"
                                              placeholder="{{ __('Tickets.Form.NotesPlaceholder') }}">{{ old('notes') }}</textarea>
                                </div>

                                <!-- Total Price -->
                                <div class="bg-gradient-to-r from-primary/10 to-indigo-500/10 rounded-2xl p-5 border border-primary/20">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('Tickets.Form.TotalPayment') }}</span>
                                        <span class="text-lg font-bold text-primary" x-text="'Rp ' + (currentPrice * quantity).toLocaleString('id-ID')">
                                            Rp {{ number_format($ticket->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                @auth('web')
                                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-check-circle"></i>
                                    {{ __('Tickets.Form.SubmitButton') }}
                                </button>
                                @else
                                <div class="w-full bg-slate-200 dark:bg-slate-700 text-slate-400 font-bold py-4 rounded-2xl text-center cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                    Login untuk Memesan
                                </div>
                                @endauth
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
