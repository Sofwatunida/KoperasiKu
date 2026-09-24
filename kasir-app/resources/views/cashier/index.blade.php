@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Daftar Produk -->
        <div class="col-md-6">
            <h4>Pilih Produk</h4>
            <div class="row">
                @foreach($products as $product)
                <div class="col-md-6 mb-3">
                    <div class="card p-3">
                        <h5>{{ $product->name }}</h5>
                        <p>Rp {{ number_format($product->price) }} | Stok: {{ $product->stock }}</p>
                        <form action="{{ route('cashier.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100">Tambah ke Keranjang</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Keranjang Belanja -->
        <div class="col-md-6">
            <h4>Keranjang</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $id => $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>
                            <form action="{{ route('cashier.updateCart') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width:60px" onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>Rp {{ number_format($item['price'] * $item['quantity']) }}</td>
                        <td><a href="{{ route('cashier.remove', $id) }}" class="text-danger">Hapus</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <h3 class="mt-3">Total: Rp {{ number_format($total) }}</h3>

            <!-- Form Pembayaran / Checkout -->
            <form action="{{ route('cashier.checkout') }}" method="POST" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label>Jumlah Uang Bayar</label>
                    <input type="number" name="pay_amount" class="form-control" required placeholder="Masukkan nominal uang diterima">
                </div>
                <button type="submit" class="btn btn-success w-100">Proses Transaksi & Cetak Struk</button>
            </form>
        </div>
    </div>
</div>
@endsection
