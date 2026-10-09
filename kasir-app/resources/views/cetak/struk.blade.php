<main class="receipt">
    <header class="center"><strong>KOPERASIKU</strong><p>Koperasi Sekolah</p></header>
    <div class="rule"></div>
    <div class="meta"><span>No Transaksi</span><strong>{{ $transaction->transaction_code }}</strong></div>
    <div class="meta"><span>Tanggal</span><span>{{ $transaction->date?->format('d/m/Y H:i') }}</span></div>
    <div class="meta"><span>Kasir</span><span>{{ $transaction->user?->name ?? 'Petugas' }}</span></div>
    <div class="rule"></div>
    @foreach ($transaction->details as $detail)
        <div class="item"><div>{{ $detail->product?->name ?? 'Produk dihapus' }}</div><div class="item-total"><span>{{ $detail->quantity }} x Rp{{ number_format($detail->price, 0, ',', '.') }}</span><span>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</span></div></div>
    @endforeach
    <div class="rule"></div>
    <div class="summary"><div class="meta"><strong>TOTAL</strong><strong>Rp{{ number_format($transaction->total, 0, ',', '.') }}</strong></div><div class="meta"><span>BAYAR</span><span>Rp{{ number_format($transaction->paid, 0, ',', '.') }}</span></div><div class="meta"><span>KEMBALI</span><span>Rp{{ number_format($transaction->change, 0, ',', '.') }}</span></div></div>
    <div class="rule"></div><footer class="center"><strong>TERIMA KASIH</strong><p class="muted">Selamat belanja kembali</p></footer>
</main>