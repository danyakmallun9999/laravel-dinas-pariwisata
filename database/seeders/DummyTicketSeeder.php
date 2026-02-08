<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\Ticket;
use App\Models\TicketOrder;
use Illuminate\Database\Seeder;

class DummyTicketSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a category first
        $category = \App\Models\Category::first();
        if (!$category) {
            $category = \App\Models\Category::create(['name' => 'Wisata Alam', 'slug' => 'wisata-alam']);
        }

        // Create places first
        $places = [
            ['name' => 'Pantai Kartini', 'slug' => 'pantai-kartini', 'address' => 'Jl. Pantai Kartini, Jepara', 'latitude' => -6.5881, 'longitude' => 110.6683],
            ['name' => 'Museum RA Kartini', 'slug' => 'museum-ra-kartini', 'address' => 'Jl. Alun-alun No.1, Jepara', 'latitude' => -6.5928, 'longitude' => 110.6743],
            ['name' => 'Pulau Karimunjawa', 'slug' => 'pulau-karimunjawa', 'address' => 'Karimunjawa, Jepara', 'latitude' => -5.8641, 'longitude' => 110.4388],
        ];

        foreach ($places as $placeData) {
            Place::firstOrCreate(
                ['slug' => $placeData['slug']],
                array_merge($placeData, [
                    'description' => 'Deskripsi ' . $placeData['name'],
                    'rating' => rand(35, 50) / 10,
                    'category_id' => $category->id,
                ])
            );
        }

        // Create tickets for each place
        $ticketTypes = [
            ['name' => 'Tiket Masuk Dewasa', 'type' => 'regular', 'price' => 15000, 'price_weekend' => 20000, 'quota' => 500],
            ['name' => 'Tiket Masuk Anak-anak', 'type' => 'regular', 'price' => 10000, 'price_weekend' => 12000, 'quota' => 300],
            ['name' => 'Paket Keluarga (4 orang)', 'type' => 'package', 'price' => 45000, 'price_weekend' => 55000, 'quota' => 100],
            ['name' => 'Tiket VIP + Wahana', 'type' => 'vip', 'price' => 75000, 'price_weekend' => 85000, 'quota' => 50],
            ['name' => 'Tiket Rombongan (min 20)', 'type' => 'group', 'price' => 12000, 'price_weekend' => 15000, 'quota' => null],
        ];

        $allPlaces = Place::all();
        foreach ($allPlaces as $place) {
            foreach ($ticketTypes as $ticketData) {
                Ticket::firstOrCreate(
                    ['name' => $ticketData['name'], 'place_id' => $place->id],
                    array_merge($ticketData, [
                        'place_id' => $place->id,
                        'description' => 'Deskripsi untuk ' . $ticketData['name'] . ' di ' . $place->name,
                        'terms_conditions' => 'Syarat dan ketentuan berlaku.',
                        'is_active' => rand(0, 1) === 1,
                    ])
                );
            }
        }

        // Create orders for all tickets
        $tickets = Ticket::with('place')->get();
        $statuses = ['pending', 'paid', 'used', 'cancelled'];
        $paymentMethods = ['transfer', 'cash', 'qris', 'ovo', 'gopay'];

        foreach ($tickets as $ticket) {
            $orderCount = rand(3, 8);
            for ($i = 0; $i < $orderCount; $i++) {
                $quantity = rand(1, 5);
                $status = $statuses[rand(0, 3)];
                
                // Generate dates - some for today, some for past 30 days
                $daysOffset = rand(-30, 7);
                $createdAt = now()->addDays($daysOffset)->subHours(rand(0, 12));
                $visitDate = $createdAt->copy()->addDays(rand(0, 5));
                
                // Set paid_at for paid/used orders
                $paidAt = null;
                $checkInTime = null;
                
                if (in_array($status, ['paid', 'used'])) {
                    $paidAt = $createdAt->copy()->addMinutes(rand(5, 120));
                    
                    // For 'used' status, set check_in_time
                    if ($status === 'used') {
                        $checkInTime = $visitDate->copy()->setHour(rand(8, 14))->setMinute(rand(0, 59));
                    }
                }
                
                TicketOrder::create([
                    'ticket_id' => $ticket->id,
                    'customer_name' => fake()->name(),
                    'customer_email' => fake()->safeEmail(),
                    'customer_phone' => fake()->phoneNumber(),
                    'customer_city' => fake()->city(),
                    'quantity' => $quantity,
                    'visit_date' => $visitDate,
                    'total_price' => $ticket->price * $quantity,
                    'unit_price' => $ticket->price,
                    'status' => $status,
                    'payment_method' => $paymentMethods[rand(0, 4)],
                    'notes' => rand(0, 1) ? fake()->sentence() : null,
                    'payed_at' => $paidAt,
                    'check_in_time' => $checkInTime,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('✅ Dummy data created:');
        $this->command->info('   - Places: ' . Place::count());
        $this->command->info('   - Tickets: ' . Ticket::count());
        $this->command->info('   - Orders: ' . TicketOrder::count());
    }
}
