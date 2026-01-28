<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Event 1: April (Kartini Day)
        Event::create([
            'title' => 'Festival Hari Kartini',
            'description' => '<p>Peringatan hari kelahiran pahlawan emansipasi wanita R.A. Kartini dengan berbagai lomba tradisional dan kirab budaya yang meriah di pusat kota Jepara.</p>',
            'location' => 'Alun-alun Jepara',
            'start_date' => Carbon::create(date('Y'), 4, 21, 8, 0, 0),
            'end_date' => Carbon::create(date('Y'), 4, 21, 16, 0, 0),
            'image' => null, // Placeholder or null
            'is_published' => true,
        ]);

        // Event 2: May (Pesta Baratan)
        Event::create([
            'title' => 'Pesta Baratan Ratu Kalinyamat',
            'description' => '<p>Tradisi arak-arakan obor dan lampion yang mengisahkan Ratu Kalinyamat saat membawa jenazah Sultan Hadlirin. Diadakan setiap malam Nisfu Sya\'ban.</p>',
            'location' => 'Desa Kriyan, Kalinyamatan',
            'start_date' => Carbon::create(date('Y'), 5, 15, 19, 0, 0), // Adjust date dynamically if needed, set to mid-May for demo
            'image' => null,
            'is_published' => true,
        ]);

        // Event 3: June (Perang Obor)
        Event::create([
            'title' => 'Perang Obor Tegalsambi',
            'description' => '<p>Upacara sedekah bumi yang unik dimana para pemuda desa saling memukulkan obor dari pelepah kelapa kering. Simbol pembersihan diri dan penolak bala.</p>',
            'location' => 'Desa Tegalsambi',
            'start_date' => Carbon::create(date('Y'), 6, 20, 20, 0, 0),
            'image' => null,
            'is_published' => true,
        ]);
        
        // Event 4: October (Perang Obor - another month example)
        Event::create([
            'title' => 'Festival Ukir Internasional',
            'description' => '<p>Pameran mahakarya seni ukir Jepara yang mendunia, diikuti oleh pengrajin lokal dan mancanegara.</p>',
            'location' => 'Jepara International Exhibition Center',
            'start_date' => Carbon::create(date('Y'), 10, 10, 9, 0, 0),
            'end_date' => Carbon::create(date('Y'), 10, 15, 21, 0, 0),
            'image' => null,
            'is_published' => true,
        ]);
    }
}
