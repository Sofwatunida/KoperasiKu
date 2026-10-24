<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $currentTheme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('nav.dashboard')) | {{ __('common.app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $sidebarCollapsed ? 'sidebar-collapsed' : '' }}">
    <script>
        @php
            $koperasikuI18n = [
                'locale' => app()->getLocale(),
                'theme' => $currentTheme,
                'i18n' => [
                    'confirm' => __('common.confirm'),
                    'yesContinue' => __('common.yes_continue'),
                    'cancel' => __('common.cancel'),
                    'showPassword' => __('common.show_password'),
                    'hidePassword' => __('common.hide_password'),
                    'increase' => __('cashier.increase', ['name' => ':name']),
                    'decrease' => __('cashier.decrease', ['name' => ':name']),
                    'remove' => __('cashier.remove'),
                    'stockMax' => __('cashier.scan_stock_max'),
                    'scanError' => __('cashier.scan_error'),
                    'scanFailed' => __('cashier.scan_failed'),
                    'resetTitle' => __('reset.confirm_title'),
                    'resetHtml' => __('reset.confirm_html'),
                    'resetPlaceholder' => __('reset.placeholder'),
                    'resetConfirm' => __('reset.confirm_button'),
                    'resetValidator' => __('reset.validator'),
                ],
            ];
        @endphp
        window.KoperasiKu = @json($koperasikuI18n);
    </script>
    @auth
        @php
            $navigation = [
                ['label' => __('nav.dashboard'), 'route' => 'dashboard', 'icon' => 'grid'],
                ['label' => __('nav.products'), 'route' => 'products.index', 'icon' => 'box'],
                ['label' => __('nav.transactions'), 'route' => 'cashier.index', 'icon' => 'cart'],
                ['label' => __('nav.reports'), 'route' => 'reports.index', 'icon' => 'chart'],
            ];

            if (auth()->user()->hasAdminAccess()) {
                $navigation[] = ['label' => __('nav.users'), 'route' => 'users.index', 'icon' => 'users'];
            }

            if (auth()->user()->isSuperAdmin()) {
                $navigation[] = ['label' => __('nav.settings'), 'route' => 'settings.index', 'icon' => 'settings'];
            }
        @endphp
        <div class="min-h-screen lg:flex">
            <button id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-navy/50 lg:hidden" type="button" aria-label="{{ __('nav.close_menu') }}"></button>
            <aside id="app-sidebar" class="app-sidebar fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-navy text-white transition-all duration-200 lg:translate-x-0">
                <a href="{{ route('dashboard') }}" class="flex h-20 items-center gap-3 border-b border-white/10 px-6" title="{{ __('common.app_name') }}">
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-primary text-lg font-bold">K</span>
                    <span class="sidebar-label"><span class="block text-base font-bold">{{ __('common.app_name') }}</span><span class="mt-0.5 block text-xs text-slate-400">{{ __('common.tagline') }}</span></span>
                </a>
                <nav class="flex-1 space-y-1 px-3 py-6" aria-label="{{ __('nav.main_menu') }}">
                    <p class="sidebar-label px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ __('nav.main_menu') }}</p>
                    @foreach ($navigation as $item)
                        @php
                            $active = match ($item['route']) {
                                'dashboard' => request()->routeIs('dashboard'),
                                'products.index' => request()->routeIs('products.*'),
                                'cashier.index' => request()->routeIs('cashier.*', 'transactions.*'),
                                'reports.index' => request()->routeIs('reports.*'),
                                'users.index' => request()->routeIs('users.*'),
                                default => request()->routeIs('settings.*'),
                            };
                        @endphp
                        <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}" @class(['sidebar-link', 'sidebar-link-active' => $active])>
                            <span class="sidebar-icon" aria-hidden="true">
                                @switch($item['icon'])
                                    @case('grid') <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg> @break
                                    @case('box') <svg viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8M12 13v9"/></svg> @break
                                    @case('cart') <svg viewBox="0 0 24 24"><circle cx="9" cy="20" r="1"/><circle cx="19" cy="20" r="1"/><path d="M2 3h2l2.7 12.4a2 2 0 0 0 2 1.6h9.8a2 2 0 0 0 2-1.6L22 8H5"/></svg> @break
                                    @case('chart') <svg viewBox="0 0 24 24"><path d="M3 3v18h18M8 15l4-4 3 3 6-7"/></svg> @break
                                    @case('users') <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg> @break
                                    @default <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.7 1l-.3 1.8h-2.8l-.3-1.8a8 8 0 0 1-1.7-1l-1.7.7-1.4 2.4 1.4 1.1a7 7 0 0 1 0-2l-1.4-1.1 1.4-2.4 1.7.7a8 8 0 0 1 1.7 1l.3-1.8h2.8l.3 1.8a8 8 0 0 1 1.7 1l1.7-.7 1.4 2.4-1.4 1.1a7 7 0 0 1 0 2Z"/></svg>
                                @endswitch
                            </span>
                            <span class="sidebar-label">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
                <div class="border-t border-white/10 p-3">
                    <div class="mb-3 flex items-center gap-3 rounded-lg bg-white/5 px-3 py-3" title="{{ auth()->user()->name }}">
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-slate-700 text-xs font-semibold">{{ auth()->user()->initials }}</span>
                        <span class="sidebar-label min-w-0"><span class="block truncate text-sm font-medium">{{ auth()->user()->name }}</span><span class="block text-xs text-slate-400">{{ auth()->user()->role_label }}</span></span>
                    </div>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="sidebar-link w-full text-left" type="submit" title="{{ __('nav.logout') }}">
                            <span class="sidebar-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg></span>
                            <span class="sidebar-label">{{ __('nav.logout') }}</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="app-content flex min-h-screen min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-line bg-surface/95 px-4 backdrop-blur sm:px-6">
                    <div class="flex items-center gap-3">
                        <button id="sidebar-toggle" class="btn-icon lg:hidden" type="button" aria-label="{{ __('nav.open_menu') }}" aria-expanded="false"><svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                        <a class="btn-icon hidden lg:grid" href="{{ route('sidebar.toggle') }}" title="{{ $sidebarCollapsed ? __('nav.expand') : __('nav.collapse') }}" aria-label="{{ $sidebarCollapsed ? __('nav.expand') : __('nav.collapse') }}"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/></svg></a>
                        <div><p class="text-sm font-semibold text-ink">@yield('title', __('nav.dashboard'))</p><p class="hidden text-xs text-muted sm:block">{{ __('header.subtitle') }}</p></div>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        @include('partials.preferences')
                        <a href="{{ route('products.index', ['stok_rendah' => 1]) }}" class="btn-icon relative" title="{{ __('header.low_stock') }}" aria-label="{{ __('header.view_low_stock') }}">
                            <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                            @isset($jumlahStokRendah)
                                @if ($jumlahStokRendah > 0)<span class="absolute right-1 top-1 grid min-w-4 place-items-center rounded-full bg-danger px-1 text-[9px] font-bold leading-4 text-white">{{ $jumlahStokRendah }}</span>@endif
                            @endisset
                        </a>
                        <span class="hidden text-right sm:block"><span class="block text-sm font-semibold">{{ auth()->user()->name }}</span><span class="block text-xs text-muted">{{ auth()->user()->role_label }}</span></span>
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-primary-light text-xs font-bold text-primary">{{ auth()->user()->initials }}</span>
                    </div>
                </header>
                <main class="mx-auto w-full max-w-[1600px] flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="notice notice-success" role="status" data-flash="success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="notice notice-error" role="alert" data-flash="error">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="notice notice-error" role="alert" data-flash="error">{{ $errors->first() }}</div>
                    @endif
                    @yield('content')
                </main>
                <footer class="border-t border-line px-6 py-4 text-center text-xs text-muted">{{ __('common.app_name') }} <span aria-hidden="true">&middot;</span> {{ __('common.tagline') }}</footer>
            </div>
        </div>
    @else
        <main class="min-h-screen">@yield('content')</main>
    @endauth
    @stack('scripts')
</body>
</html>
