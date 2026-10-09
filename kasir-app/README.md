# KoperasiKu

**Sistem Kasir & Manajemen Koperasi**

Aplikasi web untuk mengelola produk koperasi, mencatat penjualan, menghitung pembayaran, menyimpan riwayat transaksi, dan melihat ringkasan pendapatan.

## Fitur

- Login petugas dan admin menggunakan username dan password.
- Pendaftaran akun baru dengan role `petugas` dan login otomatis setelah berhasil.
- Dashboard berisi jumlah produk, stok, transaksi dan pendapatan hari ini, grafik penjualan tujuh hari, serta peringatan stok rendah.
- Ikon notifikasi pada header membuka daftar produk yang difilter ke stok rendah.
- Pencarian, pagination, tambah, edit, dan hapus produk. Produk yang sudah memiliki transaksi tidak dapat dihapus.
- Halaman kasir dengan pencarian produk, keranjang, penghitungan subtotal/total/kembalian, validasi pembayaran, dan batas stok.
- Checkout menyimpan transaksi dan detailnya dalam satu database transaction, lalu mengurangi stok.
- Struk transaksi dengan tata letak cetak thermal 80 mm.
- Riwayat dan detail transaksi.
- Laporan penjualan berdasarkan tanggal dengan ringkasan dan halaman cetak.
- Pengaturan nama, username, email, serta password petugas.
- Sidebar responsif untuk layar kecil.

Data harga, stok, transaksi, dashboard, dan laporan dibaca dari database. Nilai total serta stok checkout tetap diverifikasi di server, bukan hanya di browser.

## Teknologi

- PHP 8.3 atau lebih baru dan Laravel 13.
- SQLite untuk penyimpanan aplikasi.
- Blade untuk halaman server-rendered.
- Tailwind CSS 4 dan Vite 8 untuk aset frontend.
- JavaScript native untuk menu responsif, toggle password, dan interaksi keranjang.
- PHPUnit 12 untuk pengujian.

Aplikasi tidak menggunakan Bootstrap atau library chart eksternal. Grafik dashboard digambar sebagai SVG dari data transaksi.

## Struktur Folder

```text
kasir-app/
|-- app/
|   |-- Http/Controllers/
|   `-- Models/
|-- bootstrap/
|-- config/
|-- database/
|   |-- migrations/
|   `-- seeders/
|-- public/
|-- resources/
|   |-- css/
|   |-- js/
|   `-- views/
|-- routes/
|-- storage/
|-- tests/
|-- .env.example
|-- artisan
|-- composer.json
|-- package.json
`-- db_kasir
```

## Penjelasan Folder

- `app/Http/Controllers`: menerima request dan mengatur alur login, dashboard, produk, kasir, transaksi, laporan, dan pengaturan.
- `app/Models`: model Eloquent untuk user, produk, transaksi, dan detail transaksi.
- `bootstrap` dan `config`: bootstrap aplikasi dan konfigurasi Laravel.
- `database/migrations`: skema tabel dan perubahan kolom. Migration katalog dan kasir juga melengkapi kolom yang hilang dengan backfill untuk record lama.
- `database/seeders`: akun, produk, dan transaksi contoh untuk pengembangan.
- `public`: front controller, aset publik, dan hasil build Vite.
- `resources/views`: halaman Blade untuk login, dashboard, produk, kasir, struk, transaksi, laporan, pengaturan, dan layout bersama.
- `resources/css/app.css`: palet warna, komponen UI, tabel, form, dan gaya cetak.
- `resources/js/app.js`: menu mobile, toggle password, dan keranjang kasir.
- `routes/web.php`: route web dan middleware autentikasi.
- `storage`: log, cache, session, dan file aplikasi.
- `tests`: tes feature dan unit.
- `db_kasir`: file SQLite proyek yang digunakan untuk data lokal. Buat cadangan sebelum menjalankan perubahan skema.

## Controller

- `AuthController`: menampilkan login, memvalidasi kredensial, dan logout.
- `DashboardController`: menghitung statistik dan data grafik dari database.
- `ProductController`: pencarian, pagination, CRUD, validasi, serta perlindungan produk yang memiliki riwayat penjualan.
- `CartController`: menampilkan kasir, memvalidasi checkout, menyimpan transaksi/detail, mengurangi stok, dan menampilkan struk.
- `TransactionController`: menampilkan riwayat dan detail transaksi.
- `ReportController`: memfilter transaksi berdasarkan periode dan menampilkan laporan/cetak.
- `SettingController`: membaca dan memperbarui profil akun.

## Model dan Relasi

- `User` memiliki banyak `Transaction`.
- `Transaction` dimiliki satu `User` dan memiliki banyak `TransactionDetail`.
- `TransactionDetail` menghubungkan transaksi dengan satu `Product`.
- `Product` memiliki banyak detail transaksi.

Model berada di `app/Models`: `User.php`, `Product.php`, `Transaction.php`, dan `TransactionDetail.php`.

## Database dan Migration

Koneksi default adalah SQLite. Skema aktual mencakup tabel `users`, `products`, `transactions`, `transaction_details`, `sessions`, `cache`, `jobs`, serta tabel pendukung Laravel.

Migration utama yang tersedia:

- `0001_01_01_000000_create_users_table.php`: user, reset password, dan session.
- `0001_01_01_000001_create_cache_table.php`: cache.
- `0001_01_01_000002_create_jobs_table.php`: queue.
- `2026_09_12_022341_create_products_table.php`: tabel produk dasar.
- `2026_09_12_022428_create_transactions_table.php`: tabel transaksi dasar.
- `2026_09_24_000000_create_transaction_details_table.php`: rincian produk per transaksi.
- `2026_10_03_000001_add_username_and_role_to_users_table.php`: username dan role.
- `2026_10_03_000002_add_catalog_fields_to_products_table.php`: kode, harga beli/jual, stok, dan satuan.
- `2026_10_03_000003_add_cashier_fields_to_transactions_table.php`: kode transaksi, kasir, nilai bayar/kembalian, dan tanggal.

Jalankan `php artisan migrate` untuk menerapkan migration yang masih pending. Hindari `php artisan migrate:fresh` pada `db_kasir`: perintah itu menghapus seluruh tabel dan data.

## Route Utama

Route `login` dan `register` memakai middleware `guest`. Route aplikasi lainnya memakai middleware `auth`.

| Route | Kegunaan |
|---|---|
| `GET /login`, `POST /login` | Form dan proses login |
| `GET /register`, `POST /register` | Form dan proses pendaftaran akun petugas |
| `POST /logout` | Keluar dari akun |
| `GET /dashboard` | Dashboard |
| `GET /produk` | Daftar dan pencarian produk |
| `GET /produk/create`, `POST /produk` | Form dan penyimpanan produk |
| `GET /produk/{product}/edit`, `PUT /produk/{product}` | Form dan pembaruan produk |
| `DELETE /produk/{product}` | Menghapus produk tanpa riwayat transaksi |
| `GET /kasir`, `POST /kasir/checkout` | Kasir dan checkout |
| `GET /kasir/struk/{transaction}` | Struk transaksi |
| `GET /riwayat`, `GET /riwayat/{transaction}` | Riwayat dan detail transaksi |
| `GET /laporan`, `GET /laporan/cetak` | Laporan dan cetak berdasarkan periode |
| `GET /pengaturan`, `PUT /pengaturan` | Lihat dan perbarui akun |

## Views dan Aset

- `resources/views/layouts/app.blade.php`: sidebar, header, notifikasi, dan layout responsif.
- `resources/views/auth/login.blade.php` dan `resources/views/auth/register.blade.php`: login dan pendaftaran petugas.
- `resources/views/dashboard.blade.php`: statistik, grafik, stok rendah, dan transaksi terbaru.
- `resources/views/products/`: daftar, form reusable, tambah, dan edit produk.
- `resources/views/cashier/`: kasir dan halaman struk.
- `resources/views/cetak/struk.blade.php`: partial struk thermal yang digunakan halaman cetak.
- `resources/views/transactions/`: riwayat dan detail transaksi.
- `resources/views/reports/`: laporan dan halaman cetak.
- `resources/views/settings/`: pengaturan akun.
- `resources/views/products.blade.php` dan `resources/views/transactions.blade.php` adalah template lama yang tidak digunakan oleh route aktif.

## Alur Aplikasi

**Login dan pendaftaran:** username dicari di tabel `users`; password diverifikasi dengan hash Laravel. Form pendaftaran memvalidasi username/email unik dan password, lalu menetapkan role `petugas` di server dan langsung membuat sesi login. Role tidak dapat dikirim dari form. Halaman aplikasi hanya dapat dibuka setelah autentikasi.

**Produk:** controller memvalidasi kode unik, nama, harga, stok, dan satuan sebelum menyimpan. Pencarian mencakup kode serta nama.

**Kasir:** browser menghitung tampilan subtotal dan kembalian. Saat checkout, server membaca ulang harga dan stok dari database, menggabungkan baris produk duplikat, menolak stok atau pembayaran yang tidak cukup, lalu menyimpan transaksi dan detail dalam transaksi database. Stok baru dikurangi setelah validasi berhasil.

**Laporan:** data berasal dari tabel transaksi dan dapat difilter menggunakan tanggal mulai dan tanggal akhir.

## Cara Menjalankan

Jalankan perintah dari folder `kasir-app` di PowerShell:

```powershell
cd kasir-app
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
```

Di `.env`, pastikan `DB_CONNECTION=sqlite` dan atur `DB_DATABASE` ke path absolut file `db_kasir`. Contoh format Windows:

```text
DB_DATABASE=C:/path/ke/KoperasiKu/kasir-app/db_kasir
```

Cadangkan `db_kasir` sebelum migration. Kemudian jalankan:

```powershell
php artisan migrate
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`. Untuk hot reload frontend, jalankan `npm run dev` di terminal kedua sebagai pengganti `npm run build` saat mengembangkan.

Jika menyiapkan database SQLite baru, buat file database terlebih dahulu, atur `DB_DATABASE` ke path file tersebut, lalu jalankan `php artisan migrate`. Seeder contoh hanya perlu dijalankan pada database kosong:

```powershell
php artisan db:seed
```

Seeder menyediakan akun pengembangan `siti` dan `admin`, keduanya menggunakan password `password`. Kredensial ini hanya untuk lokal; ubah password sebelum aplikasi dipakai bersama atau dipublikasikan. `TransactionSeeder` membuat transaksi demo dan tidak dimaksudkan untuk dijalankan berulang kali.

## Menjalankan Tes

```powershell
php artisan test
```

Tes feature mencakup akses guest/auth, pendaftaran akun, login/logout, halaman aplikasi, CRUD produk, filter stok rendah, checkout, pembayaran kurang, stok tidak cukup, baris keranjang duplikat, filter tanggal laporan, penyimpanan detail, pengurangan stok, dan struk.

## Troubleshooting

- **`No application encryption key has been specified`:** pastikan `.env` ada, lalu jalankan `php artisan key:generate`.
- **Database SQLite tidak ditemukan:** periksa path absolut pada `DB_DATABASE`; path harus menunjuk ke file yang benar-benar ada.
- **`Vite manifest not found`:** jalankan `npm install` lalu `npm run build`, atau hidupkan `npm run dev` di terminal kedua.
- **Error 500:** periksa `storage/logs/laravel.log`. Pastikan migration sudah dijalankan dan konfigurasi database mengarah ke `db_kasir`.
- **`php artisan db:show` meminta ekstensi `intl`:** pada beberapa versi PHP perintah tersebut membutuhkan ekstensi Intl; aplikasi dan tes tetap dapat memakai koneksi SQLite tanpa perintah itu.
