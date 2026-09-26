@extends('layouts.app')

@section('content')
<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Produk</h2>
        <a href="{{ route('cashier.index') }}" class="btn btn-secondary">Ke Halaman Kasir &rarr;</a>
    </div>

    <!-- Form Tambah Produk -->
    <form action="{{ route('products.store') }}" method="POST" class="row g-3 mb-4 p-3 bg-light rounded">
        @csrf
        <h4>Tambah Produk Baru</h4>
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Nama Produk" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="price" class="form-control" placeholder="Harga" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="stock" class="form-control" placeholder="Stok Awal" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Tambah</button>
        </div>
    </form>

    <!-- Tabel Produk -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <input type="text" name="name" value="{{ $product->name }}" class="form-control form-control-sm" form="update-form-{{ $product->id }}">
                </td>
                <td>
                    <input type="number" name="price" value="{{ $product->price }}" class="form-control form-control-sm" form="update-form-{{ $product->id }}">
                </td>
                <td>
                    <input type="number" name="stock" value="{{ $product->stock }}" class="form-control form-control-sm" form="update-form-{{ $product->id }}">
                </td>
                <td>
                    <form id="update-form-{{ $product->id }}" action="{{ route('products.update', $product->id) }}" method="POST" class="d-inline">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                    </form>

                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada produk. Silakan tambahkan produk baru di atas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection