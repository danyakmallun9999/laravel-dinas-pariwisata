<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JeparaEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = public_path('data_event_jepara.json');

        if (! File::exists($jsonPath)) {
            $this->command->error("File not found at: $jsonPath");

            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (! isset($data['data_event'])) {
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

            if (! $startDate) {
                $this->command->warn("Could not parse date for event: $title ($rawDate). Skipping.");

                continue;
            }

            Event::create([
                'title' => $title,
                'slug' => Str::slug($title).'-'.Str::random(5),
                'description' => $title.' yang akan dilaksanakan di '.$location.'.',
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
        // Map Indonesian Month names to English for Carbon
        $monthMap = [
            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
            'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
            'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December',
        ];

        try {
            // Pattern 1: dd/mm/yyyy anywhere in the string
            // Example: "sedekah bumi ... (23/04/2026 ...)"
            if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $rawDate, $matches)) {
                 $d = $matches[1];
                 $m = $matches[2];
                 $y = $matches[3];
                 return Carbon::createFromDate($y, $m, $d);
            }

            // Pattern 2: dd Month yyyy anywhere in the string
            // Named months in Indonesian
            // Example: "20 April 2026"
            $monthNamesRegex = implode('|', array_keys($monthMap));
            if (preg_match('/(\d{1,2})\s+('.$monthNamesRegex.')\s+(\d{4})/i', $rawDate, $matches)) {
                $d = $matches[1];
                $mName = ucfirst(strtolower($matches[2])); // Normalize case if needed, but array keys are Title Case
                // Fix casing if regex matched lowercase
                foreach ($monthMap as $indo => $eng) {
                    if (strcasecmp($indo, $mName) === 0) {
                        $mName = $indo;
                        break;
                    }
                }
                $y = $matches[3];
                $englishMonth = $monthMap[$mName] ?? $mName;
                return Carbon::createFromFormat('j F Y', "$d $englishMonth $y");
            }

            // Pattern 3: Standard clean check (fallback to original logic for simple cases)
             // 1. Clean string: remove content in parens, asterisks, trim
            $cleanDate = preg_replace('/\s*\(.*?\)/', '', $rawDate); // Remove (Setiap hari...)
            $cleanDate = str_replace('*', '', $cleanDate);
            $cleanDate = trim($cleanDate);

            // Case: "1-Feb-26" -> j-M-y
            if (preg_match('/^\d{1,2}-[A-Za-z]+-\d{2}$/', $cleanDate)) {
                 return Carbon::parse($cleanDate);
            }
             // Case: "5/28/2026" (m/d/Y) or "10/1/2026" - Start/End anchor
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $cleanDate)) {
                 return Carbon::parse($cleanDate); 
            }
            
            // If we are here, we haven't found a specific numeric date.
            // Check if it's a "Javanese date" text like "Kamis Pahing..."
            // We will fallback to the 1st of the provided $monthName and assumed year 2026 (or 2025).
            // The dataset seems to be for 2026 mostly based on previous errors.
            // Let's assume 2026 for now, or use current year.
            
            $year = 2026;
            $englishMonth = $monthMap[$monthName] ?? null;

            if ($englishMonth) {
                // Return 1st of that month
                return Carbon::createFromFormat('j F Y', "1 $englishMonth $year");
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
