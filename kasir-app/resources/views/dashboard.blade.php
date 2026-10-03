@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary">
            <div class="inner">
                <h3>{{ $totalProducts }}</h3>
                <p>Total Produk</p>
            </div>
            <div class="icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <a href="{{ route('products.index') }}" class="small-box-footer">
                Lihat Produk <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3>{{ $totalTransactions }}</h3>
                <p>Total Transaksi</p>
            </div>
            <div class="icon">
                <i class="bi bi-cart-check"></i>
            </div>
            <a href="{{ route('transactions.index') }}" class="small-box-footer">
                Lihat Transaksi <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-warning">
            <div class="inner">
                <h3>Rp {{ number_format($totalOmzet) }}</h3>
                <p>Total Omzet</p>
            </div>
            <div class="icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <a href="{{ route('transactions.index') }}" class="small-box-footer">
                Riwayat Transaksi <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-danger">
            <div class="inner">
                <h3>&nbsp;</h3>
                <p>Mulai Transaksi</p>
            </div>
            <div class="icon">
                <i class="bi bi-cart-plus"></i>
            </div>
            <a href="{{ route('cashier.index') }}" class="small-box-footer">
                Buka Kasir <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Selamat Datang{{ Auth::check() ? ', '.Auth::user()->name : '' }}</h3>
    </div>

    <div class="card-body">
        Selamat datang di <strong>KoperasiKu</strong>.
        Silakan gunakan menu di sidebar untuk mengelola produk, membuka kasir, dan melihat riwayat transaksi.
    </div>
</div>
@endsection