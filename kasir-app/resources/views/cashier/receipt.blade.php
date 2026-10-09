<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Struk {{ $transaction->transaction_code }} | KoperasiKu</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; color: #0f172a; font-family: 'Courier New', monospace; }
        .toolbar { display: flex; justify-content: center; gap: 10px; padding: 24px; font: 14px ui-sans-serif, system-ui, sans-serif; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; padding: 10px 14px; background: #2563eb; color: #fff; text-decoration: none; cursor: pointer; }
        .toolbar a { background: #fff; color: #0f172a; border: 1px solid #e2e8f0; }
        .receipt { width: min(100%, 360px); margin: 0 auto 32px; padding: 24px; background: #fff; font-size: 12px; }
        .center { text-align: center; }
        .muted { color: #64748b; }
        .rule { margin: 14px 0; border-top: 1px dashed #94a3b8; }
        .meta { display: flex; justify-content: space-between; gap: 12px; margin: 5px 0; }
        .item { margin: 10px 0; }
        .item-total { display: flex; justify-content: space-between; gap: 12px; margin-top: 4px; }
        .summary .meta { margin: 7px 0; }
        @media print { @page { size: 80mm auto; margin: 4mm; } body { background: #fff; } .toolbar { display: none; } .receipt { width: 100%; margin: 0; padding: 0; } }
    </style>
</head>
<body>
    <nav class="toolbar no-print"><a href="{{ route('cashier.index') }}">Kembali ke kasir</a><button type="button" onclick="window.print()">Cetak Struk</button></nav>
    @include('cetak.struk')
</body>
</html>