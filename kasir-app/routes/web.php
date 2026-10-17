<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Tamu
|--------------------------------------------------------------------------
| Belum login: hanya halaman login yang bisa diakses.
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);
    Route::get('register', [AuthController::class, 'registration'])->name('register');
    Route::post('register', [AuthController::class, 'storeRegistration'])->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Halaman Applications
|--------------------------------------------------------------------------
| Setelah login berhasil semua halaman aplikasi dapat diakses.
*/
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Produk (CRUD)
    Route::resource('produk', ProductController::class)
        ->parameters(['produk' => 'product'])
        ->except(['show'])
        ->names('products');

    // Kasir, Checkout & Struk
    Route::get('kasir', [CartController::class, 'index'])->name('cashier.index');
    Route::get('kasir/barcode', [CartController::class, 'scan'])->name('cashier.scan');
    Route::post('kasir/checkout', [CartController::class, 'checkout'])->name('cashier.checkout');
    Route::get('kasir/struk/{transaction}', [CartController::class, 'receipt'])->name('cashier.receipt');

    // Riwayat & Detail Transaksi
    Route::get('riwayat', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('riwayat/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Laporan Penjualan
    Route::get('laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('laporan/cetak', [ReportController::class, 'print'])->name('reports.print');
    Route::get('laporan/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('laporan/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('laporan/export/word', [ReportController::class, 'exportWord'])->name('reports.export.word');

    // Manajemen Pengguna (khusus admin)
    Route::middleware('admin')->group(function () {
        Route::resource('pengguna', UserController::class)
            ->parameters(['pengguna' => 'user'])
            ->except(['show'])
            ->names('users');
    });

    // Pengaturan Akun
    Route::get('pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('pengaturan', [SettingController::class, 'update'])->name('settings.update');

    // Profil (Breeze-compatible)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
