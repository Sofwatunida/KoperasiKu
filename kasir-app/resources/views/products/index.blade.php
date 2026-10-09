@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div><h1 class="page-title">Data Produk</h1><p class="page-subtitle">Kelola katalog, harga, dan stok koperasi.</p></div>
        <a class="btn-primary" href="{{ route('products.create') }}"><span aria-hidden="true">+</span> Tambah Produk</a>
    </div>
    <section class="card">
        <div class="card-header">
            <div><h2 class="card-title">Daftar Produk</h2><p class="mt-1 text-xs text-muted">{{ number_format($totalProduk, 0, ',', '.') }} produk terdaftar</p></div>
            <form action="{{ route('products.index') }}" method="get" class="flex w-full gap-2 sm:w-auto">
                @if ($stokRendah) <input type="hidden" name="stok_rendah" value="1"> @endif
                <label class="sr-only" for="product-search">Cari nama atau kode produk</label><input class="input min-w-0 sm:w-72" id="product-search" name="search" value="{{ $search }}" placeholder="Cari nama atau kode produk..."><button class="btn-secondary" type="submit">Cari</button>
                @if ($search || $stokRendah) <a class="btn-ghost" href="{{ route('products.index') }}">Reset</a> @endif
            </form>
        </div>
        @if ($stokRendah) <p class="border-b border-line bg-warning-soft px-5 py-3 text-sm font-medium text-ink">Menampilkan produk dengan stok {{ \App\Models\Product::LOW_STOCK_THRESHOLD }} atau kurang.</p> @endif
        <div class="table-wrap"><table class="table"><thead><tr><th>Kode</th><th>Nama Produk</th><th>Harga Jual</th><th>Stok</th><th>Satuan</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
            @forelse ($products as $product)
                <tr>
                    <td class="font-medium text-muted">{{ $product->code }}</td><td class="font-semibold">{{ $product->name }}</td><td class="tabular">Rp{{ number_format($product->selling_price, 0, ',', '.') }}</td><td class="tabular">{{ number_format($product->stock, 0, ',', '.') }}</td><td>{{ $product->unit }}</td>
                    <td><span @class(['badge-success' => $product->stock_status === 'normal', 'badge-warning' => $product->stock_status === 'rendah', 'badge-danger' => $product->stock_status === 'habis'])>{{ $product->stock_status_label }}</span></td>
                    <td><div class="flex items-center gap-2"><a class="btn-secondary btn-sm" href="{{ route('products.edit', $product) }}">Edit</a><form action="{{ route('products.destroy', $product) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">@csrf @method('DELETE')<button class="btn-ghost btn-sm text-danger" type="submit">Hapus</button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-12 text-center"><p class="font-medium">{{ $search ? 'Produk tidak ditemukan.' : 'Belum ada produk.' }}</p><p class="mt-1 text-sm text-muted">{{ $search ? 'Coba kata kunci lain.' : 'Tambahkan produk pertama koperasi.' }}</p></td></tr>
            @endforelse
        </tbody></table></div>
        @if ($products->hasPages()) <div class="border-t border-line px-5 py-4">{{ $products->links() }}</div> @endif
    </section>
@endsection