<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ __('receipt.title') }} {{ $transaction->transaction_code }} | KoperasiKu</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; color: #0f172a; font-family: 'Courier New', monospace; }
        .toolbar { display: flex; justify-content: center; gap: 10px; padding: 24px; font: 14px ui-sans-serif, system-ui, sans-serif; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; padding: 10px 14px; background: #2563eb; color: #fff; text-decoration: none; cursor: pointer; }
        .toolbar a { background: #fff; color: #0f172a; border: 1px solid #e2e8f0; }
        .paper-hint { margin: 0 auto 8px; width: 58mm; text-align: center; color: #64748b; font: 12px ui-sans-serif, system-ui, sans-serif; }
        .receipt { width: 58mm; margin: 0 auto 32px; padding: 3mm; background: #fff; font-size: 11px; line-height: 1.35; }
        .center { text-align: center; }
        .muted { color: #64748b; }
        .rule { margin: 8px 0; border-top: 1px dashed #94a3b8; }
        .meta { display: flex; justify-content: space-between; gap: 6px; margin: 3px 0; }
        .item { margin: 6px 0; }
        .item-total { display: flex; justify-content: space-between; gap: 6px; margin-top: 2px; }
        .summary .meta { margin: 4px 0; }
        @media print { @page { size: 58mm auto; margin: 2mm; } body { background: #fff; } .toolbar, .paper-hint { display: none; } .receipt { width: 100%; margin: 0; padding: 0; } }
    </style>
</head>
<body>
    <nav class="toolbar no-print"><a href="{{ route('cashier.index') }}">{{ __('receipt.back_to_cashier') }}</a><button type="button" onclick="window.print()">{{ __('receipt.print') }}</button></nav>
    <p class="paper-hint no-print">{{ __('receipt.paper_hint') }}</p>
    @include('cetak.struk')
</body>
</html>
