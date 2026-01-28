<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdditionalEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Event 2 in April (Same month as Kartini)
        Event::create([
            'title' => 'Lomba Melukis Pantai',
            'description' => '<p>Perlombaan melukis pemandangan pantai bagi pelajar SD dan SMP se-Jepara.</p>',
            'location' => 'Pantai Kartini',
            'start_date' => Carbon::create(date('Y'), 4, 25, 9, 0, 0),
            'image' => null,
            'is_published' => true,
        ]);

        // Event 3 in April (Same month as Kartini)
        Event::create([
            'title' => 'Seminar Budaya Jepara',
            'description' => '<p>Diskusi publik mengenai pelestarian seni ukir dan tenun ikat Troso.</p>',
            'location' => 'Pendopo Kabupaten',
            'start_date' => Carbon::create(date('Y'), 4, 28, 13, 0, 0),
            'image' => null,
            'is_published' => true,
        ]);
    }
}
