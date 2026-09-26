@extends('layouts.app')

@section('content')
<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Riwayat Transaksi</h2>
        <a href="{{ route('cashier.index') }}" class="btn btn-success">+ Transaksi Baru</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Nota</th>
                <th>Tanggal</th>
                <th>Detail</th>
                <th class="text-end">Total</th>
                <th class="text-end">Bayar</th>
                <th class="text-end">Kembali</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td>{{ $transaction->invoice_number }}</td>
                <td>{{ $transaction->created_at->format('d-m-Y H:i') }}</td>
                <td>
                    @foreach($transaction->details as $detail)
                        <div>{{ $detail->product->name }} x{{ $detail->quantity }} = Rp {{ number_format($detail->subtotal) }}</div>
                    @endforeach
                </td>
                <td class="text-end">Rp {{ number_format($transaction->total_price) }}</td>
                <td class="text-end">Rp {{ number_format($transaction->pay_amount) }}</td>
                <td class="text-end">Rp {{ number_format($transaction->change_amount) }}</td>
                <td>
                    <a href="{{ route('cashier.receipt', $transaction->id) }}" class="btn btn-sm btn-outline-primary">Cetak Struk</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">Belum ada transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection