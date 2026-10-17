@extends('layouts.app')

@section('title', 'Transaksi Kasir')

@section('content')
    <div class="mb-6"><h1 class="page-title">Kasir</h1><p class="page-subtitle">Scan barcode atau pilih produk, periksa keranjang, lalu selesaikan pembayaran.</p></div>

    <div class="grid items-start gap-6 xl:grid-cols-[1.1fr_0.9fr]" data-cashier data-barcode-endpoint="{{ route('cashier.scan') }}">
        <div class="min-w-0 space-y-6">
            <section class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Scan Barcode</h2>
                        <p class="mt-1 text-xs text-muted">Arahkan scanner ke barcode produk. Kolom ini selalu siap menerima scan.</p>
                    </div>
                    <span class="badge-primary">Otomatis</span>
                </div>
                <div class="p-5">
                    <label class="sr-only" for="barcode-input">Barcode produk</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 grid place-items-center text-muted">
                            <svg class="size-5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5v14M7 5v14M11 5v14M14 5v14M18 5v14M21 5v14"/></svg>
                        </span>
                        <input class="input pl-11 text-lg font-semibold tracking-widest" id="barcode-input" data-barcode-input type="text" inputmode="text" autocomplete="off" autocapitalize="off" spellcheck="false" placeholder="Scan atau ketik kode barcode lalu Enter..." autofocus>
                    </div>
                    <p class="mt-2 hidden text-sm font-medium" data-scan-message role="status" aria-live="polite"></p>
                </div>
            </section>

            <section class="card min-w-0">
                <div class="card-header"><div><h2 class="card-title">Cari / Pilih Produk</h2><p class="mt-1 text-xs text-muted">Gunakan pencarian atau tombol tambah pada produk.</p></div><a class="text-sm font-semibold text-primary hover:underline" href="{{ route('products.index') }}">Kelola produk</a></div>
                <div class="p-5">
                    <form action="{{ route('cashier.index') }}" method="get" class="mb-4 flex gap-2"><label class="sr-only" for="cashier-search">Cari produk</label><input class="input" id="cashier-search" name="search" value="{{ $search }}" placeholder="Cari nama atau kode produk..."><button class="btn-secondary" type="submit">Cari</button></form>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @forelse ($products as $product)
                            <article class="flex min-w-0 items-center justify-between gap-3 rounded-field border border-line p-4"><div class="min-w-0"><p class="truncate text-sm font-semibold">{{ $product->name }}</p><p class="mt-1 text-xs text-muted">{{ $product->code }} <span aria-hidden="true">&middot;</span> {{ $product->stock }} {{ $product->unit }}</p><p class="mt-2 text-sm font-semibold text-primary">Rp{{ number_format($product->selling_price, 0, ',', '.') }}</p></div><button class="grid size-9 shrink-0 place-items-center rounded-field bg-primary text-lg font-semibold text-white hover:bg-primary-dark disabled:cursor-not-allowed disabled:bg-slate-300" type="button" data-add-product data-id="{{ $product->id }}" data-code="{{ $product->code }}" data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}" aria-label="Tambahkan {{ $product->name }}" @disabled($product->stock <= 0)>+</button></article>
                        @empty
                            <p class="col-span-full py-10 text-center text-sm text-muted">Tidak ada produk yang cocok.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <section class="card min-w-0">
            <div class="card-header"><div><h2 class="card-title">Keranjang</h2><p class="mt-1 text-xs text-muted">Jumlah dan subtotal diperbarui otomatis.</p></div><a class="text-sm font-semibold text-primary hover:underline" href="{{ route('transactions.index') }}">Riwayat</a></div>
            <div class="table-wrap"><table class="table min-w-[560px]"><thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th><th></th></tr></thead><tbody data-cart-rows></tbody></table></div>
            <div data-cart-empty class="px-5 py-8 text-center text-sm text-muted">Keranjang masih kosong.</div>
            <form action="{{ route('cashier.checkout') }}" method="post" data-checkout-form class="border-t border-line p-5">
                @csrf
                <div data-cart-inputs></div>
                <div class="flex items-center justify-between"><span class="text-sm font-medium text-muted">Total</span><span class="text-xl font-bold tabular" data-cart-total>Rp0</span></div>
                <div class="mt-5"><label class="label" for="paid">Uang pembayaran</label><input class="input @error('paid') input-error @enderror" id="paid" name="paid" type="number" min="0" step="1" value="{{ old('paid') }}" data-payment required placeholder="Masukkan jumlah pembayaran"></div>
                <div class="mt-4 flex items-center justify-between"><span class="text-sm font-medium text-muted">Kembalian</span><span class="font-semibold text-success tabular" data-cart-change>Rp0</span></div>
                @error('paid') <p class="field-error">{{ $message }}</p> @enderror
                <button class="btn-primary btn-lg mt-5" type="submit" data-checkout-submit disabled>Bayar &amp; Simpan Transaksi</button>
            </form>
        </section>
    </div>
@endsection
