<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetPreferences;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

/**
 * Menyimpan preferensi tampilan pengguna (bahasa, tema, sidebar) ke cookie
 * sehingga pilihan tetap berlaku pada kunjungan berikutnya.
 */
class PreferenceController extends Controller
{
    /**
     * Ganti bahasa antarmuka (dengan bendera pada pemilih bahasa).
     */
    public function language(string $locale): RedirectResponse
    {
        Cookie::queue('locale', $locale, SetPreferences::COOKIE_LIFETIME);

        App::setLocale($locale);

        return back();
    }

    /**
     * Ganti tema antarmuka terang/gelap.
     */
    public function theme(string $theme): RedirectResponse
    {
        Cookie::queue('theme', $theme, SetPreferences::COOKIE_LIFETIME);

        return back();
    }

    /**
     * Sembunyikan/tampilkan sidebar (collapse) pada layar besar.
     */
    public function sidebar(): RedirectResponse
    {
        $collapsed = request()->cookie('sidebar') === 'collapsed';

        Cookie::queue('sidebar', $collapsed ? 'expanded' : 'collapsed', SetPreferences::COOKIE_LIFETIME);

        return back();
    }
}
