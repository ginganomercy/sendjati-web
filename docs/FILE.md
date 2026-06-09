# 📂 Dokumentasi Lengkap Struktur & File: Sendjati Cafe

Dokumen ini menyediakan penjelasan menyeluruh untuk **setiap** folder dan file dalam proyek **Sendjati Cafe**.

---

## 🧠 1. Backend (Logika & Data - `app/`)

### 📁 `app/Http/Controllers/` (Pusat Kendali)
- **`DashboardController.php`**: Mengolah statistik untuk grafik (Chart.js) dan KPI dashboard.
- **`ItemController.php`**: CRUD Barang, pencarian, dan fitur stok opname.
- **`TransactionController.php`**: Alur masuk/keluar stok dan riwayat transaksi.
- **`CategoryController.php`** & **`LedgerController.php`**: Manajemen kategori dan laporan audit stok.
- **`ProfileController.php`**: Pengaturan akun user.
- **`Auth/`**: (Folder) Berisi controller Login, Register, Verify Email, dll (dari Laravel Breeze).

### 📁 `app/Http/Middleware/` (Penyaring)
- **`HandleInertiaRequests.php`**: **Sangat Penting.** Menentukan data apa yang dikirim ke semua halaman Vue (misal: data user yang login atau pesan notifikasi Flash).

### 📁 `app/Models/` (Struktur Objek Database)
- **`Item.php`, `Category.php`, `Unit.php`**: Master data barang.
- **`Transaction.php`, `TransactionDetail.php`**: Data transaksi.
- **`InventoryLedger.php`**: Log audit mutasi stok.
- **`User.php`**: Data pengguna sistem.

### 📁 `app/Services/` (Logika Bisnis Terpisah)
- **`TransactionService.php`**: Menangani logika berat transaksi (Locking database, perhitungan stok).
- **`ItemService.php`** & **`CategoryService.php`**: Helper logika untuk barang dan kategori.

### 📁 `app/Providers/` (Pendaftaran Layanan)
- **`AppServiceProvider.php`**: Tempat mendaftarkan layanan global atau konfigurasi awal aplikasi.

---

## 🎨 2. Frontend (Antarmuka - `resources//`)

### 📁 `resources/js/Pages/` (Halaman SPA)
- **`Dashboard/`**: `Index.vue` (Dashboard Utama).
- **`MasterData/`**: `Items/Index.vue`, `Items/Create.vue`, dll.
- **`Transactions/`**: `Index.vue`, `Create.vue` (Input stok masuk/keluar).
- **`Ledger/`**: `Index.vue` (Halaman audit trail).
- **`Profile/`**: Edit profil dan hapus akun.
- **`Auth/`**: Login, Register, Forgot Password.

### 📁 `resources/js/Components/` (Potongan UI)
- **`Sidebar.vue`, `Navbar.vue`**: Navigasi utama.
- **`KpiCard.vue`**: Widget angka dashboard.
- **`Modal.vue`, `PrimaryButton.vue`, `InputLabel.vue`**: UI kit dasar.

### 📁 `resources/css/` & `resources/views/`
- **`app.css`**: Import Tailwind CSS.
- **`app.blade.php`**: Template HTML tunggal tempat aplikasi Vue "menempel".

---

## 🗄️ 3. Database & Konfigurasi (`database/` & `config/`)

### 📁 `database/`
- **`migrations/`**: (Banyak file) File pembentuk tabel database.
- **`seeders/`**: `DatabaseSeeder.php`, `SendjatiSeeder.php` (Pengisi data awal).
- **`factories/`**: Template data palsu untuk testing.
- **`database.sqlite`**: File database utama (semua data ada di sini).

### 📁 `config/` (Pengaturan Framework)
- **`app.php`**: Nama app, Timezone (Asia/Jakarta), bahasa.
- **`database.php`**: Pengaturan koneksi ke SQLite.
- **`permission.php`**: Konfigurasi Role & Permission (Spatie).
- **`logging.php`**, **`mail.php`**, **`session.php`**: Pengaturan log, email, dan sesi login.

---

## 🚀 4. Folder Pendukung Lainnya

- **`bootstrap/`**: Berisi `app.php` (inisialisasi aplikasi) dan `providers.php`.
- **`public/`**: Aset yang bisa diakses langsung (gambar, favicon, JS/CSS hasil build).
- **`routes/`**: `web.php` (rute utama) dan `auth.php` (rute login).
- **`storage/`**: Folder `logs/` (catatan error) dan `framework/` (file sementara).
- **`tests/`**: Berisi pengujian otomatis (Feature & Unit tests).
- **`vendor/`**: (Folder Besar) Semua library PHP dari Composer. **Jangan diedit manual.**
- **`node_modules/`**: (Folder Besar) Semua library JS dari NPM. **Jangan diedit manual.**

---

## 📄 5. File di Root (Konfigurasi Alat)

| File | Fungsi |
| :--- | :--- |
| **`.env`** | Pengaturan lingkungan (Database, APP_KEY). |
| **`artisan`** | Tool command line Laravel. |
| **`composer.json`** | Daftar library PHP. |
| **`package.json`** | Daftar library JS & script running (`dev`, `build`). |
| **`vite.config.js`** | Konfigurasi Vite (Compiler frontend). |
| **`tailwind.config.js`** | Konfigurasi desain & warna Tailwind. |
| **`.gitignore`** | Daftar file yang tidak boleh masuk ke Git (seperti `.env`). |
| **`.editorconfig`** | Standar penulisan kode agar rapi di semua aplikasi editor. |
| **`jsconfig.json`** | Membantu editor (VSCode) mengenali alias folder (seperti `@` untuk `resources/js`). |
| **`postcss.config.js`** | Tool tambahan untuk memproses CSS Tailwind. |
| **`phpunit.xml`** | Konfigurasi untuk menjalankan testing. |
| **`php.ini`** | Pengaturan khusus PHP untuk server lokal. |
| **`serve.bat`** | File batch untuk menjalankan server Laravel dengan cepat. |

---

> [!IMPORTANT]
> Proyek ini menggunakan **SQLite**, sehingga Anda tidak perlu menginstal database server terpisah. Semua data aplikasi tersimpan secara aman di dalam file `database/database.sqlite`.
