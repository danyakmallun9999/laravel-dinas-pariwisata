<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DestinasiImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = Place::all();
        $imageDir = public_path('images/destinasi');

        if (!File::exists($imageDir)) {
            $this->command->error("Directory not found: $imageDir");
            return;
        }

        $files = File::files($imageDir);
        $totalImages = 0;
        
        // Stop words to ignore in matching (too common)
        $stopWords = ['pantai', 'wisata', 'air', 'terjun', 'pulau', 'desa', 'jepara', 'kabupaten', 'gunung', 'bukit', 'taman', 'dan', 'di', 'ke'];

        foreach ($places as $place) {
            $matchedFiles = [];
            
            // 1. Prepare Place Keywords
            $placeSlug = Str::slug($place->name);
            $placeWords = explode('-', $placeSlug);
            $placeKeywords = array_diff($placeWords, $stopWords);

            foreach ($files as $file) {
                $filename = $file->getFilename();
                $filenameLower = strtolower($filename);
                $nameWithoutExt = pathinfo($filenameLower, PATHINFO_FILENAME);
                
                // 2. Prepare File Keywords
                $fileSlug = Str::slug($nameWithoutExt);
                $fileWords = explode('-', $fileSlug);
                $fileKeywords = array_diff($fileWords, $stopWords);

                // 3. Check for Intersection
                // Finds words that exist in BOTH the place name and the filename
                $intersection = array_intersect($placeKeywords, $fileKeywords);
                
                // Quality Check:
                // - If we have matched 'significant' words (not stop words)
                // - Special case: If the ONLY word is a stop word (e.g. "Pantai Jepara"), we might fallback, but for now strict.
                
                $isMatch = false;

                // Rule A: Exact Slug Containment (Strongest)
                // e.g. "benteng-portugis.jpg" in "benteng-portugis"
                if (str_contains($placeSlug, $fileSlug) || str_contains($fileSlug, $placeSlug)) {
                     $isMatch = true;
                }
                // Rule B: Significant Keywork Match
                // If we match at least 1 significant keyword (unique name identifier)
                elseif (count($intersection) >= 1) {
                    // Refinement: If the file only has 1 significant word, and it matches, it's a match.
                    // e.g. place "Pantai Bandengan" (bandengan) -> file "bandengan.jpg" (bandengan) -> Match
                    $isMatch = true;
                }

                if ($isMatch) {
                    $matchedFiles[] = $filename;
                }
            }

            if (!empty($matchedFiles)) {
                $this->command->info("Found " . count($matchedFiles) . " images for {$place->name}");
                
                $place->images()->delete();

                // Sort to be consistent
                sort($matchedFiles);

                // Set Main Image
                $place->image_path = 'images/destinasi/' . $matchedFiles[0];
                $place->save();

                // Populate Gallery
                foreach ($matchedFiles as $filename) {
                    $place->images()->create([
                        'image_path' => 'images/destinasi/' . $filename
                    ]);
                    $totalImages++;
                }

            } else {
                 // Try a desperate fallback: Check original slug start
                 // (In case users named files exactly after the OLD slugs)
                 // But most likely the keyword match covers this.
                 
                 // $this->command->warn("No images found for {$place->name} (Keywords: " . implode(',', $placeKeywords) . ")");
            }
        }

        $this->command->info("Seeding complete. Mapped $totalImages images.");
    }
}
