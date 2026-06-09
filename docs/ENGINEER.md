# Laporan Analisis Proyek: Sendjati Cafe Web

## 1. Ringkasan Eksekutif (Executive Summary)
Proyek **Sendjati Cafe Web** adalah sebuah aplikasi web modern yang dirancang untuk kebutuhan sistem manajemen kafe, *Dashboard* admin, atau *Point of Sales* (POS). Aplikasi ini mengadopsi standar industri terkini dalam pengembangan web reaktif (*Single Page Application*) dengan fokus pada kemudahan implementasi (*zero-config*).

---

## 2. Arsitektur Teknologi (Tech Stack)
Proyek ini dibangun di atas arsitektur **VILT Stack** yang dimodifikasi untuk portabilitas maksimal:

* **V - Vue.js (Versi 3)**: Bertugas menangani tampilan pengguna (Frontend). Vue menggunakan pendekatan berbasis komponen (Component-based) dengan *Composition API*, membuat antarmuka menjadi sangat dinamis dan interaktif.
* **I - Inertia.js**: Berfungsi sebagai "jembatan" yang menghubungkan Backend Laravel dan Frontend Vue tanpa memerlukan REST API terpisah, sehingga menjaga kecepatan pengembangan tetap tinggi.
* **L - Laravel (Versi 12)**: Berfungsi sebagai pusat logika dan *server* (Backend). Menggunakan fitur terbaru PHP 8.2+ untuk menjamin performa dan keamanan data.
* **T - Tailwind CSS (Versi 3/4)**: Memberikan *styling* dengan pendekatan *utility-first classes*, memastikan desain konsisten dan responsif di berbagai ukuran layar.

---

## 3. Strategi Database & Keamanan
Salah satu keputusan engineering utama dalam proyek ini adalah penggunaan sistem database yang *portable*:

* **Database (SQLite Architecture)**: 
  * Aplikasi menggunakan **SQLite** sebagai basis data utama. Seluruh data disimpan dalam file `database/database.sqlite`.
  * **Keuntungan:** Tidak memerlukan instalasi server database eksternal (seperti MySQL/MariaDB), sehingga aplikasi bersifat "Plug & Play".
  * **Skalabilitas:** Meskipun menggunakan SQLite untuk kemudahan, arsitektur *Eloquent ORM* Laravel memungkinkan transisi ke MySQL atau PostgreSQL hanya dengan mengubah satu baris di file `.env`.
* **Sistem Hak Akses (RBAC)**: Menggunakan `Spatie Laravel Permission` untuk memisahkan wewenang antara Admin dan staf lainnya secara aman.
* **Integritas Data**: Menggunakan mekanisme *Database Transactions* dan *Pessimistic Locking* untuk mencegah kesalahan perhitungan stok saat terjadi transaksi simultan.

---

## 4. Analisis Komponen Frontend
*   **Analitik Visual (ApexCharts)**: Menggunakan `vue3-apexcharts` untuk menyajikan data penjualan dan stok dalam bentuk grafik yang mudah dipahami oleh manajemen kafe.
*   **Akselerator Vite**: Menjamin proses pengembangan yang instan dengan *Hot Module Replacement (HMR)*.

---

## 5. Kesimpulan
Proyek **Sendjati Cafe Web** merupakan perpaduan antara ketangguhan *enterprise* (Laravel) dan fleksibilitas *modern web* (Vue/Inertia). Keputusan menggunakan **SQLite** mempertegas posisi proyek ini sebagai sistem yang efisien, mudah dipelihara, dan sangat cepat untuk diimplementasikan di lingkungan kafe nyata.
