<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Wisata Alam',
                'icon_class' => 'fa-solid fa-tree',
                'color' => '#16a34a', // green-600
            ],
            [
                'name' => 'Wisata Buatan',
                'icon_class' => 'fa-solid fa-water',
                'color' => '#0ea5e9', // sky-500
            ],
            [
                'name' => 'Wisata Budaya',
                'icon_class' => 'fa-solid fa-monument',
                'color' => '#d97706', // amber-600
            ],
            [
                'name' => 'Wisata Religi',
                'icon_class' => 'fa-solid fa-mosque',
                'color' => '#8b5cf6', // violet-500
            ],
            [
                'name' => 'Kuliner',
                'icon_class' => 'fa-solid fa-utensils',
                'color' => '#ef4444', // red-500
            ],
            [
                'name' => 'Penginapan',
                'icon_class' => 'fa-solid fa-bed',
                'color' => '#6366f1', // indigo-500
            ],
            [
                'name' => 'Belanja & Oleh-oleh',
                'icon_class' => 'fa-solid fa-bag-shopping',
                'color' => '#ec4899', // pink-500
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'icon_class' => $cat['icon_class'],
                    'color' => $cat['color'],
                ]
            );
        }
        
        $this->command->info('Default categories seeded.');
    }
}
