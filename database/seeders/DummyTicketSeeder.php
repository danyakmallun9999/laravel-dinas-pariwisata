<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Place;
use App\Models\Ticket;
use App\Models\TicketOrder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyTicketSeeder extends Seeder
{
    /* ──────────────────────────────────────────────────────────────
     |  Configuration — tweak these to control data volume
     |──────────────────────────────────────────────────────────── */

    /** How many months of historical data to generate */
    private int $months = 8;

    /** Average orders per "active" day (will be randomised) */
    private int $avgOrdersPerDay = 6;

    /* ────────────────────────────────────────────────────────── */

    public function run(): void
    {
        $this->command->info('🔄 Seeding dummy ticket ecosystem…');
        $this->command->newLine();

        // ── 1. Categories (Load Existing) ──
        $categories = $this->seedCategories();

        // ── 2. Destinations (Places) ──
        $places = $this->seedPlaces();

        // ── 3. Tickets ──
        $tickets = $this->seedTickets($places);

        // ── 4. Orders / Transactions ──
        $orderCount = $this->seedOrders($tickets);

        // ── Summary ──
        $this->command->newLine();
        $this->command->info('✅ Dummy data seed completed:');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Categories',  Category::count()],
                ['Places',      Place::count()],
                ['Tickets',     Ticket::count()],
                ['Orders',      TicketOrder::count()],
                ['Paid Orders', TicketOrder::where('status', 'paid')->count()],
                ['Date Range',  Carbon::today()->subMonths($this->months)->format('d M Y').' → '.Carbon::today()->format('d M Y')],
            ]
        );
    }

    /* ================================================================
     |  1. Categories
     |================================================================ */

    private function seedCategories()
    {
        // Hanya ambil kategori yang sudah ada di database (dari CategorySeeder)
        $categories = Category::all()->keyBy('slug');
        
        $this->command->info('   📁 Categories loaded: '.$categories->count());

        return $categories;
    }

    /* ================================================================
     |  2. Destinations (Places)
     |================================================================ */

    private function seedPlaces(): \Illuminate\Support\Collection
    {
        // 1. Panggil PariwisataSeeder jika belum ada data tempat
        if (Place::count() === 0) {
            $this->call(PariwisataSeeder::class);
        }

        // 2. Ambil semua tempat yang tersedia
        $places = Place::all();
        $this->command->info('   📍 Places loaded: '.$places->count());

        return $places;
    }

    /* ================================================================
     |  3. Tickets
     |================================================================ */

    private function seedTickets($places): \Illuminate\Support\Collection
    {
        // Ticket types matching the system: general, adult, child, foreigner
        $templates = [
            ['name' => 'Tiket Masuk Umum',        'type' => 'general',   'price' => 10000, 'pw' => 15000, 'quota' => 500],
            ['name' => 'Tiket Masuk Dewasa',       'type' => 'adult',     'price' => 15000, 'pw' => 20000, 'quota' => 500],
            ['name' => 'Tiket Masuk Anak-anak',    'type' => 'child',     'price' => 7500,  'pw' => 10000, 'quota' => 300],
            ['name' => 'Tiket Masuk Mancanegara',  'type' => 'foreigner', 'price' => 25000, 'pw' => 30000, 'quota' => 200],
        ];

        foreach ($places as $place) {
            // Each place gets 3–4 ticket types
            $subset = collect($templates)->shuffle()->take(rand(3, 4));
            foreach ($subset as $t) {
                Ticket::firstOrCreate(
                    ['name' => $t['name'], 'place_id' => $place->id],
                    [
                        'place_id' => $place->id,
                        'name' => $t['name'],
                        'type' => $t['type'],
                        'price' => $t['price'],
                        'price_weekend' => $t['pw'],
                        'quota' => $t['quota'],
                        'description' => $t['name'].' untuk '.$place->name,
                        'terms_conditions' => 'Syarat dan ketentuan berlaku. Tiket tidak dapat ditukar.',
                        'is_active' => true,
                    ]
                );
            }
        }

        $tickets = Ticket::with('place')->get();
        $this->command->info('   🎫 Tickets: '.$tickets->count());

        return $tickets;
    }

    /* ================================================================
     |  4. Orders / Transactions
     |================================================================ */

    private function seedOrders($tickets): int
    {
        $this->command->info('   🔁 Generating orders (this may take a moment)…');

        $paymentMethods = ['transfer', 'cash', 'qris', 'ovo', 'gopay', 'dana', 'shopeepay'];
        $paymentWeights = [25,          20,      20,     12,    10,      8,      5]; // popularity weights
        $statuses = ['paid', 'paid', 'paid', 'paid', 'used', 'pending', 'cancelled']; // 4/7 paid → ~57 %
        $customerCities = ['Jepara', 'Semarang', 'Kudus', 'Surabaya', 'Jakarta', 'Yogyakarta', 'Solo', 'Pati', 'Demak', 'Pekalongan', 'Bandung', 'Malang'];

        $startDate = Carbon::today()->subMonths($this->months);
        $endDate = Carbon::today();
        $period = CarbonPeriod::create($startDate, $endDate);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $orderCount = 0;
        $batch = [];
        $batchSize = 100;

        foreach ($period as $date) {
            // ── Realistic daily variance ──
            // Weekends get 1.8–2.5× more orders
            $isWeekend = $date->isWeekend();
            $baseOrders = $this->avgOrdersPerDay;

            // Holiday seasons boost (June–July, Dec–Jan, Lebaran approx)
            $month = (int) $date->format('m');
            $seasonMultiplier = 1.0;
            if (in_array($month, [6, 7, 12])) {
                $seasonMultiplier = 1.6;
            } // school holiday / year-end
            if ($month === 1) {
                $seasonMultiplier = 1.3;
            }

            // Weekend multiplier
            $weekendMultiplier = $isWeekend ? rand(180, 250) / 100 : 1.0;

            // Random daily variance (some days are slow, some are booming)
            $variance = rand(30, 170) / 100; // 0.3× to 1.7×

            $dailyOrders = max(0, (int) round($baseOrders * $seasonMultiplier * $weekendMultiplier * $variance));

            // ~15 % chance of zero-order day (realism: rainy day, Tuesday lull, etc.)
            if (rand(1, 100) <= 15 && ! $isWeekend) {
                $dailyOrders = 0;
            }

            // Make sure today always has some data for dashboard
            if ($date->isToday()) {
                $dailyOrders = max($dailyOrders, rand(8, 15));
            }

            for ($i = 0; $i < $dailyOrders; $i++) {
                $ticket = $tickets->random();
                $quantity = $this->weightedRandom([1 => 45, 2 => 30, 3 => 12, 4 => 8, 5 => 5]);
                $status = $statuses[array_rand($statuses)];

                // Determine price
                $unitPrice = $isWeekend && $ticket->price_weekend ? $ticket->price_weekend : $ticket->price;
                $totalPrice = $unitPrice * $quantity;

                // Time within the day (08:00–21:00)
                $hour = rand(8, 21);
                $minute = rand(0, 59);
                $createdAt = $date->copy()->setHour($hour)->setMinute($minute)->setSecond(rand(0, 59));

                // Paid at (for paid/used orders, 5–90 min after creation)
                $payedAt = null;
                $checkInTime = null;

                if (in_array($status, ['paid', 'used'])) {
                    $payedAt = $createdAt->copy()->addMinutes(rand(5, 90));

                    if ($status === 'used') {
                        // Check-in on visit date, morning
                        $checkInTime = $date->copy()
                            ->addDays(rand(0, 2))
                            ->setHour(rand(8, 14))
                            ->setMinute(rand(0, 59));
                    }
                }

                // Visit date: same day or up to 7 days later
                $visitDate = $date->copy()->addDays(rand(0, 7));

                // Payment method (weighted random)
                $pmIndex = $this->weightedRandomIndex($paymentWeights);
                $paymentMethod = $paymentMethods[$pmIndex];

                $orderNumber = 'TKT-'.$createdAt->format('Ymd').'-'.strtoupper(Str::random(6));

                $batch[] = [
                    'ticket_id' => $ticket->id,
                    'order_number' => $orderNumber,
                    'customer_name' => fake('id_ID')->name(),
                    'customer_email' => fake('id_ID')->safeEmail(),
                    'customer_phone' => fake('id_ID')->phoneNumber(),
                    'customer_city' => $customerCities[array_rand($customerCities)],
                    'visit_date' => $visitDate->toDateString(),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'tax_amount' => 0,
                    'app_fee' => 0,
                    'discount_amount' => 0,
                    'status' => $status,
                    'payment_method' => $paymentMethod,
                    'qr_code' => json_encode(['order' => $orderNumber, 'ticket' => $ticket->id, 'qty' => $quantity]),
                    'notes' => rand(1, 5) === 1 ? fake('id_ID')->sentence(6) : null,
                    'payed_at' => $payedAt,
                    'check_in_time' => $checkInTime,
                    'refund_status' => null,
                    'refund_amount' => 0,
                    'refunded_at' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $orderCount++;

                // Flush batch
                if (count($batch) >= $batchSize) {
                    TicketOrder::insert($batch);
                    $batch = [];
                }
            }
        }

        // Flush remaining
        if (! empty($batch)) {
            TicketOrder::insert($batch);
        }

        $this->command->info('   📦 Orders created: '.$orderCount);

        return $orderCount;
    }

    /* ================================================================
     |  Helpers
     |================================================================ */

    /**
     * Weighted random selection — returns the key.
     * Example: [1 => 45, 2 => 30] → returns 1 with 60 % probability.
     */
    private function weightedRandom(array $weighted): int
    {
        $total = array_sum($weighted);
        $rand = rand(1, $total);
        foreach ($weighted as $value => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $value;
            }
        }

        return array_key_first($weighted);
    }

    /**
     * Weighted random — returns the array index.
     */
    private function weightedRandomIndex(array $weights): int
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);
        foreach ($weights as $idx => $weight) {
            $rand -= $weight;
            if ($rand <= 0) {
                return $idx;
            }
        }

        return 0;
    }
}
