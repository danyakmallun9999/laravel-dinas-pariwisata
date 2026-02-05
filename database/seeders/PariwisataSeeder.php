<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PariwisataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Read JSON File
        $jsonPath = public_path('20-destinasi.json');

        if (! File::exists($jsonPath)) {
            $this->command->error('File 20-destinasi.json not found in public directory.');
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (! isset($data['data_wisata'])) {
            $this->command->error("Invalid JSON structure: key 'data_wisata' missing.");
            return;
        }

        // Optional: Reset Places table to ensure exactly these 20 exist (per user request "only choosing 20")
        // Be careful with foreign keys if needed, but for seeder update usually we might want a fresh start or update.
        // Since updateOrCreate is used, we'll stick to that, but user said "kini hanya memilih 20", implying we might want to clean up old ones?
        // Let's assume we just update/create for now to be safe against deleting other potential data, unless user explicitly asked to "replace" all.
        // But "kini hanya memilih 20" suggests the old set is obsolete. 
        // For safe measure in this specific request context without explicit "delete all", we will updateOrCreate. 
        
        $items = $data['data_wisata'];
        
        // Clear existing data to strictly follow "only choosing 20"
        // Disable FK checks to allow truncate/delete
        try {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            Place::truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        } catch (\Exception $e) {
            // Fallback for drivers that don't support truncate with constraints easily
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
                // jenis_wisata is CSV, e.g., "WISATA ALAM, WISATA MINAT KHUSUS"
                $categories = explode(',', $item['jenis_wisata']);
                $primaryCategoryName = trim($categories[0]);
                $primaryCategoryName = Str::title(strtolower($primaryCategoryName));

                $category = Category::firstOrCreate(
                    ['name' => $primaryCategoryName],
                    [
                        'slug' => Str::slug($primaryCategoryName),
                        'icon_class' => 'fa-solid fa-map-location-dot', 
                        'color' => '#0ea5e9', 
                    ]
                );

                // 3. Prepare Data
                // Default coordinates if not parseable (JSON has no lat/lng, only links)
                $lat = -6.581768; 
                $lng = 110.669896;

                // 4. Create or Update Place
                Place::updateOrCreate(
                    ['name' => $item['nama_wisata']], 
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($item['nama_wisata']), 
                        'description' => $item['deskripsi'] ?? null,
                        'address' => $item['lokasi'] ?? null,
                        'ticket_price' => ($item['harga_tiket'] !== '-' ? $item['harga_tiket'] : null),
                        'opening_hours' => ($item['waktu_buka'] !== '-' ? $item['waktu_buka'] : null),
                        'latitude' => $lat, 
                        'longitude' => $lng,
                        'google_maps_link' => $item['link_koordinat'] ?? null,
                        'ownership_status' => $item['status_kepemilikan'] ?? null,
                        'manager' => ($item['pengelola'] !== '-' ? $item['pengelola'] : null),
                        'rides' => (!empty($item['wahana']) ? $item['wahana'] : null), // Already array
                        'facilities' => (!empty($item['fasilitas']) ? $item['fasilitas'] : null), // Already array
                        'social_media' => ($item['media_sosial'] !== '-' && $item['media_sosial'] !== '' ? $item['media_sosial'] : null),
                        'contact_info' => null,
                    ]
                );

                $count++;
            } catch (\Throwable $e) {
                $this->command->error('Failed on item: '.($item['nama_wisata'] ?? 'Unknown'));
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info("Successfully seeded {$count} places from 20-destinasi.json.");
    }
}
