<style>
    body {
        font-family: 'Courier New', Courier, monospace;
        width: 300px;
        margin: 20px auto;
        padding: 10px;
        font-size: 14px;
    }
    .text-center { text-align: center; }
    .line { border-top: 1px dashed #000; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; }
    .text-right { text-align: right; }
    @media print {
        body { margin: 0; width: 100%; }
        .no-print { display: none; }
    }
</style>

<div class="text-center">
    <h3>TOKO KITA JAYA</h3>
    <p>Jl. Pembangunan No. 123<br>Telp: 08123456789</p>
</div>

<div class="line"></div>

<p>
    No. Nota : {{ $transaction->invoice_number }}<br>
    Tanggal  : {{ $transaction->created_at->format('d-m-Y H:i') }}
</p>

<div class="line"></div>

<table>
    <tbody>
        @foreach($transaction->details as $detail)
        <tr>
            <td colspan="2">{{ $detail->product->name }}</td>
        </tr>
        <tr>
            <td>{{ $detail->quantity }} x {{ number_format($detail->price) }}</td>
            <td class="text-right">Rp {{ number_format($detail->subtotal) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="line"></div>

<table>
    <tr>
        <td><strong>TOTAL :</strong></td>
        <td class="text-right"><strong>Rp {{ number_format($transaction->total_price) }}</strong></td>
    </tr>
    <tr>
        <td>Bayar :</td>
        <td class="text-right">Rp {{ number_format($transaction->pay_amount) }}</td>
    </tr>
    <tr>
        <td>Kembali :</td>
        <td class="text-right">Rp {{ number_format($transaction->change_amount) }}</td>
    </tr>
</table>

<div class="line"></div>
<div class="text-center">
    <p>-- Terima Kasih --<br>Selamat Berbelanja Kembali</p>
</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>