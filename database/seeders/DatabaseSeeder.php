<?php

namespace Database\Seeders;

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
        $this->call(PariwisataSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(BoundarySeeder::class);
        $this->call(DestinasiImageSeeder::class);
        $this->call(JeparaEventSeeder::class);
        // $this->call(DummyTicketSeeder::class);

        // Fetch Categories
        $nature = \App\Models\Category::where('slug', 'wisata-alam')->first();
        $culture = \App\Models\Category::where('slug', 'wisata-budaya')->first();
        $culinary = \App\Models\Category::where('slug', 'wisata-kuliner')->first();

    }
}
