# Panduan Developer: Sendjati Cafe Web ☕

Selamat datang di repositori **Sendjati Cafe Web**! Dokumen `DEV.md` ini dirancang khusus untuk programmer/developer yang akan berkontribusi dalam project ini. Di sini dijelaskan secara detail mengenai arsitektur, teknologi, dan *business logic* krusial yang digunakan.

---

## 1. Ikhtisar Proyek & Tech Stack
Sendjati Cafe Web adalah sistem Point of Sale (POS) dan Manajemen Inventaris yang didesain khusus untuk kebutuhan kedai kopi.

**Teknologi Utama (VILT Stack):**
*   **Backend:** Laravel 11/12 (PHP)
*   **Frontend:** Vue.js 3 (Composition API) + Inertia.js
*   **Styling:** Tailwind CSS (dengan efek modern seperti *Glassmorphism*)
*   **Database:** SQLite (Portable, File-based di `database/database.sqlite`)

---

## 2. Struktur Arsitektur & Prinsip Desain

Project ini berpegang teguh pada pola desain **SOLID**, **Event-Sourcing**, dan arsitektur **Multi-Department**.

### A. Segregasi Data Berbasis Departemen (Multi-Tenant lite)
Aplikasi dipecah secara hierarkis menjadi dua pilar: **Kitchen** dan **Bar**.
* Setiap barang (`Item`), kategori (`Category`), dan transaksi secara ketat membawa parameter `department`.
* Controller selalu memfilter *query* dengan kondisi `where('department', $request->department)` apabila dikirim dari *Frontend*.
* Dashboard analitik memproses kalkulasi secara terisolasi berdasarkan parameter *department* yang sedang aktif.

### B. Otomasi dengan Eloquent Observer
Pembuatan SKU tidak lagi membebani controller atau memusingkan pengguna. Hal ini diotomatisasi melalui `booted()` method di dalam Model `Item`. Sistem menggunakan *locking query* sederhana untuk menghitung nomor urut dan menyematkan *prefix* (contoh: `BAR-EQP-004`).

### C. Interface Segregation Principle (ISP) & Event Sourcing
Logika inventaris dipisah secara tegas berdasarkan sifat barangnya:
1.  `ConsumableInventoryInterface`: Kontrak untuk bahan baku (nambah & kurang stok).
2.  `EquipmentInventoryInterface`: Kontrak untuk aset tetap/alat. **Hanya memiliki fungsi penambahan stok**.
3.  **Ledger as Source of Truth**: Stok Alat dihitung secara dinamis dari tabel `inventory_ledgers`. Hal ini dilakukan via *Accessor* (`getAggregatedStockAttribute()`) untuk mencegah manipulasi stok database secara langsung.

### D. Optimasi UX (Single Page Application & Slide-over)
Pada iterasi terbaru, kompleksitas *routing* dikurangi drastis. Halaman `Transactions/Create.vue` telah dilebur ke dalam `Transactions/Index.vue` menggunakan *Slide-over Panel*. Pengembang *frontend* wajib memperhatikan reaktivitas Inertia form (`useForm`) dan *state management* agar form bisa ditutup/direset mulus tanpa me-*refresh* URL.

### E. Mobile-First & Responsive Layout
UI aplikasi dirancang secara *mobile-first* menggunakan *utility classes* Tailwind (`sm:`, `md:`). Komponen *table* data yang memakan banyak ruang secara otomatis ditransformasi menjadi format *Card View* vertikal saat diakses dari perangkat layar sempit. Seluruh form input, *sidebar* navigasi (*off-canvas*), dan grafik dasbor akan beradaptasi secara elegan tanpa mengorbankan fungsionalitas.

---

## 3. Peta Direktori Penting (Project Map)

Berikut adalah lokasi file-file *Core Logic* yang harus dipahami:

*   📂 **`app/Models/`**
    *   `Item.php`: Model utama barang. Di sinilah letak logika pemisahan *Consumable* dan *Equipment*, serta *auto-generate* SKU pada fungsi *booted*.
    *   `InventoryLedger.php`: Menyimpan setiap riwayat mutasi masuk/keluar.
*   📂 **`app/Contracts/`** & 📂 **`app/Services/`**
    *   Tempat penyimpanan *Interfaces* dan Service *backend* yang merangkap validasi bisnis logika dan *Event-sourcing*.
*   📂 **`resources/js/Pages/`**
    *   `Transactions/Index.vue`: Halaman paling vital yang memuat tabel riwayat sekaligus *Slide-over panel* untuk pembuatan transaksi baru.
    *   `MasterData/Items/Index.vue`: Fitur deteksi cerdas yang mencegah duplikasi barang dengan validasi asinkron (AJAX).

---

## 4. Alur Kerja (Workflow) Transaksi

1.  **Request Masuk:** Frontend Vue mengirim JSON ke `TransactionController@store`.
2.  **Validasi Request:** Diperiksa oleh `StoreTransactionRequest`.
3.  **Delegasi ke Service:** Masuk ke `TransactionService@processTransaction`.
4.  **Locking:** Barang di-lock di database (`lockForUpdate`).
5.  **Pemisahan Tipe:**
    *   Jika **Alat (Equipment)** ➔ Dialihkan ke `EquipmentService`. Jika tipe request adalah "keluar/retur", sistem langsung melempar `Exception` (menolak).
    *   Jika **Bahan (Consumable)** ➔ Dialihkan ke `ConsumableService`. Stok diproses secara konvensional.
6.  **Ledger:** Semua mutasi (sukses) dicatat secara mutlak di tabel `inventory_ledgers`.

---

## 5. Panduan Menjalankan Lingkungan Lokal

Bagi developer baru yang akan menjalankan aplikasi:

**1. Menjalankan Server (Shortcut Bersamaan)**
Gunakan perintah khusus di terminal untuk menyalakan Backend (Laravel) dan Frontend (Vite) secara bersamaan:
```bash
composer dev
```

**2. Menjalankan Server (Cara Manual)**
Buka dua terminal secara terpisah:
*   Terminal 1: `php artisan serve`
*   Terminal 2: `npm run dev`

**3. Informasi Login Default**
*   **Email:** `admin@sendjati.com`
*   **Password:** `password`

---

*Dokumen ini dibuat dan dikelola untuk memastikan seluruh tim developer sejalan dengan visi arsitektur perangkat lunak yang tangguh dan terukur.*
