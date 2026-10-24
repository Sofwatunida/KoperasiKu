{{-- Tombol tampil/sembunyikan password dengan satu ikon yang berganti antara eye dan eye-off. --}}
<button class="absolute inset-y-0 right-2 my-auto grid size-8 place-items-center rounded-field text-muted transition hover:text-ink focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none" type="button" data-password-toggle="#{{ $for }}" aria-label="{{ __('common.show_password') }}" title="{{ __('common.show_password') }}">
    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-password-icon aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
</button>
