<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Support\Str;

class CulinarySeeder extends Seeder
{
    public function run()
    {
        // Ensure Category exists or use existing
        $category = Category::firstOrCreate(
            ['name' => 'Wisata Kuliner'],
            ['slug' => 'wisata-kuliner', 'description' => 'Wisata Kuliner Khas Jepara', 'icon_class' => 'restaurant']
        );

        $foods = [
            [
                'name' => 'Pindang Serani',
                'description' => "Sup ikan laut dengan kuah bening segar berbumbu belimbing wuluh dan rempah khas Jepara. \n\nRasanya yang segar, pedas, dan asam membuat hidangan ini sangat populer di kalangan wisatawan. Biasanya menggunakan ikan kakap, kerapu, atau bandeng.",
                'image_path' => 'images/kuliner-jppr/srani.png',
                'ticket_price' => 'Rp 15.000 - Rp 35.000',
            ],
            [
                'name' => 'Durian Jepara',
                'description' => "Raja buah lokal Petruk dari Jepara dengan daging tebal manis dan aroma menggoda. \n\nDurian Petruk asli Jepara dikenal karena dagingnya yang tebal, biji kecil, dan rasa manis yang legit dengan sedikit rasa pahit yang khas.",
                'image_path' => 'images/kuliner-jppr/duren.png',
                'ticket_price' => 'Rp 50.000 - Rp 150.000',
            ],
            [
                'name' => 'Adon-adon Coro',
                'description' => "Minuman jamu tradisional hangat berbahan santan, jahe, gula merah, dan rempah pilihan. \n\nMinuman ini sangat cocok dinikmati saat cuaca dingin atau malam hari untuk menghangatkan tubuh.",
                'image_path' => 'images/kuliner-jppr/adon-coro.png',
                'ticket_price' => 'Rp 4.000 - Rp 7.000',
            ],
            [
                'name' => 'Horog-horog',
                'description' => "Pengganti nasi unik bertekstur butiran kenyal, terbuat dari tepung pohon aren. \n\nHorog-horog biasanya disajikan sebagai pendamping sate, pecel, atau dimakan langsung dengan kelapa parut.",
                'image_path' => 'images/kuliner-jppr/horog.png',
                'ticket_price' => 'Rp 2.000 - Rp 5.000',
            ],
            [
                'name' => 'Carang Madu',
                'description' => "Kue oleh-oleh renyah berbentuk sarang madu dengan siraman gula merah manis. \n\nTeksturnya renyah dan rasanya manis, cocok sebagai camilan atau oleh-oleh untuk keluarga.",
                'image_path' => 'images/kuliner-jppr/carang-madu.png',
                'ticket_price' => 'Rp 10.000 - Rp 20.000',
            ],
            [
                'name' => 'Es Gempol Pleret',
                'description' => "Minuman es segar berisi gempol beras dan pleret tepung, disiram kuah santan dan sirup. \n\nRasanya manis dan gurih, dengan sensasi kenyal dari gempol dan pleret.",
                'image_path' => 'images/kuliner-jppr/gempol.png',
                'ticket_price' => 'Rp 5.000 - Rp 8.000',
            ],
            [
                'name' => 'Kopi Jeparanan',
                'description' => "Kopi robusta khas pegunungan Muria Jepara dengan aroma kuat dan cita rasa otentik. \n\nCocok bagi pecinta kopi yang menyukai karakter rasa yang bold dan body yang tebal.",
                'image_path' => 'images/kuliner-jppr/kopi.png',
                'ticket_price' => 'Rp 5.000 - Rp 15.000',
            ],
            [
                'name' => 'Kacang Listrik',
                'description' => "Kacang tanah sangrai unik yang dimatangkan dengan bantuan oven, gurih dan renyah. \n\nKacang ini tidak digoreng dengan minyak, sehingga lebih sehat dan renyah tahan lama.",
                'image_path' => 'images/kuliner-jppr/kcang.png',
                'ticket_price' => 'Rp 15.000 - Rp 30.000',
            ],
            [
                'name' => 'Krupuk Ikan Tengiri',
                'description' => "Kerupuk gurih dengan rasa ikan tengiri asli yang kuat, oleh-oleh wajib khas pesisir. \n\nSangat pas dijadikan pelengkap makan nasi atau sekadar camilan.",
                'image_path' => 'images/kuliner-jppr/krpktgr.png',
                'ticket_price' => 'Rp 15.000 - Rp 35.000',
            ],
        ];

        foreach ($foods as $food) {
            Place::updateOrCreate(
                ['slug' => Str::slug($food['name'])],
                [
                    'name' => $food['name'],
                    'category_id' => $category->id,
                    'description' => $food['description'],
                    'image_path' => $food['image_path'],
                    'ticket_price' => $food['ticket_price'],
                    'address' => 'Jepara',
                    'opening_hours' => '09:00 - 21:00',
                    'contact_info' => 'Tersedia di berbagai pusat oleh-oleh dan warung makan',
                    'rating' => 4.5,
                    'latitude' => -6.581761,
                    'longitude' => 110.678314,
                ]
            );
        }
    }
}
