<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Manajemen Produk</title>
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between mb-3">
            <h4>Manajemen Stok Produk</h4>
            <a href="{{ route('cashier.index') }}" class="btn btn-secondary">Buka Kasir -&gt;</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Button Trigger Modal Tambah -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">+ Tambah Produk</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>Rp {{ number_format($p->price) }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{$p->id}}">Edit</button>
                        <form action="{{ route('products.destroy', $p->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal{{$p->id}}" tabindex="-1">
                    <div class="modal-dialog"><form action="{{ route('products.update', $p->id) }}" method="POST" class="modal-content">
                        @csrf @method('PUT')
                        <div class="modal-header"><h5>Edit Produk</h5></div>
                        <div class="modal-body">
                            <input type="text" name="name" value="{{$p->name}}" class="form-control mb-2" required>
                            <input type="number" name="price" value="{{$p->price}}" class="form-control mb-2" required>
                            <input type="number" name="stock" value="{{$p->stock}}" class="form-control mb-2" required>
                        </div>
                        <div class="modal-footer"><button type="submit" class="btn btn-success">Simpan</button></div>
                    </form></div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog"><form action="{{ route('products.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header"><h5>Tambah Produk</h5></div>
            <div class="modal-body">
                <input type="text" name="name" placeholder="Nama Produk" class="form-control mb-2" required>
                <input type="number" name="price" placeholder="Harga" class="form-control mb-2" required>
                <input type="number" name="stock" placeholder="Stok Awal" class="form-control mb-2" required>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Tambah</button></div>
        </form></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>