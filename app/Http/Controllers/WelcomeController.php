<?php

namespace App\Http\Controllers;

use App\Models\Boundary;
use App\Models\Category;
use App\Models\Infrastructure;
use App\Models\LandUse;
use App\Models\Place;
use Illuminate\Http\JsonResponse;

class WelcomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('places')->get();
        
        // Detailed Statistics
        $totalPlaces = Place::count();
        
        // Destinasi: Exclude Kuliner/Hotel to get true "Tourist Spots"
        $countDestinasi = Place::whereHas('category', function($q) {
            $q->whereNotIn('name', ['Kuliner', 'Hotel', 'Penginapan', 'Hotel & Penginapan']);
        })->count();

        // Kuliner Count
        $countKuliner = $categories->first(fn($c) => \Illuminate\Support\Str::contains($c->name, 'Kuliner', true))?->places_count ?? 0;

        // Event Count
        $countEvent = \App\Models\Event::count();

        // Desa Wisata / Wilayah
        $countDesa = Boundary::count();

        $totalCategories = $categories->count();
        $totalBoundaries = Boundary::count(); // Represents Dukuh/Wilayah count
        $totalArea = Boundary::sum('area_hectares');
        // $totalInfrastructures = Infrastructure::count();
        // $totalLandUses = LandUse::count();
        $lastUpdate = Place::latest('updated_at')->first()?->updated_at;
        // $population = \App\Models\Population::first();
        $places = \App\Models\Place::with('category')->latest()->take(6)->get();
        $posts = \App\Models\Post::where('is_published', true)->latest('published_at')->take(3)->get();

        // Specific Cultures Data (Requested by User)
        $cultures = [
            [
                'name' => 'Perang Obor',
                'slug' => 'perang-obor',
                'location' => 'Tegal Sambi',
                'description' => 'Tradisi unik perang api menggunakan obor dari pelepah kelapa kering. Dilakukan sebagai bentuk syukur dan tolak bala, mempertemukan keberanian dan keyakinan masyarakat Tegal Sambi.',
                'image' => 'images/culture/obor.png',
                'highlight' => 'Senin Pahing, Dzulhijjah'
            ],
            [
                'name' => 'Festival Kupat Lepet',
                'slug' => 'festival-kupat-lepet',
                'location' => 'Pantai Kartini',
                'description' => 'Tradisi gunungan kupat dan lepet yang menyemarakkan perayaan Syawalan. Simbol kebersamaan, saling memaafkan, dan rasa syukur masyarakat pesisir setelah berpuasa.',
                'image' => 'images/culture/festival-kupat-lepet.png',
                'highlight' => '8 Syawal (H+7 Lebaran)'
            ],
            [
                'name' => 'Kirab Buka Luwur',
                'slug' => 'kirab-buka-luwur',
                'location' => 'Makam Mantingan',
                'description' => 'Prosesi sakral penggantian kain penutup makam Ratu Kalinyamat dan Sultan Hadlirin. Diwarnai iring-iringan budaya dan doa bersama mengenang jasa leluhur Jepara.',
                'image' => 'images/culture/kirab-buka-luwur.png',
                'highlight' => '19 Jumadil Akhir'
            ],
            [
                'name' => 'Festival Jondang Kawak',
                'slug' => 'festival-jondang-kawak',
                'location' => 'Desa Kawak',
                'description' => 'Arak-arakan kotak kayu (jondang) berisi hasil bumi sebagai wujud syukur. Jondang dihias unik dan diarak keliling desa, melambangkan kemakmuran dan kerukunan warga.',
                'image' => 'images/culture/jondang-kawak.png',
                'highlight' => 'Kamis Kliwon, Dzulhijjah'
            ],
            [
                'name' => 'Barikan Kubro',
                'slug' => 'barikan-kubro',
                'location' => 'Karimunjawa',
                'description' => 'Ritual tolak bala dan syukur masyarakat Karimunjawa menjelang musim baratan. Ditandai dengan 9 tumpeng raksasa yang diarak ke laut dan alun-alun.',
                'image' => 'images/culture/barikan-kubro.png',
                'highlight' => 'Kamis Pon, Suro/Muharram'
            ],
            [
                'name' => 'Pesta Lomban',
                'slug' => 'pesta-lomban',
                'location' => 'Laut Jepara',
                'description' => 'Sedekah laut para nelayan yang telah melegenda. Dimeriahkan dengan larungan kepala kerbau dan perang laut sebagai ungkapan syukur atas rezeki bahari.',
                'image' => 'images/culture/lomban.png',
                'highlight' => '8 Syawal (Puncak Syawalan)'
            ]
        ];
        
        // Convert to object for consistency in view
        $cultures = json_decode(json_encode($cultures));

        // Culinary Data
        $culinaries = [
            [
                'name' => 'Pindang Serani',
                'slug' => 'pindang-serani',
                'description' => 'Sup ikan laut dengan kuah bening segar berbumbu belimbing wuluh dan rempah khas Jepara.',
                'image' => 'images/kuliner-jppr/srani.png',
                'full_description' => 'Pindang Serani adalah masakan khas Jepara berupa sup ikan laut. Rasanya merupakan perpaduan pedas, asam dan manis yang umumnya disajikan pada siang hari. Tidak seperti pindang ikan pada umumnya yang menggunakan kuah kecap, Pindang Serani memiliki kuah bening yang sangat segar. Bumbu utamanya adalah belimbing wuluh, daun kemangi, tomat, serai, dan cabai rawit.'
            ],
            [
                'name' => 'Durian Jepara',
                'slug' => 'durian-jepara',
                'description' => 'Raja buah lokal Petruk dari Jepara dengan daging tebal manis dan aroma menggoda.',
                'image' => 'images/kuliner-jppr/duren.png',
                'full_description' => 'Jepara terkenal sebagai salah satu penghasil durian terbaik di Jawa Tengah, khususnya varietas Durian Petruk. Karakteristik durian Jepara adalah daging buahnya yang tebal, biji cenderung kecil (kempes), rasa manis legit dengan sedikit pahit alkohol yang pas, serta aroma yang sangat menyengat menggoda. Musim rayanya biasanya terjadi pada akhir tahun hingga awal tahun.'
            ],
            [
                'name' => 'Adon-adon Coro',
                'slug' => 'adon-adon-coro',
                'description' => 'Minuman jamu tradisional hangat berbahan santan, jahe, gula merah, dan rempah pilihan.',
                'image' => 'images/kuliner-jppr/adon-coro.png',
                'full_description' => 'Adon-adon Coro adalah minuman tradisional khas Jepara yang sering disebut sebagai "Jamu"-nya orang Jepara. Minuman ini terbuat dari campuran rempah-rempah seperti jahe, kayu manis, cengkeh, lengkuas, merica bubuk, dan santan kelapa yang dimasak dengan gula merah. Rasanya hangat, pedas, dan manis, sangat cocok diminum saat cuaca dingin atau malam hari untuk menghangatkan badan.'
            ],
            [
                'name' => 'Horog-horog',
                'slug' => 'horog-horog',
                'description' => 'Pengganti nasi unik bertekstur butiran kenyal, terbuat dari tepung pohon aren.',
                'image' => 'images/kuliner-jppr/horog.png',
                'full_description' => 'Horog-horog adalah makanan pengganti nasi yang hanya bisa ditemui di Jepara. Terbuat dari tepung aren yang diolah sedemikian rupa hingga berbentuk butiran-butiran kecil berwarna putih dan bertekstur kenyal. Biasanya disajikan dengan parutan kelapa, gula pasir, atau dimakan bersama sate kikil, pecel, dan bakso. Makanan ini sangat legendaris dan menjadi identitas kuliner masyarakat Jepara.'
            ],
            [
                'name' => 'Carang Madu',
                'slug' => 'carang-madu',
                'description' => 'Kue oleh-oleh renyah berbentuk sarang madu dengan siraman gula merah manis.',
                'image' => 'images/kuliner-jppr/carang-madu.png',
                'full_description' => 'Carang Madu adalah jajanan tradisional atau oleh-oleh khas Jepara khususnya dari daerah Welahan. Kue ini terbuat dari adonan tepung beras, santan, dan telur yang digoreng membentuk sarang yang tidak beraturan menyerupai ranting bambu (carang), kemudian disiram dengan gula merah cair di atasnya. Rasanya renyah dan manis legit, sangat cocok dijadikan buah tangan.'
            ],
            [
                'name' => 'Es Gempol Pleret',
                'slug' => 'es-gempol-pleret',
                'description' => 'Minuman es segar berisi gempol beras dan pleret tepung, disiram kuah santan dan sirup.',
                'image' => 'images/kuliner-jppr/gempol.png',
                'full_description' => 'Es Gempol Pleret adalah minuman segar yang terdiri dari gempol (bulatan dari tepung beras) dan pleret (adonanan tepung beras yang dipipihkan). Keduanya disajikan dalam mangkuk dengan kuah santan encer dan sirup gula merah atau sirup frambozen, serta es batu. Rasanya gurih santan berpadu dengan manisnya sirup, sangat menyegarkan di tengah panasnya udara pesisir Jepara.'
            ],
            [
                'name' => 'Kopi Jeparanan',
                'slug' => 'kopi-jeparanan',
                'description' => 'Kopi robusta khas pegunungan Muria Jepara dengan aroma kuat dan cita rasa otentik.',
                'image' => 'images/kuliner-jppr/kopi.png',
                'full_description' => 'Kopi Jepara atau dikenal dengan Kopi Tempur dan Kopi Damarwulan berasal dari kawasan pegunungan Muria di wilayah Kabupaten Jepara. Kopi ini umumnya berjenis Robusta dengan aroma wangi yang khas, body yang tebal, dan acidity yang rendah. Pengolahan tradisional yang masih dipertahankan petani lokal memberikan cita rasa otentik yang berbeda dari kopi daerah lain.'
            ],
            [
                'name' => 'Kacang Listrik',
                'slug' => 'kacang-listrik',
                'description' => 'Kacang tanah sangrai unik yang dimatangkan dengan bantuan oven, gurih dan renyah.',
                'image' => 'images/kuliner-jppr/kcang.png',
                'full_description' => 'Kacang Listrik bukan berarti kacang yang bisa nyetrum, melainkan kacang tanah yang proses pematangannya menggunakan oven (tenaga listrik/pemanas) atau disangrai dengan pasir, bukan digoreng minyak. Hal ini membuat tekstur kacangnya sangat renyah, kering, tidak berminyak, dan gurih alami. Ini adalah salah satu camilan wajib saat berkunjung ke Jepara.'
            ],
            [
                'name' => 'Krupuk Ikan Tengiri',
                'slug' => 'krupuk-ikan-tengiri',
                'description' => 'Kerupuk gurih dengan rasa ikan tengiri asli yang kuat, oleh-oleh wajib khas pesisir.',
                'image' => 'images/kuliner-jppr/krpktgr.png',
                'full_description' => 'Sebagai daerah pesisir, Jepara terkenal dengan olahan hasil lautnya, salah satunya Krupuk Ikan Tengiri. Dibuat dari daging ikan tengiri asli yang dihaluskan dan dicampur tepung tapioka serta bumbu rempah. Rasanya sangat gurih dan terasa sekali ikannya, berbeda dengan kerupuk ikan biasa yang lebih banyak tepungnya. Sangat renyah dan cocok dijadikan pelengkap makan atau camilan.'
            ]
        ];
        
        $culinaries = json_decode(json_encode($culinaries));

        return view('welcome', compact(
            'categories', 
            'totalPlaces',
            'countDestinasi',
            'countKuliner',
            'countEvent',
            'countDesa',
            'totalCategories', 
            'totalBoundaries', 
            'totalArea',
            // 'totalInfrastructures', 
            // 'totalLandUses', 
            'lastUpdate', 
            // 'population',
            'places',
            'posts',
            'cultures',
            'culinaries'
        ));
    }

    public function showCulture($slug)
    {
        // Reuse the same data source (in a real app, this would be in a database)
        $cultures = [
            [
                'name' => 'Perang Obor',
                'slug' => 'perang-obor',
                'location' => 'Tegal Sambi',
                'description' => 'Tradisi unik perang api menggunakan obor dari pelepah kelapa kering. Dilakukan sebagai bentuk syukur dan tolak bala, mempertemukan keberanian dan keyakinan masyarakat Tegal Sambi.',
                'image' => 'images/culture/obor.png',
                'highlight' => 'Senin Pahing, Dzulhijjah',
                'full_description' => 'Perang Obor adalah tradisi sakral masyarakat Desa Tegal Sambi yang dilaksanakan setahun sekali pada malam Senin Pahing bulan Dzulhijjah (Besar). Tradisi ini bermula dari kisah Ki Gemblong dan Ki Babadan. Dalam upacara ini, para pemuda desa saling serang menggunakan obor yang terbuat dari gulungan pelepah kelapa kering dan daun pisang. Meskipun terlihat berbahaya dan seringkali menimbulkan luka bakar, warga percaya bahwa luka tersebut akan sembuh dengan sendirinya atau dengan minyak khusus ramuan leluhur. Tradisi ini dimaknai sebagai ungkapan rasa syukur kepada Tuhan YME atas melimpahnya hasil bumi dan ternak, serta sebagai tolak bala atau penolak kesialan dan penyakit bagi warga desa.'
            ],
            [
                'name' => 'Festival Kupat Lepet',
                'slug' => 'festival-kupat-lepet',
                'location' => 'Pantai Kartini',
                'description' => 'Tradisi gunungan kupat dan lepet yang menyemarakkan perayaan Syawalan. Simbol kebersamaan, saling memaafkan, dan rasa syukur masyarakat pesisir setelah berpuasa.',
                'image' => 'images/culture/festival-kupat-lepet.png',
                'highlight' => '8 Syawal (H+7 Lebaran)',
                'full_description' => 'Festival Kupat Lepet merupakan puncak perayaan tradisi Syawalan atau Lomban di Jepara, yang digelar seminggu setelah Hari Raya Idul Fitri (8 Syawal). Acara ini dipusatkan di kawasan Pantai Kartini. Gunungan raksasa yang terbuat dari ribuan ketupat dan lepet diarak dan kemudian diperebutkan oleh masyarakat. Kupat (Ketupat) menyimbolkan "Ngaku Lepat" (mengakui kesalahan), sedangkan Lepet menyimbolkan "Disilep ingkang Rapet" (kesalahan dikubur/ditutup rapat). Tradisi ini mengajarkan filosofi luhur tentang pentingnya saling memaafkan dan menjalin silaturahmi yang erat antar sesama.'
            ],
            [
                'name' => 'Kirab Buka Luwur',
                'slug' => 'kirab-buka-luwur',
                'location' => 'Makam Mantingan',
                'description' => 'Prosesi sakral penggantian kain penutup makam Ratu Kalinyamat dan Sultan Hadlirin. Diwarnai iring-iringan budaya dan doa bersama mengenang jasa leluhur Jepara.',
                'image' => 'images/culture/kirab-buka-luwur.png',
                'highlight' => '19 Jumadil Akhir',
                'full_description' => 'Kirab Buka Luwur adalah upacara adat penggantian kelambu (luwur) penutup makam Ratu Kalinyamat dan suaminya, Sultan Hadlirin, di kompleks Masjid dan Makam Mantingan. Dilaksanakan setiap tanggal 9 Apit/Dzulqa\'dah (namun seringkali disesuaikan dengan haul pada 19 Jumadil Akhir dalam konteks modern atau variasi lokal). Prosesi dimulai dengan kirab budaya yang menampilkan iring-iringan prajurit patang puluhan dan abdi dalem membawa luwur baru. Acara ini merupakan bentuk penghormatan tertinggi masyarakat Jepara kepada Ratu Kalinyamat, pahlawan nasional dan tokoh wanita legendaris yang membawa kejayaan maritim Jepara.'
            ],
            [
                'name' => 'Festival Jondang Kawak',
                'slug' => 'festival-jondang-kawak',
                'location' => 'Desa Kawak',
                'description' => 'Arak-arakan kotak kayu (jondang) berisi hasil bumi sebagai wujud syukur. Jondang dihias unik dan diarak keliling desa, melambangkan kemakmuran dan kerukunan warga.',
                'image' => 'images/culture/jondang-kawak.png',
                'highlight' => 'Kamis Kliwon, Dzulhijjah',
                'full_description' => 'Festival Jondang di Desa Kawak adalah tradisi sedekah bumi yang unik. Jondang sendiri adalah kotak kayu kuno yang biasanya digunakan untuk menyimpan harta benda atau hantaran lamaran. Dalam festival ini, Jondang diisi dengan aneka hasil bumi, makanan tradisional, dan tumpeng, kemudian diarak keliling desa menuju punden leluhur. Tradisi ini merupakan wujud syukur masyarakat Desa Kawak atas hasil panen yang melimpah dan doa untuk keselamatan desa. Festival ini juga menjadi ajang pelestarian gotong royong dan kerukunan antar warga.'
            ],
            [
                'name' => 'Barikan Kubro',
                'slug' => 'barikan-kubro',
                'location' => 'Karimunjawa',
                'description' => 'Ritual tolak bala dan syukur masyarakat Karimunjawa menjelang musim baratan. Ditandai dengan 9 tumpeng raksasa yang diarak ke laut dan alun-alun.',
                'image' => 'images/culture/barikan-kubro.png',
                'highlight' => 'Kamis Pon, Suro/Muharram',
                'full_description' => 'Barikan Kubro adalah tradisi besar masyarakat kepulauan Karimunjawa yang dilaksanakan di bulan Suro (Muharram), khususnya pada hari Kamis Wage atau Jumat Kliwon. "Barikan" bermakna barokah atau keselamatan. Ritual ini bertujuan memohon keselamatan kepada Tuhan YME dan menolak bala (bencana), terutama menghadapi musim angin baratan yang ombaknya besar. Masyarakat membuat tumpeng-tumpeng besar yang diarak menuju pelabuhan atau alun-alun untuk didoakan bersama, kemudian dimakan bersama-sama (kembul bujana). Sebagian sesaji juga dilarung ke laut sebagai simbol harmoni manusia dengan alam.'
            ],
            [
                'name' => 'Pesta Lomban',
                'slug' => 'pesta-lomban',
                'location' => 'Laut Jepara',
                'description' => 'Sedekah laut para nelayan yang telah melegenda. Dimeriahkan dengan larungan kepala kerbau dan perang laut sebagai ungkapan syukur atas rezeki bahari.',
                'image' => 'images/culture/lomban.png',
                'highlight' => '8 Syawal (Puncak Syawalan)',
                'full_description' => 'Pesta Lomban adalah "Lebaran"-nya masyarakat nelayan Jepara. Diadakan pada 8 Syawal, tradisi ini dimulai dengan pelarungan sesaji berupa kepala kerbau ke tengah lautan dari TPI Ujung Batu. Setelah prosesi pelarungan, ratusan kapal nelayan akan melakukan "perang laut" simbolis dengan saling melempar ketupat dan air. Lomban bermakna "lomba-lomba" atau bersenang-senang merayakan kemenangan pasca puasa, sekaligus ungkapan syukur nelayan atas rezeki dari laut. Acara dilanjutkan dengan makan bersama dan hiburan rakyat di Pantai Kartini.'
            ]
        ];

        $culture = collect($cultures)->firstWhere('slug', $slug);

        if (!$culture) {
            abort(404);
        }

        // Convert to object
        $culture = json_decode(json_encode($culture));

        return view('public.culture.show', compact('culture'));
    }

    public function showCulinary($slug)
    {
         // Culinary Data (Replicated for show method)
         $culinaries = [
            [
                'name' => 'Pindang Serani',
                'slug' => 'pindang-serani',
                'description' => 'Sup ikan laut dengan kuah bening segar berbumbu belimbing wuluh dan rempah khas Jepara.',
                'image' => 'images/kuliner-jppr/srani.png',
                'full_description' => 'Pindang Serani adalah masakan khas Jepara berupa sup ikan laut. Rasanya merupakan perpaduan pedas, asam dan manis yang umumnya disajikan pada siang hari. Tidak seperti pindang ikan pada umumnya yang menggunakan kuah kecap, Pindang Serani memiliki kuah bening yang sangat segar. Bumbu utamanya adalah belimbing wuluh, daun kemangi, tomat, serai, dan cabai rawit.'
            ],
            [
                'name' => 'Durian Jepara',
                'slug' => 'durian-jepara',
                'description' => 'Raja buah lokal Petruk dari Jepara dengan daging tebal manis dan aroma menggoda.',
                'image' => 'images/kuliner-jppr/duren.png',
                'full_description' => 'Jepara terkenal sebagai salah satu penghasil durian terbaik di Jawa Tengah, khususnya varietas Durian Petruk. Karakteristik durian Jepara adalah daging buahnya yang tebal, biji cenderung kecil (kempes), rasa manis legit dengan sedikit pahit alkohol yang pas, serta aroma yang sangat menyengat menggoda. Musim rayanya biasanya terjadi pada akhir tahun hingga awal tahun.'
            ],
            [
                'name' => 'Adon-adon Coro',
                'slug' => 'adon-adon-coro',
                'description' => 'Minuman jamu tradisional hangat berbahan santan, jahe, gula merah, dan rempah pilihan.',
                'image' => 'images/kuliner-jppr/adon-coro.png',
                'full_description' => 'Adon-adon Coro adalah minuman tradisional khas Jepara yang sering disebut sebagai "Jamu"-nya orang Jepara. Minuman ini terbuat dari campuran rempah-rempah seperti jahe, kayu manis, cengkeh, lengkuas, merica bubuk, dan santan kelapa yang dimasak dengan gula merah. Rasanya hangat, pedas, dan manis, sangat cocok diminum saat cuaca dingin atau malam hari untuk menghangatkan badan.'
            ],
            [
                'name' => 'Horog-horog',
                'slug' => 'horog-horog',
                'description' => 'Pengganti nasi unik bertekstur butiran kenyal, terbuat dari tepung pohon aren.',
                'image' => 'images/kuliner-jppr/horog.png',
                'full_description' => 'Horog-horog adalah makanan pengganti nasi yang hanya bisa ditemui di Jepara. Terbuat dari tepung aren yang diolah sedemikian rupa hingga berbentuk butiran-butiran kecil berwarna putih dan bertekstur kenyal. Biasanya disajikan dengan parutan kelapa, gula pasir, atau dimakan bersama sate kikil, pecel, dan bakso. Makanan ini sangat legendaris dan menjadi identitas kuliner masyarakat Jepara.'
            ],
            [
                'name' => 'Carang Madu',
                'slug' => 'carang-madu',
                'description' => 'Kue oleh-oleh renyah berbentuk sarang madu dengan siraman gula merah manis.',
                'image' => 'images/kuliner-jppr/carang-madu.png',
                'full_description' => 'Carang Madu adalah jajanan tradisional atau oleh-oleh khas Jepara khususnya dari daerah Welahan. Kue ini terbuat dari adonan tepung beras, santan, dan telur yang digoreng membentuk sarang yang tidak beraturan menyerupai ranting bambu (carang), kemudian disiram dengan gula merah cair di atasnya. Rasanya renyah dan manis legit, sangat cocok dijadikan buah tangan.'
            ],
            [
                'name' => 'Es Gempol Pleret',
                'slug' => 'es-gempol-pleret',
                'description' => 'Minuman es segar berisi gempol beras dan pleret tepung, disiram kuah santan dan sirup.',
                'image' => 'images/kuliner-jppr/gempol.png',
                'full_description' => 'Es Gempol Pleret adalah minuman segar yang terdiri dari gempol (bulatan dari tepung beras) dan pleret (adonanan tepung beras yang dipipihkan). Keduanya disajikan dalam mangkuk dengan kuah santan encer dan sirup gula merah atau sirup frambozen, serta es batu. Rasanya gurih santan berpadu dengan manisnya sirup, sangat menyegarkan di tengah panasnya udara pesisir Jepara.'
            ],
            [
                'name' => 'Kopi Jeparanan',
                'slug' => 'kopi-jeparanan',
                'description' => 'Kopi robusta khas pegunungan Muria Jepara dengan aroma kuat dan cita rasa otentik.',
                'image' => 'images/kuliner-jppr/kopi.png',
                'full_description' => 'Kopi Jepara atau dikenal dengan Kopi Tempur dan Kopi Damarwulan berasal dari kawasan pegunungan Muria di wilayah Kabupaten Jepara. Kopi ini umumnya berjenis Robusta dengan aroma wangi yang khas, body yang tebal, dan acidity yang rendah. Pengolahan tradisional yang masih dipertahankan petani lokal memberikan cita rasa otentik yang berbeda dari kopi daerah lain.'
            ],
            [
                'name' => 'Kacang Listrik',
                'slug' => 'kacang-listrik',
                'description' => 'Kacang tanah sangrai unik yang dimatangkan dengan bantuan oven, gurih dan renyah.',
                'image' => 'images/kuliner-jppr/kcang.png',
                'full_description' => 'Kacang Listrik bukan berarti kacang yang bisa nyetrum, melainkan kacang tanah yang proses pematangannya menggunakan oven (tenaga listrik/pemanas) atau disangrai dengan pasir, bukan digoreng minyak. Hal ini membuat tekstur kacangnya sangat renyah, kering, tidak berminyak, dan gurih alami. Ini adalah salah satu camilan wajib saat berkunjung ke Jepara.'
            ],
            [
                'name' => 'Krupuk Ikan Tengiri',
                'slug' => 'krupuk-ikan-tengiri',
                'description' => 'Kerupuk gurih dengan rasa ikan tengiri asli yang kuat, oleh-oleh wajib khas pesisir.',
                'image' => 'images/kuliner-jppr/krpktgr.png',
                'full_description' => 'Sebagai daerah pesisir, Jepara terkenal dengan olahan hasil lautnya, salah satunya Krupuk Ikan Tengiri. Dibuat dari daging ikan tengiri asli yang dihaluskan dan dicampur tepung tapioka serta bumbu rempah. Rasanya sangat gurih dan terasa sekali ikannya, berbeda dengan kerupuk ikan biasa yang lebih banyak tepungnya. Sangat renyah dan cocok dijadikan pelengkap makan atau camilan.'
            ]
        ];

        $culinary = collect($culinaries)->firstWhere('slug', $slug);

        if (!$culinary) {
            // Find similar items instead of just 404ing immediately could be an enhancement, but simple 404 for now
            abort(404);
        }

        $culinary = json_decode(json_encode($culinary));

        return view('public.culinary.show', compact('culinary'));
    }

    public function geoJson(): JsonResponse
    {
        $features = Place::with('category')
            ->get()
            ->map(function (Place $place) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $place->id,
                        'name' => $place->name,
                        'description' => $place->description,
                        'image_url' => $place->image_path ? asset($place->image_path) : null,
                        'ticket_price' => $place->ticket_price,
                        'opening_hours' => $place->opening_hours,
                        'contact_info' => $place->contact_info,
                        'rating' => $place->rating,
                        'website' => $place->website,
                        'category' => [
                            'id' => $place->category?->id,
                            'name' => $place->category?->name,
                            'color' => $place->category?->color,
                            'icon_class' => $place->category?->icon_class,
                        ],
                        'address' => $place->address,
                        'google_maps_link' => $place->google_maps_link,
                        'notes' => $place->notes,
                        'slug' => $place->slug,
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $place->longitude,
                            (float) $place->latitude,
                        ],
                    ],
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function boundariesGeoJson(): JsonResponse
    {
        $features = Boundary::all()
            ->map(function (Boundary $boundary) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $boundary->id,
                        'name' => $boundary->name,
                        'type' => $boundary->type,
                        'description' => $boundary->description,
                        'area_hectares' => $boundary->area_hectares,
                    ],
                    'geometry' => $boundary->geometry,
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }


    public function infrastructuresGeoJson(): JsonResponse
    {
        $features = Infrastructure::all()
            ->map(function (Infrastructure $infrastructure) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $infrastructure->id,
                        'name' => $infrastructure->name,
                        'type' => $infrastructure->type,
                        'length_meters' => $infrastructure->length_meters,
                        'width_meters' => $infrastructure->width_meters,
                        'condition' => $infrastructure->condition,
                        'description' => $infrastructure->description,
                    ],
                    'geometry' => $infrastructure->geometry,
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    public function landUsesGeoJson(): JsonResponse
    {
        $features = LandUse::all()
            ->map(function (LandUse $landUse) {
                return [
                    'type' => 'Feature',
                    'properties' => [
                        'id' => $landUse->id,
                        'name' => $landUse->name,
                        'type' => $landUse->type,
                        'area_hectares' => $landUse->area_hectares,
                        'owner' => $landUse->owner,
                        'description' => $landUse->description,
                    ],
                    'geometry' => $landUse->geometry,
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
    public function exploreMap()
    {
        $categories = Category::withCount('places')->get();
        $totalPlaces = Place::count();
        $totalBoundaries = Boundary::count();
        // $totalInfrastructures = Infrastructure::count();
        // $totalLandUses = LandUse::count();

        return view('explore-map', compact(
            'categories', 
            'totalPlaces', 
            'totalBoundaries', 
            // 'totalInfrastructures', 
            // 'totalLandUses'
        ));
    }

    public function posts()
    {
        $featuredPost = \App\Models\Post::where('is_published', true)
            ->latest('published_at')
            ->first();

        $posts = \App\Models\Post::where('is_published', true)
            ->where('id', '!=', $featuredPost?->id)
            ->latest('published_at')
            ->paginate(9);

        return view('public.posts.index', compact('featuredPost', 'posts'));
    }

    public function showPost(\App\Models\Post $post)
    {
        if (!$post->is_published) {
            abort(404);
        }

        $relatedPosts = \App\Models\Post::where('id', '!=', $post->id)
            ->where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        $recommendedPlaces = \App\Models\Place::inRandomOrder()
            ->take(3)
            ->get();

        return view('public.posts.show', compact('post', 'relatedPosts', 'recommendedPlaces'));
    }

    public function places()
    {
        $categories = \App\Models\Category::withCount('places')->get();
        $places = \App\Models\Place::with('category')->latest()->get();

        return view('public.places.index', compact('places', 'categories'));
    }

    public function showProduct(\App\Models\Product $product)
    {
        return view('public.products.show', compact('product'));
    }

    public function showPlace(\App\Models\Place $place)
    {
        return view('public.places.show', compact('place'));
    }

    public function searchPlaces(\Illuminate\Http\Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([]);
        }

        $places = Place::where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'slug', 'description', 'image_path')
            ->take(5)
            ->get()
            ->map(function ($place) {
                return [
                    'id' => $place->id,
                    'name' => $place->name,
                    'slug' => $place->slug,
                    'description' => \Illuminate\Support\Str::limit($place->description, 50),
                    'image_url' => $place->image_path ? asset($place->image_path) : null,
                    'type' => 'Lokasi'
                ];
            });

        return response()->json($places);
    }
}
