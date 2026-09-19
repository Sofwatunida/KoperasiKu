<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - {{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: monospace; width: 300px; margin: 0 auto; padding: 20px; }
        .text-center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h4>TOKO KASIR KITA</h4>
        <p>Nota: {{ $transaction->invoice_number }}<br>{{ $transaction->created_at }}</p>
    </div>
    <div class="line"></div>
    <table>
        @foreach($transaction->items as $item)
        <tr>
            <td>{{ $item['name'] }} x{{ $item['quantity'] }}</td>
            <td class="text-right">Rp {{ number_format($item['price'] * $item['quantity']) }}</td>
        </tr>
        @endforeach
    </table>
    <div class="line"></div>
    <table>
        <tr>
            <td><strong>Total:</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($transaction->total_price) }}</strong></td>
        </tr>
        <tr>
            <td>Bayar:</td>
            <td class="text-right">Rp {{ number_format($transaction->pay_amount) }}</td>
        </tr>
        <tr>
            <td>Kembali:</td>
            <td class="text-right">Rp {{ number_format($transaction->pay_amount - $transaction->total_price) }}</td>
        </tr>
    </table>
    <div class="line"></div>
    <p class="text-center">Terima Kasih Atas Kunjungan Anda</p>
    
    <div class="text-center" style="margin-top: 20px;">
        <a href="{{ route('cashier.index') }}" style="text-decoration: none; color: blue;">[ Kembali ke Kasir ]</a>
    </div>
</body>
</html>
