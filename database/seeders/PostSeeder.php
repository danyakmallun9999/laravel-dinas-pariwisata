<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Festival Kartini 2026 Segera Digelar',
                'content' => 'Pemerintah Kabupaten Jepara akan kembali menggelar Festival Kartini pada bulan April mendatang. Acara ini akan dimeriahkan dengan kirab budaya, pameran UMKM, dan pentas seni tradisional.',
                'type' => 'event',
                'image_path' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=800&q=80',
                'published_at' => now()->subDays(2),
                'is_published' => true,
            ],
            [
                'title' => 'Jepara Raih Penghargaan Kota Wisata Bersih',
                'content' => 'Kabupaten Jepara kembali menorehkan prestasi membanggakan dengan meraih penghargaan sebagai Kota Wisata Bersih tingkat provinsi. Penghargaan ini menjadi motivasi untuk terus menjaga kebersihan destinasi wisata.',
                'type' => 'news',
                'image_path' => 'https://images.unsplash.com/photo-1542401886-65d6c61db217?auto=format&fit=crop&w=800&q=80',
                'published_at' => now()->subDays(5),
                'is_published' => true,
            ],
            [
                'title' => 'Pameran Seni Ukir Internasional',
                'content' => 'Jangan lewatkan Pameran Seni Ukir Internasional yang akan diadakan di Alun-alun Jepara. Menghadirkan karya terbaik dari pengrajin lokal dan mancanegara.',
                'type' => 'event',
                'image_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'published_at' => now()->addDays(10), // Future event
                'is_published' => true,
            ],
            [
                'title' => 'Revitalisasi Pantai Bandengan Selesai',
                'content' => 'Proyek revitalisasi fasilitas umum di Pantai Bandengan telah selesai dilaksanakan. Pengunjung kini dapat menikmati area parkir yang lebih luas dan toilet yang berstandar internasional.',
                'type' => 'news',
                'image_path' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
                'published_at' => now()->subWeeks(1),
                'is_published' => true,
            ],
        ];

        foreach ($posts as $post) {
            $post['slug'] = Str::slug($post['title']);
            Post::create($post);
        }
    }
}
