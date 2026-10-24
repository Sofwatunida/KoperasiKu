@extends('layouts.app')

@section('title', __('products.title'))

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div><h1 class="page-title">{{ __('products.page_title') }}</h1><p class="page-subtitle">{{ __('products.page_subtitle') }}</p></div>
        <a class="btn-primary" href="{{ route('products.create') }}"><span aria-hidden="true">+</span> {{ __('products.add') }}</a>
    </div>
    <section class="card">
        <div class="card-header">
            <div><h2 class="card-title">{{ __('products.list_title') }}</h2><p class="mt-1 text-xs text-muted">{{ __('products.registered', ['count' => number_format($totalProduk, 0, ',', '.')]) }}</p></div>
            <form action="{{ route('products.index') }}" method="get" class="flex w-full gap-2 sm:w-auto">
                @if ($stokRendah) <input type="hidden" name="stok_rendah" value="1"> @endif
                <label class="sr-only" for="product-search">{{ __('products.search_placeholder') }}</label><input class="input min-w-0 sm:w-72" id="product-search" name="search" value="{{ $search }}" placeholder="{{ __('products.search_placeholder') }}"><button class="btn-secondary" type="submit">{{ __('common.search') }}</button>
                @if ($search || $stokRendah) <a class="btn-ghost" href="{{ route('products.index') }}">{{ __('common.reset') }}</a> @endif
            </form>
        </div>
        @if ($stokRendah) <p class="border-b border-line bg-warning-soft px-5 py-3 text-sm font-medium text-ink">{{ __('products.low_stock_notice', ['threshold' => \App\Models\Product::LOW_STOCK_THRESHOLD]) }}</p> @endif
        <form id="bulk-delete-form" action="{{ route('products.bulk-destroy') }}" method="post" class="flex flex-wrap items-center gap-3 border-b border-line px-5 py-3" data-confirm="{{ __('products.delete_selected_confirm') }}" data-confirm-title="{{ __('products.delete_selected_title') }}" data-confirm-tone="danger" data-confirm-button="{{ __('common.yes_continue') }}">
            @csrf
            @method('DELETE')
            <button class="btn-danger btn-sm" type="submit" data-bulk-delete disabled>{{ __('products.delete_selected') }} (<span data-selected-count>0</span>)</button>
            <span class="text-xs text-muted">{{ __('products.select_hint') }}</span>
        </form>
        <div class="table-wrap"><table class="table"><thead><tr><th class="w-10"><input type="checkbox" class="size-4 cursor-pointer rounded border-line text-primary focus:ring-primary" data-select-all aria-label="{{ __('products.select_hint') }}"></th><th>{{ __('product_form.code') }}</th><th>{{ __('product_form.name') }}</th><th>{{ __('product_form.selling_price') }}</th><th>{{ __('product_form.stock') }}</th><th>{{ __('product_form.unit') }}</th><th>{{ __('common.status') }}</th><th>{{ __('common.action') }}</th></tr></thead><tbody>
            @forelse ($products as $product)
                <tr>
                    <td><input type="checkbox" class="size-4 cursor-pointer rounded border-line text-primary focus:ring-primary" name="product_ids[]" value="{{ $product->id }}" form="bulk-delete-form" data-product-checkbox aria-label="{{ $product->name }}"></td>
                    <td class="font-medium text-muted">{{ $product->code }}</td><td class="font-semibold">{{ $product->name }}</td><td class="tabular">Rp{{ number_format($product->selling_price, 0, ',', '.') }}</td><td class="tabular">{{ number_format($product->stock, 0, ',', '.') }}</td><td>{{ $product->unit }}</td>
                    <td><span @class(['badge-success' => $product->stock_status === 'normal', 'badge-warning' => $product->stock_status === 'rendah', 'badge-danger' => $product->stock_status === 'habis'])>{{ $product->stock_status_label }}</span></td>
                    <td><div class="flex items-center gap-2"><a class="btn-secondary btn-sm" href="{{ route('products.edit', $product) }}">{{ __('common.edit') }}</a><form action="{{ route('products.destroy', $product) }}" method="post" data-confirm="{{ __('products.delete_confirm') }}" data-confirm-title="{{ __('products.delete_title') }}" data-confirm-tone="danger" data-confirm-button="{{ __('common.yes_continue') }}">@csrf @method('DELETE')<button class="btn-ghost btn-sm text-danger" type="submit">{{ __('common.delete') }}</button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="8" class="py-12 text-center"><p class="font-medium">{{ $search ? __('products.not_found') : __('products.empty') }}</p><p class="mt-1 text-sm text-muted">{{ $search ? __('common.not_found_hint') : __('products.empty_hint') }}</p></td></tr>
            @endforelse
        </tbody></table></div>
        @if ($products->hasPages()) <div class="border-t border-line px-5 py-4">{{ $products->links() }}</div> @endif
    </section>
@endsection
