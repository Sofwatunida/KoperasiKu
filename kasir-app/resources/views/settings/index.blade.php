@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="mb-6"><h1 class="page-title">Pengaturan Akun</h1><p class="page-subtitle">Perbarui informasi petugas yang sedang masuk.</p></div>
    <div class="max-w-3xl">
        <form action="{{ route('settings.update') }}" method="post" class="card">
            @csrf @method('PUT')
            <div class="card-header"><div><h2 class="card-title">Informasi Akun</h2><p class="mt-1 text-xs text-muted">Role akun: <span class="capitalize">{{ $user->role }}</span></p></div></div>
            <div class="grid gap-5 p-5 sm:grid-cols-2">
                <div><label class="label" for="name">Nama Petugas</label><input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div><label class="label" for="username">Username</label><input class="input" id="username" name="username" value="{{ old('username', $user->username) }}" required>@error('username') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div class="sm:col-span-2"><label class="label" for="email">Email</label><input class="input" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>@error('email') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div><label class="label" for="password">Password Baru</label><input class="input" id="password" name="password" type="password" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah">@error('password') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div><label class="label" for="password_confirmation">Konfirmasi Password Baru</label><input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"></div>
            </div>
            <div class="flex justify-end border-t border-line px-5 py-4"><button class="btn-primary" type="submit">Simpan Perubahan</button></div>
        </form>
    </div>
@endsection