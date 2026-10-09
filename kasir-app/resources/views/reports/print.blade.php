<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Laporan Penjualan | KoperasiKu</title>@vite(['resources/css/app.css'])</head>
<body class="bg-white text-ink">
    <main class="mx-auto max-w-5xl p-6 print:p-0">
        <div class="no-print mb-6 flex justify-between"><a class="btn-secondary" href="{{ route('reports.index', ['dari' => $dari, 'sampai' => $sampai]) }}">Kembali</a><button class="btn-primary" type="button" onclick="window.print()">Cetak Laporan</button></div>
        <header class="mb-6 border-b border-line pb-5"><p class="text-sm font-semibold text-primary">KOPERASIKU</p><h1 class="mt-2 text-2xl font-semibold">Laporan Penjualan</h1><p class="mt-1 text-sm text-muted">Periode {{ \Illuminate\Support\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($sampai)->format('d/m/Y') }}</p></header>
        <div class="mb-6 grid grid-cols-2 gap-4"><div><p class="text-xs text-muted">Total Transaksi</p><p class="mt-1 text-lg font-semibold">{{ number_format($totalTransaksi, 0, ',', '.') }}</p></div><div><p class="text-xs text-muted">Total Pendapatan</p><p class="mt-1 text-lg font-semibold">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</p></div></div>
        <div class="table-wrap"><table class="table min-w-0"><thead><tr><th>No</th><th>ID Transaksi</th><th>Tanggal</th><th>Kasir</th><th class="text-right">Total</th></tr></thead><tbody>@forelse($transaksi as $index => $transaction)<tr><td>{{ $index + 1 }}</td><td>{{ $transaction->transaction_code }}</td><td>{{ $transaction->date?->format('d/m/Y H:i') }}</td><td>{{ $transaction->user?->name ?? 'Petugas' }}</td><td class="text-right">Rp{{ number_format($transaction->total, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="5" class="py-8 text-center">Tidak ada transaksi pada periode ini.</td></tr>@endforelse</tbody></table></div>
    </main>
</body>
</html>