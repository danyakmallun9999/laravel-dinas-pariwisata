
# 🗺️ Sistem Informasi Geografis Desa Mayong Lor

![Banner Project](/public/images/balaidesa.jpeg)

> **Platform pemetaan digital terintegrasi untuk transparansi data, pembangunan infrastruktur, dan pelayanan publik Desa Mayong Lor.**

## 📖 Tentang Project

**Mayonglor GIS** adalah aplikasi web berbasis *Geographic Information System* (GIS) yang dikembangkan untuk memetakan potensi, aset, dan infrastruktur Desa Mayong Lor secara digital.

Aplikasi ini memudahkan perangkat desa dalam mengelola data spasial (seperti batas wilayah, jalan, irigasi, dan penggunaan lahan) serta memberikan akses informasi yang transparan dan mudah diakses bagi masyarakat luas.

## 🛠️ Tech Stack

Project ini dibangun menggunakan teknologi modern untuk menjamin performa, keamanan, dan kemudahan pengembangan.

| Tech | Badge |
| --- | --- |
| **Framework** | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white) |
| **Language** | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white) |
| **Frontend** | ![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white) ![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white) |
| **Mapping** | ![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=leaflet&logoColor=white) |

## ✨ Fitur Utama

-   **🗺️ Peta Interaktif**: Jelajahi peta desa dengan tampilan satelit, *hybrid*, dan *terrain*.
-   **📍 Manajemen Kategori**: Pencarian lokasi berdasarkan kategori (Masjid, Sekolah, Balai Desa, dll).
-   **📏 Layer Spasial**:
    -   **Batas Wilayah**: Visualisasi batas RT/RW dan dusun.
    -   **Infrastruktur**: Pemetaan jalan, sungai, dan saluran irigasi.
    -   **Penggunaan Lahan**: Data sawah, perkebunan, dan pemukiman.
-   **📊 Dashboard Admin**: Panel khusus untuk mengelola data lokasi dan spasial secara visual.
-   **👥 Data Kependudukan**: Ringkasan statistik demografi desa.
-   **📄 Laporan**: Export data ke format CSV dan HTML.

## ⚙️ Prasyarat (Prerequisites)

Sebelum memulai, pastikan sistem Anda telah terinstal:

-   **PHP**: Versi 8.2 atau lebih baru.
-   **Composer**: Untuk manajemen dependensi PHP.
-   **Node.js & NPM**: Untuk *compile* aset frontend.
-   **MySQL**: Database server.

## 🚀 Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di komputer lokal Anda:

### 1. Clone Repository

```bash
git clone https://github.com/username/landing-page-mayonglor-gis.git
cd landing-page-mayonglor-gis
```

### 2. Jalankan Setup Otomatis

Project ini memiliki *script* setup otomatis yang akan:
- Menginstall dependensi PHP (Composer)
- Menyalin file konfigurasi `.env`
- Membuat *APP_KEY*
- Menjalankan migrasi database
- Menginstall & build dependensi frontend (NPM)

```bash
composer run setup
```

> **Catatan:** Pastikan Anda sudah membuat database kosong bernama `mayonglor_gis` (atau sesuaikan di `.env`) sebelum menjalankan perintah di atas.

### 3. Konfigurasi Database (Manual)

Jika setup otomatis gagal atau Anda ingin konfigurasi manual, edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mayonglor_gis
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Server Development

```bash
composer run dev
```
Perintah ini akan menjalankan server Laravel dan Vite secara bersamaan.

Akses aplikasi di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## 🔑 Akun Default

(Jika ada seeder user default, tambahkan di sini. Jika tidak, kosongkan atau beri instruksi cara buat user).

## 📦 Dependencies Utama

-   `laravel/framework`: Core framework.
-   `leaflet`: Library open-source untuk peta interaktif.
-   `intervention/image`: Manipulasi gambar (jika digunakan).
-   `maatwebsite/excel`: Export laporan (asumsi dari fitur export).

---

© 2025 Pemerintah Desa Mayong Lor. Dibuat dengan ❤️ untuk kemajuan desa.
