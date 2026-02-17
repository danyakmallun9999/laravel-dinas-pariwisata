# 🏝️ Jelajah Jepara - Portal Resmi Pariwisata

![Banner Project](/public/images/agenda/logo-agenda.png)

> **Platform digital terintegrasi untuk promosi pariwisata, ekonomi kreatif, dan e-ticketing Kabupaten Jepara.**

---

## 📖 Tentang Project

**Jelajah Jepara** adalah portal web modern yang dikembangkan oleh Mahasiswa Magang Unisnu Jepara Jurusan Teknik Informatika. Aplikasi ini mendigitalisasi sektor pariwisata mulai dari promosi hingga transaksi tiket masuk secara elektronik.

Tujuan utama aplikasi ini adalah:
1.  **Promosi Wisata**: Menampilkan destinasi wisata unggulan dengan visualisasi yang menarik.
2.  **E-Ticketing System**: Memudahkan wisatawan membeli tiket masuk secara online (cashless) dan mengurangi kebocoran pendapatan daerah.
3.  **Informasi Terpusat**: Menyajikan berita, agenda budaya, dan data statistik pariwisata secara *real-time*.

## ✨ Fitur Utama

### 🌍 Halaman Publik (Landing Page)
-   **Hero Map 3D**: Visualisasi lanskap Kabupaten Jepara menggunakan **MapLibre GL JS**.
-   **Jelajah Peta (Interactive GIS)**: Pencarian lokasi wisata, hotel, dan kuliner berbasis peta.
-   **Multilingual Support**: Tersedia dalam Bahasa Indonesia 🇮🇩 dan Bahasa Inggris 🇬🇧.
-   **SEO Optimized**: Dilengkapi Open Graph tags dan optimasi gambar untuk performa maksimal.

### 🎟️ E-Ticketing System
-   **Booking Online**: Wisatawan dapat memesan tiket masuk untuk berbagai destinasi.
-   **Pembayaran Digital**: Terintegrasi dengan **Midtrans** (GoPay, QRIS, Virtual Account).
-   **Tiket QR Code**: Tiket elektronik dengan QR Code unik untuk scan di pintu masuk.
-   **Laporan Keuangan**: Dashboard pendapatan real-time untuk pengelola wisata.

### 🔐 Role-Based Access Control (RBAC)
Sistem memiliki manajemen hak akses yang detail untuk berbagai peran pengguna:

1.  **Super Admin**: Akses penuh ke seluruh sistem, manajemen user, dan konfigurasi global.
2.  **Admin Wisata**: Mengelola data destinasi wisata, tiket, dan laporan keuangan spesifik.
3.  **Admin Berita**: Fokus pada publikasi artikel, berita, dan *event* pariwisata.
4.  **Pengelola Wisata**: Akun khusus untuk petugas di lapangan (scan tiket, validasi).

## 🛠️ Tech Stack

| Kategori | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Framework** | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white) | Framework PHP utama (v12). |
| **Database** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) | Penyimpanan data relasional & spasial. |
| **Frontend** | ![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white) | Framework CSS *utility-first*. |
| **Authorization** | ![Spatie](https://img.shields.io/badge/Spatie-Permission-important?style=flat-square) | Manajemen Role & Permission. |
| **Payment** | ![Midtrans](https://img.shields.io/badge/Midtrans-Payment-blue?style=flat-square) | Payment Gateway Indonesia. |
| **Maps** | ![MapLibre](https://img.shields.io/badge/MapLibre-1A1E29?style=flat-square&logo=maplibre&logoColor=white) | Visualisasi peta 3D interaktif. |

## ⚙️ Prasyarat (Prerequisites)

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
    php artisan key:generate
    ```
    Atur konfigurasi database di `.env`:
    ```env
    DB_DATABASE=dinas_pariwisata
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4.  **Setup Database & Seeders**
    ```bash
    php artisan migrate:fresh --seed
    ```
    *> Perintah ini akan membuat database baru dan mengisi data dummy (wisata, user admin, tiket).*

5.  **Jalankan Server**
    ```bash
    composer run dev
    ```
    Akses aplikasi di: **[http://localhost:8000](http://localhost:8000)**

## 👤 Akun Admin Default

**🔐 Security Note:** Password admin sekarang menggunakan environment variables untuk keamanan.

### Setup Environment Variables

Tambahkan ke file `.env`:

```env
# Initial Super Admin Password (wajib untuk production)
INITIAL_ADMIN_PASSWORD=your_secure_password_here

# Sample Admin Password (hanya untuk development)
SAMPLE_ADMIN_PASSWORD=password
```

Lihat dokumentasi lengkap: [SEEDER-ENVIRONMENT-VARIABLES.md](./SEEDER-ENVIRONMENT-VARIABLES.md)

### Default Admin Accounts

Setelah menjalankan `php artisan migrate:fresh --seed`, akun berikut akan dibuat:

| Role | Email | Password | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@jepara.go.id` | Dari `INITIAL_ADMIN_PASSWORD` atau random | Akses penuh seluruh sistem. |
| **Admin Wisata** | `wisata@jepara.go.id` | Dari `SAMPLE_ADMIN_PASSWORD` (default: `password`) | Mengelola destinasi dan tiket. |
| **Admin Berita** | `berita@jepara.go.id` | Dari `SAMPLE_ADMIN_PASSWORD` (default: `password`) | Mengelola konten berita/event. |

**⚠️ Penting:** 
- Jika `INITIAL_ADMIN_PASSWORD` tidak di-set, password random akan di-generate dan ditampilkan di console
- Ganti password segera setelah first login untuk keamanan
- Jangan jalankan seeder di production tanpa environment variables yang proper

## 📁 Struktur Direktori & Modul Penting

Proyek ini memiliki struktur folder standar Laravel dengan beberapa modul kustom:

```
├── app/
│   ├── Http/Controllers/     # Logika Bisnis (Admin, Public, Auth)
│   ├── Models/               # Eloquent ORM (Place, Ticket, Event)
│   ├── Policies/             # Logika Otorisasi RBAC (PlacePolicy, etc.)
│   └── Services/             # Service Layer (File Upload, Slug Generator)
├── database/
│   ├── migrations/           # Skema Database & Relasi Tabel
│   └── seeders/              # Data Awal (User Admin, Kategori, Wisata)
├── resources/views/
│   ├── admin/                # Template Dashboard (Back-office)
│   ├── components/           # Komponen UI Reusable (Navbar, Sidebar)
│   └── public/               # Halaman Depan (Landing Page, Detail)
└── routes/
    └── web.php               # Definisi Rute & Middleware Group
```

---

**Dikembangkan dengan ❤️ oleh Mahasiswa Magang Unisnu Jepara - Teknik Informatika**
*Bekerja sama dengan Dinas Pariwisata dan Kebudayaan Kabupaten Jepara*

<p align="center"><img src="/public/images/logo-kura.png" width="400" alt="Logo Kura"></p>

© 2026 **Jelajah Jepara**. All Rights Reserved.
