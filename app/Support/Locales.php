<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Locales
{
    public static function all(): array
    {
        return config('locales.supported', []);
    }

    public static function codes(): array
    {
        return array_keys(self::all());
    }

    public static function label(string $code): string
    {
        return self::all()[$code]['label'] ?? $code;
    }

    public static function labels(array $codes): array
    {
        return array_map(
            fn (string $code) => self::label($code),
            $codes
        );
    }

    public static function short(string $code): string
    {
        return self::all()[$code]['short'] ?? strtoupper($code);
    }

    public static function isSupported(string $code): bool
    {
        return array_key_exists($code, self::all());
    }

    /**
     * Keep only translations for currently supported locale codes.
     * Lets seeders define many languages while enabling/disabling via config.
     *
     * @param  array<string, mixed>  $byLocale
     * @return array<string, mixed>
     */
    public static function pick(array $byLocale): array
    {
        return array_intersect_key($byLocale, array_flip(self::codes()));
    }

    public static function rule(): string
    {
        return 'in:'.implode(',', self::codes());
    }

    /**
     * Apply the best available locale for the current request.
     *
     * Used by error pages (including 503 maintenance, which never reaches
     * the web SetLocale middleware) so copy matches the visitor language.
     */
    public static function applyFromRequest(?Request $request = null, bool $useBrowserFallback = false): string
    {
        $request ??= request();
        $candidates = [];

        $query = $request->query('locale');
        if (is_string($query)) {
            $candidates[] = $query;
        }

        try {
            if ($request->hasSession()) {
                $sessionLocale = $request->session()->get('locale');
                if (is_string($sessionLocale)) {
                    $candidates[] = $sessionLocale;
                }
            }
        } catch (\Throwable) {
        }

        $userLocale = $request->user()?->locale;
        if (is_string($userLocale)) {
            $candidates[] = $userLocale;
        }

        if ($useBrowserFallback) {
            $fromHeader = self::fromAcceptLanguage($request->header('Accept-Language'));
            if (is_string($fromHeader)) {
                $candidates[] = $fromHeader;
            }
        }

        $candidates[] = (string) config('app.locale');

        $locale = (string) config('app.locale');
        foreach ($candidates as $candidate) {
            if (is_string($candidate) && self::isSupported($candidate)) {
                $locale = $candidate;
                break;
            }
        }

        App::setLocale($locale);
        self::applyCarbon($locale);

        return $locale;
    }

    public static function applyCarbon(string $locale): void
    {
        $carbon = match (true) {
            str_starts_with($locale, 'fr') => 'fr',
            str_starts_with($locale, 'de') => 'de',
            str_starts_with($locale, 'it') => 'it',
            default => 'en',
        };

        \Carbon\Carbon::setLocale($carbon);
        \Carbon\CarbonImmutable::setLocale($carbon);
    }

    public static function fromAcceptLanguage(?string $header): ?string
    {
        if (! $header) {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $tag = strtolower(trim(explode(';', $part)[0]));

            if ($tag === '') {
                continue;
            }

            if (self::isSupported($tag)) {
                return $tag;
            }

            $short = strtok($tag, '-');

            foreach (self::codes() as $code) {
                if (str_starts_with(strtolower($code), $short)) {
                    return $code;
                }
            }
        }

        return null;
    }
}
