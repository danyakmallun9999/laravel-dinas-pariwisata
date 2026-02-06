<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DestinasiPilihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Read JSON File
        $jsonPath = public_path('destinasi_pilihan.json');

        if (! File::exists($jsonPath)) {
            $this->command->error('File destinasi_pilihan.json not found in public directory.');
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (! isset($data['data_wisata'])) {
            $this->command->error("Invalid JSON structure: key 'data_wisata' missing.");
            return;
        }
        
        $items = $data['data_wisata'];
        
        // Clear existing data
        try {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            Place::truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        } catch (\Exception $e) {
            Place::query()->delete();
        }

        $count = 0;

        foreach ($items as $item) {
            try {
                // Skip empty names if any
                if (empty($item['nama_wisata'])) {
                    continue;
                }

                // 2. Find or Create Category
                $categories = explode(',', $item['jenis_wisata']);
                $primaryCategoryName = trim($categories[0]);
                $primaryCategoryName = Str::title(strtolower($primaryCategoryName));

                $category = Category::firstOrCreate(
                    ['name' => $primaryCategoryName],
                    [
                        'slug' => Str::slug($primaryCategoryName),
                        'icon_class' => 'fa-solid fa-map-location-dot', // Default icon
                        'color' => '#0ea5e9', // Default color
                    ]
                );

                // 3. Prepare Data
                // Handle Wahana parsing
                $rides = [];
                if (!empty($item['wahana']) && $item['wahana'] !== '-') {
                    // Split by pattern like "1. ", " 2. "
                    // We prepend a space to ensure the first "1. " is matched if it's at start
                    $rawRides = preg_split('/\s+\d+\.\s+/', ' ' . $item['wahana'], -1, PREG_SPLIT_NO_EMPTY);
                    $rides = array_map('trim', $rawRides);
                }

                // Handle Fasilitas parsing
                $facilities = [];
                if (!empty($item['fasilitas']) && $item['fasilitas'] !== '-') {
                    $rawFacilities = preg_split('/\s+\d+\.\s+/', ' ' . $item['fasilitas'], -1, PREG_SPLIT_NO_EMPTY);
                    $facilities = array_map('trim', $rawFacilities);
                }

                // Default coordinates (Jepara Center)
                $lat = -6.581768; 
                $lng = 110.669896;

                // 4. Create Place
                Place::create([
                    'name' => $item['nama_wisata'], 
                    'category_id' => $category->id,
                    'slug' => Str::slug($item['nama_wisata']), 
                    'description' => $item['deskripsi'] ?? null,
                    'address' => $item['lokasi'] ?? null, // Map 'lokasi' to 'address'
                    'ticket_price' => ($item['harga_tiket'] !== '-' ? $item['harga_tiket'] : null),
                    'opening_hours' => ($item['waktu_buka'] !== '-' ? $item['waktu_buka'] : null),
                    'latitude' => $lat, 
                    'longitude' => $lng,
                    'google_maps_link' => $item['link_koordinat'] ?? null,
                    'ownership_status' => $item['status_kepemilikan'] ?? null,
                    'manager' => ($item['pengelola'] !== '-' ? $item['pengelola'] : null),
                    'rides' => !empty($rides) ? $rides : null,
                    'facilities' => !empty($facilities) ? $facilities : null,
                    'social_media' => ($item['media_sosial'] !== '-' && $item['media_sosial'] !== '' ? $item['media_sosial'] : null),
                    // contact_info is not in JSON directly, leaving null or could be parsed from desc if needed
                    'contact_info' => null, 
                ]);

                $count++;
            } catch (\Throwable $e) {
                $this->command->error('Failed on item: '.($item['nama_wisata'] ?? 'Unknown'));
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info("Successfully seeded {$count} places from destinasi_pilihan.json.");
    }
}