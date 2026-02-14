Saya sedang mengembangkan sistem web berbasis Laravel (Blade), Tailwind CSS, dan Alpine.js.

Tampilan desktop saat ini sudah sangat baik secara visual dan layout.
Namun tampilan mobile (≤640px) masih terasa seperti versi desktop yang diperkecil dan belum optimal dari sisi UX.

Saya ingin melakukan improvisasi UI/UX dengan pendekatan **mobile-first refinement**, tanpa merusak tampilan desktop.

Tujuan utama:

* Mobile terasa seperti aplikasi native (modern, clean, fokus).
* Navigasi lebih mudah dengan satu tangan.
* Elemen penting lebih cepat diakses.
* Interaksi terasa ringan dan intuitif.
* Visual lebih tegas dan hierarki lebih jelas.

Silakan lakukan evaluasi dan perbaikan dengan prinsip berikut:

1. Prioritas Konten Mobile

   * Identifikasi aksi utama halaman.
   * Pastikan aksi utama terlihat dalam 1–2 scroll pertama.
   * Kurangi elemen yang tidak esensial di mobile.
   * Fokus pada clarity dan kecepatan interaksi.

2. Layout & Spacing

   * Optimalkan padding dan margin khusus mobile.
   * Hindari whitespace berlebihan di bagian atas.
   * Gunakan edge-to-edge layout jika relevan.
   * Hindari nested container berlebihan di mobile.

3. Navigasi

   * Pastikan navigasi nyaman untuk satu tangan.
   * Gunakan sticky bottom navigation atau sticky CTA jika relevan.
   * Breadcrumb di mobile harus compact, tidak wrap, dan tidak memakan ruang berlebihan.

4. Interactive Elements

   * Ukuran tombol minimal 44–48px tinggi.
   * Jarak antar elemen cukup agar tidak salah tap.
   * Feedback visual saat hover/active/selected lebih tegas.
   * Gunakan shadow, scale, atau border yang jelas untuk state aktif.

5. Grid & Card Layout

   * Hindari grid padat di mobile.
   * Gunakan horizontal scroll untuk opsi pilihan jika lebih efisien.
   * Pastikan card tidak terlalu tinggi tanpa alasan.

6. Hierarki Visual

   * Heading harus lebih tegas dari body text.
   * Gunakan kontras warna yang cukup.
   * Elemen sekunder tidak boleh mengganggu fokus utama.

7. Konsistensi Sistem

   * Jangan ubah logic backend.
   * Jangan ubah route atau controller.
   * Fokus pada layer UI (HTML + Tailwind + Alpine).
   * Desktop behavior harus tetap aman.

Hasil akhir yang diharapkan:

* Tampilan mobile terasa premium dan profesional.
* User bisa memahami tujuan halaman dalam 3 detik.
* Navigasi cepat dan natural.
* Tidak terasa seperti desktop yang dipaksa masuk ke layar kecil.
* Konsisten dengan desain sistem secara keseluruhan.

