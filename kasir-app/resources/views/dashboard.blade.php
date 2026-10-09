@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3"><div><h1 class="page-title">Selamat datang, {{ auth()->user()->name }}.</h1><p class="page-subtitle">Ringkasan aktivitas koperasi hari ini.</p></div><p class="text-sm font-medium text-muted">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p></div>
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Statistik koperasi">
        @foreach ($statistik as $item)
            <article class="card p-5"><div class="flex items-start justify-between gap-3"><div><p class="text-sm font-medium text-muted">{{ $item['label'] }}</p><p class="mt-3 text-2xl font-semibold tabular text-ink">@if (!empty($item['money'])) Rp{{ number_format($item['value'], 0, ',', '.') }} @else {{ number_format($item['value'], 0, ',', '.') }} @endif</p><p class="mt-1 text-xs text-muted">{{ $item['suffix'] }}</p></div><span class="grid size-10 place-items-center rounded-lg bg-primary-light text-primary" aria-hidden="true">@switch($item['icon']) @case('box') <span class="text-lg">▣</span> @break @case('layers') <span class="text-lg">▤</span> @break @case('cart') <span class="text-lg">⌑</span> @break @default <span class="text-sm font-bold">Rp</span> @endswitch</span></div></article>
        @endforeach
    </section>
    <div class="mt-6 grid gap-6 xl:grid-cols-[1.65fr_1fr]">
        <section class="card min-w-0"><div class="card-header"><div><h2 class="card-title">Grafik Penjualan</h2><p class="mt-1 text-xs text-muted">7 hari terakhir</p></div></div><div class="card-body">
            @php $grafikMax = max(1, (int) $grafik->max('value')); $grafikCount = max($grafik->count() - 1, 1); $grafikPoints = $grafik->values()->map(fn ($point, $index) => (36 + ($index * 628 / $grafikCount)).','. (185 - (int) round($point['value'] / $grafikMax * 145)))->implode(' '); @endphp
            <div class="overflow-x-auto"><svg class="h-56 min-w-[560px] w-full" viewBox="0 0 700 220" role="img" aria-label="Grafik penjualan tujuh hari terakhir"><path d="M36 40H664M36 110H664M36 180H664" stroke="#e2e8f0" stroke-dasharray="4 5"/><polyline points="{{ $grafikPoints }}" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                @foreach ($grafik->values() as $index => $point) @php $chartX = 36 + ($index * 628 / $grafikCount); $chartY = 185 - (int) round($point['value'] / $grafikMax * 145); @endphp <circle cx="{{ $chartX }}" cy="{{ $chartY }}" r="4" fill="#fff" stroke="#2563eb" stroke-width="3"><title>{{ $point['label'] }}: Rp{{ number_format($point['value'], 0, ',', '.') }}</title></circle><text x="{{ $chartX }}" y="210" text-anchor="middle" fill="#64748b" font-size="11">{{ $point['label'] }}</text> @endforeach
            </svg></div>
        </div></section>
        <section class="card min-w-0"><div class="card-header"><div><h2 class="card-title">Stok Perlu Diperhatikan</h2><p class="mt-1 text-xs text-muted">Stok 10 atau kurang</p></div><a class="text-sm font-semibold text-primary hover:underline" href="{{ route('products.index') }}">Semua produk</a></div><div class="divide-y divide-line">
            @forelse ($stokMenipis as $product)<div class="flex items-center justify-between gap-3 px-5 py-4"><div class="min-w-0"><p class="truncate text-sm font-medium">{{ $product->name }}</p><p class="mt-1 text-xs text-muted">{{ $product->code }}</p></div><span @class(['badge-danger' => $product->stock_status === 'habis', 'badge-warning' => $product->stock_status === 'rendah'])>{{ $product->stock }} {{ $product->unit }}</span></div>@empty<p class="px-5 py-8 text-center text-sm text-muted">Semua stok dalam kondisi normal.</p>@endforelse
        </div></section>
    </div>
    <section class="card mt-6"><div class="card-header"><div><h2 class="card-title">Transaksi Terbaru</h2><p class="mt-1 text-xs text-muted">Aktivitas penjualan terkini</p></div><a class="btn-secondary btn-sm" href="{{ route('transactions.index') }}">Lihat semua</a></div><div class="table-wrap"><table class="table"><thead><tr><th>No</th><th>ID Transaksi</th><th>Tanggal</th><th>Kasir</th><th class="text-right">Total</th></tr></thead><tbody>
        @forelse ($transaksiTerbaru as $index => $transaction)<tr><td class="text-muted">{{ $index + 1 }}</td><td><a class="font-semibold text-primary hover:underline" href="{{ route('transactions.show', $transaction) }}">{{ $transaction->transaction_code }}</a></td><td>{{ $transaction->date?->format('d M Y, H:i') }}</td><td>{{ $transaction->user?->name ?? 'Petugas' }}</td><td class="text-right font-semibold tabular">Rp{{ number_format($transaction->total, 0, ',', '.') }}</td></tr>@empty<tr><td colspan="5" class="py-10 text-center text-muted">Belum ada transaksi.</td></tr>@endforelse
    </tbody></table></div></section>
@endsection