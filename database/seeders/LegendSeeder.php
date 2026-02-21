<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legends = [
            [
                'name' => 'Ratu Shima',
                'image' => 'images/legenda/shima.jpg',
                'quote_id' => 'Keadilan Tanpa Pandang Bulu',
                'quote_en' => 'Justice Without Prejudice',
                'description_id' => 'Penguasa Kerajaan Kalingga yang termasyhur akan ketegasan hukumnya. Simbol integritas dan keadilan sejati dari masa lampau.',
                'description_en' => 'Ruler of the Kalingga Kingdom renowned for her strict laws. A symbol of integrity and true justice from the past.',
                'order' => 0,
            ],
            [
                'name' => 'Ratu Kalinyamat',
                'image' => 'images/legenda/kalinyamat.jpg',
                'quote_id' => 'Sang Ratu Laut yang Gagah Berani',
                'quote_en' => 'The Brave Queen of the Sea',
                'description_id' => 'Penguasa maritim Nusantara yang disegani. Membangun Jepara menjadi pusat niaga dan kekuatan laut yang tak tertandingi.',
                'description_en' => 'A respected maritime ruler of the Archipelago. Built Jepara into an unrivaled trade center and naval power.',
                'order' => 1,
            ],
            [
                'name' => 'R.A. Kartini',
                'image' => 'images/legenda/kartini.jpg',
                'quote_id' => 'Habis Gelap Terbitlah Terang',
                'quote_en' => 'After Darkness Comes Light',
                'description_id' => 'Pahlawan emansipasi yang memperjuangkan hak pendidikan wanita. Sosoknya menginspirasi perubahan besar dari Jepara untuk Indonesia.',
                'description_en' => 'Emancipation hero who fought for women\'s education rights. Her figure inspires great change from Jepara for Indonesia.',
                'order' => 2,
            ],
        ];

        foreach ($legends as $legend) {
            \App\Models\Legend::create($legend);
        }
    }
}
