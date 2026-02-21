<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;
use App\Models\TravelAgency;

class FlagshipKarimunjawaSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. Pastikan destinasi Karimunjawa = unggulan
        // =============================================
        $karimun = Place::where('slug', 'taman-nasional-karimunjawa')->first();

        if ($karimun) {
            $karimun->update(['is_flagship' => true]);
        }

        // =============================================
        // 2. Data biro wisata dari registrasi resmi
        // =============================================
        $agencies = [
            [
                'name' => 'Eco Tour & Travel',
                'owner_name' => 'Ikke Ayu Safitri',
                'business_type' => 'Perorangan',
                'nib' => null,
                'address' => 'Karimunjawa RT 02 RW 03',
                'website' => 'https://ecotourtravel.id',
                'instagram' => '@paketwisatakarimunjawa',
                'description' => 'Biro wisata lokal Karimunjawa yang menyediakan layanan tur dan travel untuk menjelajahi keindahan kepulauan.',
            ],
            [
                'name' => 'Harmoni Karimunjawa Tour',
                'owner_name' => 'Tri Pramono',
                'business_type' => 'Perorangan',
                'nib' => '2201260077263',
                'address' => 'Jl. Kapuran RT 04 RW 01, Karimunjawa',
                'website' => 'https://www.harmonikarimunjawa.com',
                'instagram' => '@harmoni.karimunjawa',
                'description' => 'Biro wisata terpercaya dengan pengalaman bertahun-tahun melayani wisatawan Karimunjawa.',
            ],
            [
                'name' => 'Arsyila Tour dan Travel',
                'owner_name' => 'Muchamad Rizki Raharjo',
                'business_type' => 'CV',
                'nib' => '1211000121011',
                'address' => 'Jl. Maisan RT 05/RW 02, Ds. Kemujan, Kec. Karimunjawa',
                'website' => 'https://www.arsyilatours.com',
                'instagram' => '@arsyilatours',
                'description' => 'CV Arsyila Tours menyediakan paket wisata lengkap untuk Karimunjawa, dari snorkeling hingga island hopping.',
            ],
            [
                'name' => 'Ayokarimun',
                'owner_name' => 'Irkham Irfana',
                'business_type' => 'PT Perorangan',
                'nib' => '12.550.053.8-516.000',
                'address' => 'Karimunjawa RT 01 RW 02, Kec. Karimunjawa, Kab. Jepara',
                'website' => 'https://www.ayokarimun.com',
                'instagram' => '@ayokarimun_official',
                'description' => 'Platform resmi wisata Karimunjawa yang menawarkan berbagai paket liburan terbaik.',
            ],
            [
                'name' => 'Karimunjawa Everyday Trip',
                'owner_name' => 'Anang Prasetyo Anto',
                'business_type' => 'Perorangan',
                'nib' => null,
                'address' => 'Jl. Kapuran, Karimunjawa',
                'website' => null,
                'instagram' => '@karimunjawaeverydaytrip',
                'description' => 'Layanan perjalanan harian di Karimunjawa untuk pengalaman wisata yang fleksibel.',
            ],
            [
                'name' => 'Nautika Karimunjawa Tour and Travel',
                'owner_name' => 'Bagus Gunawan',
                'business_type' => 'PT Perorangan',
                'nib' => '2610240028294',
                'address' => 'Jl. Slamet Riyadi Gang Kenanga RT 03/01, Karimunjawa',
                'website' => 'https://nautikakarimunjawa.com',
                'instagram' => '@paket_tour_karimunjawa',
                'description' => 'Nautika Karimunjawa menyediakan paket tur laut dan darat untuk menjelajahi kepulauan.',
            ],
            [
                'name' => 'Trip Jelajah Karimunjawa',
                'owner_name' => 'Zaenal Wafa',
                'business_type' => 'Perorangan',
                'nib' => '2906230020258',
                'address' => 'Homestay Gemilang, Jl. Diponegoro RT 01 RW 02, Ds. Karimunjawa',
                'website' => null,
                'instagram' => '@tripjelajahkarimunjawa',
                'description' => 'Biro wisata lokal yang mengutamakan pengalaman jelajah autentik di Karimunjawa.',
            ],
            [
                'name' => 'Awesome Karimunjawa',
                'owner_name' => 'Muksin',
                'business_type' => 'Perorangan',
                'nib' => '0107230039621',
                'address' => 'Kemujan RT 01 RW 03',
                'website' => 'https://www.awesomekarimunjawa.com',
                'instagram' => '@awesomekarimunjawa',
                'description' => 'Awesome Karimunjawa menawarkan pengalaman wisata bahari yang tak terlupakan.',
            ],
            [
                'name' => 'Asok Karimunjawa Tour',
                'owner_name' => 'Budi Santoso',
                'business_type' => 'Perorangan',
                'nib' => '2210220012545',
                'address' => 'Jl. Danyang Joyo RT 04 RW 01, Karimunjawa, Kab. Jepara',
                'website' => null,
                'instagram' => '@asokkarimunjawa',
                'description' => 'Asok Karimunjawa Tour melayani paket wisata dengan harga terjangkau di Karimunjawa.',
            ],
            [
                'name' => 'Love Karimunjawa Tour',
                'owner_name' => 'Puji Supriyatno',
                'business_type' => 'Perorangan',
                'nib' => null,
                'address' => 'Desa Karimunjawa RT 03 RW 02',
                'website' => null,
                'instagram' => '@lovekarimunjawatour',
                'description' => 'Love Karimunjawa Tour menawarkan perjalanan wisata romantis dan keluarga di Karimunjawa.',
            ],
            [
                'name' => 'Karjaw Tour & Travel',
                'owner_name' => 'Hasanudin',
                'business_type' => 'CV',
                'nib' => '1606230001069',
                'address' => 'RT 02/RW 01, Ds. Karimunjawa, Kab. Jepara',
                'website' => 'https://www.karjaw.com',
                'instagram' => '@karjaw',
                'description' => 'CV Karjaw Tour & Travel menyediakan layanan perjalanan wisata profesional di Karimunjawa.',
            ],
            [
                'name' => 'Samudra Karimunjawa',
                'owner_name' => 'Ubaidillah',
                'business_type' => 'Perorangan',
                'nib' => '0904220017564',
                'address' => 'Karimunjawa RT 01/RW 03',
                'website' => null,
                'instagram' => '@samudrakarimunjawa',
                'description' => 'Samudra Karimunjawa mengajak Anda menjelajahi keindahan laut Karimunjawa.',
            ],
            [
                'name' => 'Blak Karimunjawa',
                'owner_name' => 'Nursalilis',
                'business_type' => 'PT Perorangan',
                'nib' => '1503250016358',
                'address' => 'Jl. Sunan Nyamplungan KM 6, Karimunjawa',
                'website' => 'https://krimon.id',
                'instagram' => '@blakkarimunjawa',
                'description' => 'Blak Karimunjawa menyediakan pengalaman wisata alam dan budaya pulau.',
            ],
            [
                'name' => 'Karimunjawa.co.id',
                'owner_name' => 'Sidra Febrian Hardiyanto',
                'business_type' => 'CV',
                'nib' => '8120114270859',
                'address' => 'Jl. Pemuda No. 5, Karimunjawa, Kab. Jepara',
                'website' => 'https://karimunjawa.co.id',
                'instagram' => '@karimunjawa.co.id',
                'description' => 'Portal resmi wisata Karimunjawa yang menyediakan informasi lengkap dan paket wisata.',
            ],
            [
                'name' => 'Ailana Trip',
                'owner_name' => 'Jessica Enki Van Thiel',
                'business_type' => 'Perorangan',
                'nib' => '1107230002404',
                'address' => 'Jl. Slamet Riyadi RT 02, Karimunjawa',
                'website' => 'https://ailanatrip.com',
                'instagram' => '@ailanatripkarimunjawa',
                'description' => 'Ailana Trip menawarkan perjalanan wisata unik dan personal di Karimunjawa.',
            ],
            [
                'name' => 'Karimunjawa Paket',
                'owner_name' => 'Sudarmono',
                'business_type' => 'Perorangan',
                'nib' => '0207230033592',
                'address' => 'Jl. I.J. Kasimo RT 001/RW 004, Ds. Karimunjawa, Kec. Karimunjawa, Kab. Jepara',
                'website' => 'https://karimunjawapaket.com',
                'instagram' => '@karimunjawapaketwisata',
                'description' => 'Karimunjawa Paket menyediakan paket wisata lengkap dengan harga bersaing.',
            ],
            [
                'name' => 'Jagodolan Tour dan Travel',
                'owner_name' => 'Tri Yuli Hardyanto',
                'business_type' => 'Perorangan',
                'nib' => '0220001871176',
                'address' => 'Kemujan RT 005 RW 002',
                'website' => 'https://jagodolan.com',
                'instagram' => '@jagodolantour',
                'description' => 'Jagodolan Tour dan Travel menghadirkan paket wisata Karimunjawa yang berkesan.',
            ],
            [
                'name' => 'Putra Karimunjawa Tour',
                'owner_name' => 'Fatkhul Nakif',
                'business_type' => 'Perorangan',
                'nib' => '2206230003454',
                'address' => 'Jl. KH Ahmad Dahlan/Lego RT 04/RW 03, Karimunjawa, Kab. Jepara',
                'website' => 'https://www.putrakarimunjawa.com',
                'instagram' => '@putrakarimunjawatour',
                'description' => 'Putra Karimunjawa Tour melayani perjalanan wisata bahari dengan panduan lokal berpengalaman.',
            ],
            [
                'name' => 'Nemo Karimunjawa',
                'owner_name' => 'Taofik Maliki',
                'business_type' => 'Perorangan',
                'nib' => '1107230058048',
                'address' => 'Karimunjawa RT 03 RW 02',
                'website' => null,
                'instagram' => '@nemokarimunjawa',
                'description' => 'Nemo Karimunjawa menawarkan petualangan snorkeling dan diving di spot terbaik.',
            ],
            [
                'name' => 'Batu Putih Adventure',
                'owner_name' => 'Ahmad Firdaus',
                'business_type' => 'CV',
                'nib' => '0209220068401',
                'address' => 'Nyamplungan RT 02 RW 05, Karimunjawa',
                'website' => 'https://batuputihadventure.com',
                'instagram' => '@batuputihadventure',
                'description' => 'CV Batu Putih Adventure menyediakan paket wisata petualangan di Karimunjawa.',
            ],
            [
                'name' => 'Ekowisata Karimunjawa Tour',
                'owner_name' => 'Dhian Pramono Sakty',
                'business_type' => 'Perorangan',
                'nib' => '0107230006938',
                'address' => 'Jl. Pemuda RT 01/RW 02, Karimunjawa',
                'website' => 'https://www.ekowisatakarimunjawa.com',
                'instagram' => '@ekowisatakarimunjawa',
                'description' => 'Ekowisata Karimunjawa Tour mengutamakan wisata ramah lingkungan dan berkelanjutan.',
            ],
            [
                'name' => 'Spot Karimunjawa Tour and Travel',
                'owner_name' => 'Muhammad Ikbal Husni',
                'business_type' => 'Perorangan',
                'nib' => '8120013152549',
                'address' => 'Karimunjawa RT 002 RW 001, Kec. Karimunjawa, Kab. Jepara',
                'website' => 'https://www.spotkarimunjawa.com',
                'instagram' => '@spotkarimunjawa',
                'description' => 'Spot Karimunjawa Tour and Travel membantu Anda menemukan spot-spot terbaik di Karimunjawa.',
            ],
        ];

        // =============================================
        // 3. Insert/Update semua biro dan link ke Karimunjawa
        // =============================================
        foreach ($agencies as $data) {
            $agency = TravelAgency::updateOrCreate(
                ['name' => $data['name']],
                $data
            );

            // Auto-link ke destinasi Karimunjawa
            if ($karimun) {
                $agency->places()->syncWithoutDetaching([$karimun->id]);
            }
        }

        // Hapus 3 biro sample lama jika ada
        TravelAgency::whereIn('name', [
            'Karimun Explore',
            'Jepara Ocean Tour',
            'Nusantara Trip',
        ])->each(function ($agency) {
            $agency->places()->detach();
            $agency->delete();
        });
    }
}
