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
        // Clear existing events to ensure clean slate
        \Illuminate\Support\Facades\DB::table('events')->truncate();

        $year = now()->year;
        $titles = [
            'Festival Durian',
            'Pesta Lomban',
            'Gebyar Merah Putih',
            'Festival Kopi Jepara',
            'Jepara Jazz Festival',
            'Pekan Budaya Karimunjawa',
            'Festival Ukir',
            'Kirab Budaya',
            'Sedekah Bumi',
            'Festival Lampion',
            'Expo UMKM',
            'Pesta Tahun Baru'
        ];

        // Generate events for all 12 months
        for ($month = 1; $month <= 12; $month++) {
            // Use create/safe date to avoid overflow issues (e.g. Feb 30)
            $startDate = Carbon::create($year, $month, 15, 9, 0);
            
            // Event 1: Main Monthly Event
            Event::create([
                'title' => ($titles[$month - 1] ?? 'Event Budaya') . ' ' . $year,
                'description' => '<p>Acara tahunan yang dinanti-nanti masyarakat Jepara dengan berbagai keseruan dan hiburan menarik untuk seluruh keluarga.</p>',
                'location' => 'Alun-alun Jepara',
                'start_date' => $startDate->copy(),
                'end_date' => $startDate->copy()->addDays(2),
                'image' => null,
                'is_published' => true,
            ]);

            // Event 2: Workshop/Minor Event
            Event::create([
                'title' => 'Workshop Kreatif ' . $startDate->translatedFormat('F'),
                'description' => '<p>Pelatihan seni dan kreatifitas untuk pemuda pemudi Jepara guna meningkatkan skill dan inovasi.</p>',
                'location' => 'Gedung Wanita',
                'start_date' => $startDate->copy()->addDays(5)->setTime(13, 0),
                'end_date' => $startDate->copy()->addDays(5)->setTime(16, 0),
                'image' => null,
                'is_published' => true,
            ]);
        }
    }
}
