@extends('layouts.app')

@section('title', __('report.title'))

@section('content')
    <div class="mb-6"><h1 class="page-title">{{ __('report.title') }}</h1><p class="page-subtitle">{{ __('report.subtitle') }}</p></div>
    @php
        $filterParams = ['dari' => $dari, 'sampai' => $sampai];
        if ($kasirId) {
            $filterParams['kasir'] = $kasirId;
        }
    @endphp
    <section class="card mb-6">
        <form action="{{ route('reports.index') }}" method="get" class="grid gap-4 p-5 sm:grid-cols-[1fr_1fr_1fr_auto_auto] sm:items-end">
            <div><label class="label" for="dari">{{ __('report.start_date') }}</label><input class="input" id="dari" name="dari" type="date" value="{{ $dari }}" required></div>
            <div><label class="label" for="sampai">{{ __('report.end_date') }}</label><input class="input" id="sampai" name="sampai" type="date" value="{{ $sampai }}" required></div>
            <div><label class="label" for="kasir">{{ __('report.cashier') }}</label>
                <select class="input" id="kasir" name="kasir">
                    <option value="">{{ __('report.all_cashiers') }}</option>
                    @foreach ($kasirList as $kasir)<option value="{{ $kasir->id }}" @selected($kasirId === $kasir->id)>{{ $kasir->name }}</option>@endforeach
                </select>
            </div>
            <button class="btn-primary" type="submit">{{ __('report.show') }}</button>
            <a class="btn-secondary" href="{{ route('reports.print', $filterParams) }}" target="_blank" rel="noopener">{{ __('report.print') }}</a>
        </form>
        <div class="flex flex-wrap items-center gap-2 border-t border-line px-5 py-4"><span class="mr-1 text-sm font-medium text-muted">{{ __('report.export_label') }}</span><a class="btn-secondary btn-sm" href="{{ route('reports.export.pdf', $filterParams) }}">{{ __('report.export_pdf') }}</a><a class="btn-secondary btn-sm" href="{{ route('reports.export.excel', $filterParams) }}">{{ __('report.export_excel') }}</a><a class="btn-secondary btn-sm" href="{{ route('reports.export.word', $filterParams) }}">{{ __('report.export_word') }}</a></div>
    </section>
    <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <article class="card p-5"><p class="text-sm font-medium text-muted">{{ __('report.total_transactions') }}</p><p class="mt-2 text-2xl font-semibold tabular">{{ number_format($totalTransaksi, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">{{ __('report.transactions_this_period') }}</p></article>
        <article class="card p-5"><p class="text-sm font-medium text-muted">{{ __('report.total_revenue') }}</p><p class="mt-2 text-2xl font-semibold tabular">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">{{ __('report.from_saved_transactions') }}</p></article>
        <article class="card p-5"><p class="text-sm font-medium text-muted">{{ __('report.items_sold') }}</p><p class="mt-2 text-2xl font-semibold tabular">{{ number_format($totalItemTerjual, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">{{ __('report.transactions_this_period') }}</p></article>
        <article class="card p-5"><p class="text-sm font-medium text-muted">{{ __('report.average') }}</p><p class="mt-2 text-2xl font-semibold tabular">Rp{{ number_format($rataRataTransaksi, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">{{ __('report.from_saved_transactions') }}</p></article>
    </section>
    <section class="card mb-6"><div class="card-header"><div><h2 class="card-title">{{ __('report.daily_sales') }}</h2><p class="mt-1 text-xs text-muted">{{ __('report.selected_period') }}</p></div></div><div class="card-body"><div class="flex min-h-40 items-end gap-2 overflow-x-auto border-b border-line pb-2">
        @php $grafikMax = max(1, (int) $grafik->max('value')); @endphp
        @foreach ($grafik as $point)<div class="flex min-w-8 flex-1 flex-col items-center justify-end gap-2" title="{{ $point['label'] }}: Rp{{ number_format($point['value'], 0, ',', '.') }}"><div class="w-full max-w-10 rounded-t-sm bg-primary" style="height: {{ max(4, (int) round($point['value'] / $grafikMax * 112)) }}px"></div><span class="whitespace-nowrap text-[10px] text-muted">{{ $point['label'] }}</span></div>@endforeach
    </div></div></section>
    @if ($produkTerlaris->isNotEmpty())
    <section class="card mb-6"><div class="card-header"><div><h2 class="card-title">{{ __('report.top_products') }}</h2><p class="mt-1 text-xs text-muted">{{ __('report.top_products_hint') }}</p></div></div><div class="table-wrap"><table class="table"><thead><tr><th>{{ __('report.rank') }}</th><th>{{ __('report.columns.product') }}</th><th class="text-right">{{ __('report.qty_sold') }}</th></tr></thead><tbody>
        @foreach ($produkTerlaris as $index => $product)<tr><td class="text-muted">{{ $index + 1 }}</td><td class="font-semibold">{{ $product->name }}</td><td class="text-right font-semibold tabular">{{ number_format((int) $product->total_qty, 0, ',', '.') }}</td></tr>@endforeach
    </tbody></table></div></section>
    @endif
    <section class="card"><div class="card-header"><h2 class="card-title">{{ __('report.transactions_table') }}</h2></div><div class="table-wrap"><table class="table"><thead><tr><th>{{ __('common.no') }}</th><th>{{ __('transaction.number') }}</th><th>{{ __('report.columns.date') }}</th><th>{{ __('report.columns.cashier') }}</th><th class="text-right">{{ __('common.total') }}</th><th>{{ __('common.action') }}</th></tr></thead><tbody>
        @forelse ($transaksi as $index => $transaction)<tr><td class="text-muted">{{ $index + 1 }}</td><td class="font-semibold">{{ $transaction->transaction_code }}</td><td>{{ $transaction->date?->format('d M Y, H:i') }}</td><td>{{ $transaction->user?->name ?? __('report.unknown_cashier') }}</td><td class="text-right font-semibold tabular">Rp{{ number_format($transaction->total, 0, ',', '.') }}</td><td><a class="btn-secondary btn-sm" href="{{ route('transactions.show', $transaction) }}">{{ __('common.detail') }}</a></td></tr>@empty<tr><td colspan="6" class="py-10 text-center text-sm text-muted">{{ __('report.no_transactions') }}</td></tr>@endforelse
    </tbody></table></div></section>
@endsection
