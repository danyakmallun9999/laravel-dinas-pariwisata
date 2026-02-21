<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;
use App\Models\TravelAgency;
use App\Models\TourPackage;

class FlagshipKarimunjawaSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan atau buat destinasi Karimunjawa
        $karimun = Place::firstOrCreate(
            ['slug' => 'pulau-karimunjawa'],
            [
                'name' => 'Pulau Karimunjawa',
                'category_id' => 1, // Asumsi 1 adalah wisata alam
                'description' => 'Kepulauan eksotis di utara Jepara dengan pesona bawah laut yang luar biasa.',
                'latitude' => -5.8822,
                'longitude' => 110.4287,
                'is_flagship' => true,
            ]
        );

        // Pastikan is_flagship true jika tempat sudah ada
        $karimun->update(['is_flagship' => true]);

        // Daftar Biro Wisata (Sample 3 Biro)
        $agency1 = TravelAgency::firstOrCreate(
            ['contact_wa' => '081234567890'],
            [
                'name' => 'Karimun Explore',
                'description' => 'Biro wisata resmi dan terpercaya untuk semua kebutuhan liburan di Karimunjawa.',
                'website' => 'https://karimunexplore.com',
                'instagram' => '@karimun.explore',
            ]
        );

        $agency2 = TravelAgency::firstOrCreate(
            ['contact_wa' => '089876543210'],
            [
                'name' => 'Jepara Ocean Tour',
                'description' => 'Spesialis paket private laut lepas dan honeymoon Karimunjawa.',
                'website' => 'https://jeparaoceantour.com',
                'instagram' => '@jeparaoceantour',
            ]
        );

        $agency3 = TravelAgency::firstOrCreate(
            ['contact_wa' => '087766554433'],
            [
                'name' => 'Nusantara Trip',
                'description' => 'Melayani perjalanan wisata murah bergaya backpacker se-Indonesia.',
                'website' => 'https://nusantaratrip.id',
                'instagram' => '@nusantaratrip',
            ]
        );

        // Paket Wisata untuk Biro 1
        TourPackage::firstOrCreate(
            ['name' => 'Paket Honeymoon Romantis 3H2M'],
            [
                'place_id' => $karimun->id,
                'travel_agency_id' => $agency1->id,
                'description' => 'Paket eksklusif untuk pasangan, termasuk makan malam romantis di pinggir pantai dan dokumentasi drone.',
                'price_start' => 2500000,
                'price_end' => 3500000,
                'duration_days' => 3,
                'duration_nights' => 2,
                'inclusions' => [
                    'Tiket Kapal Express PP',
                    'Hotel Bintang 3 (AC)',
                    'Makan 6x + 1x Romantic Dinner',
                    'Sewa Kapal Snorkeling Private',
                    'Dokumentasi (Drone + GoPro)'
                ],
                'itinerary' => [
                    ['day' => 1, 'time' => '08:00', 'activity' => 'Meeting point Pelabuhan Kartini Jepara'],
                    ['day' => 1, 'time' => '11:00', 'activity' => 'Tiba di Karimunjawa, Check-in Hotel'],
                    ['day' => 1, 'time' => '14:00', 'activity' => 'Eksplorasi Pantai Tanjung Gelam'],
                    ['day' => 2, 'time' => '08:00', 'activity' => 'Snorkeling di Menjangan Kecil'],
                    ['day' => 2, 'time' => '13:00', 'activity' => 'BBQ Ikan Bakar di Pulau Cemara'],
                    ['day' => 3, 'time' => '09:00', 'activity' => 'Pusat Oleh-Oleh & Transfer ke Pelabuhan'],
                ]
            ]
        );

        TourPackage::firstOrCreate(
            ['name' => 'Paket Backpacker 2H1M'],
            [
                'place_id' => $karimun->id,
                'travel_agency_id' => $agency3->id,
                'description' => 'Solusi hemat untuk pelajar dan mahasiswa.',
                'price_start' => 750000,
                'price_end' => 950000,
                'duration_days' => 2,
                'duration_nights' => 1,
                'inclusions' => [
                    'Tiket Kapal Feri PP',
                    'Homestay Kipas',
                    'Makan 3x',
                    'Sewa Kapal Kolotok Sharing',
                    'Dokumentasi Underwater'
                ],
                'itinerary' => [
                    ['day' => 1, 'time' => '06:00', 'activity' => 'Meeting point Pelabuhan Jepara'],
                    ['day' => 1, 'time' => '13:00', 'activity' => 'Tiba di Karimunjawa & Makan Siang'],
                    ['day' => 1, 'time' => '14:00', 'activity' => 'Snorkeling Maer'],
                    ['day' => 2, 'time' => '06:00', 'activity' => 'Penangkaran Hiu'],
                    ['day' => 2, 'time' => '11:00', 'activity' => 'Kembali ke Jepara'],
                ]
            ]
        );

        // Paket Wisata untuk Biro 2
        TourPackage::firstOrCreate(
            ['name' => 'Diving Expedition 4H3M'],
            [
                'place_id' => $karimun->id,
                'travel_agency_id' => $agency2->id,
                'description' => 'Eksplorasi titik-titik diving terbaik di Kepulauan Karimunjawa termasuk bangkai kapal Indonor.',
                'price_start' => 4500000,
                'price_end' => 6000000,
                'duration_days' => 4,
                'duration_nights' => 3,
                'inclusions' => [
                    'Tiket Kapal Express PP',
                    'Resort Premium',
                    'Makan 9x',
                    'Peralatan Diving Lengkap',
                    'Dive Master Guide',
                    'Dokumentasi Profesional'
                ],
                'itinerary' => [
                    ['day' => 1, 'time' => '09:00', 'activity' => 'Keberangkatan dari Jepara'],
                    ['day' => 1, 'time' => '15:00', 'activity' => 'Check Dive & Orientasi'],
                    ['day' => 2, 'time' => '08:00', 'activity' => 'Diving Spot Indonor Wreck'],
                    ['day' => 2, 'time' => '14:00', 'activity' => 'Diving Spot Kemujan'],
                    ['day' => 3, 'time' => '08:00', 'activity' => 'Diving Gosong Cemara'],
                    ['day' => 4, 'time' => '10:00', 'activity' => 'Free time & Kepulangan'],
                ]
            ]
        );
    }
}
