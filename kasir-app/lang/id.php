<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Umum
    |--------------------------------------------------------------------------
    */

    'common' => [
        'app_name' => 'KoperasiKu',
        'tagline' => 'Kasir & Manajemen Koperasi',
        'search' => 'Cari',
        'reset' => 'Reset',
        'edit' => 'Edit',
        'delete' => 'Hapus',
        'cancel' => 'Batal',
        'back' => 'Kembali',
        'detail' => 'Detail',
        'no' => 'No',
        'status' => 'Status',
        'action' => 'Aksi',
        'total' => 'Total',
        'yes_continue' => 'Ya, lanjutkan',
        'confirm' => 'Konfirmasi',
        'officer' => 'Petugas',
        'deleted_product' => 'Produk dihapus',
        'show_password' => 'Tampilkan password',
        'hide_password' => 'Sembunyikan password',
        'not_found_hint' => 'Coba kata kunci lain.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigasi & Layout
    |--------------------------------------------------------------------------
    */

    'nav' => [
        'main_menu' => 'Menu utama',
        'dashboard' => 'Dashboard',
        'products' => 'Produk',
        'transactions' => 'Transaksi',
        'reports' => 'Laporan',
        'users' => 'Pengguna',
        'settings' => 'Pengaturan',
        'logout' => 'Keluar',
        'open_menu' => 'Buka menu',
        'close_menu' => 'Tutup menu',
        'collapse' => 'Sembunyikan sidebar',
        'expand' => 'Tampilkan sidebar',
    ],

    'header' => [
        'subtitle' => 'Sistem Kasir & Manajemen Koperasi',
        'low_stock' => 'Produk dengan stok rendah',
        'view_low_stock' => 'Lihat produk dengan stok rendah',
    ],

    'preferences' => [
        'title' => 'Pengaturan tampilan',
        'theme' => 'Tema',
        'light' => 'Terang',
        'dark' => 'Gelap',
        'language' => 'Bahasa',
    ],

    /*
    |--------------------------------------------------------------------------
    | Label tabel
    |--------------------------------------------------------------------------
    */

    'table' => [
        'transaction_id' => 'ID Transaksi',
        'date' => 'Tanggal',
        'cashier' => 'Kasir',
        'total' => 'Total',
        'code' => 'Kode',
        'product_name' => 'Nama Produk',
        'selling_price' => 'Harga Jual',
        'purchase_price' => 'Harga Beli',
        'stock' => 'Stok',
        'unit' => 'Satuan',
        'name' => 'Nama',
        'username' => 'Username',
        'email' => 'Email',
        'role' => 'Role',
        'qty' => 'Qty',
        'price' => 'Harga',
        'product' => 'Produk',
        'subtotal' => 'Subtotal',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'welcome' => 'Selamat datang, :name.',
        'subtitle' => 'Ringkasan aktivitas koperasi hari ini.',
        'stat_total_products' => 'Total Produk',
        'stat_products_suffix' => 'Produk',
        'stat_stock' => 'Stok Barang',
        'stat_stock_suffix' => 'Item',
        'stat_today_transactions' => 'Transaksi Hari Ini',
        'stat_transactions_suffix' => 'Transaksi',
        'stat_today_revenue' => 'Pendapatan Hari Ini',
        'stat_revenue_suffix' => 'Total Penjualan',
        'chart_title' => 'Grafik Penjualan',
        'chart_subtitle' => '7 hari terakhir',
        'chart_alt' => 'Grafik penjualan tujuh hari terakhir',
        'low_stock_title' => 'Stok Perlu Diperhatikan',
        'low_stock_subtitle' => 'Stok :threshold atau kurang',
        'all_products' => 'Semua produk',
        'stock_normal' => 'Semua stok dalam kondisi normal.',
        'latest_transactions' => 'Transaksi Terbaru',
        'latest_subtitle' => 'Aktivitas penjualan terkini',
        'view_all' => 'Lihat semua',
        'no_transactions' => 'Belum ada transaksi.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Produk
    |--------------------------------------------------------------------------
    */

    'products' => [
        'title' => 'Produk',
        'page_title' => 'Data Produk',
        'page_subtitle' => 'Kelola katalog, harga, dan stok koperasi.',
        'add' => 'Tambah Produk',
        'list_title' => 'Daftar Produk',
        'registered' => ':count produk terdaftar',
        'search_placeholder' => 'Cari nama atau kode produk...',
        'low_stock_notice' => 'Menampilkan produk dengan stok :threshold atau kurang.',
        'select_hint' => 'Centang produk yang ingin dihapus, lalu klik tombol ini.',
        'delete_selected' => 'Hapus Terpilih',
        'delete_selected_count' => ':count produk',
        'delete_selected_confirm' => 'Hapus produk yang dipilih? Produk yang memiliki riwayat transaksi tidak akan dihapus.',
        'delete_selected_title' => 'Hapus produk terpilih',
        'delete_confirm' => 'Apakah Anda yakin ingin menghapus produk ini?',
        'delete_title' => 'Hapus produk',
        'empty' => 'Belum ada produk.',
        'empty_hint' => 'Tambahkan produk pertama koperasi.',
        'not_found' => 'Produk tidak ditemukan.',
        'create_title' => 'Tambah Produk',
        'create_heading' => 'Tambah Produk Baru',
        'create_subtitle' => 'Masukkan data barang koperasi.',
        'edit_title' => 'Edit Produk',
        'edit_subtitle' => 'Perbarui informasi :name.',
        'save' => 'Simpan Produk',
        'update' => 'Update Produk',
        'created' => 'Produk berhasil ditambahkan.',
        'updated' => 'Produk berhasil diperbarui.',
        'deleted' => 'Produk berhasil dihapus.',
        'delete_blocked' => 'Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi.',
        'bulk_min' => 'Pilih minimal satu produk untuk dihapus.',
        'bulk_missing' => 'Sebagian produk yang dipilih tidak ditemukan.',
        'bulk_none_deleted' => 'Tidak ada produk yang dihapus.',
        'bulk_all_blocked' => 'Tidak ada produk yang dihapus. Produk terpilih sudah memiliki riwayat transaksi.',
        'bulk_success' => ':count produk berhasil dihapus.',
        'bulk_blocked' => ':count produk dilewati karena sudah memiliki riwayat transaksi.',
    ],

    'product_form' => [
        'title' => 'Informasi Produk',
        'new_title' => 'Informasi Produk Baru',
        'hint' => 'Isi data produk dengan benar.',
        'code' => 'Kode Produk',
        'name' => 'Nama Produk',
        'purchase_price' => 'Harga Beli',
        'selling_price' => 'Harga Jual',
        'stock' => 'Stok',
        'unit' => 'Satuan',
    ],

    'stock' => [
        'out' => 'Stok habis',
        'low' => 'Stok rendah',
        'normal' => 'Stok normal',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kasir
    |--------------------------------------------------------------------------
    */

    'cashier' => [
        'page_title' => 'Kasir',
        'page_subtitle' => 'Scan barcode atau pilih produk, periksa keranjang, lalu selesaikan pembayaran.',
        'scan_title' => 'Scan Barcode',
        'scan_hint' => 'Arahkan scanner ke barcode produk. Kolom ini selalu siap menerima scan.',
        'scan_badge' => 'Otomatis',
        'barcode_label' => 'Barcode produk',
        'barcode_placeholder' => 'Scan atau ketik kode barcode lalu Enter...',
        'barcode_required' => 'Barcode wajib diisi.',
        'scan_error' => 'Barcode tidak dapat diproses.',
        'scan_failed' => 'Gagal menghubungi server. Coba scan ulang.',
        'scan_not_found' => 'Produk dengan barcode tersebut tidak ditemukan.',
        'scan_stock_max' => 'Jumlah produk sudah mencapai stok maksimum.',
        'scan_added' => ':name ditambahkan ke keranjang.',
        'find_title' => 'Cari / Pilih Produk',
        'find_hint' => 'Gunakan pencarian atau tombol tambah pada produk.',
        'manage_products' => 'Kelola produk',
        'no_products' => 'Tidak ada produk yang cocok.',
        'add_label' => 'Tambahkan :name',
        'cart_title' => 'Keranjang',
        'cart_hint' => 'Jumlah dan subtotal diperbarui otomatis.',
        'history' => 'Riwayat',
        'cart_empty' => 'Keranjang masih kosong.',
        'paid_label' => 'Uang pembayaran',
        'paid_placeholder' => 'Masukkan jumlah pembayaran',
        'paid_required' => 'Uang pembayaran wajib diisi.',
        'change_label' => 'Kembalian',
        'pay' => 'Bayar & Simpan Transaksi',
        'increase' => 'Tambah :name',
        'decrease' => 'Kurangi :name',
        'remove' => 'Hapus',
        'stock_insufficient' => 'Stok :name tidak mencukupi.',
        'paid_insufficient' => 'Jumlah pembayaran tidak mencukupi.',
        'checkout_success' => 'Transaksi berhasil disimpan.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Riwayat & Detail Transaksi
    |--------------------------------------------------------------------------
    */

    'transactions' => [
        'title' => 'Riwayat Transaksi',
        'page_subtitle' => 'Telusuri transaksi yang tercatat di koperasi.',
        'new' => 'Transaksi Baru',
        'list_title' => 'Daftar Transaksi',
        'search_placeholder' => 'Cari ID transaksi',
        'empty' => 'Belum ada transaksi.',
        'not_found' => 'Transaksi tidak ditemukan.',
    ],

    'transaction' => [
        'title' => 'Detail Transaksi',
        'number' => 'No Transaksi',
        'date' => 'Tanggal',
        'product' => 'Produk',
        'quantity' => 'Qty',
        'price' => 'Harga',
        'subtotal' => 'Subtotal',
        'paid' => 'Bayar',
        'change' => 'Kembalian',
        'print_receipt' => 'Cetak Struk',
    ],

    /*
    |--------------------------------------------------------------------------
    | Struk (58mm)
    |--------------------------------------------------------------------------
    */

    'receipt' => [
        'title' => 'Struk',
        'back_to_cashier' => 'Kembali ke kasir',
        'print' => 'Cetak Struk',
        'paper_hint' => 'Ukuran kertas 58mm',
    ],

    'struk' => [
        'shop_tagline' => 'Koperasi Sekolah',
        'total' => 'TOTAL',
        'paid' => 'BAYAR',
        'change' => 'KEMBALI',
        'thanks' => 'TERIMA KASIH',
        'thanks_sub' => 'Selamat belanja kembali',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengguna
    |--------------------------------------------------------------------------
    */

    'users' => [
        'title' => 'Manajemen Pengguna',
        'page_subtitle' => 'Kelola akun super admin, admin, dan kasir koperasi.',
        'add' => 'Tambah Pengguna',
        'list_title' => 'Daftar Pengguna',
        'super_admin' => 'super admin',
        'admin' => 'admin',
        'kasir' => 'kasir',
        'search_placeholder' => 'Cari nama, username, atau email...',
        'delete_confirm' => 'Apakah Anda yakin ingin menghapus pengguna ini?',
        'delete_title' => 'Hapus pengguna',
        'empty' => 'Belum ada pengguna.',
        'empty_hint' => 'Tambahkan pengguna pertama.',
        'not_found' => 'Pengguna tidak ditemukan.',
        'create_title' => 'Tambah Pengguna',
        'create_heading' => 'Tambah Pengguna Baru',
        'create_subtitle' => 'Buat akun super admin, admin, atau kasir koperasi.',
        'edit_title' => 'Edit Pengguna',
        'edit_subtitle' => 'Perbarui data, role, atau password akun.',
        'save' => 'Simpan Pengguna',
        'update' => 'Update Pengguna',
        'created' => 'Pengguna berhasil ditambahkan.',
        'updated' => 'Pengguna berhasil diperbarui.',
        'deleted' => 'Pengguna berhasil dihapus.',
        'cannot_delete_self' => 'Anda tidak bisa menghapus akun sendiri.',
        'last_admin' => 'Minimal harus ada satu admin yang aktif.',
        'last_super_admin' => 'Minimal harus ada satu super admin yang aktif.',
        'has_transactions' => 'Akun tidak bisa dihapus karena memiliki riwayat transaksi.',
        'super_admin_only' => 'Akun super admin hanya dapat dikelola oleh super admin.',
        'admin_manages_kasir_only' => 'Admin hanya dapat mengelola akun kasir.',
        'username_taken' => 'Username sudah digunakan.',
    ],

    'user_form' => [
        'title' => 'Informasi Pengguna',
        'new_title' => 'Informasi Pengguna Baru',
        'hint' => 'Role menentukan akses: super admin untuk akses penuh, admin untuk pengelola, kasir untuk transaksi.',
        'name' => 'Nama Lengkap',
        'username_pattern' => 'Hanya huruf, angka, garis bawah, dan strip.',
        'password_keep' => 'Kosongkan jika tidak diubah',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Akun
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'title' => 'Pengaturan',
        'page_title' => 'Pengaturan Akun',
        'page_subtitle' => 'Perbarui email, password, dan informasi akun Anda.',
        'info_title' => 'Informasi Akun',
        'role_hint' => 'Role akun: :role · role tidak dapat diubah dari halaman ini.',
        'field_name' => 'Nama',
        'field_username' => 'Username',
        'field_email' => 'Email',
        'current_password' => 'Password Saat Ini',
        'current_password_placeholder' => 'Diperlukan untuk mengubah email atau password',
        'current_password_hint' => 'Wajib diisi hanya jika Anda mengubah email atau password.',
        'password_title' => 'Ubah Password',
        'password_hint' => 'Kosongkan jika tidak ingin mengganti password.',
        'new_password' => 'Password Baru',
        'new_password_placeholder' => 'Minimal 8 karakter',
        'confirm_password' => 'Konfirmasi Password Baru',
        'save' => 'Simpan Perubahan',
        'updated' => 'Pengaturan berhasil diperbarui.',
        'email_taken' => 'Email sudah digunakan oleh akun lain.',
        'invalid_email' => 'Format email tidak valid.',
        'password_min' => 'Password minimal 8 karakter.',
        'password_mismatch' => 'Konfirmasi password baru tidak cocok.',
        'current_password_invalid' => 'Password saat ini tidak sesuai.',
        'reset_confirmation_required' => 'Ketik RESET untuk mengonfirmasi reset data.',
        'reset_confirmation_invalid' => 'Konfirmasi salah. Ketik RESET (huruf besar) untuk melanjutkan.',
        'reset_success' => 'Reset berhasil. :transactions transaksi dan :products produk telah dihapus.',
        'import_success' => 'Impor berhasil. :users pengguna, :products produk, dan :transactions transaksi dipulihkan.',
        'import_invalid_file' => 'Berkas backup tidak dapat dibaca.',
    ],

    'backup' => [
        'title' => 'Backup Data',
        'description' => 'Unduh seluruh data pengguna, produk, dan transaksi dalam satu berkas backup (.json).',
        'hint' => 'Simpan berkas backup di tempat aman. Berkas ini dapat dipulihkan kembali melalui menu Impor Data.',
        'button' => 'Unduh Backup',
    ],

    'import' => [
        'title' => 'Impor Data',
        'description' => 'Pulihkan data dari berkas backup. Seluruh data saat ini akan digantikan oleh isi berkas.',
        'file_label' => 'Berkas backup (.json)',
        'button' => 'Impor & Pulihkan',
        'confirm' => 'Seluruh data pengguna, produk, dan transaksi akan digantikan oleh isi berkas backup. Lanjutkan?',
        'confirm_title' => 'Impor data backup',
        'confirm_button' => 'Ya, impor sekarang',
        'file_required' => 'Pilih berkas backup yang akan diimpor.',
        'invalid' => 'Berkas backup tidak valid atau formatnya tidak didukung.',
        'invalid_structure' => 'Struktur berkas backup tidak lengkap.',
    ],

    'reset' => [
        'title' => 'Reset Data',
        'description' => 'Mengosongkan seluruh riwayat transaksi dan semua produk sehingga koperasi mulai dari awal. Akun pengguna tidak terpengaruh.',
        'warning' => 'Seluruh riwayat transaksi dan data produk akan dihapus permanen dan tidak dapat dibatalkan. Anda akan diminta mengetik <b>RESET</b> untuk mengonfirmasi.',
        'button' => 'Reset Semua Data',
        'confirm_title' => 'Apakah Anda yakin akan mereset?',
        'confirm_html' => 'Seluruh <b>riwayat transaksi</b> dan <b>semua produk</b> akan dihapus permanen.<br>Ketik <b>RESET</b> untuk melanjutkan.',
        'placeholder' => 'Ketik RESET',
        'confirm_button' => 'Ya, reset sekarang',
        'validator' => 'Ketik RESET (huruf besar) untuk melanjutkan.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Autentikasi
    |--------------------------------------------------------------------------
    */

    'login' => [
        'title' => 'Masuk',
        'hero_tagline' => 'Kasir & manajemen koperasi',
        'hero_heading' => 'Semua kebutuhan koperasi, dalam satu tempat.',
        'hero_text' => 'Kelola produk, layani transaksi, dan pantau penjualan harian dengan lebih teratur.',
        'summary_label' => 'Ringkasan penjualan',
        'today' => 'Hari ini',
        'heading' => 'Masuk ke akun Anda',
        'subtitle' => 'Silakan login untuk melanjutkan.',
        'identifier' => 'Email atau Username',
        'submit' => 'Masuk',
        'no_account' => 'Belum punya akun?',
        'register' => 'Daftar',
        'identifier_required' => 'Email atau username wajib diisi.',
        'invalid_credentials' => 'Email/username atau password salah.',
        'logged_out' => 'Anda berhasil keluar.',
        'registered' => 'Akun berhasil dibuat.',
    ],

    'register' => [
        'title' => 'Daftar',
        'hero_heading' => 'Buat akun petugas koperasi.',
        'hero_text' => 'Akun baru mendapat akses petugas. Hak akses admin hanya dapat diberikan oleh pengelola.',
        'heading' => 'Buat akun baru',
        'subtitle' => 'Daftarkan akun petugas untuk mulai menggunakan aplikasi.',
        'name' => 'Nama lengkap',
        'username' => 'Username',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Konfirmasi password',
        'submit' => 'Daftar sebagai Petugas',
        'have_account' => 'Sudah punya akun?',
        'login' => 'Masuk',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laporan
    |--------------------------------------------------------------------------
    */

    'report' => [
        'title' => 'Laporan Penjualan',
        'subtitle' => 'Ringkasan transaksi berdasarkan periode.',
        'unknown_cashier' => 'Petugas',
        'deleted_product' => 'Produk dihapus',
        'period' => 'Periode',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Akhir',
        'show' => 'Tampilkan',
        'print' => 'Cetak Laporan',
        'export_label' => 'Export laporan:',
        'export_pdf' => 'Export PDF',
        'export_excel' => 'Export Excel',
        'export_word' => 'Export Word',
        'cashier' => 'Kasir',
        'all_cashiers' => 'Semua kasir',
        'total_transactions' => 'Total Transaksi',
        'total_revenue' => 'Total Pendapatan',
        'items_sold' => 'Item Terjual',
        'average' => 'Rata-rata / Transaksi',
        'transactions_this_period' => 'Transaksi pada periode ini',
        'from_saved_transactions' => 'Dari transaksi tersimpan',
        'daily_sales' => 'Penjualan per Hari',
        'selected_period' => 'Periode terpilih',
        'transactions_table' => 'Transaksi pada Periode Ini',
        'top_products' => 'Produk Terlaris',
        'top_products_hint' => '5 produk dengan quantity terjual terbanyak',
        'rank' => 'Peringkat',
        'qty_sold' => 'Qty Terjual',
        'back' => 'Kembali',
        'no_transactions' => 'Tidak ada transaksi pada periode ini.',
        'columns' => [
            'date' => 'Tanggal',
            'code' => 'Kode',
            'cashier' => 'Kasir',
            'product' => 'Produk',
            'qty' => 'Qty',
            'price' => 'Harga',
            'subtotal' => 'Subtotal',
        ],
        'summary' => [
            'total_transactions' => 'Total Transaksi',
            'total_revenue' => 'Total Pendapatan',
        ],
    ],

];
