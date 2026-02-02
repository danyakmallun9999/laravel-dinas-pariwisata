<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Place;
use App\Models\Category;
use Illuminate\Support\Str;

class PariwisataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Read JSON File
        $jsonPath = public_path('data_wisata_jepara.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("File data_wisata_jepara.json not found in public directory.");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (!isset($data['data_wisata'])) {
            $this->command->error("Invalid JSON structure: key 'data_wisata' missing.");
            return;
        }

        $items = $data['data_wisata'];
        $count = 0;

        foreach ($items as $item) {
            try {
                // Skip empty names if any
                if (empty($item['nama_wisata'])) {
                    continue;
                }

                // 2. Find or Create Category
                // jenis_wisata is CSV, e.g., "WISATA ALAM, WISATA MINAT KHUSUS"
                // We take the first one as the primary category.
                $categories = explode(',', $item['jenis_wisata']);
                $primaryCategoryName = trim($categories[0]);
                
                // Normalizing category name for better display (Title Case)
                $primaryCategoryName = Str::title(strtolower($primaryCategoryName));

                $category = Category::firstOrCreate(
                    ['name' => $primaryCategoryName],
                    [
                        'slug' => Str::slug($primaryCategoryName),
                        'icon_class' => 'fa-solid fa-map-location-dot', // Default icon
                        'color' => '#0ea5e9', // Default color (sky-500)
                    ]
                );

                // 3. Prepare Data
                // Default coordinates for Jepara since JSON only has links
                $lat = -6.581768; 
                $lng = 110.669896;

                // 4. Create or Update Place
                Place::updateOrCreate(
                    ['name' => $item['nama_wisata']], // Use name as unique identifier
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($item['nama_wisata']) . '-' . Str::random(5),
                        'description' => $item['deskripsi'] ?? null,
                        'address' => $item['lokasi'] ?? null,
                        'ticket_price' => ($item['harga_tiket'] !== '-' ? $item['harga_tiket'] : null),
                        'opening_hours' => ($item['waktu_buka'] !== '-' ? $item['waktu_buka'] : null),
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'google_maps_link' => $item['link_koordinat'] ?? null,
                        'ownership_status' => $item['status_kepemilikan'] ?? null,
                        'manager' => $item['pengelola'] ?? null,
                        'rides' => ($item['wahana'] !== '-' ? $item['wahana'] : null),
                        'facilities' => ($item['fasilitas'] !== '-' ? $item['fasilitas'] : null),
                        'social_media' => ($item['media_sosial'] !== '-' ? $item['media_sosial'] : null),
                        'contact_info' => null // Phone not explicitly in JSON
                    ]
                );
                
                $count++;
            } catch (\Throwable $e) {
                $this->command->error("Failed on item: " . ($item['nama_wisata'] ?? 'Unknown'));
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info("Successfully seeded {$count} places from new JSON.");
    }
}
