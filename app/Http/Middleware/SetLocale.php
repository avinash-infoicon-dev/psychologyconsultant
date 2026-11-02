<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale')
            ?? ($request->user()?->locale)
            ?? config('app.locale');

        if (! Locales::isSupported($locale)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        Locales::applyCarbon($locale);

        return $next($request);
    }


    // public function handle(Request $request, Closure $next): Response
    // {
    //     // Locale from URL: /en/... or /fr/...
    //     $urlLocale = $request->route('locale');

    //     if ($urlLocale) {
    //         // Convert URL locale to application locale
    //         $locale = match ($urlLocale) {
    //             'fr' => 'fr_CH',
    //             'en' => 'en',
    //             default => config('app.locale'),
    //         };
    //     } else {
    //         // Existing fallback logic
    //         $locale = $request->session()->get('locale')
    //             ?? $request->user()?->locale
    //             ?? config('app.locale');
    //     }

    //     // Make sure the application locale exists in config
    //     if (! Locales::isSupported($locale)) {
    //         $locale = config('app.locale');
    //     }

    //     App::setLocale($locale);

    //     // Automatically provide {locale} to route()
    //     URL::defaults([
    //         'locale' => $urlLocale ?? ($locale === 'fr_CH' ? 'fr' : 'en'),
    //     ]);

    //     return $next($request);
    // }

    //  public function handle(Request $request, Closure $next): Response
    // {
    //     // Get locale from URL first
    //     $routeLocale = $request->route('locale');

    //     if ($routeLocale) {
    //         $locale = $routeLocale === 'fr'
    //             ? 'fr_CH'
    //             : 'en';
    //     } else {
    //         // Fallback to your existing logic
    //         $locale = $request->session()->get('locale')
    //             ?? ($request->user()?->locale)
    //             ?? config('app.locale');
    //     }

    //     if (! Locales::isSupported($locale)) {
    //         $locale = config('app.locale');
    //     }

    //     App::setLocale($locale);

    //     // Automatically add the URL locale to route() calls
    //     URL::defaults([
    //         'locale' => $routeLocale ?? ($locale === 'fr_CH' ? 'fr' : 'en'),
    //     ]);

    //     return $next($request);
    // }

}
