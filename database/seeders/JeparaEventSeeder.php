<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Event;

class JeparaEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = public_path('data_event_jepara.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("File not found at: $jsonPath");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (!isset($data['data_event'])) {
            $this->command->error("Invalid JSON structure: key 'data_event' missing.");
            return;
        }

        $this->command->info('Seeding events from JSON...');
        
        $count = 0;
        foreach ($data['data_event'] as $item) {
            $title = $item['nama_event'];
            $location = $item['lokasi_pelaksanaan'];
            $rawDate = $item['tanggal_pelaksanaan'];
            $monthName = $item['bulan'];

            // Parse Date
            $startDate = $this->parseDate($rawDate, $monthName);

            if (!$startDate) {
                $this->command->warn("Could not parse date for event: $title ($rawDate). Skipping.");
                continue;
            }

            Event::create([
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'description' => $title . ' yang akan dilaksanakan di ' . $location . '.',
                'location' => $location,
                'start_date' => $startDate,
                'end_date' => $startDate, // Assuming 1 day event by default
                'image' => null, // No image in JSON
                'is_published' => true,
            ]);
            $count++;
        }

        $this->command->info("Successfully seeded $count events.");
    }

    private function parseDate($rawDate, $monthName)
    {
        // 1. Clean string: remove content in parens, asterisks, trim
        $cleanDate = preg_replace('/\s*\(.*?\)/', '', $rawDate); // Remove (Setiap hari...)
        $cleanDate = str_replace('*', '', $cleanDate);
        $cleanDate = trim($cleanDate);

        // Map Indonesian Month names to English for Carbon
        $monthMap = [
            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
            'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
            'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
        ];
        
        $bulanInggris = $monthMap[$monthName] ?? $monthName;

        try {
            // Try explicit standard formats first
            // Case: "29/01/2026" -> d/m/Y
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $cleanDate)) {
                return Carbon::createFromFormat('d/m/Y', $cleanDate);
            }

            // Case: "1-Feb-26" -> j-M-y
            if (preg_match('/^\d{1,2}-[A-Za-z]+-\d{2}$/', $cleanDate)) {
                 return Carbon::parse($cleanDate);
            }

            // Case: "5/28/2026" (m/d/Y) or "10/1/2026"
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $cleanDate, $matches)) {
                // Check if month matches
                // matches[1] = part 1, matches[2] = part 2
                // We need to know which is month.
                // Comparison with $monthName
                
                $p1 = (int)$matches[1];
                $p2 = (int)$matches[2];
                $y = (int)$matches[3];

                // Rough Map of month name to index
                $monthIndexMap = [
                    'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
                    'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
                    'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
                ];
                $expectedMonth = $monthIndexMap[$monthName] ?? 0;

                if ($p1 == $expectedMonth) {
                    // It's m/d/Y
                    return Carbon::createFromDate($y, $p1, $p2);
                } elseif ($p2 == $expectedMonth) {
                    // It's d/m/Y
                    return Carbon::createFromDate($y, $p2, $p1);
                }
                
                // Fallback: guess
                if ($p1 > 12) return Carbon::createFromDate($y, $p2, $p1); // p1 must be day
                if ($p2 > 12) return Carbon::createFromDate($y, $p1, $p2); // p2 must be day
                
                // Default to m/d/Y as seen in 5/28/2026
                 return Carbon::createFromDate($y, $p1, $p2);
            }

             // Case: "Apr-26" -> M-y
            if (preg_match('/^[A-Za-z]+-\d{2}$/', $cleanDate)) {
                 // Prepend 1-
                 return Carbon::parse("1-$cleanDate");
            }
            
            // Case: "18 Juni 2025" or "10 Juli 2026" - Indonesian format
            // Replace Month Name with English
            $englishDateStr = strtr($cleanDate, $monthMap);
            return Carbon::parse($englishDateStr);

        } catch (\Exception $e) {
            return null;
        }
        
        return null;
    }
}
