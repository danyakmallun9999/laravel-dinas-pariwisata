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
        $now = Carbon::now();

        // Event 1: Next Month
        Event::create([
            'title' => 'Festival Hari Kartini',
            'description' => '<p>Peringatan hari kelahiran pahlawan emansipasi wanita R.A. Kartini dengan berbagai lomba tradisional dan kirab budaya yang meriah di pusat kota Jepara.</p>',
            'location' => 'Alun-alun Jepara',
            'start_date' => $now->copy()->addMonth()->setDay(21)->setTime(8, 0),
            'end_date' => $now->copy()->addMonth()->setDay(21)->setTime(16, 0),
            'image' => null,
            'is_published' => true,
        ]);

        // Event 2: +2 Months
        Event::create([
            'title' => 'Pesta Baratan Ratu Kalinyamat',
            'description' => '<p>Tradisi arak-arakan obor dan lampion yang mengisahkan Ratu Kalinyamat saat membawa jenazah Sultan Hadlirin. Diadakan setiap malam Nisfu Sya\'ban.</p>',
            'location' => 'Desa Kriyan, Kalinyamatan',
            'start_date' => $now->copy()->addMonths(2)->setDay(15)->setTime(19, 0),
            'image' => null,
            'is_published' => true,
        ]);

        // Event 3: +3 Months
        Event::create([
            'title' => 'Perang Obor Tegalsambi',
            'description' => '<p>Upacara sedekah bumi yang unik dimana para pemuda desa saling memukulkan obor dari pelepah kelapa kering.</p>',
            'location' => 'Desa Tegalsambi',
            'start_date' => $now->copy()->addMonths(3)->setDay(20)->setTime(20, 0),
            'image' => null,
            'is_published' => true,
        ]);
        
        // Event 4: +5 Months
        Event::create([
            'title' => 'Festival Ukir Internasional',
            'description' => '<p>Pameran mahakarya seni ukir Jepara yang mendunia, diikuti oleh pengrajin lokal dan mancanegara.</p>',
            'location' => 'Jepara International Exhibition Center',
            'start_date' => $now->copy()->addMonths(5)->setDay(10)->setTime(9, 0),
            'end_date' => $now->copy()->addMonths(5)->setDay(15)->setTime(21, 0),
            'image' => null,
            'is_published' => true,
        ]);
    }
}
