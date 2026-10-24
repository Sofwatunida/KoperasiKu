@extends('layouts.app')

@section('title', __('settings.title'))

@section('content')
    <div class="mb-6"><h1 class="page-title">{{ __('settings.page_title') }}</h1><p class="page-subtitle">{{ __('settings.page_subtitle') }}</p></div>
    <div class="max-w-3xl">
        <form action="{{ route('settings.update') }}" method="post" class="card">
            @csrf @method('PUT')
            <div class="card-header">
                <div><h2 class="card-title">{{ __('settings.info_title') }}</h2><p class="mt-1 text-xs text-muted">{{ __('settings.role_hint', ['role' => $user->role_label]) }}</p></div>
            </div>
            <div class="grid gap-5 p-5 sm:grid-cols-2">
                <div><label class="label" for="name">{{ __('settings.field_name') }}</label><input class="input @error('name') input-error @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>@error('name') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div><label class="label" for="username">{{ __('settings.field_username') }}</label><input class="input @error('username') input-error @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>@error('username') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div class="sm:col-span-2"><label class="label" for="email">{{ __('settings.field_email') }}</label><input class="input @error('email') input-error @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>@error('email') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div class="sm:col-span-2">
                    <label class="label" for="current_password">{{ __('settings.current_password') }}</label>
                    <div class="relative"><input class="input pr-11 @error('current_password') input-error @enderror" id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="{{ __('settings.current_password_placeholder') }}">@include('partials.password-toggle', ['for' => 'current_password'])</div>
                    @error('current_password') <p class="field-error">{{ $message }}</p> @else <p class="mt-1.5 text-xs text-muted">{{ __('settings.current_password_hint') }}</p> @enderror
                </div>
            </div>
            <div class="card-header border-t border-line">
                <div><h2 class="card-title">{{ __('settings.password_title') }}</h2><p class="mt-1 text-xs text-muted">{{ __('settings.password_hint') }}</p></div>
            </div>
            <div class="grid gap-5 p-5 sm:grid-cols-2">
                <div><label class="label" for="password">{{ __('settings.new_password') }}</label><div class="relative"><input class="input pr-11 @error('password') input-error @enderror" id="password" name="password" type="password" autocomplete="new-password" placeholder="{{ __('settings.new_password_placeholder') }}">@include('partials.password-toggle', ['for' => 'password'])</div>@error('password') <p class="field-error">{{ $message }}</p> @enderror</div>
                <div><label class="label" for="password_confirmation">{{ __('settings.confirm_password') }}</label><div class="relative"><input class="input pr-11" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">@include('partials.password-toggle', ['for' => 'password_confirmation'])</div></div>
            </div>
            <div class="flex justify-end border-t border-line px-5 py-4"><button class="btn-primary" type="submit">{{ __('settings.save') }}</button></div>
        </form>

        <section class="card mt-6">
            <div class="card-header">
                <div><h2 class="card-title">{{ __('backup.title') }}</h2><p class="mt-1 text-xs text-muted">{{ __('backup.description') }}</p></div>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 p-5">
                <p class="text-xs text-muted">{{ __('backup.hint') }}</p>
                <a class="btn-secondary" href="{{ route('settings.backup') }}">{{ __('backup.button') }}</a>
            </div>
        </section>

        <section class="card mt-6">
            <div class="card-header">
                <div><h2 class="card-title">{{ __('import.title') }}</h2><p class="mt-1 text-xs text-muted">{{ __('import.description') }}</p></div>
            </div>
            <form action="{{ route('settings.import') }}" method="post" enctype="multipart/form-data" class="space-y-4 p-5"
                data-confirm="{{ __('import.confirm') }}"
                data-confirm-title="{{ __('import.confirm_title') }}"
                data-confirm-button="{{ __('import.confirm_button') }}">
                @csrf
                <div>
                    <label class="label" for="backup_file">{{ __('import.file_label') }}</label>
                    <input class="input @error('backup_file') input-error @enderror" id="backup_file" name="backup_file" type="file" accept=".json,.txt,application/json,text/plain" required>
                    @error('backup_file') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end"><button class="btn-primary" type="submit">{{ __('import.button') }}</button></div>
            </form>
        </section>

        <section class="card mt-6 border-danger/30">
            <div class="card-header">
                <div><h2 class="card-title text-danger">{{ __('reset.title') }}</h2><p class="mt-1 text-xs text-muted">{{ __('reset.description') }}</p></div>
            </div>
            <form action="{{ route('settings.reset') }}" method="post" class="space-y-4 p-5">
                @csrf @method('DELETE')
                <input type="hidden" name="confirmation" value="">
                <div class="flex items-start gap-3 rounded-field border border-danger/20 bg-danger-soft px-4 py-3 text-sm font-medium text-danger" role="alert">
                    <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
                    <span>{!! __('reset.warning') !!}</span>
                </div>
                <div class="flex justify-end"><button class="btn-danger" type="button" data-reset-trigger>{{ __('reset.button') }}</button></div>
            </form>
        </section>
    </div>
@endsection
