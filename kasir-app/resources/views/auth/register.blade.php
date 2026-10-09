@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
        <section class="hidden overflow-hidden bg-navy px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
            <a href="{{ route('register') }}" class="flex w-fit items-center gap-3"><span class="grid size-11 place-items-center rounded-lg bg-primary text-lg font-bold">K</span><span class="text-lg font-bold">KoperasiKu</span></a>
            <div class="mx-auto w-full max-w-lg py-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-300">Sistem Kasir &amp; Manajemen Koperasi</p>
                <h1 class="mt-4 max-w-md text-4xl font-semibold leading-tight">Buat akun petugas koperasi.</h1>
                <p class="mt-4 max-w-md text-sm leading-6 text-slate-300">Akun baru mendapat akses petugas. Hak akses admin hanya dapat diberikan oleh pengelola.</p>
            </div>
            <p class="text-xs text-slate-500">KoperasiKu <span aria-hidden="true">&middot;</span> Sistem Kasir &amp; Manajemen Koperasi</p>
        </section>
        <section class="flex items-center justify-center px-5 py-10 sm:px-10">
            <div class="w-full max-w-md">
                <a href="{{ route('register') }}" class="mb-8 flex w-fit items-center gap-3 lg:hidden"><span class="grid size-10 place-items-center rounded-lg bg-primary text-lg font-bold text-white">K</span><span class="font-bold">KoperasiKu</span></a>
                <p class="text-sm font-semibold text-primary">KoperasiKu</p><h2 class="mt-2 text-3xl font-semibold tracking-normal">Buat akun baru</h2><p class="mt-2 text-sm text-muted">Daftarkan akun petugas untuk mulai menggunakan aplikasi.</p>
                @if ($errors->any()) <div class="notice notice-error mt-5" role="alert">{{ $errors->first() }}</div> @endif
                <form action="{{ route('register.store') }}" method="post" class="mt-7 space-y-4">
                    @csrf
                    <div><label class="label" for="name">Nama lengkap</label><input class="input @error('name') input-error @enderror" id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>@error('name') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="username">Username</label><input class="input @error('username') input-error @enderror" id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required>@error('username') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="email">Email</label><input class="input @error('email') input-error @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="password">Password</label><input class="input @error('password') input-error @enderror" id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>@error('password') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="password_confirmation">Konfirmasi password</label><input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required></div>
                    <button class="btn-primary w-full" type="submit">Daftar sebagai Petugas</button>
                </form>
                <p class="mt-6 text-center text-sm text-muted">Sudah punya akun? <a class="font-semibold text-primary hover:underline" href="{{ route('login') }}">Masuk</a></p>
            </div>
        </section>
    </div>
@endsection