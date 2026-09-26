<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

// Root -> halaman kasir
Route::get('/', fn () => redirect()->route('cashier.index'));

// Manajemen Produk
Route::resource('products', ProductController::class);

// Kasir & Struk
Route::get('cashier', [CartController::class, 'index'])->name('cashier.index');
Route::post('cashier/checkout', [CartController::class, 'checkout'])->name('cashier.checkout');
Route::get('cashier/receipt/{transaction}', [CartController::class, 'receipt'])->name('cashier.receipt');

// Riwayat Transaksi
Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
