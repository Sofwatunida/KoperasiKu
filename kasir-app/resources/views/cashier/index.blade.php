@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <h2>Kasir Digital</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">&larr; Manajemen Produk</a>
    </div>

    <div class="row">
        <!-- Daftar Produk -->
        <div class="col-md-6">
            <div class="bg-white p-4 rounded shadow-sm">
                <h4>Pilih Produk</h4>
                <table class="table table-hover mt-3">
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
                            <td>{{ $product->name }}</td>
                            <td>Rp {{ number_format($product->price) }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}" onclick="addToCart(this)">
                                    + Tambah
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada produk dengan stok tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Keranjang Belanja & Pembayaran -->
        <div class="col-md-6">
            <div class="bg-white p-4 rounded shadow-sm">
                <h4>Keranjang Belanja</h4>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th style="width: 100px;">Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cart-table">
                        <!-- Diisi via JS -->
                    </tbody>
                </table>

                <form action="{{ route('cashier.checkout') }}" method="POST" id="checkout-form" class="mt-4 border-top pt-3">
                    @csrf
                    <input type="hidden" name="cart" id="cart-input">
                    <input type="hidden" name="total_price" id="total-price-input">

                    <div class="d-flex justify-content-between mb-2">
                        <h5>Total Belanja:</h5>
                        <h5 id="total-text">Rp 0</h5>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Uang Bayar</label>
                        <input type="number" name="pay_amount" id="pay-input" class="form-control form-control-lg" placeholder="0" required oninput="calculateChange()">
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <h5>Kembalian:</h5>
                        <h5 id="change-text" class="text-success">Rp 0</h5>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100" id="btn-submit" disabled>Proses Transaksi & Cetak Struk</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];

    function addToCart(btn) {
        let id = parseInt(btn.dataset.id);
        let name = btn.dataset.name;
        let price = parseInt(btn.dataset.price);
        let maxStock = parseInt(btn.dataset.stock);

        let item = cart.find(p => p.id === id);
        if (item) {
            if (item.qty < maxStock) item.qty++;
            else alert('Stok tidak mencukupi!');
        } else {
            cart.push({ id, name, price, qty: 1, maxStock });
        }
        renderCart();
    }

    function updateQty(id, qty) {
        let item = cart.find(p => p.id === id);
        if (item) {
            item.qty = parseInt(qty);
            if (item.qty > item.maxStock) {
                alert('Stok tidak mencukupi!');
                item.qty = item.maxStock;
            }
            if (item.qty <= 0 || isNaN(item.qty)) {
                cart = cart.filter(p => p.id !== id);
            }
        }
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(p => p.id !== id);
        renderCart();
    }

    function renderCart() {
        let html = '';
        let total = 0;

        cart.forEach(item => {
            let subtotal = item.price * item.qty;
            total += subtotal;
            html += `
                <tr>
                    <td>${item.name}</td>
                    <td>Rp ${item.price.toLocaleString()}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" value="${item.qty}" min="1" max="${item.maxStock}" onchange="updateQty(${item.id}, this.value)">
                    </td>
                    <td>Rp ${subtotal.toLocaleString()}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">X</button></td>
                </tr>
            `;
        });

        document.getElementById('cart-table').innerHTML = html;
        document.getElementById('total-text').innerText = 'Rp ' + total.toLocaleString();
        document.getElementById('total-price-input').value = total;
        document.getElementById('cart-input').value = JSON.stringify(cart);

        calculateChange();
    }

    function calculateChange() {
        let total = parseInt(document.getElementById('total-price-input').value) || 0;
        let pay = parseInt(document.getElementById('pay-input').value) || 0;
        let change = pay - total;

        if (change >= 0 && total > 0) {
            document.getElementById('change-text').innerText = 'Rp ' + change.toLocaleString();
            document.getElementById('btn-submit').disabled = false;
        } else {
            document.getElementById('change-text').innerText = 'Rp 0';
            document.getElementById('btn-submit').disabled = true;
        }
    }
</script>
@endpush