<?php

namespace App\Providers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Lokasi berkas terjemahan tunggal per bahasa (lang/id.php, lang/en.php).
     *
     * @var array<int, string>
     */
    private const LOCALES = ['id', 'en'];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (self::LOCALES as $locale) {
            $path = lang_path($locale.'.php');

            if (is_file($path)) {
                Lang::addLines($this->flatten(include $path), $locale);
            }
        }
    }

    /**
     * Ratakan array bertingkat menjadi kunci notasi-dik (mis. login.title).
     *
     * @param  array<string, mixed>  $lines
     * @return array<string, string>
     */
    private function flatten(array $lines, string $prefix = ''): array
    {
        $flattened = [];

        foreach ($lines as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $flattened += $this->flatten($value, $fullKey);

                continue;
            }

            $flattened[$fullKey] = $value;
        }

        return $flattened;
    }
}
