@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div><h1 class="page-title">Manajemen Pengguna</h1><p class="page-subtitle">Kelola akun super admin, admin, dan kasir koperasi.</p></div>
        <a class="btn-primary" href="{{ route('users.create') }}"><span aria-hidden="true">+</span> Tambah Pengguna</a>
    </div>
    <section class="card">
        <div class="card-header">
            <div><h2 class="card-title">Daftar Pengguna</h2><p class="mt-1 text-xs text-muted">{{ number_format($totalSuperAdmin, 0, ',', '.') }} super admin &middot; {{ number_format($totalAdmin, 0, ',', '.') }} admin &middot; {{ number_format($totalKasir, 0, ',', '.') }} kasir</p></div>
            <form action="{{ route('users.index') }}" method="get" class="flex w-full gap-2 sm:w-auto">
                <label class="sr-only" for="user-search">Cari nama, username, atau email</label><input class="input min-w-0 sm:w-72" id="user-search" name="search" value="{{ $search }}" placeholder="Cari nama, username, atau email..."><button class="btn-secondary" type="submit">Cari</button>
                @if ($search) <a class="btn-ghost" href="{{ route('users.index') }}">Reset</a> @endif
            </form>
        </div>
        <div class="table-wrap"><table class="table"><thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead><tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="font-semibold">{{ $user->name }}</td><td class="text-muted">{{ $user->username }}</td><td>{{ $user->email }}</td>
                    <td><span @class(['badge-primary' => $user->isSuperAdmin(), 'badge-success' => $user->isAdmin(), 'badge-muted' => $user->isKasir()])>{{ $user->role_label }}</span></td>
                    <td><div class="flex items-center gap-2"><a class="btn-secondary btn-sm" href="{{ route('users.edit', $user) }}">Edit</a><form action="{{ route('users.destroy', $user) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">@csrf @method('DELETE')<button class="btn-ghost btn-sm text-danger" type="submit">Hapus</button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center"><p class="font-medium">{{ $search ? 'Pengguna tidak ditemukan.' : 'Belum ada pengguna.' }}</p><p class="mt-1 text-sm text-muted">{{ $search ? 'Coba kata kunci lain.' : 'Tambahkan pengguna pertama.' }}</p></td></tr>
            @endforelse
        </tbody></table></div>
        @if ($users->hasPages()) <div class="border-t border-line px-5 py-4">{{ $users->links() }}</div> @endif
    </section>
@endsection