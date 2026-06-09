# Panduan Belajar (ENGINEER.md) 🚀
*Ditujukan untuk Awam, Mahasiswa, atau Junior Programmer yang ingin mempelajari cara kerja sistem ini.*

Halo! Jika kamu baru belajar pemrograman web, Laravel, atau Vue, kamu berada di tempat yang tepat. Dokumen ini akan menjelaskan **cara kerja aplikasi Sendjati Cafe** dengan bahasa yang mudah dipahami layaknya sebuah cerita.

## 1. Apa itu "VILT" Stack?
Aplikasi ini menggunakan perpaduan teknologi yang sering disingkat **VILT**:
- **V (Vue.js)**: Ini adalah alat yang menggambar antarmuka (UI) di *browser* kamu. Semua tombol, warna, dan tabel yang bisa kamu klik dibuat pakai Vue. Vue membuat website terasa sangat mulus seperti aplikasi HP (sangat cepat dan halaman tidak berkedip putih saat pindah menu).
- **I (Inertia.js)**: Bayangkan Inertia seperti "jembatan ajaib". Biasanya, Vue (Frontend) dan Laravel (Backend) butuh jembatan rumit bernama API (REST/GraphQL). Tapi dengan Inertia, Laravel bisa langsung "melempar" data mentah ke Vue secara ajaib tanpa kode tambahan yang rumit.
- **L (Laravel)**: Ini adalah "otak" server. Dialah yang mengatur *database*, menghitung jumlah stok secara matematis, dan menentukan aturan bisnis (misalnya: tidak boleh membuang alat kopi).
- **T (Tailwind CSS)**: Ini adalah "tukang cat". Semua warna indah dan bentuk tombol bundar di aplikasi ini diketik menggunakan kode bahasa inggris singkat (contohnya kode `bg-emerald-500 rounded-lg`).

---

## 2. Konsep Departemen: Dapur vs Bar
Di sebuah kafe, area Dapur (Kitchen) dan Meja Peracik Kopi (Bar) adalah dua dunia yang berbeda. Sendjati Cafe memisahkan data keduanya sejak awal.
- Saat kamu menekan menu **"Bahan Baku Kitchen"**, Laravel akan mencari daftar barang di *database* dengan syarat/filter tertentu (`department = kitchen`).
- Hasilnya, staf Bar tidak akan pusing melihat daftar stok "Bawang Merah", dan staf Dapur tidak akan keliru mengubah stok "Sirup Karamel". Ini disebut isolasi data.

---

## 3. Sistem SKU Otomatis (Robot Penomoran)
Dulu, admin/kasir harus mengetik kode unik barang (SKU) secara manual yang rawan salah ketik. Sekarang, aplikasi ini punya "robot pengawas" di sisi Laravel yang bernama pola **Observer**.
- Saat kasir menekan tombol Simpan untuk barang baru, robot ini menahan data itu sejenak: *"Oh, ini barang Kitchen tipe Bahan Baku (Raw)"*. 
- Robot lalu mengecek di rak *database* nomor urut terakhir, lalu seketika memberi nomor urut baru yang rapi, misalnya `KTC-RAW-005` sebelum data itu dikunci ke *database*.

---

## 4. Keajaiban "Peralatan yang Tak Bisa Dihapus" (Event-Sourcing)
Di aplikasi ini, ada perbedaan hukum alam yang besar antara **Bahan Baku (Gula, Kopi)** dan **Alat (Mesin Espresso)**.
- Gula adalah barang konsumsi, stoknya bisa habis dipakai (bisa dikurangi).
- Mesin kopi tidak bisa "habis" dilarutkan ke cangkir! Dia adalah aset tetap.
- Oleh karena itu, *programmer* membuat aturan level sistem: **Alat tidak bisa di-Retur atau dikeluarkan**. Jika ada celah aplikasi yang mencoba mengurangi stok mesin kopi, sistem akan memunculkan *Error* dan menolak keras aksi tersebut. Konsep pemisahan perilaku benda ini sering disebut bagian dari prinsip **SOLID**.

---

## 5. Form Transaksi Estetik (Slide-Over Panel)
Coba klik menu "Transaksi Kitchen", lalu klik tombol "Buat Transaksi Baru". Perhatikan bahwa kamu **tidak dipindah ke halaman web baru**. Melainkan, ada laci (panel) yang meluncur perlahan dari sisi kanan layarmu!
- Ini dikoding di *file* `resources/js/Pages/Transactions/Index.vue`.
- Sistem menggunakan variabel `slideOverOpen` yang memiliki nilai `true` (benar) atau `false` (salah). 
- Jika diset `true`, panel dimunculkan. Jika `false` panel bersembunyi.
- Karena halaman tidak perlu berganti *link*, kuota internet lebih hemat dan kerja kasir menjadi berkali-kali lipat lebih cepat. Keren kan?

---

## Bagaimana Cara Saya Mulai Belajar?
1. Buka *folder* `routes/web.php` untuk melihat daftar semua URL (link jalur masuk) aplikasi.
2. Dari jalur tersebut, telusuri ke *folder* `app/Http/Controllers/` (Otak pengendali logika jembatan).
3. Lalu buka *folder* `resources/js/Pages/` untuk melihat bagaimana cantiknya tampilan halaman itu dikoding menggunakan Vue.

Selamat membongkar dan belajar dari aplikasi Sendjati Cafe Web ini! 💻
