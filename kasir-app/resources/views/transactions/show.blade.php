@extends('layouts.custom')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Transaksi {{ $transaction->invoice_number }}</h2>
        <div>
            <a href="{{ route('cashier.receipt', $transaction->id) }}" class="btn btn-success">Cetak Struk</a>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">&larr; Kembali ke Riwayat</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="border rounded p-3 h-100">
                <div class="text-muted small">No. Nota</div>
                <div class="fw-bold">{{ $transaction->invoice_number }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 h-100">
                <div class="text-muted small">Tanggal</div>
                <div class="fw-bold">{{ $transaction->created_at->format('d-m-Y H:i') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded p-3 h-100">
                <div class="text-muted small">Jumlah Item</div>
                <div class="fw-bold">{{ $transaction->details->sum('quantity') }}</div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-end">Harga</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaction->details as $detail)
            <tr>
                <td>{{ $detail->product->name }}</td>
                <td class="text-end">Rp {{ number_format($detail->price) }}</td>
                <td class="text-end">{{ $detail->quantity }}</td>
                <td class="text-end">Rp {{ number_format($detail->subtotal) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Transaksi ini tidak memiliki detail produk.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">TOTAL</td>
                <td class="text-end">Rp {{ number_format($transaction->total_price) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end">Bayar</td>
                <td class="text-end">Rp {{ number_format($transaction->pay_amount) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end">Kembali</td>
                <td class="text-end">Rp {{ number_format($transaction->change_amount) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection