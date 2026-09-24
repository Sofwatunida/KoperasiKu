# KoperasiKu — Kasir App

Aplikasi kasir berbasis **Laravel 13** terdiri dari dua bagian:

1. **Manajemen Produk** — tambah, edit, hapus, dan atur stok produk.
2. **Transaksi Kasir** — keranjang belanja dengan hitung total otomatis.
3. **Cetak Struk** — nota pembayaran yang otomatis terbuka saat transaksi selesai.

Struktur utama aplikasi ada di folder `kasir-app/`.

---

## Daftar Kesalahan yang Sudah Diperbaiki

Berikut ringkasan kesalahan yang ditemukan, penyebabnya, dan cara penyelesaiannya.

### 1. PHP CLI tidak bisa berjalan sama sekali (fatal error)

- **Gejala:** setiap perintah `php` gagal dengan `Fatal error: Unable to start standard module` dan warning `Cannot open "C:\xampp\php\extras\browscap.ini"`.
- **Penyebab:** konfigurasi `browscap` di `C:\xampp\php\php.ini` menunjuk ke file `browscap.ini` yang tidak ada. PHP ikut mati sehingga tidak ada perintah `artisan`/`composer` yang jalan.
- **Cara solve:** buat file kosong `C:\xampp\php\extras\browscap.ini`, atau komentari baris `browscap=...` di `php.ini`. Setelah itu `php -v` kembali normal.
- **Cek:** `php -v` dan `php -r "echo 'OK';"`.

### 2. File PHP tanpa tag pembuka `<?php`

- **Gejala:** "class not found" / teks mentah tampil di halaman, route tidak dikenali.
- **Penyebab:** 5 file PHP kehilangan tag pembuka `<?php` sehingga seluruh isi file dianggap sebagai teks biasa, bukan kode PHP:
  - `routes/web.php`
  - `app/Http/Controllers/ProductController.php`
  - `app/Http/Controllers/CartController.php`
  - `app/Models/Product.php`
  - 2 file migration (lihat no. 3)
- **Cara solve:** tambahkan tag `<?php` di awal setiap file.
- **Cek:** `php -l <nama_file>` akan menampilkan `No syntax errors detected` setelah diperbaiki.

### 3. File migration rusak/tidak lengkap

- **Gejala:** perintah `php artisan migrate` error atau tabel tidak terbentuk; transaksi gagal karena kolom tidak ada.
- **Penyebab & solusi:**
  - `create_products_table` & `create_transactions_table` tidak memiliki `<?php`, import (`Migration`, `Blueprint`, `Schema`), dan class anonymous (`return new class extends Migration`). Penulisan ulang lengkap mengikuti boilerplate Laravel.
  - Typo `table->id();` (tanpa `$`) pada `create_products_table` → `$table->id();`.
  - Kolom **`items`** tidak ada di tabel `transactions`, padahal `CartController::checkout()` menginsert datanya → kolom `items` (JSON) ditambahkan.
  - Kolom **`change_amount`** dibuat `nullable` karena belum tentu terisi pada data lama.
- **Cek:** `php artisan migrate:fresh --seed` berjalan tanpa error.

### 4. Error pada Blade template

- **Gejala:** halaman 500 atau tampilan kacau.
- **Penyebab & solusi:**
  - `resources/views/cashier/index.blade.php` memakai directive `@block('content')` / `@block` yang tidak ada di Blade → diganti `@section('content')` / `@endsection`.
  - `layouts/app.blade.php` pakai URL CDN Bootstrap palsu `https://jsdelivr.net` → diganti URL resmi:
    - CSS: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css`
    - JS: `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js`
  - Pemakaian `Request::is('...')` tanpa import facade → diganti helper global `request()->is('...')`.
  - `products/index.blade.php` isinya terduplikasi dua kali (HTML ganda) → ditulis ulang menjadi satu halaman utuh.

### 5. Model `Transaction` kosong

- **Gejala:** `Transaction::create([...])` gagal karena field tidak boleh diisi (mass assignment), kolom `items` tidak ter-cast.
- **Cara solve:** tambahkan `$fillable` dan cast `items => array` pada model `Transaction`. Imbasnya `CartController::receipt()` tidak perlu `json_decode()` manual lagi (akan error jika diterapkan pada array yang sudah ter-cast).
- **Cek:** transaksi tersimpan lengkap + struk tampil benar di `cashier/receipt/{id}`.

### 6. `checkout` kurang validasi dan tidak menyimpan kembalian

- **Penyebab:** `pay_amount` tidak divalidasi; nilai `change_amount` tidak disimpan (kolom selalu null).
- **Cara solve:** tambahkan `$request->validate(['pay_amount' => 'required|numeric|min:0'])` dan simpan `change_amount = pay_amount - total`.

### 7. Test gagal (404 & "no such table")

- **Penyebab:**
  - Route `/` tidak ada, padahal test bawaan Laravel mengetes `/`.
  - Test memakai database `:memory:` tanpa menjalankan migration.
- **Cara solve:**
  - Tambahkan route `/` yang me-redirect ke halaman kasir.
  - Gunakan trait `RefreshDatabase` pada test feature agar struktur tabel dibuat otomatis di database uji.

---

## Langkah Diagnosa & Solusi (SOP)

Urutan yang dipakai untuk menemukan dan memperbaiki semua error ini:

1. **Cek lingkungan** — pastikan PHP & Composer jalan: `php -v`, `composer -V`. Bila fatal error, cek `php.ini`
   (kasus `browscap.ini` di atas / ekstensi yang gagal dimuat).
2. **Lint semua file PHP yang dicurigai** — `php -l <file>` untuk menemukan syntax error, termasuk file tanpa `<?php`.
3. **Pastikan dependency terpasang** — `composer install`. Tanpa `vendor/`, `artisan` tidak jalan.
4. **Siapkan environment** — salin `.env.example` ke `.env` (bila belum ada), lalu `php artisan key:generate`.
5. **Jalankan migration** — `php artisan migrate`. Error di sini menunjuk masalah skema/DB.
6. **Cek route & controller** — `php artisan route:list` memastikan semua route terdaftar dengan benar.
7. **Perbaiki berdasarkan gejalanya** — per file/view/model sesuai tabel di atas.
8. **Uji end-to-end** — `php artisan test`. Test mencakup alur lengkap:
   tambah produk → tambah ke keranjang → checkout → struk → stok berkurang → kembalian benar.
9. **Jalankan server** — `php artisan serve`, buka `http://localhost:8000`.

---

## Hal yang Harus Diperhatikan ke Depannya (agar tidak terulang)

### Soal kode PHP/Laravel
- **Setiap file `.php` wajib diawali `<?php`** — periksa dengan `php -l` sebelum menyimpan.
- **Migration wajib mengikuti boilerplate standar** (`return new class extends Migration`, import `Migration`,
  `Blueprint`, `Schema`). Jangan pernah menghapus bagian boilerplate saat menempel kode.
- **Skema DB harus cocok dengan data yang di-insert** — setiap kolom yang dipakai di
  `Model::create()` harus ada di migration (contoh kasus kolom `items`).
- **Gunakan mass assignment dengan benar** — isi `$fillable` pada model, jangan cuma `$guarded = []` seenaknya.
- **Hindari directive Blade yang tidak dikenali** — gunakan `@section`/`@endsection`, bukan `@block`.
- **Hati-hati menyalin CDN/library** — pastikan URL benar-benar valid (bukan `https://jsdelivr.net` kosong).
- **Jangan duplikasi konten Blade** — gunakan layout + partial (`@extends` / `@include`) seperti `layouts/app.blade.php`.
- **Gunakan helper/facade yang tepat** — `request()->is()` (helper global) atau `use Illuminate\Http\Request;`
  secara eksplisit, agar tidak bergantung pada aliases yang dihapus di Laravel modern.
- **Cast tipe data** (mis. `items => array`) agar perilaku kolom JSON konsisten dan kode pembaca lebih ringkas.

### Soal lingkungan
- **Pastikan PHP CLI sehat** — XAMPP sering bermasalah soal `browscap.ini` atau ekstensi; cek `php -v` dulu.
- **Jangan commit `vendor/`, `.env`, dan `database/*.sqlite`** — biarkan di `.gitignore`. `.env` dihasilkan dari
  `.env.example`.
- **Setelah klon di mesin lain**, jalankan: `composer install`, salin `.env`, `php artisan key:generate`,
  `php artisan migrate`.

### Soal proses pengembangan
- **Selalu jalankan `php artisan test`** sebelum commit — ini menangkap error integrasi lebih cepat
  (kasus test 500 di aplikasi ini).
- **Jalankan `php artisan route:list`** setiap menambah route baru.
- **Uji alur nyata** (bukan hanya halaman tampil): tambah produk → hitung total → checkout → cek pengurangan stok
  & kembalian.
- **Simpan perubahan kecil per batch** agar error mudah dilacak; jangan menyalin seluruh kode tanpa menyesuaikan
  dengan versi Laravel yang dipakai (Laravel 13 di sini).

---

## Menjalankan Aplikasi

```bash
cd kasir-app
composer install
copy .env.example .env        # Windows
php artisan key:generate
php artisan migrate
php artisan serve
```

Buka `http://localhost:8000` (otomatis mengarah ke halaman Kasir).

---

## Menjalankan Test

```bash
cd kasir-app
php artisan test
```

Test memastikan halaman produk & kasir terbuka, produk bisa ditambah, serta alur transaksi (keranjang → checkout →
struk → stok berkurang → kembalian) berjalan benar.