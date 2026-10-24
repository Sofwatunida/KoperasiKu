<main class="receipt">
    <header class="center"><strong>KOPERASIKU</strong><p>{{ __('struk.shop_tagline') }}</p></header>
    <div class="rule"></div>
    <div class="meta"><span>{{ __('transaction.number') }}</span><strong>{{ $transaction->transaction_code }}</strong></div>
    <div class="meta"><span>{{ __('report.columns.date') }}</span><span>{{ $transaction->date?->format('d/m/Y H:i') }}</span></div>
    <div class="meta"><span>{{ __('report.columns.cashier') }}</span><span>{{ $transaction->user?->name ?? __('report.unknown_cashier') }}</span></div>
    <div class="rule"></div>
    @foreach ($transaction->details as $detail)
        <div class="item"><div>{{ $detail->product?->name ?? __('report.deleted_product') }}</div><div class="item-total"><span>{{ $detail->quantity }} x Rp{{ number_format($detail->price, 0, ',', '.') }}</span><span>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</span></div></div>
    @endforeach
    <div class="rule"></div>
    <div class="summary"><div class="meta"><strong>{{ __('struk.total') }}</strong><strong>Rp{{ number_format($transaction->total, 0, ',', '.') }}</strong></div><div class="meta"><span>{{ __('struk.paid') }}</span><span>Rp{{ number_format($transaction->paid, 0, ',', '.') }}</span></div><div class="meta"><span>{{ __('struk.change') }}</span><span>Rp{{ number_format($transaction->change, 0, ',', '.') }}</span></div></div>
    <div class="rule"></div><footer class="center"><strong>{{ __('struk.thanks') }}</strong><p class="muted">{{ __('struk.thanks_sub') }}</p></footer>
</main>
