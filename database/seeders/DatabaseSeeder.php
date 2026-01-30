<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(PariwisataSeeder::class); // Run early to ensure categories/places exist
        $this->call(ProductSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(BoundarySeeder::class);
        $this->call(DestinasiImageSeeder::class);


        // Fetch Categories
        $nature = \App\Models\Category::where('slug', 'wisata-alam')->first();
        $culture = \App\Models\Category::where('slug', 'wisata-budaya')->first();
        $culinary = \App\Models\Category::where('slug', 'wisata-kuliner')->first();

        // Places
        // Manual Places creation commented out in favor of JSON data
        /*
        if ($nature) {
            \App\Models\Place::create([
                'category_id' => $nature->id,
                'name' => 'Pantai Bandengan',
                'slug' => 'pantai-bandengan',
                'description' => 'Pantai pasir putih eksotis dengan air jernih, populer untuk water sports dan sunset.',
                'image_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'latitude' => -6.5539,
                'longitude' => 110.6481,
                'ticket_price' => 'Rp 10.000',
                'opening_hours' => '07:00 - 18:00',
                'rating' => 4.7
            ]);
        }

        if ($culture) {
            \App\Models\Place::create([
                'category_id' => $culture->id,
                'name' => 'Museum R.A. Kartini',
                'slug' => 'museum-ra-kartini',
                'description' => 'Museum yang menyimpan peninggalan R.A. Kartini dan benda-benda warisan budaya Jepara.',
                'image_path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Museum_RA_Kartini.jpg/1280px-Museum_RA_Kartini.jpg',
                'latitude' => -6.5898,
                'longitude' => 110.6682,
                'ticket_price' => 'Rp 5.000',
                'opening_hours' => '08:00 - 16:00',
                'rating' => 4.5
            ]);
        }
        
        if ($culinary) {
            \App\Models\Place::create([
                'category_id' => $culinary->id,
                'name' => 'SCJ (Shopping Center Jepara)',
                'slug' => 'scj-jepara',
                'description' => 'Pusat kuliner malam yang menyajikan berbagai makanan khas Jepara seperti Pindang Serani dan Adon-adon Coro.',
                'image_path' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=800&q=80',
                'latitude' => -6.5925,
                'longitude' => 110.6690,
                'ticket_price' => 'Gratis',
                'opening_hours' => '17:00 - 23:00',
                'rating' => 4.6
            ]);
        }
        */
    }
}
