@extends('layouts.app')

@block('content')
<div class="container mt-4">
    <h2>Manajemen Produk</h2>
    
    <!-- Form Tambah Produk -->
    <div class="card my-3 p-3">
        <h5>Tambah Produk Baru</h5>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Nama Produk" required></div>
                <div class="col-md-3"><input type="number" name="price" class="form-control" placeholder="Harga" required></div>
                <div class="col-md-3"><input type="number" name="stock" class="form-control" placeholder="Stok" required></div>
                <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Tambah</button></div>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Produk -->
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
            @foreach($products as $product)
            <tr>
                <form action="{{ route('products.update', $product->id) }}" method="POST">
                    @csrf @method('PUT')
                    <td><input type="text" name="name" value="{{ $product->name }}" class="form-control"></td>
                    <td><input type="number" name="price" value="{{ $product->price }}" class="form-control"></td>
                    <td><input type="number" name="stock" value="{{ $product->stock }}" class="form-control"></td>
                    <td>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </form>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk?')">Hapus</button>
                        </form>
                    </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endblock
