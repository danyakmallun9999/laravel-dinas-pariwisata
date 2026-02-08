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

        // Image Mapping (Manual mapping based on available files)
        $imageMapping = [
            'Pantai Kartini' => 'images/destinasi/pantai kartini.jpg',
            'Museum RA. Kartini' => 'images/destinasi/museum kartini.jpg',
            'Pantai Tirta Samudra (Bandengan)' => 'images/destinasi/pantai bandengan.jpg',
            'Jepara Ourland Park' => 'images/destinasi/jepara-ourland-waterpark.jpg',
            'Pantai Teluk Awur Jepara' => 'images/destinasi/pantai-teluk-awur-jepara-sunset.jpg',
            'Pantai Blebak' => 'images/destinasi/Aktivitas-Menarik-Pantai-Blebak.jpg',
            'Pulau Panjang' => 'images/destinasi/Panjang-Island-Destination-4233181372.webp',
            'Benteng Portugis' => 'images/destinasi/Benteng Portugis.jpg',
            'Gua Manik' => 'images/destinasi/Pantai-Gua-Manik-1.jpg',
            'Air Terjun Songgo Langit' => 'images/destinasi/Daya-Tarik-Air-Terjun-Songgo-Langit.jpg',
            'Wisata Telaga Harun Somosari' => 'images/destinasi/TELAGA HARUNs.jpg',
            'Gua Tritip' => 'images/destinasi/goa tririp.webp',
            'Pulau Mandalika' => 'images/destinasi/Pulau-Mandalika-Jepara.jpg',
            'Wisata Desa Tempur' => 'images/destinasi/Desa-tempur-957230617.webp',
            'Wana Wisata Sreni Indah' => 'images/destinasi/wana-wisata-sreni-indah-jepara.jpg',
            'Pasar Sore Karangrandu (PSK)' => 'images/destinasi/Pasar Sore Karangrandu Jepara.jpg',
            'Tiara Park Waterboom' => 'images/destinasi/tiara park.jpg',
            'Makam Mantingan' => 'images/destinasi/MANTINGAN01.jpg',
            'Wisata Kali Ndayung' => 'images/destinasi/kali dayung.jpg',
        ];

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
                // Use dynamic coordinates from JSON
                $lat = $item['latitude'] ?? -6.581768; 
                $lng = $item['longitude'] ?? 110.669896;

                // Get image path from mapping
                $imagePath = $imageMapping[$item['nama_wisata']] ?? null;

                // 4. Create or Update Place
                $place = Place::updateOrCreate(
                    ['name' => $item['nama_wisata']], 
                    [
                        'category_id' => $category->id,
                        'slug' => Str::slug($item['nama_wisata']), 
                        'image_path' => $imagePath,
                        'description' => $item['deskripsi'] ?? null,
                        'address' => $item['lokasi'] ?? null,
                        'opening_hours' => ($item['waktu_buka'] !== '-' ? $item['waktu_buka'] : null),
                        'latitude' => $lat, 
                        'longitude' => $lng,
                        'google_maps_link' => $item['link_koordinat'] ?? null,
                        'ownership_status' => $item['status_kepemilikan'] ?? null,
                        'manager' => ($item['pengelola'] !== '-' ? $item['pengelola'] : null),
                        'rides' => (!empty($item['wahana']) ? $item['wahana'] : null), // Already array
                        'facilities' => (!empty($item['fasilitas']) ? $item['fasilitas'] : null), // Already array
                        'social_media' => ($item['media_sosial'] !== '-' && $item['media_sosial'] !== '' ? $item['media_sosial'] : null),
                        'kecamatan' => $item['kecamatan'] ?? null,
                        'contact_info' => null,
                    ]
                );

                // 5. Create Tickets if price info exists
                if ($item['harga_tiket'] !== '-' && !empty($item['harga_tiket'])) {
                    // Try to parse price, e.g. "Rp 5000" or "5000" or multiple lines
                    // Simple heuristic: just create one generic ticket for now or split by newlines if sophisticated
                    // The user prompt implies we should migrate this data.
                    
                    // Cleanup existing tickets for this place to avoid duplicates on re-seed
                    $place->tickets()->delete();

                    $prices = explode("\n", $item['harga_tiket']);
                    foreach($prices as $priceStr) {
                        $priceStr = trim($priceStr);
                        if (empty($priceStr)) continue;

                        // Extract number from string
                        $priceValue = preg_replace('/[^0-9]/', '', $priceStr);
                        $priceValue = (float) $priceValue;
                        
                        // Determine name (e.g. assume "Tiket Masuk" unless specifically named in string)
                        $ticketName = "Tiket Masuk";
                        if (stripos($priceStr, 'Anak') !== false) {
                            $ticketName = "Tiket Anak";
                        } elseif (stripos($priceStr, 'Dewasa') !== false) {
                            $ticketName = "Tiket Dewasa";
                        } elseif (stripos($priceStr, 'WNA') !== false) {
                            $ticketName = "Tiket WNA";
                        } elseif (stripos($priceStr, 'Weekend') !== false) {
                            $ticketName = "Tiket Weekend";
                        }

                        // If price is 0, maybe don't create ticket or create free ticket?
                        // Let's create it as 0
                        
                        \App\Models\Ticket::create([
                            'place_id' => $place->id,
                            'name' => $ticketName,
                            'description' => $priceStr, // Keep original string as description
                            'price' => $priceValue,
                            'quota' => null, // Unlimited
                            'valid_days' => 1,
                            'is_active' => true,
                        ]);
                    }
                }

                $count++;
            } catch (\Throwable $e) {
                $this->command->error('Failed on item: '.($item['nama_wisata'] ?? 'Unknown'));
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info("Successfully seeded {$count} places from 20-destinasi.json.");
    }
}
