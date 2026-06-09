# ⚙️ Arsitektur & Teknologi: Sendjati Cafe Inventory

Dokumen ini memuat detail tingkat lanjut mengenai tumpukan teknologi (*tech stack*), arsitektur, pustaka (*libraries*), serta di bagian mana dari *codebase* teknologi-teknologi tersebut diimplementasikan.

---

## 1. Tumpukan Teknologi Inti (Tech Stack)

Sistem ini dibangun di atas arsitektur **Modular Monolith SPA (Single Page Application)**. Ini memberikan kecepatan *rendering* sisi klien (*client-side*) sekelas aplikasi *mobile*, namun dengan kemudahan pemeliharaan (*maintenance*) sebuah sistem *monolith*.

**A. Backend: Laravel 12 (PHP)**
*   **Lokasi Kode:** Folder `app/Http/Controllers/`, `app/Models/`, `routes/web.php`
*   **Peran:** Menangani logika database (SQLite), routing server, otentikasi, dan validasi.
*   **Konteks Spesifik:** Menggunakan SQLite sebagai engine database internal yang ringan dan portable. Laravel mengelola integritas data melalui fitur modern seperti *Route Model Binding* (contoh: di `ItemController.php` pada metode `history(Item $item)`), *Form Requests* untuk validasi ketat (`app/Http/Requests/*`), dan Eloquent ORM.

**B. Frontend: Vue.js 3 (Composition API)**
*   **Lokasi Kode:** Folder `resources/js/Pages/` dan `resources/js/Components/`
*   **Peran:** Mengatur antarmuka pengguna (UI) secara dinamis dan reaktif.
*   **Konteks Spesifik:** Menggunakan gaya penulisan `<script setup>` terbaru. Pengelolaan *state* lokal seperti daftar form yang dapat ditambah/dikurang (Multi-line item) menggunakan fungsi bawaan Vue seperti `ref` dan `computed` (lihat `resources/js/Pages/Transactions/Create.vue`).

**C. Bridge: Inertia.js**
*   **Lokasi Kode:** `resources/js/app.js` dan *Middleware* `app/Http/Middleware/HandleInertiaRequests.php`
*   **Peran:** Menghilangkan kebutuhan untuk membangun REST API terpisah. Laravel langsung merespon berupa komponen Vue melalui fungsi `Inertia::render()`.
*   **Konteks Spesifik:** Prop data global (seperti *Badge Alert Stok Kritis* yang muncul di Sidebar seluruh halaman) dieksekusi dan disuntikkan secara terpusat melalui fungsi `share()` di `HandleInertiaRequests.php`.

**D. Styling: Tailwind CSS 3/4**
*   **Lokasi Kode:** Langsung di dalam atribut `class="..."` pada semua file `*.vue` dan diatur di `tailwind.config.js`.
*   **Peran:** *Utility-first CSS framework* untuk mempercepat pengembangan UI yang konsisten. Dalam aplikasi ini, Tailwind digunakan intensif untuk membangun tema warna *Emerald* khas Sendjati, termasuk animasi halus pada tombol dan kartu.

---

## 2. Pustaka & Plugin Eksternal (Libraries)

**A. Chart.js (`chart.js/auto`)**
*   **Lokasi Pemasangan:** Di-*import* pada file `resources/js/Pages/Dashboard/Index.vue`.
*   **Peran:** Menggambar *Area Chart* interaktif (grafik garis dengan *gradient fill* warna Emerald dan Rose) untuk memvisualisasikan tren mutasi stok masuk dan keluar selama 6 bulan ke belakang.
*   **Implementasi Aktual:** Dihubungkan ke dalam elemen HTML melalui referensi Vue `<canvas ref="chartCanvas">`. Menggunakan Vue *Lifecycle Hooks* `onMounted` dan *watcher* `watch` untuk menggambar ulang grafik secara otomatis jika ada *update* dari server tanpa *refresh* layar.

**B. Spatie Laravel Permission**
*   **Lokasi Pemasangan:** Model `app/Models/User.php` (menggunakan *trait* `HasRoles`), Routing `routes/web.php` (penggunaan *middleware* `role:admin`), dan *Seeder* `database/seeders/SendjatiSeeder.php`.
*   **Peran:** Mengelola *Role-Based Access Control (RBAC)*. Memastikan bahwa modul inti seperti Inventaris, Transaksi, dan Keuangan dilindungi oleh *middleware* dan hanya dapat diakses oleh "Admin", bukan sembarang akun.

**C. Laravel Breeze (Inertia Stack)**
*   **Lokasi Pemasangan:** `resources/js/Pages/Auth/*` dan Controller bawaan Breeze di `app/Http/Controllers/Auth/`.
*   **Peran:** Menyediakan sistem otentikasi standar industri (Login, Register, Reset Password) yang kodenya sudah terhubung apik antara struktur backend Laravel dan UI komponen Vue.

---

## 3. Standar Keamanan & Integritas Data (Engineering Decisions)

**A. Pessimistic Locking (Mencegah Deadlock & Race Condition)**
*   **Lokasi Spesifik:** `app/Services/TransactionService.php` (pada pemanggilan `Item::where('id', ...)->lockForUpdate()->firstOrFail();`)
*   **Deskripsi Teknis:** Ini adalah fitur kritikal. Jika ada 2 staf (dari HP dan PC yang berbeda) men-submit pemotongan stok untuk item yang sama di milidetik yang bersamaan, sistem biasa akan mengalami *"Double Spend"* (stok menjadi minus tanpa disadari). Perintah `lockForUpdate()` memaksa *database engine* untuk memberikan status "terkunci" (*exclusive lock*) ke baris data tersebut. Jika terjadi bentrokan eksekusi (*Deadlock*), sistem diprogram untuk mengantri dan mencoba ulang prosesnya otomatis sebanyak maksimal 3 kali melalui perintah `DB::transaction(function() {...}, 3)`.

**B. Immutable Ledger (Buku Besar Mutlak)**
*   **Lokasi Spesifik:** Di ujung proses `app/Services/TransactionService.php` dan `app/Http/Controllers/LedgerController.php`
*   **Deskripsi Teknis:** Mengadopsi prinsip dasar *"Event Sourcing"*. Setiap aktivitas tidak hanya memodifikasi kolom `current_stock` di tabel barang, tetapi *wajib* mencetak log sejarah mutasi di tabel `inventory_ledgers`. Record di Ledger ini dikonfigurasi bersifat "abadi" (tidak boleh diedit), untuk menjamin jejak audit (Audit Trail) transaksi stok 100% transparan dan tidak bisa dimanipulasi diam-diam.

**C. Pencegahan Serangan Siber Dasar (XSS, SQL Injection & CSRF)**
*   **Lokasi Spesifik:** *Inherited Security* pada Vue Templates `{{ }}`, Eloquent Queries, dan Middleware.
*   **Deskripsi Teknis:**
    *   **SQL Injection:** Modifikasi data nakal tidak mungkin terjadi karena sistem secara ketat memisahkan sintaks SQL dari input data menggunakan metode `PDO Parameter Binding` bawaan Laravel.
    *   **XSS (Cross-Site Scripting):** Kode script yang disisipkan penyerang pada form akan ter-*escape* otomatis menjadi tulisan literal berkat standar *rendering* Vue.js. Atribut `v-html` hanya digunakan secara aman pada fitur Paginasi.
    *   **CSRF (Cross-Site Request Forgery):** Dilindungi penuh oleh *middleware* Laravel (XSRF-TOKEN) yang terhubung otomatis melalui *interceptor* dari Axios di dalam *core* Inertia.js.

**D. Analisis Celah Keamanan Lanjutan (Advanced Vulnerabilities)**
*   **Web Scraping (Data Mining):** Relatif sangat aman. Karena menggunakan *Client-Side Rendering* (Inertia), data dirender sebagai payload JSON internal, bukan elemen HTML mentah tradisional. Ditambah perlindungan `auth` middleware, bot *scraper* tanpa otentikasi akan otomatis terhalang oleh halaman Login.
*   **Insecure Direct Object Reference (IDOR / ID Enumeration):** Saat ini dilindungi oleh *middleware* `role:admin` pada rute RESTful seperti `/items/{item}`. Secara sistem, celah eksploitasi parameter URL sudah tertutup. Evaluasi arsitektural hanya diperlukan jika suatu saat ada level "Staff" parsial, untuk memastikan mereka tidak memanipulasi parameter ID guna mengintip data di luar batas otorisasinya.
*   **Reverse Engineering (Pemaparan Logika Frontend):** Karena bersifat SPA, bundel `app.js` memuat logika UI. Aturan arsitekturnya: **Sangat dilarang** untuk menyimpan algoritma rahasia perusahaan (seperti perhitungan harga rahasia, kunci API pihak ketiga, atau logika diskon) di dalam file Vue `*.vue`. Semua kalkulasi data wajib, dan saat ini sudah, tersentralisasi di sisi Backend (`app/Services/`).
*   **Information Leakage (Kebocoran Konfigurasi Lingkungan):** File konfigurasi krusial seperti `.env` dan direktori `docs/` ditempatkan dengan arsitektur standar di luar *public root* (berada di bawah kap mesin), sehingga tidak dapat dieksekusi atau diunduh langsung oleh peramban web (URL `domain.com/.env` akan menghasilkan *Error 404*). Satu-satunya perhatian khusus saat *Deployment* adalah variabel lingkungan `APP_DEBUG=true` yang **wajib dinonaktifkan menjadi `APP_DEBUG=false`** untuk mencegah eksposur kredensial database jika aplikasi mengalami kendala/crash.

---

## 4. Hirarki dan Pemisahan Tugas Direktori

Untuk mempercepat proses *troubleshooting*, ketahui pemisahan tanggung jawab *folder* berikut:

*   `app/Http/Controllers/`: (*Gateway*) Menjadi titik penerimaan *request* HTTP dan pengiriman respon (`Inertia::render`). Contoh: `DashboardController.php`.
*   `app/Services/`: (*Core Business Logic*) Menangani algoritma bisnis dan manipulasi kompleks database. Contoh: `TransactionService.php`. Sengaja dipisahkan dari Controller untuk mematuhi *Single Responsibility Principle (SRP)*.
*   `resources/js/Pages/`: Komponen UI tingkat Halaman yang dipetakan langsung dengan URL dari `routes/web.php`.
*   `resources/js/Components/`: Komponen UI kepingan (elemental) yang dipakai berulang di berbagai halaman, contoh: `Sidebar.vue`, `KpiCard.vue`, `PrimaryButton.vue`.
