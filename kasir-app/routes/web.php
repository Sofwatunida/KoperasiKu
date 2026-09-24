<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

// Root -> halaman kasir
Route::get('/', fn () => redirect()->route('cashier.index'));

// Manajemen Produk
Route::resource('products', ProductController::class);

// Halaman Kasir & Transaksi
Route::get('cashier', [CartController::class, 'index'])->name('cashier.index');
Route::post('cashier/add/{id}', [CartController::class, 'add'])->name('cashier.add');
Route::post('cashier/update-cart', [CartController::class, 'updateCart'])->name('cashier.updateCart');
Route::get('cashier/remove/{id}', [CartController::class, 'remove'])->name('cashier.remove');
Route::post('cashier/checkout', [CartController::class, 'checkout'])->name('cashier.checkout');
Route::get('cashier/receipt/{id}', [CartController::class, 'receipt'])->name('cashier.receipt');
