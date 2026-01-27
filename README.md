# 🏝️ Pesona Jepara - Portal Resmi Pariwisata

![Banner Project](/public/images/landing-page.png)

> **Platform digital terintegrasi untuk promosi pariwisata, ekonomi kreatif, dan informasi publik Kabupaten Jepara.**

---

## 📖 Tentang Project

**Pesona Jepara** adalah portal web modern yang dikembangkan oleh Dinas Pariwisata dan Kebudayaan Kabupaten Jepara. Aplikasi ini bertujuan mendigitalisasi informasi pariwisata dan potensi daerah dalam satu wadah yang interaktif dan mudah diakses.

Tujuan utama aplikasi ini adalah:
1.  **Promosi Wisata**: Menampilkan destinasi wisata unggulan dengan visualisasi yang menarik.
2.  **Ekonomi Kreatif**: Menjadi etalase digital bagi produk-produk *Ekraf* lokal seperti ukiran, tenun, dan kerajinan.
3.  **Informasi Terpusat**: Menyajikan berita, agenda budaya, dan data statistik pariwisata secara *real-time*.

## ✨ Fitur Utama

### 🌍 Halaman Publik (Landing Page)
-   **Hero Map 3D**: Visualisasi lanskap Kabupaten Jepara yang memukau menggunakan teknologi **MapLibre GL JS** dengan efek 3D.
-   **Jelajah Peta (Interactive GIS)**:
    -   🔍 **Pencarian Lokasi**: Temukan destinasi wisata, hotel, atau kuliner favorit.
    -   🗺️ **Layer Control**: Lihat infrastruktur, penggunaan lahan, dan batas wilayah.
    -   📍 **Filter Kategori**: Wisata Alam, Budaya, Kuliner, dll.
-   **Galeri Visual**: Tampilan foto-foto destinasi berkualitas tinggi.
-   **Produk Unggulan**: Katalog produk ekonomi kreatif dari pengrajin lokal.

### 🔐 Admin Dashboard
-   **Manajemen Destinasi**: Input data wisata, lokasi koordinat, harga tiket, dan **multi-upload** galeri foto.
-   **Berita & Artikel**: Publikasi artikel blog dan event pariwisata.
-   **Manajemen Produk**: Kelola katalog produk UMKM/Ekraf.
-   **Otomatisasi**: Pembuatan *slug* (URL) otomatis yang SEO-friendly.
-   **Manajemen Spasial**: Import data GeoJSON untuk batas wilayah dan infrastruktur.

## 🛠️ Tech Stack

Project ini dibangun dengan stack teknologi *monolith* modern yang handal:

| Kategori | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Framework** | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white) | Framework PHP utama (v12). |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) | Penyimpanan data relasional & spasial. |
| **Frontend** | ![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white) | Framework CSS *utility-first* untuk styling responsif. |
| **Interactivity** | ![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=flat-square&logo=alpine.js&logoColor=white) | Framework JS ringan untuk interaksi UI. |
| **Maps** | ![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=flat-square&logo=leaflet&logoColor=white) | Peta interaktif 2D. |
| **3D Maps** | ![MapLibre](https://img.shields.io/badge/MapLibre-1A1E29?style=flat-square&logo=maplibre&logoColor=white) | Visualisasi peta 3D di Hero Section. |

## ⚙️ Prasyarat (Prerequisites)

Pastikan lingkungan kerja Anda sudah terinstal:
-   **PHP** >= 8.2
-   **Composer**
-   **Node.js** & **NPM**
-   **MySQL** / MariaDB (support spatial extensions)

## 🚀 Cara Instalasi

1.  **Clone Repository**
    ```bash
    git clone https://github.com/danyakmallun9999/dinas-pariwisata.git
    cd dinas-pariwisata
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Setup Environment**
    ```bash
    cp .env.example .env
    ```
    Atur konfigurasi database di `.env`:
    ```env
    DB_DATABASE=dinas_pariwisata
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Setup Database**
    ```bash
    php artisan key:generate
    php artisan migrate:fresh --seed
    ```
    *> Perintah `migrate:fresh --seed` akan mereset database dan mengisi data awal (wisata, kategori, admin).*

5.  **Jalankan Server**
    ```bash
    composer run dev
    ```
    Akses aplikasi di: **[http://localhost:8000](http://localhost:8000)**

## 👤 Akun Admin Default

Gunakan kredensial ini untuk login ke Dashboard Admin:

-   **Email**: `admin@jepara.go.id`
-   **Password**: `adminwisata`

## 📁 Struktur Direktori Penting

-   `app/Models/Place.php`: Model data wisata dengan properti spasial.
-   `resources/views/welcome.blade.php`: Halaman Publik & Landing Page.
-   `resources/views/admin/`: Folder tampilan dashboard admin.
-   `database/seeders/`: Script pengisi data awal (Database Seeder).

---

**© 2025 Dinas Pariwisata dan Kebudayaan Kabupaten Jepara**
*Membangun Pesona Jepara untuk Dunia.*
