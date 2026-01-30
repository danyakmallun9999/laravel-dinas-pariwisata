<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Support\Str;

class CultureSeeder extends Seeder
{
    public function run()
    {
        // Category: Kebudayaan
        $category = Category::firstOrCreate(
            ['name' => 'Kebudayaan'],
            ['slug' => 'kebudayaan', 'description' => 'Jelajahi kekayaan tradisi, sejarah, dan festival budaya Jepara', 'icon_class' => 'festival']
        );

        $cultures = [
            [
                'name' => 'Perang Obor Tegal Sambi',
                'description' => "Tradisi ritual sakral tolak bala yang digelar setahun sekali di Desa Tegal Sambi. \n\nPara pemuda desa saling memukulkan obor dari pelepah kelapa kering yang menyala, menciptakan atraksi api yang dramatis namun dipercaya membawa berkah dan kesehatan.",
                'image_path' => 'images/culture/perang-obor.jpg',
                'ticket_price' => 'Gratis',
                'address' => 'Desa Tegal Sambi, Tahunan',
                'lat' => -6.619000, 
                'lng' => 110.665000,
            ],
            /*
            [
                'name' => 'Festival Kupat Lepet',
                'description' => "Puncak perayaan Syawalan (H+7 Lebaran) di Pantai Kartini. \n\nDiwarnai dengan arak-arakan gunungan kupat dan lepet yang kemudian diperebutkan oleh masyarakat sebagai simbol saling memaafkan dan menutup kesalahan masa lalu.",
                'image_path' => 'images/culture/festival-kupat-lepet.jpg', // Placeholder filename
                'ticket_price' => 'Gratis (Tiket Masuk Pantai)',
                'address' => 'Pantai Kartini, Jepara',
                'lat' => -6.583333,
                'lng' => 110.650000,
            ],
            [
                'name' => 'Kirab Buka Luwur Mantingan',
                'description' => "Upacara penggantian kelambu (luwur) Makam Sultan Hadlirin dan Ratu Kalinyamat. \n\nProsesi kirab budaya membawa luwur baru diiringi sholawat dan kesenian tradisional, menjadi wujud penghormatan kepada leluhur Jepara.",
                'image_path' => 'images/culture/buka-luwur.jpg', // Placeholder
                'ticket_price' => 'Gratis',
                'address' => 'Masjid Mantingan, Tahunan',
                'lat' => -6.611599,
                'lng' => 110.686522,
            ],
            [
                'name' => 'Festival Jondang Kawak',
                'description' => "Tradisi sedekah bumi unik di Desa Kawak di mana warga mengarak 'Jondang' (kotak kayu ukir berisi hasil bumi) keliling desa. \n\nMenampilkan semangat gotong royong dan rasa syukur masyarakat atas kelimpahan panen.",
                'image_path' => 'images/culture/jondang-kawak.jpg', // Placeholder
                'ticket_price' => 'Gratis',
                'address' => 'Desa Kawak, Pakis Aji',
                'lat' => -6.550000,
                'lng' => 110.750000,
            ],
            [
                'name' => 'Barikan Kubro Karimun Jawa',
                'description' => "Ritual tolak bala dan sedekah laut masyarakat kepulauan Karimunjawa. \n\nDiwarnai dengan pelepasan tumpeng kecil ke laut dan doa bersama lintas agama, mencerminkan harmoni manusia dengan alam dan Sang Pencipta.",
                'image_path' => 'images/culture/barikan-kubro.jpg', // Placeholder
                'ticket_price' => 'Gratis',
                'address' => 'Alun-alun Karimunjawa',
                'lat' => -5.846500,
                'lng' => 110.428500,
            ]
            */
        ];

        // Clear existing cultures to prevent duplicates if names changed slightly, or just use updateOrCreate
        // Since we want to Replace the list to match user request strictly, we can check if we should delete others.
        // But updateOrCreate is safer to keep IDs. For now I will assume just adding/updating these is enough.
        // User implied "kebudayaan nya..." list, so maybe I should delete others? 
        // I will stick to updateOrCreate. If I see old ones (Museum Kartini), I might leave them or they might settle at bottom.
        // To be clean, I will delete items in this category first? No, that deletes IDs. 
        // I'll just upsert.
        
        // Minimal debug
        try {
            Place::create([
               'name' => 'Minimal Seeder Place',
               'category_id' => $category->id,
               'description' => 'Test',
               'latitude' => -6.0,
               'longitude' => 110.0,
               'ticket_price' => '1000',
               'slug' => 'minimal-seeder-place-' . rand(100,999) 
            ]);
            $this->command->info("Minimal insert success");
        } catch (\Throwable $e) {
            $this->command->error("Minimal insert failed: " . $e->getMessage());
        }
    }
}
