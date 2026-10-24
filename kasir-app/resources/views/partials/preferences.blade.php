@php
    $flagNames = ['id' => 'Indonesia', 'en' => 'English'];
@endphp
<div class="relative" data-dropdown>
    <button class="btn-icon" type="button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false" aria-label="{{ __('preferences.title') }}" title="{{ __('preferences.title') }}">
        <img src="{{ asset('images/flags/'.$currentLocale.'.svg') }}" alt="{{ strtoupper($currentLocale) }}" class="size-5 rounded-sm object-cover" width="20" height="20">
    </button>
    <div class="dropdown-menu absolute right-0 z-50 mt-2 hidden w-56 rounded-card border border-line bg-surface p-2 shadow-pop" data-dropdown-menu role="menu">
        <p class="dropdown-heading">{{ __('preferences.theme') }}</p>
        @foreach (['light', 'dark'] as $themeOption)
            <a class="dropdown-item" href="{{ route('theme.switch', $themeOption) }}" role="menuitem" @if ($currentTheme === $themeOption) aria-current="true" @endif>
                <span class="sidebar-icon" aria-hidden="true">
                    @if ($themeOption === 'light')
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                    @else
                        <svg viewBox="0 0 24 24"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
                    @endif
                </span>
                <span>{{ $themeOption === 'light' ? __('preferences.light') : __('preferences.dark') }}</span>
            </a>
        @endforeach
        <div class="my-2 border-t border-line"></div>
        <p class="dropdown-heading">{{ __('preferences.language') }}</p>
        @foreach ($availableLocales as $locale)
            <a class="dropdown-item" href="{{ route('language.switch', $locale) }}" role="menuitem" @if ($currentLocale === $locale) aria-current="true" @endif>
                <img src="{{ asset('images/flags/'.$locale.'.svg') }}" alt="" class="size-4 shrink-0 rounded-sm object-cover" width="16" height="16">
                <span>{{ $flagNames[$locale] ?? strtoupper($locale) }}</span>
            </a>
        @endforeach
    </div>
</div>
