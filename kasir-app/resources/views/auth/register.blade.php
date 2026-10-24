@extends('layouts.app')

@section('title', __('register.title'))

@section('content')
    <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
        <section class="hidden overflow-hidden bg-navy px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between xl:px-16">
            <a href="{{ route('register') }}" class="flex w-fit items-center gap-3"><span class="grid size-11 place-items-center rounded-lg bg-primary text-lg font-bold">K</span><span class="text-lg font-bold">{{ __('common.app_name') }}</span></a>
            <div class="mx-auto w-full max-w-lg py-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-300">{{ __('common.tagline') }}</p>
                <h1 class="mt-4 max-w-md text-4xl font-semibold leading-tight">{{ __('register.hero_heading') }}</h1>
                <p class="mt-4 max-w-md text-sm leading-6 text-slate-300">{{ __('register.hero_text') }}</p>
            </div>
            <p class="text-xs text-slate-500">{{ __('common.app_name') }} <span aria-hidden="true">&middot;</span> {{ __('common.tagline') }}</p>
        </section>
        <section class="flex items-center justify-center px-5 py-10 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-6 flex items-center justify-between lg:justify-end">
                    <a href="{{ route('register') }}" class="flex w-fit items-center gap-3 lg:hidden"><span class="grid size-10 place-items-center rounded-lg bg-primary text-lg font-bold text-white">K</span><span class="font-bold">{{ __('common.app_name') }}</span></a>
                    @include('partials.preferences')
                </div>
                <p class="text-sm font-semibold text-primary">{{ __('common.app_name') }}</p><h2 class="mt-2 text-3xl font-semibold tracking-normal">{{ __('register.heading') }}</h2><p class="mt-2 text-sm text-muted">{{ __('register.subtitle') }}</p>
                @if ($errors->any()) <div class="notice notice-error mt-5" role="alert">{{ $errors->first() }}</div> @endif
                <form action="{{ route('register.store') }}" method="post" class="mt-7 space-y-4">
                    @csrf
                    <div><label class="label" for="name">{{ __('register.name') }}</label><input class="input @error('name') input-error @enderror" id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>@error('name') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="username">{{ __('register.username') }}</label><input class="input @error('username') input-error @enderror" id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required>@error('username') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="email">{{ __('register.email') }}</label><input class="input @error('email') input-error @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="password">{{ __('register.password') }}</label><div class="relative"><input class="input pr-11 @error('password') input-error @enderror" id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>@include('partials.password-toggle', ['for' => 'password'])</div>@error('password') <p class="field-error">{{ $message }}</p> @enderror</div>
                    <div><label class="label" for="password_confirmation">{{ __('register.password_confirmation') }}</label><div class="relative"><input class="input pr-11" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>@include('partials.password-toggle', ['for' => 'password_confirmation'])</div></div>
                    <button class="btn-primary w-full" type="submit">{{ __('register.submit') }}</button>
                </form>
                <p class="mt-6 text-center text-sm text-muted">{{ __('register.have_account') }} <a class="font-semibold text-primary hover:underline" href="{{ route('login') }}">{{ __('register.login') }}</a></p>
            </div>
        </section>
    </div>
@endsection
