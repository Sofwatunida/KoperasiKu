<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menerapkan preferensi tampilan pengguna dari cookie: bahasa (locale)
 * dan tema (terang/gelap), lalu membagikannya ke seluruh view agar layout
 * dapat merender attr <html>, pemilih bahasa, dan pemilih tema yang konsisten.
 */
class SetPreferences
{
    /**
     * Bahasa yang didukung aplikasi.
     *
     * @var array<int, string>
     */
    public const SUPPORTED_LOCALES = ['id', 'en'];

    /**
     * Tema tampilan yang didukung aplikasi.
     *
     * @var array<int, string>
     */
    public const SUPPORTED_THEMES = ['light', 'dark'];

    public const DEFAULT_LOCALE = 'id';

    public const DEFAULT_THEME = 'light';

    /**
     * Umur cookie preferensi dalam menit (satu tahun).
     */
    public const COOKIE_LIFETIME = 60 * 24 * 365;

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('locale');
        $locale = in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : self::DEFAULT_LOCALE;

        $theme = $request->cookie('theme');
        $theme = in_array($theme, self::SUPPORTED_THEMES, true) ? $theme : self::DEFAULT_THEME;

        App::setLocale($locale);

        view()->share([
            'currentLocale' => $locale,
            'availableLocales' => self::SUPPORTED_LOCALES,
            'currentTheme' => $theme,
            'sidebarCollapsed' => $request->cookie('sidebar') === 'collapsed',
        ]);

        return $next($request);
    }
}
