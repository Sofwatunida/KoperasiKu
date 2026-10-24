@extends('layouts.app')

@section('title', __('login.title'))

@section('content')
    <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
        <section class="relative hidden overflow-hidden bg-navy px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
            <a href="{{ route('login') }}" class="flex w-fit items-center gap-3"><span class="grid size-11 place-items-center rounded-lg bg-primary text-lg font-bold">K</span><span class="text-lg font-bold">{{ __('common.app_name') }}</span></a>
            <div class="mx-auto w-full max-w-lg py-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-300">{{ __('login.hero_tagline') }}</p>
                <h1 class="mt-4 max-w-md text-4xl font-semibold leading-tight">{{ __('login.hero_heading') }}</h1>
                <p class="mt-4 max-w-md text-sm leading-6 text-slate-300">{{ __('login.hero_text') }}</p>
                <div class="mt-10 rounded-lg border border-white/10 bg-white/6 p-5 shadow-pop">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4"><div><p class="text-xs text-slate-400">{{ __('login.summary_label') }}</p><p class="mt-1 text-xl font-semibold">Rp2.450.000</p></div><span class="rounded-full bg-emerald-400/10 px-2.5 py-1 text-xs font-semibold text-emerald-300">{{ __('login.today') }}</span></div>
                    <div class="space-y-3 pt-4 text-sm"><div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-200"><span class="size-2 rounded-full bg-blue-400"></span>Pulpen Standard</span><span class="text-slate-300">2 &times; Rp2.000</span></div><div class="flex items-center justify-between"><span class="flex items-center gap-2 text-slate-200"><span class="size-2 rounded-full bg-amber-400"></span>Buku Tulis</span><span class="text-slate-300">1 &times; Rp5.000</span></div></div>
                </div>
            </div>
            <p class="text-xs text-slate-500">{{ __('common.app_name') }} <span aria-hidden="true">&middot;</span> {{ __('common.tagline') }}</p>
        </section>
        <section class="flex items-center justify-center px-5 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-6 flex items-center justify-between lg:justify-end">
                    <a href="{{ route('login') }}" class="flex w-fit items-center gap-3 lg:hidden"><span class="grid size-10 place-items-center rounded-lg bg-primary text-lg font-bold text-white">K</span><span class="font-bold">{{ __('common.app_name') }}</span></a>
                    @include('partials.preferences')
                </div>
                <p class="text-sm font-semibold text-primary">{{ __('common.app_name') }}</p><h2 class="mt-2 text-3xl font-semibold tracking-normal">{{ __('login.heading') }}</h2><p class="mt-2 text-sm text-muted">{{ __('login.subtitle') }}</p>
                @if (session('success')) <div class="notice notice-success mt-6" role="status">{{ session('success') }}</div> @endif
                @if ($errors->any()) <div class="notice notice-error mt-6" role="alert">{{ $errors->first() }}</div> @endif
                <form action="{{ route('login') }}" method="post" class="mt-8 space-y-5">
                    @csrf
                    <div><label class="label" for="username">{{ __('login.identifier') }}</label><input class="input @error('username') input-error @enderror" id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" autofocus required>@error('username') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="password">{{ __('register.password') }}</label><div class="relative"><input class="input pr-11 @error('password') input-error @enderror" id="password" name="password" type="password" autocomplete="current-password" required>@include('partials.password-toggle', ['for' => 'password'])</div>@error('password') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <button class="btn-primary w-full" type="submit">{{ __('login.submit') }}</button>
                </form>
                <p class="mt-6 text-center text-sm text-muted">{{ __('login.no_account') }} <a class="font-semibold text-primary hover:underline" href="{{ route('register') }}">{{ __('login.register') }}</a></p>
            </div>
        </section>
    </div>
@endsection
