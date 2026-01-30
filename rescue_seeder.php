<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Place;
use Illuminate\Support\Str;

echo "Starting Manual Seeding (Adding Lomban)...\n";

try {
    // Category
    $category = Category::firstOrCreate(
        ['name' => 'Kebudayaan'],
        ['slug' => 'kebudayaan', 'description' => 'Jelajahi kekayaan tradisi, sejarah, dan festival budaya Jepara', 'icon_class' => 'festival']
    );
    echo "Category ID: " . $category->id . "\n";

    $cultures = [
        [
            'name' => 'Perang Obor Tegal Sambi',
            'description' => "Tradisi ritual sakral tolak bala yang digelar setahun sekali di Desa Tegal Sambi. \n\nPara pemuda desa saling memukulkan obor dari pelepah kelapa kering yang menyala, menciptakan atraksi api yang dramatis namun dipercaya membawa berkah dan kesehatan.",
            'image_path' => 'images/culture/perang-obor.jpg',
            'ticket_price' => 'Gratis',
            'address' => 'Desa Tegal Sambi, Tahunan',
            'lat' => -6.619000, 
            'lng' => 110.665000,
        ],
        [
            'name' => 'Festival Kupat Lepet',
            'description' => "Puncak perayaan Syawalan (H+7 Lebaran) di Pantai Kartini. \n\nDiwarnai dengan arak-arakan gunungan kupat dan lepet yang kemudian diperebutkan oleh masyarakat sebagai simbol saling memaafkan dan menutup kesalahan masa lalu.",
            'image_path' => 'images/culture/festival-kupat-lepet.jpg',
            'ticket_price' => 'Gratis (Tiket Masuk Pantai)',
            'address' => 'Pantai Kartini, Jepara',
            'lat' => -6.583333,
            'lng' => 110.650000,
        ],
        [
            'name' => 'Kirab Buka Luwur Mantingan',
            'description' => "Upacara penggantian kelambu (luwur) Makam Sultan Hadlirin dan Ratu Kalinyamat. \n\nProsesi kirab budaya membawa luwur baru diiringi sholawat dan kesenian tradisional, menjadi wujud penghormatan kepada leluhur Jepara.",
            'image_path' => 'images/culture/buka-luwur.jpg',
            'ticket_price' => 'Gratis',
            'address' => 'Masjid Mantingan, Tahunan',
            'lat' => -6.611599,
            'lng' => 110.686522,
        ],
        [
            'name' => 'Festival Jondang Kawak',
            'description' => "Tradisi sedekah bumi unik di Desa Kawak di mana warga mengarak 'Jondang' (kotak kayu ukir berisi hasil bumi) keliling desa. \n\nMenampilkan semangat gotong royong dan rasa syukur masyarakat atas kelimpahan panen.",
            'image_path' => 'images/culture/jondang-kawak.jpg',
            'ticket_price' => 'Gratis',
            'address' => 'Desa Kawak, Pakis Aji',
            'lat' => -6.550000,
            'lng' => 110.750000,
        ],
        [
            'name' => 'Barikan Kubro Karimun Jawa',
            'description' => "Ritual tolak bala dan sedekah laut masyarakat kepulauan Karimunjawa. \n\nDiwarnai dengan pelepasan tumpeng kecil ke laut dan doa bersama lintas agama, mencerminkan harmoni manusia dengan alam dan Sang Pencipta.",
            'image_path' => 'images/culture/barikan-kubro.jpg',
            'ticket_price' => 'Gratis',
            'address' => 'Alun-alun Karimunjawa',
            'lat' => -5.846500,
            'lng' => 110.428500,
        ],
        [
            'name' => 'Pesta Lomban',
            'description' => "Pesta laut tradisional nelayan Jepara yang diadakan sepekan setelah Idul Fitri (Syawalan). \n\nPuncak acara adalah pelarungan kepala kerbau ke laut sebagai wujud syukur, diikuti dengan perang ketupat di laut dan pesta rakyat.",
            'image_path' => 'images/culture/pesta-lomban.jpg',
            'ticket_price' => 'Gratis',
            'address' => 'TPI Ujungbatu / Pantai Kartini',
            'lat' => -6.587000,
            'lng' => 110.660000,
        ]
    ];

    foreach ($cultures as $item) {
        $place = Place::updateOrCreate(
            ['slug' => Str::slug($item['name'])],
            [
                'name' => $item['name'],
                'category_id' => $category->id,
                'description' => $item['description'],
                'image_path' => $item['image_path'],
                'ticket_price' => $item['ticket_price'],
                'address' => $item['address'],
                'latitude' => $item['lat'],
                'longitude' => $item['lng'],
                'opening_hours' => 'Event Tahunan / Syawalan',
                'contact_info' => 'Dinas Pariwisata dan Kebudayaan Jepara',
                'rating' => 4.9
            ]
        );
        echo "Seeded: " . $item['name'] . " (ID: " . $place->id . ")\n";
    }

    echo "Seeding Complete!\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
