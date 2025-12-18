# Perencanaan Sistem Informasi Geografis (GIS) Desa Mayong Lor

Dokumen ini berisi perencanaan lengkap untuk pengembangan sistem pemetaan digital Desa Mayong Lor.

## 1. Identitas & Visi Project

*   **Nama Project**: Mayonglor GIS (Sistem Informasi Geografis Desa Mayong Lor).
*   **Visi**: Mewujudkan transparansi informasi spasial dan potensi desa yang mudah diakses oleh masyarakat dan pemerintah desa.
*   **Misi**:
    1.  Mendigitalisasi aset dan infrastruktur desa.
    2.  Menyediakan data kependudukan dan batas wilayah yang akurat.
    3.  Memudahkan perencanaan pembangunan desa berbasis data spasial.

## 2. Analisis Kebutuhan

### 2.1 Kebutuhan Pengguna (User Needs)
*   **Masyarakat Umum**:
    *   Melihat peta digital desa (batas, jalan, fasilitas).
    *   Mencari lokasi penting (Masjid, Sekolah, Kantor Desa).
    *   Melihat profil dan statistik desa.
*   **Administrator (Perangkat Desa)**:
    *   Mengelola data lokasi (Tambah/Edit/Hapus titik koordinat).
    *   Mengunggah file spasial (GeoJSON) untuk batas wilayah atau infrastruktur.
    *   Mengelola kategori lokasi.
    *   Melihat statistik penggunaan sistem.

### 2.2 Kebutuhan Sistem
*   **Performance**: Web harus ringan dan cepat diakses di perangkat mobile (Android/iOS).
*   **Scalability**: Struktur database harus siap menampung penambahan data di masa depan.
*   **Security**: Halaman admin terlindungi otentikasi yang aman.

## 3. Arsitektur & Teknologi

Sistem dibangun dengan arsitektur Monolithic menggunakan MVC (Model-View-Controller) Pattern.

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Framework** | Laravel 11 | Backend logic, Routing, Security. |
| **Database** | MySQL 8.0 | Relational Database Management System. |
| **Frontend** | Blade + Tailwind CSS | UI Construction & Styling. |
| **Interactivity** | Alpine.js | State management ringan untuk frontend. |
| **Peta Digital** | Leaflet.js | Library open-source untuk rendering peta. |
| **Basemap** | OpenStreetMap / Google | Layer dasar peta (Satelit/Terrain). |

## 4. Perancangan Database

Berdasarkan *migrations* yang telah ada, berikut adalah skema database yang digunakan:

### 4.1 Tabel Utama
1.  **`users`**: Menyimpan data administrator.
    *   Columns: `id`, `name`, `email`, `password`, `role`.
2.  **`categories`**: Klasifikasi lokasi (e.g., Pendidikan, Ibadah).
    *   Columns: `id`, `name`, `slug`, `icon` (marker icon), `color`.
3.  **`places`**: Titik lokasi spesifik (Point of Interest).
    *   Columns: `id`, `category_id`, `name`, `slug`, `description`, `latitude`, `longitude`, `image_path`.
    *   Relation: BelongsTo Category.

### 4.2 Tabel Spasial (Poligon/Garis)
4.  **`boundaries`**: Batas wilayah administratif (RT/RW/Dusun).
    *   Columns: `id`, `name`, `type`, `geojson_data`, `color`.
5.  **`infrastructures`**: Data jaringan jalan, irigasi, drainase.
    *   Columns: `id`, `name`, `type` (Jalan/Saluran), `length` (meter), `condition`, `geojson_data`.
6.  **`land_uses`**: Data penggunaan lahan (Sawah, Pemukiman).
    *   Columns: `id`, `type`, `area` (hektar), `geojson_data`.

### 4.3 Tabel Pendukung
7.  **`populations`**: Data statistik kependudukan.
    *   Columns: `id`, `dusun`, `rw`, `rt`, `total_kk`, `total_jiwa`, `male`, `female`.
8.  **`activity_logs`**: Mencatat aktivitas admin (Audit Trail).

## 5. Rincian Fitur & Fungsionalitas

### 5.1 Modul Publik (Frontend)
*   **Landing Page**:
    *   *Hero Section*: Judul menarik dan tombol "Jelajahi Peta".
    *   *WebGIS View*: Peta *full-screen* atau *embedded* besar yang menampilkan semua layer.
    *   *Layer Control*: Toggle untuk menyembunyikan/menampilkan layer (Batas, Infrastruktur, dll).
    *   *Search Bar*: Pencarian tempat berdasarkan nama.
*   **Detail Lokasi (Popup/Sidebar)**:
    *   Saat marker diklik, muncul informasi detail + foto lokasi.
    *   Tombol "Rute Ke Sini" (link ke Google Maps).
*   **Statistik Dashboard (Public)**:
    *   Grafik sederhana jumlah penduduk.
    *   Persentase penggunaan lahan.

### 5.2 Modul Admin (Backend)
*   **Authentication**: Login aman untuk admin.
*   **Dashboard Overview**: Ringkasan jumlah data (Places, Categories, Users).
*   **Manajemen Kategori**:
    *   CRUD Kategori.
    *   Upload custom icon untuk marker peta.
*   **Manajemen Places (Titik Lokasi)**:
    *   Input form dengan peta mini untuk *pick coordinate*.
    *   Upload foto lokasi (otomatis resize/optimize).
*   **Manajemen Layer Spasial**:
    *   Upload file GeoJSON untuk update batas wilayah.
    *   Editor atribut layer (ubah warna, opasitas).

## 6. Roadmap Pengembangan

### Phase 1: Foundation (Minggu 1)
*   [x] Instalasi Laravel & Setup Environment.
*   [x] Integrasi Tailwind CSS & Alpine.js.
*   [x] Perancangan Database Migration.
*   [x] Implementasi Authentication (Login/Logout).

### Phase 2: Core Data Management (Minggu 2)
*   [ ] Membuat CRUD untuk Categories.
*   [ ] Membuat CRUD untuk Places (termasuk upload gambar).
*   [ ] Implementasi file uploader untuk GeoJSON (Boundaries/Infrastructures).
*   [ ] Seeding data awal (Data Dummy untuk testing).

### Phase 3: Map Integration (Minggu 3)
*   [ ] Integrasi Leaflet.js di halaman publik.
*   [ ] Menampilkan marker dari database `places`.
*   [ ] Menampilkan layer GeoJSON dari database (`boundaries`, `land_uses`).
*   [ ] Membuat filter layer dan kategori di peta.

### Phase 4: UI/UX Refinement (Minggu 4)
*   [ ] Poles tampilan Landing Page (Animasi, Responsiveness).
*   [ ] Integrasi data statistik kependudukan (Charts).
*   [ ] Testing di perangkat Mobile.
*   [ ] Optimasi loading time (Lazy loading images).

## 7. Struktur Folder Utama
```text
/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/      (Controller khusus Admin)
│   │   └── Public/     (Controller halaman depan)
│   └── Models/         (Boundary, Category, Place, Infrastructure...)
├── resources/
│   ├── views/
│   │   ├── admin/      (Layout & Pages Admin)
│   │   ├── layouts/    (Master blade templates)
│   │   └── public/     (Landing page & Map view)
├── public/
│   ├── geojson/        (Penyimpanan file spasial statis jika needed)
│   └── storage/        (Symlink ke storage/app/public untuk gambar)
└── routes/
    ├── web.php         (Definisi routing public & admin group)
    └── api.php         (Endpoint JSON untuk data peta jika pakai AJAX)
```

## 8. Catatan Pengembangan
*   **Peta**: Gunakan plugin `Leaflet-AJAX` atau fetch native JS untuk memuat data GeoJSON agar peta tidak berat saat memuat.
*   **Gambar**: Pastikan fitur upload gambar memiliki validasi ukuran (max 2MB) dan format (jpg/png/webp).
*   **GeoJSON**: Validasi struktur GeoJSON sebelum disimpan ke database untuk mencegah error rendering.
