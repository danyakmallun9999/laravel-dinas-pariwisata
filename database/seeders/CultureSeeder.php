<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CultureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we load the ID language data
        app()->setLocale('id');
        $cultures = __('static_data.cultures');

        if (!is_array($cultures)) {
            $this->command->error('Static data not found or not an array.');
            return;
        }

        foreach ($cultures as $item) {
            \App\Models\Culture::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'category' => $item['category'],
                    'description' => $item['description'],
                    'content' => $item['full_description'] ?? $item['description'], // Fallback if full_description missing
                    'image' => $item['image'] ?? null,
                ]
            );
        }

        // Seed Culinaries as 'Kuliner Khas'
        $culinaries = __('static_data.culinaries');
        if (is_array($culinaries)) {
            foreach ($culinaries as $item) {
                \App\Models\Culture::updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'name' => $item['name'],
                        'category' => 'Kuliner Khas',
                        'description' => $item['description'],
                        'content' => $item['full_description'] ?? $item['description'],
                        'image' => $item['image'] ?? null,
                    ]
                );
            }
        }
        
        $this->command->info('Cultures seeded successfully.');
    }
}
