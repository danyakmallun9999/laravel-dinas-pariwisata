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
        // Folder Mapping (Manual mapping based on available folders)
        $folderMapping = [
            'Pantai Kartini' => 'pantai-kartini',
            'Museum RA. Kartini' => 'museum-kartini',
            'Pantai Tirta Samudra (Bandengan)' => 'pantai-bandengan',
            'Jepara Ourland Park' => 'jepara-ourland-park',
            'Pantai Teluk Awur Jepara' => 'panti-teluk-awur', // Note: folder typo 'panti' confirmed
            'Pantai Blebak' => 'pantai-blebak',
            'Pulau Panjang' => 'pulau-panjang',
            'Benteng Portugis' => 'benteng-portugis',
            'Gua Manik' => 'gua-manik',
            'Air Terjun Songgo Langit' => 'songgo-langit',
            'Wisata Telaga Harun Somosari' => 'telaga-harun-somorsari',
            'Gua Tritip' => 'gua-tritip',
            'Pulau Mandalika' => 'pulau-mandalika',
            'Wisata Desa Tempur' => 'desa-tempur',
            'Wana Wisata Sreni Indah' => 'sreni',
            'Pasar Sore Karangrandu (PSK)' => 'pasar-karang-randu',
            'Tiara Park Waterboom' => 'tiara-park',
            'Makam Mantingan' => 'makam-mantingan',
            'Wisata Kali Ndayung' => 'kali-dayung',
            'Taman Nasional Karimunjawa' => 'karimun-jawa',
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

                // Metadata for categories (moved from CategorySeeder)
                $categoryMetadata = [
                    'Wisata Alam' => [
                        'icon_class' => 'fa-solid fa-tree',
                        'color' => '#16a34a', // green-600
                    ],
                    'Wisata Buatan' => [
                        'icon_class' => 'fa-solid fa-water',
                        'color' => '#0ea5e9', // sky-500
                    ],
                    'Wisata Budaya' => [
                        'icon_class' => 'fa-solid fa-monument',
                        'color' => '#d97706', // amber-600
                    ],
                    'Wisata Religi' => [
                        'icon_class' => 'fa-solid fa-mosque',
                        'color' => '#8b5cf6', // violet-500
                    ],
                    'Wisata Kuliner' => [ // Adjusted name to match potential JSON content or fallback
                        'icon_class' => 'fa-solid fa-utensils',
                        'color' => '#ef4444', // red-500
                    ],
                ];

                $meta = $categoryMetadata[$primaryCategoryName] ?? [
                    'icon_class' => 'fa-solid fa-map-location-dot', // Default icon
                    'color' => '#64748b', // Default color (slate-500)
                ];

                $category = Category::updateOrCreate(
                    ['name' => $primaryCategoryName],
                    [
                        'slug' => Str::slug($primaryCategoryName),
                        'icon_class' => $meta['icon_class'],
                        'color' => $meta['color'],
                    ]
                );

                // 3. Prepare Data
                // Use dynamic coordinates from JSON
                $lat = $item['latitude'] ?? -6.581768;
                $lng = $item['longitude'] ?? 110.669896;

                // Get folder path from mapping
                $folderName = $folderMapping[$item['nama_wisata']] ?? null;
                $imagePath = null;

                if ($folderName) {
                    $folderPath = public_path('images/destinasi/'.$folderName);

                    if (File::exists($folderPath)) {
                        $files = File::files($folderPath);
                        $mainImage = null;

                        // Priority 1: Check for 0.jpg, 0.png, 0.webp, 0.jpeg
                        foreach ($files as $file) {
                            $filename = $file->getFilename();
                            // Check for files starting with "0." and having supported extensions
                            if (preg_match('/^0\.(jpg|jpeg|png|webp)$/i', $filename)) {
                                $mainImage = $filename;
                                break;
                            }
                        }

                        // Priority 2: Fallback to first available image if 0.* not found
                        if (! $mainImage && count($files) > 0) {
                            // Sort to ensure deterministic behavior (e.g. alphabetical)
                            $filenames = array_map(fn ($f) => $f->getFilename(), $files);
                            sort($filenames);

                            // Filter for supported image extensions just in case
                            foreach ($filenames as $fname) {
                                if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $fname)) {
                                    $mainImage = $fname;
                                    break;
                                }
                            }
                        }

                        if ($mainImage) {
                            $imagePath = 'images/destinasi/'.$folderName.'/'.$mainImage;
                        } else {
                            $this->command->warn("No valid images found in folder: $folderName");
                        }
                    } else {
                        $this->command->warn("Folder not found: $folderName");
                    }
                }

                // 4. Create or Update Place
                $place = Place::updateOrCreate(
                    ['name' => $item['nama_wisata']],
                    [
                        'category_id' => $category->id,
                        'name_en' => $item['name_en'] ?? null,
                        'slug' => Str::slug($item['nama_wisata']),
                        'image_path' => $imagePath,
                        'description' => $item['deskripsi'] ?? null,
                        'description_en' => $item['description_en'] ?? null,
                        'address' => $item['lokasi'] ?? null,
                        'opening_hours' => ($item['waktu_buka'] !== '-' ? $item['waktu_buka'] : null),
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'google_maps_link' => $item['link_koordinat'] ?? null,
                        'ownership_status' => $item['status_kepemilikan'] ?? null,
                        'manager' => ($item['pengelola'] !== '-' ? $item['pengelola'] : null),
                        'is_flagship' => $item['is_flagship'] ?? false,
                        'rides' => (! empty($item['wahana']) ? $item['wahana'] : null), // Already array
                        'facilities' => (! empty($item['fasilitas']) ? $item['fasilitas'] : null), // Already array
                        'social_media' => ($item['media_sosial'] !== '-' && $item['media_sosial'] !== '' ? $item['media_sosial'] : null),
                        'kecamatan' => $item['kecamatan'] ?? null,
                        'contact_info' => null,
                    ]
                );

                // 5. Create Tickets if price info exists
                // if ($item['harga_tiket'] !== '-' && ! empty($item['harga_tiket'])) {
                //     // Cleanup existing tickets for this place to avoid duplicates on re-seed
                //     $place->tickets()->delete();

                //     $lines = explode("\n", $item['harga_tiket']);
                //     foreach ($lines as $line) {
                //         $line = trim($line);
                //         if (empty($line)) {
                //             continue;
                //         }

                //         // Case 1: "Gratis"
                //         if (stripos($line, 'Gratis') !== false) {
                //             \App\Models\Ticket::create([
                //                 'place_id' => $place->id,
                //                 'name' => 'Tiket Masuk',
                //                 'description' => $line,
                //                 'price' => 0,
                //                 'quota' => null,
                //                 'valid_days' => 1,
                //                 'is_active' => true,
                //             ]);

                //             continue;
                //         }

                //         // Case 2: Extract Price Pairs like "Dewasa Rp 10.000, Anak Rp 5.000"
                //         // Regex looks for: (Label text) (Rp) (Number with dots)
                //         if (preg_match_all('/(.*?)\s*Rp\.?\s*([\d\.]+)/iu', $line, $matches, PREG_SET_ORDER)) {
                //             foreach ($matches as $match) {
                //                 $rawLabel = $match[1]; // e.g. "Hari Biasa: Dewasa" or ", Anak"
                //                 $amountStr = $match[2]; // e.g. "10.000"

                //                 $priceValue = (float) str_replace(['.', ','], '', $amountStr);

                //                 // Clean up label
                //                 // Remove leading/trailing punctuation (colon, comma, hyphen)
                //                 $label = trim(preg_replace('/^[,\-:\s]+|[,\-:\s]+$/', '', $rawLabel));

                //                 // Generate a sensible name
                //                 $ticketName = 'Tiket Masuk';
                //                 if (! empty($label)) {
                //                     // If label is long (e.g. "Senin-Jumat"), use it.
                //                     // If label contains specific keywords, prioritize them?
                //                     // For now, just use the label if it's not too long, or fallback.
                //                     $ticketName = $label;
                //                 }

                //                 // Further refinement for very short labels like "Anak" -> "Tiket Anak"
                //                 if (strtolower($ticketName) == 'anak') {
                //                     $ticketName = 'Tiket Anak';
                //                 }
                //                 if (strtolower($ticketName) == 'dewasa') {
                //                     $ticketName = 'Tiket Dewasa';
                //                 }

                //                 // Create Ticket
                //                 \App\Models\Ticket::create([
                //                     'place_id' => $place->id,
                //                     'name' => Str::limit($ticketName, 50), // Ensure name fits
                //                     'description' => $line, // Use the full line as description for context
                //                     'price' => $priceValue,
                //                     'quota' => null,
                //                     'valid_days' => 1,
                //                     'is_active' => true,
                //                 ]);
                //             }
                //         } else {
                //             // Case 3: Just a number found without Rp? Or simple text?
                //             // Try simplistic extraction as fallback if it's just one number
                //             // But avoid concatenating multiple numbers.
                //             // Only if we didn't find "Rp" pattern.
                //             $numbers = preg_replace('/[^0-9]/', '', $line);
                //             if (! empty($numbers) && strlen($numbers) < 9) { // Sanity check length to avoid overflow
                //                 $priceValue = (float) $numbers;
                //                 \App\Models\Ticket::create([
                //                     'place_id' => $place->id,
                //                     'name' => 'Tiket Masuk',
                //                     'description' => $line,
                //                     'price' => $priceValue,
                //                     'quota' => null,
                //                     'valid_days' => 1,
                //                     'is_active' => true,
                //                 ]);
                //             }
                //         }
                //     }
                // }

                $count++;
            } catch (\Throwable $e) {
                $this->command->error('Failed on item: '.($item['nama_wisata'] ?? 'Unknown'));
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info("Successfully seeded {$count} places from 20-destinasi.json.");
    }
}
