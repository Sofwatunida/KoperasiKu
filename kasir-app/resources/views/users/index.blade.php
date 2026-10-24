@extends('layouts.app')

@section('title', __('users.title'))

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div><h1 class="page-title">{{ __('users.title') }}</h1><p class="page-subtitle">{{ __('users.page_subtitle') }}</p></div>
        <a class="btn-primary" href="{{ route('users.create') }}"><span aria-hidden="true">+</span> {{ __('users.add') }}</a>
    </div>
    <section class="card">
        <div class="card-header">
            <div><h2 class="card-title">{{ __('users.list_title') }}</h2><p class="mt-1 text-xs text-muted">@if (auth()->user()->isSuperAdmin()){{ number_format($totalSuperAdmin, 0, ',', '.') }} {{ __('users.super_admin') }} &middot; {{ number_format($totalAdmin, 0, ',', '.') }} {{ __('users.admin') }} &middot; {{ number_format($totalKasir, 0, ',', '.') }} {{ __('users.kasir') }}@else{{ number_format($totalKasir, 0, ',', '.') }} {{ __('users.kasir') }}@endif</p></div>
            <form action="{{ route('users.index') }}" method="get" class="flex w-full gap-2 sm:w-auto">
                <label class="sr-only" for="user-search">{{ __('users.search_placeholder') }}</label><input class="input min-w-0 sm:w-72" id="user-search" name="search" value="{{ $search }}" placeholder="{{ __('users.search_placeholder') }}"><button class="btn-secondary" type="submit">{{ __('common.search') }}</button>
                @if ($search) <a class="btn-ghost" href="{{ route('users.index') }}">{{ __('common.reset') }}</a> @endif
            </form>
        </div>
        <div class="table-wrap"><table class="table"><thead><tr><th>{{ __('register.name') }}</th><th>{{ __('register.username') }}</th><th>{{ __('register.email') }}</th><th>Role</th><th>{{ __('common.action') }}</th></tr></thead><tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="font-semibold">{{ $user->name }}</td><td class="text-muted">{{ $user->username }}</td><td>{{ $user->email }}</td>
                    <td><span @class(['badge-primary' => $user->isSuperAdmin(), 'badge-success' => $user->isAdmin(), 'badge-muted' => $user->isKasir()])>{{ $user->role_label }}</span></td>
                    <td><div class="flex items-center gap-2"><a class="btn-secondary btn-sm" href="{{ route('users.edit', $user) }}">{{ __('common.edit') }}</a><form action="{{ route('users.destroy', $user) }}" method="post" data-confirm="{{ __('users.delete_confirm') }}" data-confirm-title="{{ __('users.delete_title') }}" data-confirm-tone="danger" data-confirm-button="{{ __('common.yes_continue') }}">@csrf @method('DELETE')<button class="btn-ghost btn-sm text-danger" type="submit">{{ __('common.delete') }}</button></form></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center"><p class="font-medium">{{ $search ? __('users.not_found') : __('users.empty') }}</p><p class="mt-1 text-sm text-muted">{{ $search ? __('common.not_found_hint') : __('users.empty_hint') }}</p></td></tr>
            @endforelse
        </tbody></table></div>
        @if ($users->hasPages()) <div class="border-t border-line px-5 py-4">{{ $users->links() }}</div> @endif
    </section>
@endsection
