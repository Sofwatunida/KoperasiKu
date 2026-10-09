@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4"><div><h1 class="page-title">Riwayat Transaksi</h1><p class="page-subtitle">Telusuri transaksi yang tercatat di koperasi.</p></div><a class="btn-primary" href="{{ route('cashier.index') }}">Transaksi Baru</a></div>
    <section class="card">
        <div class="card-header"><h2 class="card-title">Daftar Transaksi</h2><form action="{{ route('transactions.index') }}" method="get" class="flex w-full gap-2 sm:w-auto"><label class="sr-only" for="transaction-search">Cari ID transaksi</label><input class="input min-w-0 sm:w-72" id="transaction-search" name="search" value="{{ $search }}" placeholder="Cari ID transaksi"><button class="btn-secondary" type="submit">Cari</button></form></div>
        <div class="table-wrap"><table class="table"><thead><tr><th>No</th><th>ID Transaksi</th><th>Tanggal</th><th>Kasir</th><th class="text-right">Total</th><th>Aksi</th></tr></thead><tbody>
            @forelse ($transactions as $index => $transaction)
                <tr><td class="text-muted">{{ $transactions->firstItem() + $index }}</td><td class="font-semibold">{{ $transaction->transaction_code }}</td><td>{{ $transaction->date?->format('d M Y, H:i') }}</td><td>{{ $transaction->user?->name ?? 'Petugas' }}</td><td class="text-right font-semibold tabular">Rp{{ number_format($transaction->total, 0, ',', '.') }}</td><td><a class="btn-secondary btn-sm" href="{{ route('transactions.show', $transaction) }}">Detail</a></td></tr>
            @empty
                <tr><td colspan="6" class="py-12 text-center text-sm text-muted">{{ $search ? 'Transaksi tidak ditemukan.' : 'Belum ada transaksi.' }}</td></tr>
            @endforelse
        </tbody></table></div>
        @if ($transactions->hasPages()) <div class="border-t border-line px-5 py-4">{{ $transactions->links() }}</div> @endif
    </section>
@endsection