@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="mb-6"><h1 class="page-title">Pengaturan Akun</h1><p class="page-subtitle">Perbarui email, password, dan informasi akun Anda.</p></div>
    <div class="max-w-3xl">
        <form action="{{ route('settings.update') }}" method="post" class="card">
            @csrf @method('PUT')
            <div class="card-header">
                <div><h2 class="card-title">Informasi Akun</h2><p class="mt-1 text-xs text-muted">Role akun: <span class="font-semibold text-ink">{{ $user->role_label }}</span> <span aria-hidden="true">&middot;</span> role tidak dapat diubah dari halaman ini.</p></div>
            </div>
            <div class="grid gap-5 p-5 sm:grid-cols-2">
                <div><label class="label" for="name">Nama</label><input class="input @error('name') input-error @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div><label class="label" for="username">Username</label><input class="input @error('username') input-error @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>@error('username') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div class="sm:col-span-2"><label class="label" for="email">Email</label><input class="input @error('email') input-error @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>@error('email') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div class="sm:col-span-2">
                    <label class="label" for="current_password">Password Saat Ini</label>
                    <input class="input @error('current_password') input-error @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="Diperlukan untuk mengubah email atau password">
                    @error('current_password') <p class="field-error">{{ $message }}</p> @else <p class="mt-1.5 text-xs text-muted">Wajib diisi hanya jika Anda mengubah email atau password.</p> @enderror
                </div>
            </div>
            <div class="card-header border-t border-line">
                <div><h2 class="card-title">Ubah Password</h2><p class="mt-1 text-xs text-muted">Kosongkan jika tidak ingin mengganti password.</p></div>
            </div>
            <div class="grid gap-5 p-5 sm:grid-cols-2">
                <div><label class="label" for="password">Password Baru</label><input class="input @error('password') input-error @enderror" id="password" name="password" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter">@error('password') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div><label class="label" for="password_confirmation">Konfirmasi Password Baru</label><input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"></div>
            </div>
            <div class="flex justify-end border-t border-line px-5 py-4"><button class="btn-primary" type="submit">Simpan Perubahan</button></div>
        </form>
    </div>
@endsection
