@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4"><div><h1 class="page-title">Detail Transaksi</h1><p class="page-subtitle">{{ $transaction->transaction_code }}</p></div><div class="flex gap-2"><a class="btn-secondary" href="{{ route('transactions.index') }}">Kembali</a><a class="btn-primary" href="{{ route('cashier.receipt', $transaction) }}">Cetak Struk</a></div></div>
    <section class="card">
        <div class="grid gap-5 border-b border-line p-5 sm:grid-cols-3"><div><p class="text-xs text-muted">No Transaksi</p><p class="mt-1 font-semibold">{{ $transaction->transaction_code }}</p></div><div><p class="text-xs text-muted">Tanggal</p><p class="mt-1 font-medium">{{ $transaction->date?->format('d M Y, H:i') }}</p></div><div><p class="text-xs text-muted">Kasir</p><p class="mt-1 font-medium">{{ $transaction->user?->name ?? 'Petugas' }}</p></div></div>
        <div class="table-wrap"><table class="table"><thead><tr><th>Produk</th><th class="text-right">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead><tbody>
            @foreach ($transaction->details as $detail)<tr><td class="font-medium">{{ $detail->product?->name ?? 'Produk dihapus' }}</td><td class="text-right tabular">{{ $detail->quantity }}</td><td class="text-right tabular">Rp{{ number_format($detail->price, 0, ',', '.') }}</td><td class="text-right font-semibold tabular">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td></tr>@endforeach
        </tbody></table></div>
        <div class="ml-auto max-w-sm space-y-3 border-t border-line p-5"><div class="flex justify-between text-sm"><span class="text-muted">Total</span><span class="font-bold tabular">Rp{{ number_format($transaction->total, 0, ',', '.') }}</span></div><div class="flex justify-between text-sm"><span class="text-muted">Bayar</span><span class="tabular">Rp{{ number_format($transaction->paid, 0, ',', '.') }}</span></div><div class="flex justify-between text-sm"><span class="text-muted">Kembalian</span><span class="font-semibold text-success tabular">Rp{{ number_format($transaction->change, 0, ',', '.') }}</span></div></div>
    </section>
@endsection