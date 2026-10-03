<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'KoperasiKu')</title>

    {{-- Asset AdminLTE (CSS + JS) hasil build Vite --}}
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])

    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

<div class="app-wrapper">

    {{-- Navbar --}}
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    Profil Saya
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit" class="dropdown-item">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

            </ul>
        </div>
    </nav>


    {{-- Sidebar --}}
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

<div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-text fw-light">KoperasiKu</span>
            </a>
        </div>

        <div class="sidebar-wrapper">

            <nav class="mt-2">

                <ul class="nav sidebar-menu flex-column"
                    data-lte-toggle="treeview"
                    role="menu">

                    {{-- Dashboard --}}
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                            <i class="nav-icon bi bi-speedometer2"></i>

                            <p>Dashboard</p>
                        </a>
                    </li>


                    {{-- Produk --}}
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}"
                           class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">

                            <i class="nav-icon bi bi-box-seam"></i>

                            <p>Produk</p>
                        </a>
                    </li>


                    {{-- Kasir --}}
                    <li class="nav-item">
                        <a href="{{ route('cashier.index') }}"
                           class="nav-link {{ request()->routeIs('cashier.*') ? 'active' : '' }}">

                            <i class="nav-icon bi bi-cart-check"></i>

                            <p>Kasir</p>
                        </a>
                    </li>


                    {{-- Transaksi --}}
                    <li class="nav-item">
                        <a href="{{ route('transactions.index') }}"
                           class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">

                            <i class="nav-icon bi bi-receipt"></i>

                            <p>Transaksi</p>
                        </a>
                    </li>

                </ul>

            </nav>

        </div>

    </aside>


    {{-- Content --}}
    <main class="app-main">

        <div class="app-content-header">
            <div class="container-fluid">

                <h3 class="mb-0">
                    @yield('title', 'Dashboard')
                </h3>

            </div>
        </div>


        <div class="app-content">

            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot ?? '' }}

                @yield('content')

            </div>

        </div>

    </main>


    {{-- Footer --}}
    <footer class="app-footer">
        <strong>
            KoperasiKu
        </strong>

        <div class="float-end d-none d-sm-inline">
            Aplikasi Kasir
        </div>
    </footer>

</div>

@stack('scripts')

</body>
</html>