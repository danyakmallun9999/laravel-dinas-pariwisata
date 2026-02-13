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
        $this->call(TourismStatSeeder::class);
        $this->call(PariwisataSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(BoundarySeeder::class);
        $this->call(DestinasiImageSeeder::class);
        $this->call(JeparaEventSeeder::class);
        // $this->call(DummyTicketSeeder::class);

    }
}
