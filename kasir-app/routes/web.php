<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
| Sebelumnya, "/" langsung mengarah ke halaman Kasir.
*/
Route::get('/', fn () => redirect()->route('cashier.index'));

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard', [
        'totalProducts' => Product::count(),
        'totalTransactions' => Transaction::count(),
        'totalOmzet' => (int) Transaction::sum('total_price'),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authentication (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Aplikasi (butuh login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Profil User (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manajemen Produk (CRUD)
    Route::resource('products', ProductController::class)->except(['show']);

    // Kasir & Struk
    Route::get('cashier', [CartController::class, 'index'])->name('cashier.index');
    Route::post('cashier/checkout', [CartController::class, 'checkout'])->name('cashier.checkout');
    Route::get('cashier/receipt/{transaction}', [CartController::class, 'receipt'])->name('cashier.receipt');

    // Riwayat Transaksi & Detail Transaksi
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
});