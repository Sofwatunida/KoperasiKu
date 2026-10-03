# KoperasiKu - Aplikasi Kasir Sederhana (Laravel)

Dokumentasi ini ditulis untuk membantu siapa saja yang sedang belajar Laravel,
bukan hanya untuk menjalankan aplikasi ini.

---

## A. Tentang Project

**KoperasiKu** adalah aplikasi **kasir (Point of Sale)** berbasis web untuk
membantu sebuah toko atau koperasi mengelola penjualan sehari-hari.

### Tujuan aplikasi

1. Mencatat data produk (nama, harga, stok).
2. Melakukan transaksi penjualan dengan bantuan halaman kasir dan keranjang belanja.
3. Menyimpan riwayat transaksi beserta rincian produk yang terjual.
4. Mencetak struk pembayaran untuk setiap transaksi.
5. Melindungi data aplikasi dengan sistem login (Laravel Breeze).

### Fungsi utama aplikasi

| Fungsi | File View | Route |
| --- | --- | --- |
| Login / Register / Logout | `resources/views/auth/*` | `/login`, `/register`, `POST /logout` |
| Dashboard (AdminLTE) | `resources/views/dashboard.blade.php` | `/dashboard` |
| Daftar produk, tambah cepat, edit, hapus | `resources/views/products/index.blade.php` | `GET /products` |
| Form tambah produk | `resources/views/products/create.blade.php` | `GET /products/create` |
| Form edit produk | `resources/views/products/edit.blade.php` | `GET /products/{id}/edit` |
| Halaman kasir dan keranjang | `resources/views/cashier/index.blade.php` | `GET /cashier` |
| Riwayat transaksi | `resources/views/transactions.blade.php` | `GET /transactions` |
| Detail transaksi | `resources/views/transactions/show.blade.php` | `GET /transactions/{id}` |
| Cetak struk | `resources/views/cashier/receipt.blade.php` (memakai `cetak/struk.blade.php`) | `GET /cashier/receipt/{id}` |
| Profil user | `resources/views/profile/edit.blade.php` | `GET /profile` |

Semua route di atas **wajib login** (middleware `auth`), kecuali halaman
login dan register.

---

## B. Teknologi yang Digunakan

| Teknologi | Versi | Fungsi |
| --- | --- | --- |
| **PHP** | 8.4 | Bahasa pemrograman backend. Semua logika kasir, transaksi, dan query database ditulis di sini. |
| **Laravel** | 13.x | Framework PHP: routing, controller, model, database, keamanan, session. |
| **Laravel Breeze** | 2.x | Paket pembuat halaman login, register, lupa password, dan profil agar tidak ditulis manual. |
| **Blade** | bawaan Laravel | Mesin template untuk membuat halaman HTML dengan sintaks `.blade.php`. |
| **AdminLTE** | 4.x | Template dashboard: sidebar, navbar, footer, widget, dan ikon. Dipakai pada halaman **dashboard**. |
| **Bootstrap** | 5.3 | CSS framework untuk tampilan **custom** (produk, kasir, transaksi). |
| **Bootstrap Icons** | 1.x | Kumpulan ikon dengan class `bi bi-*`. |
| **Tailwind CSS** | 3.x | CSS utility untuk halaman bawaan Breeze (login, register, profil). |
| **Alpine.js** | 3.x | JavaScript ringan untuk interaksi dropdown dan menu di layout Breeze. |
| **Vite** | 8.x | Tool yang menggabungkan file CSS dan JS menjadi satu paket, serta menonaktifkan cache saat `npm run dev`. |
| **SQLite** | - | Database. File-nya ada di `database/database.sqlite`, jadi tidak perlu install server database. |
| **Composer** | - | Package manager untuk PHP, dipakai dengan `composer install`. |
| **NPM** | - | Package manager untuk JavaScript dan CSS, dipakai dengan `npm install`. |
| **Pest** | 4.x | Framework testing untuk mengecek fitur tidak rusak (`php artisan test`). |

### Kenapa ada tiga sumber tampilan sekaligus?

Supaya **desain custom tidak rusak** dan **AdminLTE tetap terpakai**:

| Sumber | Dipakai oleh | Cara memuat |
| --- | --- | --- |
| **Tailwind + Alpine (Breeze)** | login, register, profil | `@vite(['resources/css/app.css', 'resources/js/app.js'])` |
| **AdminLTE** | dashboard | `@vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])` |
| **Bootstrap 5 (custom)** | produk, kasir, transaksi, struk | tag `<link>` CDN di `resources/views/layouts/custom.blade.php` |

Pemisahan ini penting. Kalau CSS Tailwind, AdminLTE, dan Bootstrap dimuat
bersama di satu halaman, class mereka saling menimpa dan tampilan jadi kacau.

---

## C. Cara Menjalankan Project

### Kebutuhan sistem

- PHP **8.3** atau lebih baru (dianjarkan 8.4)
- Composer 2
- Node.js 20 atau lebih baru, dan NPM
- SQLite (sudah ikut bawaan PHP)

### Langkah-langkah

```bash
# 1. Install dependency PHP (Laravel, Breeze, AdminLTE, dll)
composer install

# 2. Salin file konfigurasi environment
copy .env.example .env

# 3. Buat APP_KEY
php artisan key:generate

# 4. Install dependency JavaScript
npm install

# 5. Pastikan file database SQLite ada
#    (klik kanan di folder database > New File > database.sqlite)

# 6. Jalankan migration (membuat tabel)
php artisan migrate

# 7. Pastikan .env memakai SQLite
#    DB_CONNECTION=sqlite

# 8. Build asset untuk produksi
npm run build

# 9. Jalankan server
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

### Mode pengembangan (recommended)

Untuk mengerjakan, jalankan **dua terminal**:

```bash
# Terminal 1 - server Laravel
php artisan serve

# Terminal 2 - Vite dev server (auto reload saat file CSS/JS berubah)
npm run dev
```

### Membuat user pertama

```bash
php artisan tinker
```

Lalu ketik:

```php
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@koperasiku.test',
    'password' => 'password',
]);
```

Atau daftar langsung lewat halaman `/register`.

### Menjalankan test

```bash
php artisan test
```

---

## D. Struktur Folder Laravel

```text
kasir-app/
|-- app/                          # Otak aplikasi PHP (backend)
|   |-- Http/
|   |   `-- Controllers/          # Mengatur apa yang terjadi saat URL dibuka
|   |-- Models/                   # Mengatur data dan relasi ke database
|   |-- Providers/                # Konfigurasi aplikasi
|   |-- Requests/                 # Kumpulan aturan validasi input
|   `-- View/Components/          # Komponen layout class-based (Breeze)
|
|-- bootstrap/                    # Cache dan konfigurasi awal Laravel
|-- config/                       # File konfigurasi (app, database, adminlte, dll)
|
|-- database/
|   |-- migrations/               # Definisi struktur tabel
|   |-- factories/                # Data contoh untuk testing
|   `-- database.sqlite           # File database kita
|
|-- public/                       # File yang bisa diakses langsung dari browser
|   |-- build/                    # Hasil build Vite (CSS/JS hasil build)
|   `-- vendor/adminlte/          # Asset AdminLTE yang di-publish
|
|-- resources/                    # Semua sumber tampilan (lihat bagian E)
|   |-- css/
|   |-- js/
|   `-- views/
|
|-- routes/
|   |-- web.php                   # Daftar URL untuk browser
|   `-- console.php               # Perintah terminal
|
|-- storage/                      # Log, cache, file upload, session
|-- tests/                        # Test otomatis
|-- vendor/                       # Package PHP yang di-download Composer
|-- node_modules/                 # Package JavaScript yang di-download NPM
|-- .env                          # Konfigurasi rahasia (JANGAN di-commit)
|-- artisan                       # CLI Laravel
|-- composer.json                 # Daftar package PHP
`-- package.json                  # Daftar package JavaScript
```

### Penjelasan singkat tiap folder

| Folder | Penjelasan sederhana |
| --- | --- |
| `app/` | Berisi seluruh logika aplikasi. Ini bagian yang paling sering kamu edit. |
| `app/Http/Controllers` | Resepsionis: menerima request dari route, memanggil model, lalu mengirim view. |
| `app/Models` | Pencatatan: mewakili tabel database dan relasi antar tabel. |
| `app/Http/Requests` | Berisi aturan validasi, misalnya `LoginRequest` dan `ProfileUpdateRequest`. |
| `app/View/Components` | Layout berbentuk class PHP yang dipakai Breeze: `AppLayout` dan `GuestLayout`. |
| `bootstrap/` | File internal Laravel untuk cache dan konfigurasi awal. Jarang disentuh. |
| `config/` | Semua konfigurasi aplikasi: nama app, database, locale, dan `adminlte.php`. |
| `database/migrations` | Sejarah perubahan struktur tabel. Setiap file adalah satu perubahan. |
| `database/factories` | Pembuat data palsu (misal 10 user palsu) khusus untuk testing. |
| `public/` | Folder yang isinya bisa diakses tanpa diproses Laravel, seperti aset dan hasil build. |
| `resources/` | Sumber tampilan: Blade, CSS, dan JS. Diproses oleh Vite. |
| `routes/` | Peta jalan aplikasi: URL mana yang menuju ke controller mana. |
| `storage/` | File sementara: log error, cache, hasil compile Blade, dan upload. |
| `tests/` | Test otomatis untuk memastikan fitur tidak rusak. |
| `vendor/` | Ribuan file library PHP dari Composer. **Jangan diedit manual.** |
| `node_modules/` | Ribuan file library JavaScript dari NPM. **Jangan diedit manual.** |

---

## E. Penjelasan `resources/`

### Apa itu folder `resources/`?

`resources/` adalah **folder sumber daya aplikasi**. Isinya bukan file yang
langsung dibaca browser. Semua file di sini akan **diproses atau di-build**
oleh Vite, lalu hasilnya ditaruh di `public/build/`.

```text
resources/
|-- views/       # file Blade (.blade.php) = template HTML
|-- css/         # file CSS
`-- js/          # file JavaScript
```

### Kenapa harus di-build?

Browser hanya bisa membaca CSS dan JavaScript biasa. File `.blade.php` juga
mengandung sintaks khusus Laravel seperti `@extends` dan `@section` yang tidak
dipahami browser. Karena itu:

```text
resources/css/app.css  --+
resources/js/app.js    --+-->  Vite build  -->  public/build/assets/...  -->  browser
resources/views/*.php  --+
```

Kalau file sumber berubah tapi `npm run build` tidak dijalankan, browser masih
memakai versi lama. Karena itu `npm run dev` lebih nyaman saat mengerjakan.

### Isi `resources/` pada project KoperasiKu

#### `resources/views/` - semua halaman

```text
resources/views/
|-- layouts/
|   |-- app.blade.php          # Layout Breeze (Tailwind) untuk halaman profil
|   |-- guest.blade.php        # Layout Breeze (Tailwind) untuk halaman auth
|   |-- navigation.blade.php   # Navbar Breeze (dropdown user dan logout)
|   |-- admin.blade.php        # Layout AdminLTE (sidebar + navbar) untuk dashboard
|   `-- custom.blade.php       # Layout custom (Bootstrap) untuk produk, kasir, transaksi
|
|-- auth/                       # login, register, forgot/reset password, verify email
|-- profile/                    # Halaman edit profil user
|-- components/                 # Komponen Blade Breeze (nav-link, input, button, modal)
|
|-- dashboard.blade.php         # Dashboard AdminLTE
|-- products/
|   |-- index.blade.php         # Daftar produk + tambah cepat + edit inline + hapus
|   |-- create.blade.php        # Form tambah produk
|   `-- edit.blade.php          # Form edit produk
|-- cashier/
|   |-- index.blade.php         # Halaman kasir: pilih produk, keranjang, bayar
|   `-- receipt.blade.php       # Halaman pembuka struk (tombol cetak)
|-- transactions/
|   `-- show.blade.php          # Detail satu transaksi
|-- cetak/struk.blade.php       # Tampilan struk yang siap dicetak
|-- transactions.blade.php      # Riwayat transaksi
|-- welcome.blade.php           # Halaman welcome bawaan Laravel (tidak dipakai, karena / me-redirect ke kasir)
`-- vendor/adminlte/...         # View bawaan paket AdminLTE (jangan diedit)
```

#### `resources/css/`

| File | Isi |
| --- | --- |
| `app.css` | Tailwind CSS, dipakai layout Breeze. **Tidak** mengimpor AdminLTE. |
| `adminlte.css` | Titik masuk CSS AdminLTE, Bootstrap Icons, dan OverlayScrollbars. Dipakai layout AdminLTE. |

#### `resources/js/`

| File | Isi |
| --- | --- |
| `app.js` | Alpine.js, dipakai layout Breeze. |
| `adminlte.js` | Bootstrap, OverlayScrollbars, dan AdminLTE. Dipakai layout AdminLTE. |

> JavaScript keranjang belanja pada halaman kasir **tidak** disimpan di
> `resources/js/`. Sengaja ditulis inline di bagian `@push('scripts')` pada
> `resources/views/cashier/index.blade.php` karena logikanya sangat spesifik
> untuk halaman kasir.

---

## F. Template dan Layout (Blade)

### Apa itu Blade?

Blade adalah **mesin template** Laravel. File-nya berakhiran `.blade.php`.
Tujuannya agar kamu bisa menulis HTML dengan sedikit kode PHP, misalnya
looping dan kondisi.

### Apa itu Layout?

Layout adalah **kerangka halaman** (sidebar, navbar, footer, dan tempat konten)
yang dipakai bersama oleh banyak halaman. Tujuannya supaya tidak menulis ulang
sidebar sepuluh kali.

### Directive Blade yang dipakai di project ini

| Directive | Fungsi | Contoh di project |
| --- | --- | --- |
| `@extends('nama.layout')` | Menyatakan halaman ini memakai layout tersebut. | `@extends('layouts.custom')` di `products/index.blade.php` |
| `@section('nama')` | Membuat wadah isi di dalam halaman. | `@section('content')` |
| `@yield('nama')` | Letak penampungan `@section` di dalam layout. | `@yield('content')` di `layouts/custom.blade.php` |
| `@include('nama.partial')` | Menyisipkan potongan view di dalam file lain. | `@include('cetak.struk', ['transaction' => $transaction])` di `cashier/receipt.blade.php` |
| `@push('scripts')` dan `@stack('scripts')` | Menaruh kode tambahan (misal `<script>`) di bagian bawah layout. | Dipakai halaman kasir untuk logika keranjang. |
| `@if`, `@foreach`, `@forelse`, `@else` | Logika dasar di dalam view. | Loop tabel produk dan transaksi. |
| `{{ }}` | Menampilkan nilai dengan escape otomatis (aman dari XSS). | `{{ $product->name }}` |

### Contoh halaman dengan `@extends`

```blade
{{-- resources/views/products/index.blade.php --}}
@extends('layouts.custom')

@section('title', 'Manajemen Produk')

@section('content')
    <h2>Manajemen Produk</h2>
    ...
@endsection
```

```blade
{{-- resources/views/layouts/custom.blade.php --}}
<title>@yield('title', 'Sistem Kasir Laravel')</title>

<main class="py-4">
    @yield('content')   {{-- isi dari @section('content') muncul di sini --}}
</main>

@stack('scripts')
```

### Kapan halaman tidak memakai layout?

- **`resources/views/cetak/struk.blade.php`** dan
  **`resources/views/cashier/receipt.blade.php`**: struk tidak boleh memakai
  sidebar dan navbar karena akan ikut tercetak. Keduanya sengaja dibuat HTML
  mandiri.
- **`resources/views/vendor/adminlte/`**: milik paket AdminLTE, tidak diedit.

### Layout dipakai halaman apa saja?

| Layout | Dipakai oleh | Tampilan |
| --- | --- | --- |
| `layouts/custom.blade.php` | `products/*`, `cashier/index`, `transactions*` | Custom Bootstrap, navbar "Toko Kita Jaya" |
| `layouts/admin.blade.php` | `dashboard.blade.php` | AdminLTE, sidebar dan navbar |
| `layouts/app.blade.php` | `profile/edit.blade.php` (`<x-app-layout>`) | Tailwind Breeze |
| `layouts/guest.blade.php` | seluruh `auth/*` (`<x-guest-layout>`) | Tailwind Breeze |

---

## G. Penjelasan AdminLTE

### AdminLTE dipakai untuk apa?

AdminLTE adalah **template dashboard gratis** yang sudah menyediakan komponen
bagus: sidebar, navbar, breadcrumb, footer, widget `small-box`, `card`,
`info-box`, tabel, form, sampai ikon. KoperasiKu memakainya untuk **halaman
dashboard** serta sebagai sumber komponen navigasi.

### File AdminLTE ada di mana?

| Lokasi | Isi |
| --- | --- |
| `config/adminlte.php` | Konfigurasi AdminLTE (menu, warna sidebar, layout fixed, dll). |
| `resources/css/adminlte.css` | File CSS entry point AdminLTE, dibuat oleh `adminlte:install`. |
| `resources/js/adminlte.js` | File JS entry point AdminLTE dan Bootstrap. |
| `resources/views/vendor/adminlte/` | View dan partial AdminLTE yang di-publish ke project. |
| `public/vendor/adminlte/` | Asset AdminLTE (css, js, img, font). |
| `vendor/colorlibhq/adminlte-laravel/` | Source package AdminLTE. |
| `resources/views/layouts/admin.blade.php` | Layout AdminLTE buatan project sendiri (sidebar + navbar). |

### Bagaimana asset dimuat?

Di `resources/views/layouts/admin.blade.php`:

```blade
@vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
```

`@vite` membaca `public/build/manifest.json` hasil `npm run build`, lalu menulis
tag `<link>` dan `<script>` secara otomatis. Kedua file tersebut sudah
terdaftar di `vite.config.js`:

```js
input: [
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/css/adminlte.css',
    'resources/js/adminlte.js',
],
```

### Bagaimana sidebar dibuat?

Sidebar dibuat manual di `resources/views/layouts/admin.blade.php`:

```blade
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text fw-light">KoperasiKu</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-speedometer2"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            ...
        </ul>
    </div>
</aside>
```

Penjelasan singkat:

- `<aside class="app-sidebar">` adalah elemen sidebar AdminLTE.
- `data-lte-toggle="treeview"` mengaktifkan submenu yang bisa dibuka dan ditutup.
- `<i class="nav-icon bi bi-...">` adalah ikon dari Bootstrap Icons.
- `{{ request()->routeIs('products.*') ? 'active' : '' }}` otomatis menambah
  class `active` pada menu halaman yang sedang dibuka.

### Bagaimana navbar dibuat?

```blade
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#">
                    <i class="bi bi-list"></i>   {{-- tombol buka/tutup sidebar --}}
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            @auth
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">Profil Saya</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </div>
</nav>
```

`data-bs-toggle="dropdown"` adalah fitur **Bootstrap**, bukan AdminLTE. Ia
bekerja karena `resources/js/adminlte.js` meng-import `bootstrap`.

---

## H. Penjelasan Routing

### Apa itu route?

Route adalah **peta alamat ke controller**. Didefinisikan di `routes/web.php`.

```php
Route::get('cashier', [CartController::class, 'index'])->name('cashier.index');
```

Artinya:

| Bagian | Arti |
| --- | --- |
| `Route::get` | Method HTTP yang boleh diakses. GET berarti hanya melihat. |
| `'cashier'` | Alamat di browser: `http://localhost:8000/cashier`. |
| `[CartController::class, 'index']` | Controller dan method yang menangani request tersebut. |
| `->name('cashier.index')` | Nama route, supaya bisa dipanggil dari view dengan `route()`. |

### Alur lengkap satu request

```text
Browser membuka /transactions
        |
        v
routes/web.php  -->  Route 'transactions.index'
        |
        v
TransactionController@index()
        |
        v
Model: Transaction::with('details.product')->latest()->get()
        |
        v
Database (SQLite: transactions, transaction_details, products)
        |
        v
Model mengembalikan data ke Controller
        |
        v
Controller: return view('transactions', ['transactions' => $transactions])
        |
        v
Blade transactions.blade.php  +  layouts/custom.blade.php
        |
        v
HTML dikirim kembali ke Browser
```

### Kenapa route harus punya nama?

Karena view tidak boleh menulis URL secara manual. View memakai `route('nama')`
supaya URL cukup diubah di satu tempat.

```blade
{{-- Salah: URL ditulis manual, rawan rusak --}}
<a href="/cashier">Kembali ke Kasir</a>

{{-- Benar: mengikuti route --}}
<a href="{{ route('cashier.index') }}">Kembali ke Kasir</a>
```

Kalau nanti URL `/cashier` berubah jadi `/kasir`, kamu cukup mengubah
`routes/web.php` dan semua link ikut benar.

`route()` juga bisa mengirim parameter route:

```blade
{{ route('cashier.receipt', $transaction->id) }}   -->  /cashier/receipt/5
{{ route('products.update', $product->id) }}       -->  /products/3 (PUT)
```

### Daftar route aplikasi (hasil `php artisan route:list`)

```text
GET    /                              -> redirect ke cashier.index
GET    /dashboard                     -> dashboard (auth)
GET    /login                         -> halaman login
POST   /login                         -> proses login
POST   /logout                        -> logout
GET    /register                      -> halaman register
POST   /register                      -> proses register
GET    /products                      -> products.index          (auth)
GET    /products/create               -> products.create         (auth)
POST   /products                      -> products.store          (auth)
GET    /products/{product}/edit       -> products.edit           (auth)
PUT    /products/{product}            -> products.update         (auth)
DELETE /products/{product}            -> products.destroy        (auth)
GET    /cashier                       -> cashier.index           (auth)
POST   /cashier/checkout              -> cashier.checkout        (auth)
GET    /cashier/receipt/{transaction} -> cashier.receipt        (auth)
GET    /transactions                  -> transactions.index      (auth)
GET    /transactions/{transaction}    -> transactions.show       (auth)
GET    /profile                       -> profile.edit            (auth)
PATCH  /profile                       -> profile.update          (auth)
DELETE /profile                       -> profile.destroy         (auth)
```

> Route `demo/*` dan `docs/*` milik paket demo AdminLTE, bukan bagian fitur
> KoperasiKu. Bisa dimatikan dengan mengubah `'demo' => false` dan
> `'docs' => false` di `config/adminlte.php`.

---

## I. Penjelasan Controller

### Apa itu Controller?

Controller adalah file di `app/Http/Controllers/` yang **menerima request**,
**memanggil model**, lalu **mengirim view** ke browser. Controller tidak
menyentuh tampilan secara langsung.

### Controller yang ada di project

#### `CartController.php` - halaman kasir

| Method | Route | Tugas |
| --- | --- | --- |
| `index()` | `GET /cashier` | Mengambil produk yang `stock > 0`, lalu mengirim ke `cashier/index.blade.php`. |
| `checkout(Request $request)` | `POST /cashier/checkout` | Menerima `cart` (JSON), `total_price`, dan `pay_amount`. Validasi, menolak jika uang kurang, lalu membuat `Transaction` dan `TransactionDetail` di dalam `DB::transaction()` supaya semua atau tidak sama sekali, mengurangi stok produk, lalu redirect ke struk. |
| `receipt(Transaction $transaction)` | `GET /cashier/receipt/{id}` | Mengambil transaksi dan detailnya dengan `load('details.product')` untuk dicetak. |

#### `ProductController.php` - CRUD produk

| Method | Route | Tugas |
| --- | --- | --- |
| `index()` | `GET /products` | Mengambil semua produk ke `products/index.blade.php`. |
| `create()` | `GET /products/create` | Menampilkan form tambah produk. |
| `store()` | `POST /products` | Validasi (`name`, `price`, `stock`) lalu `Product::create()`. |
| `edit()` | `GET /products/{id}/edit` | Mengambil satu produk untuk form edit. |
| `update()` | `PUT /products/{id}` | Validasi lalu `$product->update()`. |
| `destroy()` | `DELETE /products/{id}` | Mengecek `transactionDetails()->exists()`. Kalau produk sudah punya riwayat transaksi, produk tidak dihapus dan muncul pesan error. |

#### `TransactionController.php` - riwayat transaksi

| Method | Route | Tugas |
| --- | --- | --- |
| `index()` | `GET /transactions` | `Transaction::with('details.product')->latest()->get()` supaya transaksi terbaru di atas. |
| `show()` | `GET /transactions/{id}` | Detail satu transaksi beserta produknya. |

#### `ProfileController.php` - profil user (Breeze)

`edit()`, `update()`, dan `destroy()`, yaitu membuka, menyimpan, dan menghapus
akun. Validasi disimpan di `app/Http/Requests/ProfileUpdateRequest.php`.

#### `app/Http/Controllers/Auth/*` - Breeze

Login, register, logout, lupa dan reset password, serta verifikasi email.

---

## J. Penjelasan Model

### Apa itu Model?

Model adalah file di `app/Models/` yang mewakili **satu tabel di database**.
Model juga mengatur daftar kolom yang boleh diisi (`fillable`) dan relasi antar
tabel.

### `Product.php`

```php
protected $fillable = ['name', 'price', 'stock'];

public function transactionDetails()
{
    return $this->hasMany(TransactionDetail::class);
}
```

Artinya satu produk **bisa punya banyak** baris di `transaction_details`.

### `Transaction.php`

```php
protected $fillable = ['invoice_number', 'total_price', 'pay_amount', 'change_amount'];

public function details(): HasMany
{
    return $this->hasMany(TransactionDetail::class, 'transaction_id');
}
```

Satu transaksi **bisa punya banyak** detail.

### `TransactionDetail.php`

```php
protected $fillable = ['transaction_id', 'product_id', 'quantity', 'price', 'subtotal'];

public function transaction(): BelongsTo
{
    return $this->belongsTo(Transaction::class);
}

public function product(): BelongsTo
{
    return $this->belongsTo(Product::class);
}
```

Setiap detail **milik satu** transaksi dan **menunjuk satu** produk.

### Relasi antar model

```text
Product  1 ----<  TransactionDetail  >---- 1  Transaction
             |                                |
             +---------  belongsTo  -----------+
```

Rantai lengkap yang sering dipakai di view:

```php
$transaction->details                        // TransactionDetail[]
$transaction->details->first()->product      // Product
```

Atau sekaligus, supaya lebih hemat query:

```php
Transaction::with('details.product')->latest()->get();
```

---

## K. Penjelasan Migration

### Apa itu Migration?

Migration adalah file PHP di `database/migrations/` yang mendeskripsikan
**perubahan struktur tabel**. Namanya berurutan waktu, misalnya
`2026_09_12_022341_create_products_table.php`.

Isi migration di project ini:

| File | Tabel yang dibuat |
| --- | --- |
| `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` |
| `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` |
| `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` |
| `2026_09_12_022341_create_products_table.php` | `products` (`id`, `name`, `price`, `stock`, `timestamps`) |
| `2026_09_12_022428_create_transactions_table.php` | `transactions` (`id`, `invoice_number`, `total_price`, `pay_amount`, `change_amount`, `timestamps`) |
| `2026_09_24_000000_create_transaction_details_table.php` | `transaction_details` (`id`, `transaction_id`, `product_id`, `quantity`, `price`, `subtotal`, `timestamps`) |

### Hubungan migration dengan database

- **Schema** (nama tabel dan kolom) ditentukan oleh migration.
- **Data** (baris) ditentukan oleh Model, Controller, atau Seeder.

Perintah yang sering dipakai:

```bash
php artisan migrate           # menjalankan migration yang belum jalan
php artisan migrate:status    # melihat status setiap migration
php artisan migrate:rollback  # membatalkan migration terakhir
php artisan migrate:fresh     # MENGHAPUS semua tabel lalu jalankan ulang (hati-hati)
```

> **Penting:** jangan mengubah nama tabel atau kolom hanya karena menyesuaikan
> template. Kalau perlu kolom baru, tambahkan **migration baru**, jangan
> mengedit migration lama yang sudah pernah jalan.

---

## L. Penjelasan View Aplikasi

### `resources/views/products/index.blade.php`
- **Digunakan untuk:** halaman daftar produk, form tambah cepat, edit inline, dan hapus.
- **Controller:** `ProductController@index`
- **Route:** `GET /products` (`products.index`)
- **Layout:** `layouts.custom`

### `resources/views/products/create.blade.php`
- **Digunakan untuk:** form tambah produk.
- **Controller:** `ProductController@create`
- **Route:** `GET /products/create` (`products.create`)

### `resources/views/products/edit.blade.php`
- **Digunakan untuk:** form edit satu produk.
- **Controller:** `ProductController@edit`
- **Route:** `GET /products/{product}/edit` (`products.edit`)

### `resources/views/cashier/index.blade.php`
- **Digunakan untuk:** halaman kasir, daftar produk, keranjang, dan input pembayaran.
- **Controller:** `CartController@index`
- **Route:** `GET /cashier` (`cashier.index`)
- **Catatan:** logika JavaScript keranjang ada di `@push('scripts')` pada file yang sama.

### `resources/views/cashier/receipt.blade.php`
- **Digunakan untuk:** halaman pembuka struk (tombol Cetak Struk dan Kembali ke Kasir).
- **Controller:** `CartController@receipt`
- **Route:** `GET /cashier/receipt/{transaction}` (`cashier.receipt`)

### `resources/views/cetak/struk.blade.php`
- **Digunakan untuk:** isi struk yang siap dicetak. Dipanggil dari
  `cashier/receipt.blade.php` dengan `@include('cetak.struk', ['transaction' => $transaction])`.
- **Catatan:** sengaja tanpa layout agar hasil cetak bersih.

### `resources/views/transactions.blade.php`
- **Digunakan untuk:** riwayat transaksi (nota, tanggal, detail, total, bayar, kembali).
- **Controller:** `TransactionController@index`
- **Route:** `GET /transactions` (`transactions.index`)

### `resources/views/transactions/show.blade.php`
- **Digunakan untuk:** detail satu transaksi (rincian produk, total, bayar, kembali).
- **Controller:** `TransactionController@show`
- **Route:** `GET /transactions/{transaction}` (`transactions.show`)

### `resources/views/dashboard.blade.php`
- **Digunakan untuk:** dashboard AdminLTE (jumlah produk, transaksi, dan omzet).
- **Route:** `GET /dashboard` (`dashboard`)
- **Layout:** `layouts.admin`

### `resources/views/auth/*.blade.php`
- **Digunakan untuk:** login, register, forgot password, reset password, verify email, confirm password.
- **Layout:** `layouts.guest` (Breeze).
- **Controller:** `app/Http/Controllers/Auth/*`

### `resources/views/profile/edit.blade.php`
- **Digunakan untuk:** edit profil, ganti password, dan hapus akun.
- **Route:** `GET /profile` (`profile.edit`)
- **Layout:** `layouts.app` (Breeze, `<x-app-layout>`)

### `resources/views/components/*.blade.php`
- **Digunakan untuk:** komponen Blade Breeze (tombol, input, nav-link, modal, dropdown).
- Dipakai dengan tag seperti `<x-primary-button>` dan `<x-text-input>`.

---

## M. Penjelasan Alur Fitur

### 1. Melihat daftar produk

```text
User membuka /products
        |
        v
Route: products.index
        |
        v
ProductController@index  -->  Product::all()
        |
        v
Tabel products (SQLite)
        |
        v
products/index.blade.php (di dalam layouts/custom.blade.php)
        |
        v
Tabel produk tampil + alert session('success') atau session('error')
```

### 2. Menambah produk

Ada **dua cara**, keduanya memakai method `store()` yang sama:

```text
Cara A (form di /products/create)
  /products/create -> ProductController@create -> products/create.blade.php
                     -> submit POST /products

Cara B (form cepat di /products)
  /products -> form inline "Tambah Produk Baru"
             -> submit POST /products

        |
        v  (sama-sama)
Route: products.store
        |
        v
Validasi: name required, price numeric, stock numeric
        |
        v
Product::create([...])  ->  INSERT INTO products
        |
        v
redirect()->back()->with('success', 'Produk berhasil ditambahkan.')
```

### 3. Mengedit produk

```text
Cara A: /products/{id}/edit -> ProductController@edit -> products/edit.blade.php
        -> submit PUT /products/{id}

Cara B: edit langsung di tabel pada /products (form update-form-{id})
        -> submit PUT /products/{id}

        |
        v
Route: products.update  ->  ProductController@update
        |
        v
Validasi  ->  $product->update(...)  ->  UPDATE products SET ...
        |
        v
redirect()->back()->with('success', ...)
```

> Method `PUT` dikirim dari HTML biasa memakai `@method('PUT')` di dalam form POST.

### 4. Menghapus produk

```text
Tombol "Hapus" -> konfirmasi -> DELETE /products/{id}
        |
        v
Route: products.destroy  ->  ProductController@destroy
        |
        v
if ($product->transactionDetails()->exists())  ->  return error ("tidak bisa dihapus")
else                                             ->  $product->delete()
```

Produk yang sudah pernah terjual **tidak** dihapus supaya riwayat transaksi
tetap valid.

### 5. Halaman kasir dan keranjang

```text
GET /cashier
        |
        v
CartController@index  -->  Product::where('stock', '>', 0)->get()
        |
        v
cashier/index.blade.php
        |
        v
Tabel produk (kiri) + tabel keranjang (kanan)
```

Semua pergerakan keranjang terjadi di **browser** (JavaScript di
`cashier/index.blade.php`), bukan lewat request ke server:

| Aksi | Fungsi JS | Dampak |
| --- | --- | --- |
| Tambah produk ke keranjang | `addToCart(btn)` | Menambah `qty`, maksimal sama dengan stok. |
| Ubah jumlah barang | `updateQty(id, qty)` | Menghitung ulang subtotal, membatasi oleh stok, menghapus item bila jumlah 0. |
| Hapus barang dari keranjang | `removeFromCart(id)` | Menghapus item dari array `cart`. |
| Hitung total dan kembalian | `renderCart()` dan `calculateChange()` | Mengisi `#total-price-input` dan `#cart-input` (JSON). |

Tiga input yang dikirim ke server:

```html
<input type="hidden" name="cart"         id="cart-input">        <!-- JSON keranjang -->
<input type="hidden" name="total_price"  id="total-price-input"> <!-- total -->
<input type="number"  name="pay_amount"  id="pay-input">         <!-- uang bayar -->
```

Tombol submit otomatis `disabled` sampai kembalian tidak negatif.

### 6. Menyimpan transaksi

```text
Submit form  ->  POST /cashier/checkout
        |
        v
Route: cashier.checkout  ->  CartController@checkout
        |
        v
Validasi: cart (json), pay_amount (numeric), total_price (numeric)
        |
        v
json_decode($request->cart, true)
        |
        v
.pay_amount < total_price ?  ->  redirect back dengan error "Uang pembayaran kurang!"
        |
        v  (lolos)
DB::transaction(function () { ... })
   |-- Transaction::create([invoice_number, total_price, pay_amount, change_amount])
   `-- foreach item dalam keranjang:
         |-- cek stok cukup? tidak -> Exception -> semua dibatalkan
         |-- TransactionDetail::create([transaction_id, product_id, quantity, price, subtotal])
         `-- $product->decrement('stock', qty)
        |
        v
redirect()->route('cashier.receipt', $transaction->id)
```

`DB::transaction()` memastikan **menyimpan transaksi utuh atau tidak sama
sekali**. Kalau ada satu produk yang stoknya kurang, semua penyimpanan dibatalkan
dan stok tidak berkurang.

### 7. Melihat riwayat transaksi

```text
GET /transactions
        |
        v
Route: transactions.index  ->  TransactionController@index
        |
        v
Transaction::with('details.product')->latest()->get()
        |
        v
transactions.blade.php -> tabel nota, tanggal, detail, total, bayar, kembali
                           + tombol "Detail" dan "Cetak Struk"
```

### 8. Melihat detail transaksi

```text
GET /transactions/{id}
        |
        v
Route: transactions.show  ->  TransactionController@show
        |
        v
$transaction->load('details.product')
        |
        v
transactions/show.blade.php -> rincian produk + total + bayar + kembali
```

### 9. Mencetak struk

```text
GET /cashier/receipt/{id}
        |
        v
CartController@receipt  ->  $transaction->load('details.product')
        |
        v
cashier/receipt.blade.php
        |-- tombol "Cetak Struk"        -> window.print()
        |-- tombol "Kembali Ke Kasir"    -> route('cashier.index')
        |-- tombol "Riwayat Transaksi"   -> route('transactions.index')
        `-- @include('cetak.struk', ['transaction' => $transaction])
                |
                v
        cetak/struk.blade.php  (tampilan struk 300px, font monospace)
                |
                v
        window.onload = window.print()  ->  langsung menampilkan dialog cetak
```

---

## N. Penjelasan Alur Transaksi

```text
   +--------------------------------+
   |  Produk (master barang)       |
   |  name, price, stock           |
   +---------------+----------------+
                   |  kasir memilih produk
                   v
   +--------------------------------+
   |  Kasir                         |  resources/views/cashier/index.blade.php
   |  + Tambah / ubah qty           |  CartController@index
   +---------------+----------------+
                   |  isi keranjang (array JS -> JSON)
                   v
   +--------------------------------+
   |  Cart                          |  #cart-input (JSON)
   |  qty, subtotal, total          |  CartController@checkout
   +---------------+----------------+
                   |  POST /cashier/checkout
                   v
   +--------------------------------+
   |  Transaction (header)          |  invoice_number, total_price,
   |                                |  pay_amount, change_amount
   +---------------+----------------+
                   |  satu transaksi berisi banyak baris
                   v
   +--------------------------------+
   |  TransactionDetail             |  transaction_id, product_id,
   |  (rincian per produk)          |  quantity, price, subtotal
   +---------------+----------------+
                   |  disimpan ke
                   v
   +--------------------------------+
   |  Database (SQLite)             |  products
   |                                |  transactions
   |                                |  transaction_details
   +---------------+----------------+
                   |  dibaca lagi oleh
                   v
   +--------------------------------+
   |  Riwayat Transaksi             |  GET /transactions
   |  transactions.blade.php        |
   +---------------+----------------+
                   |  tombol "Cetak Struk"
                   v
   +--------------------------------+
   |  Cetak Struk                   |  GET /cashier/receipt/{id}
   |  cetak/struk.blade.php         |  -> window.print()
   +--------------------------------+
```

**Relasi database yang menghubungkan semuanya:**

```text
transactions.id  --<  transaction_details.transaction_id
products.id      --<  transaction_details.product_id
```

Dan relasi model-nya:

```text
Transaction  hasMany         TransactionDetail
Product      hasMany         TransactionDetail
TransactionDetail  belongsTo  Transaction
TransactionDetail  belongsTo  Product
```

---

## O. Perbedaan Istilah Penting

| Istilah | Bahasa sehari-hari | Di project ini |
| --- | --- | --- |
| **Route** | Alamat di address bar beserta penunjuk siapa yang harus menanganinya. | `routes/web.php` |
| **Controller** | Resepsionis: menerima request, mengerjakan, lalu meneruskan. | `app/Http/Controllers/` |
| **Model** | Buku catatan data beserta aturan relasi ke tabel lain. | `app/Models/` |
| **View** | Halaman yang dilihat user (HTML). | `resources/views/` |
| **Migration** | Gambar rancangan tabel. | `database/migrations/` |
| **Blade** | Bahasa template HTML ditambah sedikit PHP. | File `.blade.php` |
| **Layout** | Kerangka halaman yang dipakai bersama (sidebar, navbar, footer). | `resources/views/layouts/` |
| **Component / Partial** | Potongan halaman yang bisa dipakai ulang. | `resources/views/components/` (Breeze) dan `cetak/struk.blade.php` |
| **CSS** | Aturan tampilan seperti warna, jarak, dan ukuran. | `resources/css/` |
| **JavaScript** | Perilaku interaktif di browser seperti dropdown dan keranjang. | `resources/js/` dan `@push('scripts')` |
| **Middleware** | Pintu yang harus dilewati sebelum boleh masuk, misalnya harus login. | `auth` dan `verified` |
| **Vite** | Alat yang menggabungkan CSS dan JS jadi satu paket. | `vite.config.js` |

---

## P. Troubleshooting

### 1. `Route [cashier.index] not defined`

**Artinya:** ada view yang memanggil `route('cashier.index')`, tapi route
dengan nama itu tidak terdaftar.

**Penyebab:**
- Route kasir terhapus atau tidak ada di `routes/web.php`.
- Cache route masih menyimpan versi lama.

**Cara mengecek:**

```bash
php artisan route:list                       # lihat semua route
php artisan route:list --name=cashier        # cari route kasir saja
```

**Cara memperbaiki:**

1. Pastikan `routes/web.php` memuat baris berikut:

```php
Route::get('cashier', [CartController::class, 'index'])->name('cashier.index');
```

2. Bersihkan cache:

```bash
php artisan optimize:clear
php artisan route:clear
```

3. Cari pemanggilnya untuk memastikan nama route-nya benar:

```bash
Select-String -Path resources\views\*.blade.php -Pattern "route\('"
```

> Route **wajib** punya nama (`->name(...)`) kalau ingin dipanggil dengan
> `route('nama')` di view atau `redirect()->route('nama')` di controller.

### 2. `View [...] not found`

**Artinya:** controller memanggil `view('nama.view')`, tapi file-nya tidak ada
atau salah nama.

**Penyebab:** salah ketik nama view, atau salah kapitalisasi nama folder.

**Cara mengecek:**
- Lihat nama file di `resources/views/`.
- Ingat pola ini: `view('products.index')` berarti file
  `resources/views/products/index.blade.php`.

### 3. `Class "App\Http\Controllers\X" not found`

**Artinya:** controller yang dipanggil route tidak ada.

**Cara memperbaiki:**

```bash
php artisan make:controller NamaController
composer dump-autoload
php artisan optimize:clear
```

### 4. `Method [xxx] does not exist`

**Artinya:** route menunjuk ke method yang tidak ada di controller.

**Contoh klasik:** `Route::resource('transactions', ...)` membuat route
`create`, `store`, `edit`, `update`, dan `destroy`, padahal
`TransactionController` hanya punya `index` dan `show`.

**Cara memperbaiki** dengan membatasi route:

```php
Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
```

atau memakai `->only([...])` dan `->except([...])` bila tetap memakai
`Route::resource`.

### 5. Vite manifest error

**Pesan:** `Unable to locate file in Vite manifest: public/build/manifest.json`

**Artinya:** asset hasil build tidak ada, belum di-build, atau terhapus.

**Cara memperbaiki:**

```bash
npm install
npm run build          # untuk produksi
npm run dev            # untuk pengembangan
php artisan optimize:clear
```

### 6. `npm run build` error

| Error | Penyebab dan Solusi |
| --- | --- |
| `Cannot find module 'laravel-vite-plugin'` | `npm install` belum dijalankan. |
| CSS error pada `@tailwind` | Tailwind versi 3 harus lewat `postcss.config.js`. Jangan memakai `@theme` karena itu sintaks Tailwind versi 4. |
| `Failed to resolve import "admin-lte/dist/css/adminlte.css"` | `npm install` belum lengkap atau `package.json` berubah. Jalankan `npm install`. |

### 7. Migration error

**Contoh:** `SQLSTATE[...] table "products" already exists`

**Artinya:** tabel sudah ada, tetapi migration-nya tercatat belum jalan
(misal database dihapus sebagian).

**Cara memperbaiki (pilih salah satu, hati-hati data bisa hilang):**

```bash
php artisan migrate:status      # lihat status
php artisan migrate             # jalankan yang pending
php artisan migrate:fresh       # HAPUS dan buat ulang semua tabel (data hilang)
```

### 8. Halaman kasir atau riwayat tampil tapi tidak ada styling

**Artinya:** CSS tidak termuat.

**Cara mengecek:**
1. Buka View Page Source (Ctrl+U), cari `<link href="https://cdn.jsdelivr.net/npm/bootstrap`.
2. Kalau tidak ada, pastikan halaman memakai `@extends('layouts.custom')`.
3. Buka console browser (F12) untuk memastikan CDN bisa diakses, karena
   layout custom memakai CDN sehingga butuh koneksi internet.

### 9. Halaman dashboard berantakan atau ikon tidak muncul

**Penyebab:** CSS AdminLTE tidak termuat, atau CSS Tailwind ikut dimuat
bersamaan sehingga saling menimpa.

**Cara memperbaiki:**
- Pastikan `resources/views/layouts/admin.blade.php` memakai:

```blade
@vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
```

- Jalankan `npm run build`.
- **Jangan** menaruh `@import 'admin-lte/...'` di `resources/css/app.css`,
  karena `app.css` khusus Tailwind untuk layout Breeze.

---

## Ringkasan Arsitektur

```text
              +--------------------------------+
              |        routes/web.php           |
              |  dashboard, products*, cashier*,|
              |  transactions*, auth/*, profile*|
              +----------------+----------------+
                               |  middleware auth
              +----------------v----------------+
              |          Controller             |
              |  ProductController               |
              |  CartController                  |
              |  TransactionController           |
              |  ProfileController dan Auth/*    |
              +----------------+----------------+
                               |
              +----------------v----------------+
              |             Model                |
              |  Product -> TransactionDetail    |
              |            <- Transaction       |
              +----------------+----------------+
                               |
              +----------------v----------------+
              |       SQLite Database            |
              |  users, products, transactions,  |
              |  transaction_details             |
              +---------------------------------+

       Blade View + Layout
  +-----------------------------+------------------------------+
  | layouts/custom.blade.php    | layouts/admin.blade.php      |
  | (Bootstrap 5 dari CDN)      | (AdminLTE + Bootstrap Icons)  |
  |  -> products/*              |  -> dashboard                |
  |  -> cashier/index           |                              |
  |  -> transactions*           | layouts/app.blade.php        |
  |                             | layouts/guest.blade.php      |
  | cetak/struk (tanpa layout)  | (Tailwind + Alpine - Breeze)  |
  +-----------------------------+------------------------------+
```

---

## Lisensi

Dibuat untuk keperluan pembelajaran. Bebas dipakai dan dimodifikasi.