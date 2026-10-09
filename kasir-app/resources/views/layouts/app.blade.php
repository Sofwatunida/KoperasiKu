<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | KoperasiKu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @auth
        @php
            $navigation = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'grid'],
                ['label' => 'Produk', 'route' => 'products.index', 'icon' => 'box'],
                ['label' => 'Transaksi', 'route' => 'cashier.index', 'icon' => 'cart'],
                ['label' => 'Laporan', 'route' => 'reports.index', 'icon' => 'chart'],
                ['label' => 'Pengaturan', 'route' => 'settings.index', 'icon' => 'settings'],
            ];
        @endphp
        <div class="min-h-screen lg:flex">
            <button id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-navy/50 lg:hidden" type="button" aria-label="Tutup menu"></button>
            <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-navy text-white transition-transform duration-200 lg:translate-x-0">
                <a href="{{ route('dashboard') }}" class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
                    <span class="grid size-10 place-items-center rounded-lg bg-primary text-lg font-bold">K</span>
                    <span><span class="block text-base font-bold">KoperasiKu</span><span class="mt-0.5 block text-xs text-slate-400">Kasir &amp; Manajemen</span></span>
                </a>
                <nav class="flex-1 space-y-1 px-3 py-6" aria-label="Navigasi utama">
                    <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Menu utama</p>
                    @foreach ($navigation as $item)
                        @php
                            $active = match ($item['route']) {
                                'dashboard' => request()->routeIs('dashboard'),
                                'products.index' => request()->routeIs('products.*'),
                                'cashier.index' => request()->routeIs('cashier.*', 'transactions.*'),
                                'reports.index' => request()->routeIs('reports.*'),
                                default => request()->routeIs('settings.*'),
                            };
                        @endphp
                        <a href="{{ route($item['route']) }}" @class(['sidebar-link', 'sidebar-link-active' => $active])>
                            <span class="sidebar-icon" aria-hidden="true">
                                @switch($item['icon'])
                                    @case('grid') <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg> @break
                                    @case('box') <svg viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8M12 13v9"/></svg> @break
                                    @case('cart') <svg viewBox="0 0 24 24"><circle cx="9" cy="20" r="1"/><circle cx="19" cy="20" r="1"/><path d="M2 3h2l2.7 12.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L22 8H5"/></svg> @break
                                    @case('chart') <svg viewBox="0 0 24 24"><path d="M3 3v18h18M8 15l4-4 3 3 6-7"/></svg> @break
                                    @default <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.7 1l-.3 1.8h-2.8l-.3-1.8a8 8 0 0 1-1.7-1l-1.7.7-1.4-2.4 1.4-1.1a7 7 0 0 1 0-2l-1.4-1.1 1.4-2.4 1.7.7a8 8 0 0 1 1.7-1l.3-1.8h2.8l.3 1.8a8 8 0 0 1 1.7 1l1.7-.7 1.4 2.4-1.4 1.1a7 7 0 0 1 0 2Z"/></svg>
                                @endswitch
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
                <div class="border-t border-white/10 p-3">
                    <div class="mb-3 flex items-center gap-3 rounded-lg bg-white/5 px-3 py-3">
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-slate-700 text-xs font-semibold">{{ auth()->user()->initials }}</span>
                        <span class="min-w-0"><span class="block truncate text-sm font-medium">{{ auth()->user()->name }}</span><span class="block text-xs capitalize text-slate-400">{{ auth()->user()->role }}</span></span>
                    </div>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="sidebar-link w-full text-left" type="submit"><span class="text-base" aria-hidden="true">↪</span><span>Keluar</span></button>
                    </form>
                </div>
            </aside>

            <div class="flex min-h-screen min-w-0 flex-1 flex-col lg:pl-64">
                <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-line bg-white/95 px-4 backdrop-blur sm:px-6">
                    <div class="flex items-center gap-3">
                        <button id="sidebar-toggle" class="btn-icon lg:hidden" type="button" aria-label="Buka menu" aria-expanded="false"><svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                        <div><p class="text-sm font-semibold text-ink">@yield('title', 'Dashboard')</p><p class="hidden text-xs text-muted sm:block">Sistem Kasir &amp; Manajemen Koperasi</p></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('products.index', ['stok_rendah' => 1]) }}" class="btn-icon relative" title="Produk dengan stok rendah" aria-label="Lihat produk dengan stok rendah">
                            <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                            @isset($jumlahStokRendah)
                                @if ($jumlahStokRendah > 0)<span class="absolute right-1 top-1 grid min-w-4 place-items-center rounded-full bg-danger px-1 text-[9px] font-bold leading-4 text-white">{{ $jumlahStokRendah }}</span>@endif
                            @endisset
                        </a>
                        <span class="hidden text-right sm:block"><span class="block text-sm font-semibold">{{ auth()->user()->name }}</span><span class="block text-xs capitalize text-muted">{{ auth()->user()->role }}</span></span>
                        <span class="grid size-9 place-items-center rounded-full bg-primary-light text-xs font-bold text-primary">{{ auth()->user()->initials }}</span>
                    </div>
                </header>
                <main class="mx-auto w-full max-w-[1600px] flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="notice notice-success" role="status">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="notice notice-error" role="alert">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="notice notice-error" role="alert">{{ $errors->first() }}</div>
                    @endif
                    @yield('content')
                </main>
                <footer class="border-t border-line px-6 py-4 text-center text-xs text-muted">KoperasiKu <span aria-hidden="true">&middot;</span> Sistem Kasir &amp; Manajemen Koperasi</footer>
            </div>
        </div>
    @else
        <main class="min-h-screen">@yield('content')</main>
    @endauth
    @stack('scripts')
</body>
</html>
