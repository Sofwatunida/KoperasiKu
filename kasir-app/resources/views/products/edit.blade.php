@extends('layouts.custom')

@section('title', 'Edit Produk')

@section('content')
<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Produk: {{ $product->name }}</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Kembali ke Daftar Produk</a>
    </div>

    <form action="{{ route('products.update', $product->id) }}" method="POST" class="row g-3 p-3 bg-light rounded">
        @csrf
        @method('PUT')

        <div class="col-md-4">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Harga</label>
            <input type="number" name="price" value="{{ old('price', $product->price) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Stok</label>
            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control" required>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-success w-100">Simpan</button>
        </div>
    </form>

    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus Produk</button>
    </form>
</div>
@endsection