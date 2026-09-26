<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir Laravel</title>
    
    <!-- Bootstrap 5 CSS (Instan & Rapi) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Navigasi / Navbar Utama -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('cashier.index') }}">🏪 Toko Kita Jaya</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('cashier*') ? 'active' : '' }}" href="{{ route('cashier.index') }}">🛒 Transaksi Kasir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">📦 Manajemen Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">🧾 Riwayat Transaksi</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Notifikasi Alert Sukses / Eror -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Wadah Konten Utama Halaman (Halaman Produk / Kasir akan muncul di sini) -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle (Untuk interaksi tombol, dropdown, dan alert) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
