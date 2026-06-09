# Sendjati Cafe

Aplikasi web sistem manajemen dan Point of Sales (POS) untuk Sendjati Cafe.

## 🛠️ Analisis Teknologi & Fitur Unggulan

Aplikasi ini dibangun menggunakan arsitektur web modern **Modular Monolith SPA (Single Page Application)**:

- **Backend Framework**: Laravel 12 (PHP)
- **Frontend Framework**: Vue.js 3 (Composition API)
- **Penghubung (Bridge)**: Inertia.js (Memungkinkan rendering SPA sisi klien dengan kemudahan routing backend Laravel)
- **Styling**: Tailwind CSS
- **Database**: SQLite (File-based, tidak perlu server database terpisah)

**Fitur Canggih & Enterprise-Ready:**
- **Segregasi Multi-Departemen**: Sistem membedakan secara tegas inventaris dan transaksi antara area **Kitchen** dan **Bar**. Master data dan Dashboard terisolasi untuk tiap departemen.
- **Automated SKU Generator**: Kode unik barang (SKU) dibuat secara otomatis oleh sistem (*Auto-Pilot*) berdasarkan departemen dan jenis barang (contoh: `KTC-RAW-001`).
- **Pencegahan Duplikasi Pintar**: Sistem mendeteksi barang ganda secara *real-time* saat pengetikan nama dan memberikan peringatan untuk melakukan restok alih-alih membuat entri baru.
- **Slide-Over Panel UX**: Pembuatan transaksi dilakukan melalui panel geser estetik tanpa harus memuat ulang halaman (*No Reload*).
- **Event-Sourcing Ledger**: Pencatatan alat operasional menggunakan *append-only ledger* yang memastikan integritas audit mutlak.

---

## 🚀 Panduan Menjalankan Aplikasi (Untuk IT Pemula)

Ikuti langkah-langkah sistematis di bawah ini untuk menjalankan aplikasi di komputer/laptop Anda:

### 1. Persiapan Sistem (Prerequisites)
Pastikan komputer Anda sudah terinstal aplikasi berikut:
- **PHP**: Versi 8.2 atau lebih baru.
- **Composer**: Dependency manager wajib untuk aplikasi PHP/Laravel.
- **Node.js & NPM**: Wajib untuk mengelola library frontend (Vue & Tailwind).
*(Catatan: Anda **TIDAK PERLU** menginstal XAMPP/MySQL karena aplikasi ini menggunakan SQLite).*

### 2. Tahap Konfigurasi & Instalasi
Buka **Terminal** atau **Command Prompt**, lalu arahkan ke folder project ini (contoh: `cd c:\sendjati-web\sendjati-cafe-web`).

1. **Install dependensi backend (PHP):**
   ```bash
   composer install
   ```

2. **Install dependensi frontend (Node.js):**
   ```bash
   npm install
   ```

3. **Konfigurasi Environment (.env):**
   - Copy file `.env.example` dan ubah namanya menjadi `.env` (Jika belum ada).
   - Pastikan baris database di `.env` sudah diset ke **sqlite**:
     ```env
     DB_CONNECTION=sqlite
     ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Siapkan Database & Migrasi:**
   - Buat file kosong di folder `database` bernama `database.sqlite`.
   - Jalankan perintah berikut untuk membuat tabel dan mengisi data awal (seeder):
     ```bash
     php artisan migrate --seed
     ```

### 3. Cara Menjalankan Server Aplikasi
Setelah proses instalasi selesai, Anda harus menjalankan server backend (Laravel) dan frontend (Vite/Vue) agar aplikasi bisa diakses. 

**Cara Mudah (Rekomendasi):**
Jalankan satu perintah ini di terminal. Perintah ini akan menjalankan server Laravel dan Vite secara bersamaan:
```bash
composer dev
```

*(Alternatif: Jika cara di atas gagal, buka dua jendela terminal)*
- Terminal 1: `php artisan serve` (Server Backend)
- Terminal 2: `npm run dev` (Server Frontend)

### 4. Selesai! 🎉
Buka web browser Anda (Chrome/Firefox/Edge) dan ketik alamat:
**`http://localhost:8000`**

Aplikasi Sendjati Cafe sudah siap digunakan!

---

### 🔑 Informasi Login Default:
*   **Email:** `admin@sendjati.com`
*   **Password:** `password`
