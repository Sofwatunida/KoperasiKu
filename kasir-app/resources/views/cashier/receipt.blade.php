<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ $transaction->invoice_number }}</title>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center; font-family: sans-serif;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Cetak Struk</button>
        <a href="{{ route('cashier.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Kembali Ke Kasir</a>
        <a href="{{ route('transactions.index') }}" style="padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Riwayat Transaksi</a>
    </div>

    @include('cetak.struk', ['transaction' => $transaction])
</body>
</html>