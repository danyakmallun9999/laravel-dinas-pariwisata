<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Place;
use App\Models\Category;

class PariwisataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Read JSON File
        $jsonPath = public_path('data_pariwisata.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("File data_pariwisata.json not found in public directory.");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (!isset($data['data_pariwisata'])) {
            $this->command->error("Invalid JSON structure.");
            return;
        }

        $items = $data['data_pariwisata'];
        $count = 0;

        foreach ($items as $item) {
            try {
                // Skip "Lain-lain" or empty data
                if ($item['nama_wisata'] === 'Lain-lain' || $item['kategori'] === '-') {
                    continue;
                }

                // 2. Find or Create Category
                $categoryName = $item['kategori'];
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => \Illuminate\Support\Str::slug($categoryName) . '-' . \Illuminate\Support\Str::random(3)]
                );

                // 3. Prepare Data
                // Parsing coordinates is difficult from Short URL (requires HTTP request). 
                // We will set default lat/long for Jepara if not parseable.
                // Jepara Center: -6.581768, 110.669896
                $lat = -6.581768; 
                $lng = 110.669896;

                // Combine description and noted
                $description = $item['deskripsi'];
                if (!empty($item['noted']) && $item['noted'] !== '-') {
                    $description .= "\n\nCatatan: " . $item['noted'];
                }

                // 4. Create or Update Place
                Place::updateOrCreate(
                    ['name' => $item['nama_wisata']], // Use name as unique identifier
                    [
                        'category_id' => $category->id,
                        'slug' => \Illuminate\Support\Str::slug($item['nama_wisata']) . '-' . \Illuminate\Support\Str::random(5),
                        'description' => $description,
                        'address' => $item['lokasi'] !== '-' ? $item['lokasi'] : 'Jepara', // Assuming 'lokasi' maps to address roughly
                        'ticket_price' => $item['harga_tiket'] !== '-' ? $item['harga_tiket'] : null,
                        'opening_hours' => $item['jam_operasional'] !== '-' ? $item['jam_operasional'] : null,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'contact_info' => $item['titi_koordinat'] // Save the google maps link here for reference
                    ]
                );
            } catch (\Throwable $e) {
                $this->command->error("Failed on item: " . ($item['nama_wisata'] ?? 'Unknown'));
                $this->command->error($e->getMessage());
            }

            $count++;
        }

        $this->command->info("Successfully seeded {$count} places from JSON.");
    }
}
