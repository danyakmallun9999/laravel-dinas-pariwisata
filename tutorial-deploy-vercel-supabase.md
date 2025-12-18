# Tutorial Lengkap: Deploy Laravel di Vercel dengan Database Supabase

Tutorial ini akan memandu Anda langkah demi langkah untuk men-deploy aplikasi Laravel ke Vercel (gratis) menggunakan database PostgreSQL dari Supabase. Metode ini cocok untuk proyek portofolio, tugas akhir, atau aplikasi skala kecil-menengah.

---

## Prasyarat
1.  **Akun GitHub**: Untuk menyimpan source code.
2.  **Akun Vercel**: Untuk hosting aplikasi (login menggunakan GitHub).
3.  **Akun Supabase**: Untuk database PostgreSQL (gratis).
4.  **Aplikasi Laravel**: Yang sudah siap di komputer lokal.

---

## Bagian 1: Persiapan Database (Supabase)

Vercel menggunakan lingkungan serverless yang terkadang memiliki masalah koneksi dengan IPv6. Oleh karena itu, kita **WAJIB** menggunakan connection pooler IPv4.

1.  Buat proyek baru di [Supabase](https://supabase.com).
2.  Simpan password database Anda dengan aman.
3.  Setelah proyek aktif, masuk ke **Project Settings** (ikon gerigi) > **Database**.
4.  Cari bagian **Connection parameters**.
5.  **PENTING**: Aktifkan toggle **"Use connection pooler"**.
6.  Pastikan **Mode** adalah `Session`.
7.  Catat informasi berikut untuk digunakan nanti:
    *   **Host**: (Contoh: `aws-0-ap-southeast-1.pooler.supabase.com`)
    *   **Port**: (Biasanya `6543`)
    *   **User**: (Contoh: `postgres.abcdefg`)
    *   **Database**: (Biasanya `postgres`)

---

## Bagian 2: Konfigurasi Proyek Laravel

Kita perlu membuat beberapa file konfigurasi khusus agar Laravel bisa berjalan di lingkungan serverless Vercel.

### 1. Buat File `vercel.json`
Buat file baru bernama `vercel.json` di root folder proyek Anda. Isi dengan kode berikut:

```json
{
    "version": 2,
    "outputDirectory": "public",
    "framework": null,
    "functions": {
        "api/index.php": {
            "runtime": "vercel-php@0.7.4"
        }
    },
    "routes": [
        {
            "src": "/build/(.*)",
            "dest": "/public/build/$1"
        },
        {
            "src": "/(.*)",
            "dest": "/api/index.php"
        }
    ],
    "env": {
        "APP_ENV": "production",
        "APP_DEBUG": "false",
        "APP_URL": "https://nama-project-anda.vercel.app",
        "APP_CONFIG_CACHE": "/tmp/config.php",
        "APP_EVENTS_CACHE": "/tmp/events.php",
        "APP_PACKAGES_CACHE": "/tmp/packages.php",
        "APP_ROUTES_CACHE": "/tmp/routes.php",
        "APP_SERVICES_CACHE": "/tmp/services.php",
        "VIEW_COMPILED_PATH": "/tmp",
        "CACHE_DRIVER": "array",
        "LOG_CHANNEL": "stderr",
        "SESSION_DRIVER": "cookie"
    }
}
```
*Catatan: `runtime` bisa disesuaikan, saat ini `vercel-php@0.7.4` adalah yang stabil untuk Node 20.*

### 2. Buat File `api/index.php`
Karena Vercel tidak membaca `public/index.php` secara langsung sebagai entry point, kita butuh "jembatan". Buat folder `api` di root, lalu buat file `index.php` di dalamnya:

```php
<?php

require __DIR__ . '/../public/index.php';
```

### 3. Buat File `.vercelignore`
Buat file `.vercelignore` di root untuk mencegah file yang tidak perlu ikut di-upload saat build:

```text
.env
/node_modules
/tests
/storage/*.key
/storage/logs
phpunit.xml
README.md
```

### 4. Sesuaikan `.gitignore`
Pastikan folder build aset **tidak** di-ignore agar tampilan web tidak berantakan. Buka `.gitignore` dan pastikan baris ini diberi komentar atau dihapus:

```gitignore
# /public/build  <-- Pastikan ada tanda pagar (#) atau hapus baris ini
```

### 5. Force HTTPS di `AppServiceProvider`
Vercel terkadang menganggap aplikasi berjalan di HTTP biasa (Mixed Content), menyebabkan CSS tidak termuat. Edit file `app/Providers/AppServiceProvider.php` dan tambahkan kode ini di method `boot()`:

```php
public function boot(): void
{
    if($this->app->environment('production')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
```

### 6. Build Aset Frontend
Jalankan perintah ini di terminal lokal Anda sebelum push ke GitHub:

```bash
npm run build
```

---

## Bagian 3: Deployment ke Vercel

1.  **Push ke GitHub**:
    ```bash
    git add .
    git commit -m "Persiapan deploy vercel"
    git push origin main
    ```

2.  **Import Proyek di Vercel**:
    *   Buka dashboard Vercel, klik **Add New** > **Project**.
    *   Pilih repositori GitHub Anda.
    *   Pada **Framework Preset**, pilih **Other** (Biarkan default).

3.  **Setup Environment Variables**:
    Di halaman konfigurasi Vercel, masukkan Environment Variables sesuai data Supabase Anda. Copy nilai `APP_KEY` dari file `.env` lokal Anda.

    | Key | Value (Contoh) |
    | :--- | :--- |
    | `APP_KEY` | `base64:....` (Copy dari .env lokal) |
    | `APP_ENV` | `production` |
    | `APP_DEBUG` | `false` |
    | `APP_URL` | `https://nama-project-kamu.vercel.app` |
    | `DB_CONNECTION` | `pgsql` |
    | `DB_HOST` | `aws-0-ap-southeast-1.pooler.supabase.com` |
    | `DB_PORT` | `6543` |
    | `DB_DATABASE` | `postgres` |
    | `DB_USERNAME` | `postgres.namauser` |
    | `DB_PASSWORD` | `password_rahasia_kamu` |

4.  **Deploy**:
    Klik tombol **Deploy**. Tunggu proses build selesai. Jika berhasil, Anda akan melihat tampilan konfeti!

---

## Bagian 4: Migrasi Database

Karena Vercel tidak bisa menjalankan command `php artisan` secara langsung via terminal (SSH), kita bisa menjalankan migrasi dari lokal yang terhubung ke database Supabase, atau membuat route khusus. Cara teraman adalah dari lokal.

**Cara Migrasi dari Lokal:**

1.  Edit sementara file `.env` di komputer lokal Anda.
2.  Ubah `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` dengan kredensial Supabase (gunakan data Pooler yang sama dengan di Vercel).
3.  Jalankan:
    ```bash
    php artisan migrate
    ```
    *Jika ada error "Network Unreachable", pastikan Anda menggunakan Host Pooler (IPv4), bukan direct connection (IPv6).*
4.  Setelah selesai, jangan lupa kembalikan `.env` lokal ke settingan database lokal Anda (jika perlu).

---

## Troubleshooting Umum

1.  **Error "Network is unreachable"**:
    *   Penyebab: Koneksi database menggunakan IPv6.
    *   Solusi: Gunakan **Supabase Connection Pooler** (Port 6543, Host `pooler.supabase.com`).

2.  **Error Runtime "nodejs18.x is discontinued"**:
    *   Penyebab: Versi `vercel-php` terlalu lama.
    *   Solusi: Update di `vercel.json` menjadi `"runtime": "vercel-php@0.7.4"`.

3.  **Halaman CSS/JS Berantakan (404)**:
    *   Penyebab: Folder `public/build` tidak ter-upload.
    *   Solusi: Cek `.gitignore`, pastikan `/public/build` tidak di-ignore, lalu jalankan `npm run build` dan push ulang.

4.  **Error "No Output Directory named 'dist'"**:
    *   Penyebab: Vercel bingung folder outputnya dimana.
    *   Solusi: Tambahkan `"outputDirectory": "public"` di `vercel.json`.

5.  **Error "prepared statement ... does not exist"**:
    *   Penyebab: Konflik mode Transaction Pooler Supabase dengan Laravel Prepared Statements.
    *   Solusi: Tambahkan `PDO::ATTR_EMULATE_PREPARES => true` pada config `pgsql` di `config/database.php`.

---

## Bagian 5: Konfigurasi Image Storage (Supabase Storage)

Vercel menggunakan *ephemeral filesystem*, artinya file yang di-upload ke folder `storage/app/public` akan hilang dalam beberapa saat. Kita harus menggunakan layanan eksternal seperti **Supabase Storage**.

### 1. Buat Bucket di Supabase
1.  Masuk ke Dashboard Supabase > **Storage**.
2.  Klik **New Bucket**.
3.  Beri nama `uploads`.
4.  Pastikan toggle **Public bucket** AKTIF.
5.  Klik **Save**.

### 2. Install Driver S3
Jalankan perintah ini di terminal lokal:
```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### 3. Update `config/filesystems.php`
Tambahkan konfigurasi disk `supabase` di dalam array `disks`:

```php
'supabase' => [
    'driver' => 's3',
    'key' => env('SUPABASE_ACCESS_KEY_ID'),
    'secret' => env('SUPABASE_SECRET_ACCESS_KEY'),
    'region' => env('SUPABASE_REGION'),
    'bucket' => env('SUPABASE_BUCKET'),
    'url' => env('SUPABASE_URL'),
    'endpoint' => env('SUPABASE_ENDPOINT'),
    'use_path_style_endpoint' => true,
    'throw' => false,
    'report' => false,
],
```

### 4. Tambahkan Environment Variables di Vercel
Masuk ke Dashboard Vercel > Settings > Environment Variables, dan tambahkan:

| Key | Value | Cara Mendapatkan |
| :--- | :--- | :--- |
| `FILESYSTEM_DISK` | `supabase` | - |
| `SUPABASE_ACCESS_KEY_ID` | `...` | Project Settings > Storage > Access Key |
| `SUPABASE_SECRET_ACCESS_KEY` | `...` | Project Settings > Storage > Secret Key |
| `SUPABASE_REGION` | `ap-southeast-1` | Lihat di URL endpoint (misal: `...s3.ap-southeast-1...`) |
| `SUPABASE_BUCKET` | `uploads` | Nama bucket yang Anda buat |
| `SUPABASE_URL` | `https://[ref-project].supabase.co/storage/v1/object/public/uploads` | URL publik bucket Anda |
| `SUPABASE_ENDPOINT` | `https://[ref-project].supabase.co/storage/v1/s3` | Project Settings > Storage > Endpoint |

### 5. Update Kode Upload (Contoh)
Di Controller Laravel Anda, gunakan disk publik:

```php
$path = $request->file('image')->store('images', 'public'); 
// Ubah menjadi:
$path = $request->file('image')->store('images', 'supabase');
```
Atau jika default disk sudah diubah ke `supabase` di env, cukup `store('images')`.

---
*Tutorial ini dibuat berdasarkan keberhasilan deployment pada tanggal 18 Desember 2025.*
