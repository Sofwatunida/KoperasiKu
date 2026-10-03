@extends('layouts.custom')

@section('title', 'Tambah Produk')

@section('content')
<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah Produk Baru</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali ke Daftar Produk</a>
    </div>

    <form action="{{ route('products.store') }}" method="POST" class="row g-3 p-3 bg-light rounded">
        @csrf

        <div class="col-md-4">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Nama Produk" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Harga</label>
            <input type="number" name="price" value="{{ old('price') }}" class="form-control" placeholder="Harga" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Stok Awal</label>
            <input type="number" name="stock" value="{{ old('stock') }}" class="form-control" placeholder="Stok Awal" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Tambah</button>
        </div>
    </form>
</div>
@endsection