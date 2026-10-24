@extends('layouts.app')

@section('title', __('transactions.title'))

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4"><div><h1 class="page-title">{{ __('transactions.title') }}</h1><p class="page-subtitle">{{ __('transactions.page_subtitle') }}</p></div><a class="btn-primary" href="{{ route('cashier.index') }}">{{ __('transactions.new') }}</a></div>
    <section class="card">
        <div class="card-header"><h2 class="card-title">{{ __('transactions.list_title') }}</h2><form action="{{ route('transactions.index') }}" method="get" class="flex w-full gap-2 sm:w-auto"><label class="sr-only" for="transaction-search">{{ __('transactions.search_placeholder') }}</label><input class="input min-w-0 sm:w-72" id="transaction-search" name="search" value="{{ $search }}" placeholder="{{ __('transactions.search_placeholder') }}"><button class="btn-secondary" type="submit">{{ __('common.search') }}</button></form></div>
        <div class="table-wrap"><table class="table"><thead><tr><th>{{ __('common.no') }}</th><th>{{ __('transaction.number') }}</th><th>{{ __('transaction.date') }}</th><th>{{ __('report.cashier') }}</th><th class="text-right">{{ __('common.total') }}</th><th>{{ __('common.action') }}</th></tr></thead><tbody>
            @forelse ($transactions as $index => $transaction)
                <tr><td class="text-muted">{{ $transactions->firstItem() + $index }}</td><td class="font-semibold">{{ $transaction->transaction_code }}</td><td>{{ $transaction->date?->format('d M Y, H:i') }}</td><td>{{ $transaction->user?->name ?? __('common.officer') }}</td><td class="text-right font-semibold tabular">Rp{{ number_format($transaction->total, 0, ',', '.') }}</td><td><a class="btn-secondary btn-sm" href="{{ route('transactions.show', $transaction) }}">{{ __('common.detail') }}</a></td></tr>
            @empty
                <tr><td colspan="6" class="py-12 text-center text-sm text-muted">{{ $search ? __('transactions.not_found') : __('transactions.empty') }}</td></tr>
            @endforelse
        </tbody></table></div>
        @if ($transactions->hasPages()) <div class="border-t border-line px-5 py-4">{{ $transactions->links() }}</div> @endif
    </section>
@endsection
